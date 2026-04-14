<?php

namespace App\Http\Controllers;

use App\Jobs\PublishPost;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'platforms'            => 'required|array|min:1',
            'platforms.*'          => 'in:instagram,facebook',
            'connected_account_ids' => 'required|array|min:1',
            'caption'              => 'required|string|max:2200',
            'scheduled_at'         => 'required|date|after:now',
            'language'             => 'nullable|in:english,hindi,hinglish',
            'festival'             => 'nullable|string|max:60',
            'ai_generated'         => 'nullable|boolean',
            'image'                => 'nullable|image|max:8192', // 8MB
        ]);

        $user = Auth::user();
        $user->checkAndResetQuota();

        if (!$user->canScheduleMore()) {
            return response()->json([
                'error'   => 'Post quota exceeded. Please upgrade your plan.',
                'code'    => 'QUOTA_EXCEEDED',
                'upgrade' => true,
            ], 402);
        }

        // Handle image upload
        $imageUrl  = null;
        $imagePath = null;

        if ($request->hasFile('image')) {
            $path      = $request->file('image')->store('posts/' . $user->id, 'public');
            $imageUrl  = Storage::url($path);
            $imagePath = $path;
        }

        // Create post
        $scheduledAt = \Carbon\Carbon::parse($request->scheduled_at)->utc();

        $post = Post::create([
            'user_id'              => $user->id,
            'platforms'            => $request->platforms,
            'connected_account_ids' => $request->connected_account_ids,
            'caption'              => $request->caption,
            'image_url'            => $imageUrl,
            'image_path'           => $imagePath,
            'status'               => 'scheduled',
            'scheduled_at'         => $scheduledAt,
            'ai_generated'         => $request->boolean('ai_generated'),
            'festival'             => $request->festival,
            'language'             => $request->language ?? 'hinglish',
        ]);

        // Dispatch job at scheduled time
        PublishPost::dispatch($post->id)->delay($scheduledAt);

        // Deduct quota
        $user->increment('posts_used_this_month');
        $user->increment('total_posts_scheduled');

        Log::info('Post scheduled', ['post_id' => $post->id, 'user_id' => $user->id]);

        return response()->json([
            'post_id'               => $post->id,
            'status'                => $post->status,
            'scheduled_at'          => $post->scheduled_at->toISOString(),
            'posts_used_this_month' => $user->fresh()->posts_used_this_month,
            'posts_remaining'       => $user->fresh()->postsRemaining(),
        ], 201);
    }

    public function show(Post $post)
    {
        $this->authorize('view', $post);

        return response()->json([
            'post_id'          => $post->id,
            'platforms'        => $post->platforms,
            'caption'          => $post->caption,
            'image_url'        => $post->image_url,
            'status'           => $post->status,
            'scheduled_at'     => $post->scheduled_at->toISOString(),
            'scheduled_at_ist' => $post->scheduledAtIst(),
            'published_at'     => $post->published_at?->toISOString(),
            'platform_post_ids' => $post->platform_post_ids,
            'retry_count'      => $post->retry_count,
            'failure_reason'   => $post->failure_reason,
            'ai_generated'     => $post->ai_generated,
            'festival'         => $post->festival,
            'language'         => $post->language,
            'created_at'       => $post->created_at->toISOString(),
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        if (!$post->isEditable()) {
            return response()->json([
                'error' => 'This post cannot be edited.',
            ], 403);
        }

        $request->validate([
            'caption'      => 'nullable|string|max:2200',
            'scheduled_at' => 'nullable|date|after:now',
            'festival'     => 'nullable|string|max:60',
        ]);

        $data = $request->only(['caption', 'festival', 'language']);

        if ($request->scheduled_at) {
            $newTime = \Carbon\Carbon::parse($request->scheduled_at)->utc();
            $data['scheduled_at'] = $newTime;

            // Re-dispatch job with new time
            PublishPost::dispatch($post->id)->delay($newTime);
        }

        $post->update($data);

        return response()->json(['post_id' => $post->id, 'updated_at' => $post->updated_at->toISOString()]);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        if ($post->status === 'published') {
            return response()->json(['error' => 'Cannot delete a published post.'], 403);
        }

        // Delete image if exists
        if ($post->image_path) {
            Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();

        return response()->json(['success' => true]);
    }

    public function reschedule(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        if ($post->status !== 'failed') {
            return response()->json(['error' => 'Can only reschedule failed posts.'], 400);
        }

        $request->validate(['scheduled_at' => 'required|date|after:now']);

        $newTime = \Carbon\Carbon::parse($request->scheduled_at)->utc();

        $post->update([
            'status'         => 'scheduled',
            'scheduled_at'   => $newTime,
            'retry_count'    => 0,
            'failure_reason' => null,
        ]);

        PublishPost::dispatch($post->id)->delay($newTime);

        return response()->json([
            'post_id'      => $post->id,
            'status'       => 'scheduled',
            'scheduled_at' => $newTime->toISOString(),
        ]);
    }
}
