<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class AnthropicService
{
    private Client $client;
    private string $apiKey;
    private string $model = 'claude-opus-4-5';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.key');
        $this->client = new Client([
            'base_uri' => 'https://api.anthropic.com/v1/',
            'headers'  => [
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type'      => 'application/json',
            ],
            'timeout'  => 30,
        ]);
    }

    public function generateCaption(array $params): string
    {
        $prompt = $this->buildPrompt($params);

        try {
            $response = $this->client->post('messages', [
                'json' => [
                    'model'      => $this->model,
                    'max_tokens' => 1024,
                    'messages'   => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $result = collect($data['content'] ?? [])
                ->where('type', 'text')
                ->pluck('text')
                ->implode('');

            Log::info('AI caption generated', [
                'content_type'  => $params['content_type'],
                'input_tokens'  => $data['usage']['input_tokens'] ?? 0,
                'output_tokens' => $data['usage']['output_tokens'] ?? 0,
            ]);

            return $result;

        } catch (GuzzleException $e) {
            Log::error('Anthropic API error', ['error' => $e->getMessage()]);
            throw new \RuntimeException('AI generation failed. Please try again.');
        }
    }

    private function buildPrompt(array $p): string
    {
        $festival = ($p['festival'] ?? 'None') !== 'None'
            ? "Festival/Occasion: {$p['festival']}."
            : '';

        $base = "Business: \"{$p['business_name']}\" ({$p['business_type']}). "
              . "Offer: \"{$p['offer']}\". {$festival} "
              . "Language: {$p['language']}.";

        $prompts = [
            'instagram' => "{$base} Tone: {$p['tone']}. "
                . "Write an Instagram caption. Hook in first line, line breaks, "
                . "10-15 relevant hashtags at end. Max 250 words.",

            'facebook'  => "{$base} Tone: {$p['tone']}. "
                . "Write a Facebook post. Conversational, 2-3 paragraphs, "
                . "end with a call to action. Max 200 words.",

            'poster'    => "{$base} Return ONLY a valid JSON object for a poster. "
                . "Fields: {\"headline\":\"max 6 words\",\"subheadline\":\"max 12 words\","
                . "\"offer_text\":\"max 20 words\",\"call_to_action\":\"max 5 words\","
                . "\"badge\":\"e.g. 30% OFF\",\"tagline\":\"max 8 words\"}. "
                . "No markdown. JSON only.",
        ];

        return $prompts[$p['content_type']] ?? $prompts['instagram'];
    }
}
