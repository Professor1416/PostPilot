<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Routing\Controller; 
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $thirtyDaysAgo = now()->subDays(30);

        $totalUsers    = User::count();
        $newUsers30d   = User::where('created_at', '>=', $thirtyDaysAgo)->count();
        $activeUsers30d = User::where('last_active_at', '>=', $thirtyDaysAgo)->count();
        $totalPosts    = Post::count();
        $failedPosts   = Post::where('status', 'failed')->count();

        $totalRevenue  = Payment::where('status', 'success')->sum('amount') / 100;

        $planBreakdown = User::select('plan', DB::raw('count(*) as count'))
            ->groupBy('plan')
            ->pluck('count', 'plan')
            ->toArray();

        $revenueByMonth = Payment::where('status', 'success')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('SUM(amount)/100 as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $recentPayments = Payment::with('user')
            ->where('status', 'success')
            ->latest()
            ->limit(15)
            ->get()
            ->map(fn($p) => [
                'email'      => $p->user->email ?? '—',
                'plan'       => $p->plan,
                'amount'     => $p->amountInr(),
                'date'       => $p->created_at->format('d M Y'),
                'payment_id' => $p->payment_id,
            ]);

        $recentUsers = User::latest()
            ->limit(30)
            ->get(['id', 'name', 'email', 'plan', 'posts_used_this_month',
                   'total_posts_scheduled', 'total_posts_published', 'created_at', 'last_active_at']);

        $failedPostsList = Post::where('status', 'failed')
            ->with('user')
            ->latest('updated_at')
            ->limit(20)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'newUsers30d', 'activeUsers30d',
            'totalPosts', 'failedPosts', 'totalRevenue',
            'planBreakdown', 'revenueByMonth',
            'recentPayments', 'recentUsers', 'failedPostsList'
        ));
    }
}
