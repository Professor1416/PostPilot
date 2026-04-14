<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\ConnectedAccount;
use App\Services\MetaService;
use App\Mail\PostFailedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PublishPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60; // 60 seconds between retries

    public function __construct(public readonly int $postId) {}

    public function handle(MetaService $meta): void
    {
        $post = Post::with('user')->find($this->postId);

        if (!$post) {
            Log::warning('PublishPost: post not found', ['post_id' => $this->postId]);
            return;
        }

        // Skip if already handled
        if ($post->status !== 'scheduled') {
            Log::info('PublishPost: post no longer scheduled, skipping', [
                'post_id' => $this->postId,
                'status'  => $post->status,
            ]);
            return;
        }

        // Mark as publishing to prevent duplicate runs
        $post->update(['status' => 'publishing']);

        $platformPostIds = [];
        $errors = [];

        foreach ($post->connected_account_ids as $accountId) {
            try {
                $account = ConnectedAccount::find($accountId);

                if (!$account || !$account->is_active) {
                    $errors[] = "Account {$accountId} not found or inactive";
                    continue;
                }

                $accessToken = $account->getDecryptedToken();

                if ($account->platform === 'instagram') {
                    $platformPostId = $meta->publishInstagramPost(
                        $accessToken,
                        $account->platform_user_id,
                        $post->caption,
                        $post->image_url
                    );
                    $platformPostIds['instagram'] = $platformPostId;
                }

                $account->markUsed();

                Log::info('Published to platform', [
                    'post_id'  => $this->postId,
                    'platform' => $account->platform,
                    'platform_post_id' => $platformPostId ?? null,
                ]);

            } catch (\Exception $e) {
                $errors[] = "{$accountId}: " . $e->getMessage();
                Log::error('Platform publish failed', [
                    'post_id'    => $this->postId,
                    'account_id' => $accountId,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        if (!empty($platformPostIds)) {
            // At least one platform succeeded
            $post->update([
                'status'           => 'published',
                'published_at'     => now(),
                'platform_post_ids' => $platformPostIds,
                'failure_reason'   => null,
            ]);

            $post->user->increment('total_posts_published');

            Log::info('Post published successfully', [
                'post_id'          => $this->postId,
                'platform_post_ids' => $platformPostIds,
            ]);

        } else {
            // All failed — throw so Laravel retries
            $reason = implode('; ', $errors);
            $post->update(['status' => 'scheduled']); // reset for retry
            throw new \RuntimeException($reason);
        }
    }

    public function failed(\Throwable $exception): void
    {
        $post = Post::with('user')->find($this->postId);

        if (!$post) return;

        $post->update([
            'status'         => 'failed',
            'failure_reason' => $exception->getMessage(),
            'retry_count'    => $this->attempts(),
        ]);

        Log::error('Post permanently failed', [
            'post_id' => $this->postId,
            'error'   => $exception->getMessage(),
        ]);

        // Email admin
        try {
            Mail::to(config('mail.from.address'))
                ->send(new PostFailedMail($post, $exception->getMessage()));
        } catch (\Exception $e) {
            Log::error('Failed to send failure alert email', ['error' => $e->getMessage()]);
        }
    }
}
