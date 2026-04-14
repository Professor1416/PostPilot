<?php

namespace App\Http\Controllers;

use App\Models\Festival;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function calendar()
    {
        $user    = Auth::user();
        $user->checkAndResetQuota();

        $accounts = $user->connectedAccounts;
        $festivals = Festival::upcoming(3)->get();

        return view('dashboard.calendar', compact('user', 'accounts', 'festivals'));
    }

    public function queue(Request $request)
    {
        $user = Auth::user();

        $query = Post::forUser($user->id)
            ->orderBy('scheduled_at', 'desc');

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->platform) {
            $query->whereJsonContains('platforms', $request->platform);
        }

        $posts = $query->paginate(20)->withQueryString();

        return view('dashboard.queue', compact('user', 'posts'));
    }

    public function accounts()
    {
        $user     = Auth::user();
        $accounts = $user->connectedAccounts;

        return view('dashboard.accounts', compact('user', 'accounts'));
    }

    public function pricing()
    {
        $user  = Auth::user();
        $plans = config('postpilot.plans');

        return view('dashboard.pricing', compact('user', 'plans'));
    }

    public function completeOnboarding()
    {
        Auth::user()->update(['onboarding_complete' => true]);
        return response()->json(['success' => true]);
    }

    // ─── Calendar API ─────────────────────────────────────────────────────────

    public function calendarData(Request $request)
    {
        $user  = Auth::user();
        $year  = (int) ($request->year  ?? now()->year);
        $month = (int) ($request->month ?? now()->month);

        $posts = Post::forUser($user->id)
            ->forMonth($year, $month)
            ->orderBy('scheduled_at')
            ->get(['id', 'platforms', 'status', 'caption', 'scheduled_at', 'festival', 'image_url']);

        $calendar = [];
        foreach ($posts as $post) {
            $dateKey = $post->scheduled_at->format('Y-m-d');
            $calendar[$dateKey][] = [
                'post_id'        => $post->id,
                'platforms'      => $post->platforms,
                'status'         => $post->status,
                'caption_snippet' => $post->captionSnippet(),
                'festival'       => $post->festival,
                'has_image'      => (bool) $post->image_url,
                'scheduled_at'   => $post->scheduled_at->toISOString(),
            ];
        }

        return response()->json(['calendar' => $calendar, 'year' => $year, 'month' => $month]);
    }
}
