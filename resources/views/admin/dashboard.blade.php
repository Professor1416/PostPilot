<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PostPilot Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: #080810; color: #E0E0F0; }
        .header { padding: 18px 32px; border-bottom: 1px solid #16162A; background: #0E0E18; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 20px; color: #fff; }
        .page { max-width: 1200px; margin: 0 auto; padding: 32px; }
        .section-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 15px; color: #DDD; margin-bottom: 16px; }
        .card { background: #0E0E18; border: 1px solid #18182A; border-radius: 14px; padding: 22px 24px; margin-bottom: 20px; }
        .grid6 { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: #0E0E18; border: 1px solid #18182A; border-radius: 14px; padding: 20px 18px; }
        .stat-val { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 28px; color: #fff; }
        .stat-label { font-size: 11px; color: #555; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 6px; }
        .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .bar-row { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .bar-label { font-size: 12px; color: #666; width: 90px; text-transform: capitalize; }
        .bar-track { flex: 1; height: 6px; background: #16162A; border-radius: 3px; overflow: hidden; }
        .bar-fill { height: 100%; background: #FF6B00; border-radius: 3px; }
        .bar-count { font-size: 12px; color: #888; width: 30px; text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 8px 12px; text-align: left; font-size: 11px; color: #444; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #16162A; }
        td { padding: 10px 12px; font-size: 13px; color: #888; border-bottom: 1px solid #0D0D18; }
        .plan-chip { font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 6px; text-transform: uppercase; }
        .chip-free     { background: #1A1A2A; color: #555; }
        .chip-starter  { background: rgba(255,107,0,0.1); color: #FF6B00; }
        .chip-growth   { background: rgba(39,174,96,0.1); color: #27AE60; }
        .chip-agency   { background: rgba(155,89,182,0.1); color: #9B59B6; }
    </style>
</head>
<body>
    <div class="header">
        <h1>⚡ PostPilot Admin</h1>
        <span style="font-size:12px;color:#444;">Last updated: {{ now()->format('d M Y, H:i') }}</span>
    </div>

    <div class="page">
        {{-- Overview Stats --}}
        <div class="grid6">
            @foreach([
                ['Total Users',    $totalUsers,     '👤', '#4285F4'],
                ['New (30d)',       $newUsers30d,    '✨', '#25D366'],
                ['Active (30d)',    $activeUsers30d, '🔥', '#FF6B00'],
                ['Total Posts',    $totalPosts,     '⚡', '#9B59B6'],
                ['Total Revenue',  '₹'.number_format($totalRevenue,0), '💰', '#F7971E'],
                ['Failed Posts',   $failedPosts,    '✕', '#C0392B'],
            ] as [$label, $value, $icon, $color])
                <div class="stat-card">
                    <div style="font-size:18px;margin-bottom:8px;">{{ $icon }}</div>
                    <div class="stat-val" style="color:{{ $label === 'Failed Posts' && $failedPosts > 0 ? '#C0392B' : '#fff' }};">{{ $value }}</div>
                    <div class="stat-label">{{ $label }}</div>
                </div>
            @endforeach
        </div>

        {{-- Plan & Revenue --}}
        <div class="grid2">
            <div class="card">
                <div class="section-title">Users by Plan</div>
                @php $totalU = array_sum($planBreakdown) ?: 1; @endphp
                @foreach($planBreakdown as $plan => $count)
                    <div class="bar-row">
                        <span class="bar-label">{{ $plan }}</span>
                        <div class="bar-track">
                            <div class="bar-fill" style="width:{{ min(100, ($count/$totalU)*100) }}%;"></div>
                        </div>
                        <span class="bar-count">{{ $count }}</span>
                    </div>
                @endforeach
            </div>

            <div class="card">
                <div class="section-title">Revenue by Month</div>
                @foreach($revenueByMonth as $month => $amount)
                    <div class="bar-row">
                        <span class="bar-label">{{ $month }}</span>
                        <div style="flex:1;font-size:14px;font-weight:700;color:#27AE60;">₹{{ number_format($amount, 0) }}</div>
                    </div>
                @endforeach
                @if(empty($revenueByMonth))
                    <p style="font-size:13px;color:#333;">No payments yet.</p>
                @endif
            </div>
        </div>

        {{-- Recent Payments --}}
        <div class="card">
            <div class="section-title">Recent Payments</div>
            <div style="overflow-x:auto;">
                <table>
                    <thead><tr><th>Email</th><th>Plan</th><th>Amount</th><th>Date</th><th>Payment ID</th></tr></thead>
                    <tbody>
                        @forelse($recentPayments as $p)
                            <tr>
                                <td>{{ $p['email'] }}</td>
                                <td><span class="plan-chip chip-{{ $p['plan'] }}">{{ $p['plan'] }}</span></td>
                                <td style="color:#27AE60;font-weight:700;">{{ $p['amount'] }}</td>
                                <td>{{ $p['date'] }}</td>
                                <td style="font-size:11px;color:#444;">{{ $p['payment_id'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="color:#333;text-align:center;padding:20px;">No payments yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Failed Posts --}}
        @if($failedPostsList->isNotEmpty())
            <div class="card" style="border-color:rgba(192,57,43,0.3);">
                <div class="section-title" style="color:#C0392B;">⚠ Failed Posts ({{ $failedPostsList->count() }})</div>
                <div style="overflow-x:auto;">
                    <table>
                        <thead><tr><th>Post ID</th><th>User</th><th>Retries</th><th>Reason</th><th>Scheduled</th></tr></thead>
                        <tbody>
                            @foreach($failedPostsList as $post)
                                <tr>
                                    <td>#{{ $post->id }}</td>
                                    <td>{{ $post->user->email ?? '—' }}</td>
                                    <td>{{ $post->retry_count }}/3</td>
                                    <td style="font-size:11px;color:#C0392B;max-width:240px;">{{ Str::limit($post->failure_reason, 80) }}</td>
                                    <td>{{ $post->scheduled_at->format('d M, H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Recent Users --}}
        <div class="card">
            <div class="section-title">Recent Users</div>
            <div style="overflow-x:auto;">
                <table>
                    <thead><tr><th>Name</th><th>Email</th><th>Plan</th><th>Posts Used</th><th>Joined</th><th>Last Active</th></tr></thead>
                    <tbody>
                        @foreach($recentUsers as $u)
                            <tr>
                                <td>{{ $u->name }}</td>
                                <td style="font-size:12px;color:#666;">{{ $u->email }}</td>
                                <td><span class="plan-chip chip-{{ $u->plan }}">{{ $u->plan }}</span></td>
                                <td>{{ $u->posts_used_this_month }}</td>
                                <td>{{ $u->created_at->format('d M Y') }}</td>
                                <td>{{ $u->last_active_at?->format('d M Y') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
