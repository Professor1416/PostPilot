<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class MetaService
{
    private Client $client;
    private string $graphUrl;

    public function __construct()
    {
        $this->graphUrl = config('postpilot.meta.graph_url');
        $this->client   = new Client(['timeout' => 60]);
    }

    // ─── OAuth ────────────────────────────────────────────────────────────────

    public function exchangeCodeForToken(string $code, string $redirectUri): string
    {
        $response = $this->client->get("{$this->graphUrl}/oauth/access_token", [
            'query' => [
                'client_id'     => config('postpilot.meta.app_id'),
                'client_secret' => config('postpilot.meta.app_secret'),
                'redirect_uri'  => $redirectUri,
                'code'          => $code,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (isset($data['error'])) {
            throw new \RuntimeException($data['error']['message'] ?? 'OAuth exchange failed');
        }

        return $data['access_token'];
    }

    public function getLongLivedToken(string $shortToken): array
    {
        $response = $this->client->get("{$this->graphUrl}/oauth/access_token", [
            'query' => [
                'grant_type'        => 'fb_exchange_token',
                'client_id'         => config('postpilot.meta.app_id'),
                'client_secret'     => config('postpilot.meta.app_secret'),
                'fb_exchange_token' => $shortToken,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (isset($data['error'])) {
            throw new \RuntimeException($data['error']['message'] ?? 'Token exchange failed');
        }

        $expiresAt = isset($data['expires_in'])
            ? now()->addSeconds($data['expires_in'])
            : null;

        return ['token' => $data['access_token'], 'expires_at' => $expiresAt];
    }

    public function getInstagramAccountInfo(string $accessToken): array
    {
        // Get Facebook pages linked to this token
        $response = $this->client->get("{$this->graphUrl}/me/accounts", [
            'query' => [
                'fields'       => 'id,name,picture,instagram_business_account',
                'access_token' => $accessToken,
            ],
        ]);

        $pages = json_decode($response->getBody()->getContents(), true);

        if (isset($pages['error'])) {
            throw new \RuntimeException($pages['error']['message']);
        }

        $pageWithIg = collect($pages['data'] ?? [])
            ->first(fn($p) => isset($p['instagram_business_account']));

        if (!$pageWithIg) {
            throw new \RuntimeException(
                'No Instagram Business account found. Please link your Instagram to a Facebook Page first.'
            );
        }

        $igId = $pageWithIg['instagram_business_account']['id'];

        // Get Instagram account details
        $igResponse = $this->client->get("{$this->graphUrl}/{$igId}", [
            'query' => [
                'fields'       => 'id,username,profile_picture_url',
                'access_token' => $accessToken,
            ],
        ]);

        $ig = json_decode($igResponse->getBody()->getContents(), true);

        return [
            'platform_user_id'    => $ig['id'],
            'account_name'        => $ig['username'] ?? $pageWithIg['name'],
            'profile_picture_url' => $ig['profile_picture_url'] ?? $pageWithIg['picture']['data']['url'] ?? null,
        ];
    }

    // ─── Publishing ───────────────────────────────────────────────────────────

    public function publishInstagramPost(string $accessToken, string $igUserId, string $caption, ?string $imageUrl): string
    {
        if (!$imageUrl) {
            throw new \RuntimeException('Instagram requires an image. Please upload an image for your post.');
        }

        // Step 1: Create media container
        $containerResponse = $this->client->post("{$this->graphUrl}/{$igUserId}/media", [
            'json' => [
                'image_url'    => $imageUrl,
                'caption'      => $caption,
                'access_token' => $accessToken,
            ],
        ]);

        $container = json_decode($containerResponse->getBody()->getContents(), true);

        if (isset($container['error'])) {
            throw new \RuntimeException('Media container failed: ' . $container['error']['message']);
        }

        $containerId = $container['id'];

        // Step 2: Wait for container to be ready
        $this->waitForContainer($igUserId, $containerId, $accessToken);

        // Step 3: Publish
        $publishResponse = $this->client->post("{$this->graphUrl}/{$igUserId}/media_publish", [
            'json' => [
                'creation_id'  => $containerId,
                'access_token' => $accessToken,
            ],
        ]);

        $published = json_decode($publishResponse->getBody()->getContents(), true);

        if (isset($published['error'])) {
            throw new \RuntimeException('Publish failed: ' . $published['error']['message']);
        }

        Log::info('Instagram post published', ['ig_user_id' => $igUserId, 'post_id' => $published['id']]);

        return $published['id'];
    }

    private function waitForContainer(string $igUserId, string $containerId, string $accessToken, int $maxWaitSeconds = 30): void
    {
        $start = time();

        while (time() - $start < $maxWaitSeconds) {
            $response = $this->client->get("{$this->graphUrl}/{$containerId}", [
                'query' => [
                    'fields'       => 'status_code,status',
                    'access_token' => $accessToken,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if ($data['status_code'] === 'FINISHED') return;
            if ($data['status_code'] === 'ERROR') {
                throw new \RuntimeException('Media container error: ' . ($data['status'] ?? 'unknown'));
            }

            sleep(3);
        }

        throw new \RuntimeException('Media container timed out after ' . $maxWaitSeconds . ' seconds.');
    }

    public function getOAuthUrl(): string
    {
        $params = http_build_query([
            'client_id'     => config('postpilot.meta.app_id'),
            'redirect_uri'  => config('postpilot.meta.redirect_uri'),
            'scope'         => 'instagram_basic,instagram_content_publish,pages_read_engagement,pages_show_list',
            'response_type' => 'code',
            'state'         => csrf_token(),
        ]);

        return "https://www.facebook.com/v19.0/dialog/oauth?{$params}";
    }
}
