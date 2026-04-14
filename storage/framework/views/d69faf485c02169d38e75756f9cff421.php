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
        <span style="font-size:12px;color:#444;">Last updated: <?php echo e(now()->format('d M Y, H:i')); ?></span>
    </div>

    <div class="page">
        
        <div class="grid6">
            <?php $__currentLoopData = [
                ['Total Users',    $totalUsers,     '👤', '#4285F4'],
                ['New (30d)',       $newUsers30d,    '✨', '#25D366'],
                ['Active (30d)',    $activeUsers30d, '🔥', '#FF6B00'],
                ['Total Posts',    $totalPosts,     '⚡', '#9B59B6'],
                ['Total Revenue',  '₹'.number_format($totalRevenue,0), '💰', '#F7971E'],
                ['Failed Posts',   $failedPosts,    '✕', '#C0392B'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $value, $icon, $color]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="stat-card">
                    <div style="font-size:18px;margin-bottom:8px;"><?php echo e($icon); ?></div>
                    <div class="stat-val" style="color:<?php echo e($label === 'Failed Posts' && $failedPosts > 0 ? '#C0392B' : '#fff'); ?>;"><?php echo e($value); ?></div>
                    <div class="stat-label"><?php echo e($label); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="grid2">
            <div class="card">
                <div class="section-title">Users by Plan</div>
                <?php $totalU = array_sum($planBreakdown) ?: 1; ?>
                <?php $__currentLoopData = $planBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bar-row">
                        <span class="bar-label"><?php echo e($plan); ?></span>
                        <div class="bar-track">
                            <div class="bar-fill" style="width:<?php echo e(min(100, ($count/$totalU)*100)); ?>%;"></div>
                        </div>
                        <span class="bar-count"><?php echo e($count); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="card">
                <div class="section-title">Revenue by Month</div>
                <?php $__currentLoopData = $revenueByMonth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bar-row">
                        <span class="bar-label"><?php echo e($month); ?></span>
                        <div style="flex:1;font-size:14px;font-weight:700;color:#27AE60;">₹<?php echo e(number_format($amount, 0)); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if(empty($revenueByMonth)): ?>
                    <p style="font-size:13px;color:#333;">No payments yet.</p>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="card">
            <div class="section-title">Recent Payments</div>
            <div style="overflow-x:auto;">
                <table>
                    <thead><tr><th>Email</th><th>Plan</th><th>Amount</th><th>Date</th><th>Payment ID</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($p['email']); ?></td>
                                <td><span class="plan-chip chip-<?php echo e($p['plan']); ?>"><?php echo e($p['plan']); ?></span></td>
                                <td style="color:#27AE60;font-weight:700;"><?php echo e($p['amount']); ?></td>
                                <td><?php echo e($p['date']); ?></td>
                                <td style="font-size:11px;color:#444;"><?php echo e($p['payment_id']); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="5" style="color:#333;text-align:center;padding:20px;">No payments yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <?php if($failedPostsList->isNotEmpty()): ?>
            <div class="card" style="border-color:rgba(192,57,43,0.3);">
                <div class="section-title" style="color:#C0392B;">⚠ Failed Posts (<?php echo e($failedPostsList->count()); ?>)</div>
                <div style="overflow-x:auto;">
                    <table>
                        <thead><tr><th>Post ID</th><th>User</th><th>Retries</th><th>Reason</th><th>Scheduled</th></tr></thead>
                        <tbody>
                            <?php $__currentLoopData = $failedPostsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>#<?php echo e($post->id); ?></td>
                                    <td><?php echo e($post->user->email ?? '—'); ?></td>
                                    <td><?php echo e($post->retry_count); ?>/3</td>
                                    <td style="font-size:11px;color:#C0392B;max-width:240px;"><?php echo e(Str::limit($post->failure_reason, 80)); ?></td>
                                    <td><?php echo e($post->scheduled_at->format('d M, H:i')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="card">
            <div class="section-title">Recent Users</div>
            <div style="overflow-x:auto;">
                <table>
                    <thead><tr><th>Name</th><th>Email</th><th>Plan</th><th>Posts Used</th><th>Joined</th><th>Last Active</th></tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($u->name); ?></td>
                                <td style="font-size:12px;color:#666;"><?php echo e($u->email); ?></td>
                                <td><span class="plan-chip chip-<?php echo e($u->plan); ?>"><?php echo e($u->plan); ?></span></td>
                                <td><?php echo e($u->posts_used_this_month); ?></td>
                                <td><?php echo e($u->created_at->format('d M Y')); ?></td>
                                <td><?php echo e($u->last_active_at?->format('d M Y') ?? '—'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\postpilot-laravel\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>