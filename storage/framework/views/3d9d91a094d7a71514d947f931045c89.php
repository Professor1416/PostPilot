<?php $__env->startSection('title', 'Connected Accounts'); ?>
<?php $__env->startSection('content'); ?>
<div class="pp-main" style="max-width:900px;">
    <h1 class="pp-page-title">Connected Accounts</h1>
    <p class="pp-page-sub">
        <?php echo e($accounts->count()); ?> of <?php echo e($user->account_limit); ?> account<?php echo e($user->account_limit !== 1 ? 's' : ''); ?> connected
        <?php if($accounts->count() >= $user->account_limit): ?> — <a href="<?php echo e(route('dashboard.pricing')); ?>" style="color:#FF6B00;">upgrade to connect more</a><?php endif; ?>
    </p>

    
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:28px;">
        <div style="flex:1;height:6px;background:#18182A;border-radius:3px;overflow:hidden;">
            <div style="height:100%;background:linear-gradient(90deg,#FF6B00,#FF3D00);border-radius:3px;width:<?php echo e(min(100, ($accounts->count() / max(1, $user->account_limit)) * 100)); ?>%;transition:width 0.4s;"></div>
        </div>
        <span style="font-size:12px;color:#555;flex-shrink:0;"><?php echo e($accounts->count()); ?> / <?php echo e($user->account_limit); ?></span>
    </div>

    
    <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:12px;color:#666;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;">Your Accounts</div>

    <?php if($accounts->isEmpty()): ?>
        <div style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:40px 20px;background:#0E0E18;border:1px dashed #18182A;border-radius:16px;margin-bottom:24px;">
            <span style="font-size:36px;">🔗</span>
            <p style="font-size:15px;font-weight:600;color:#555;font-family:'Syne',sans-serif;">No accounts connected yet</p>
            <p style="font-size:13px;color:#333;text-align:center;max-width:320px;">Connect your Instagram Business account to start scheduling posts</p>
        </div>
    <?php else: ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-bottom:32px;">
            <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="background:#0E0E18;border:1px solid <?php echo e($account->isTokenExpiringSoon() ? 'rgba(230,126,34,0.4)' : '#18182A'); ?>;border-radius:16px;padding:20px;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                        <?php if($account->profile_picture_url): ?>
                            <img src="<?php echo e($account->profile_picture_url); ?>" style="width:44px;height:44px;border-radius:50%;border:2px solid #FF6B00;object-fit:cover;" alt="">
                        <?php else: ?>
                            <div style="width:44px;height:44px;border-radius:50%;background:#18182A;display:flex;align-items:center;justify-content:center;color:#555;font-size:18px;">@</div>
                        <?php endif; ?>
                        <div style="flex:1;">
                            <div style="font-size:14px;font-weight:700;color:#DDD;">{{ $account->account_name }}</div>
                            <div style="font-size:11px;color:#555;margin-top:2px;">📸 Instagram Business</div>
                        </div>
                        <div style="width:10px;height:10px;border-radius:50%;background:<?php echo e($account->isTokenExpiringSoon() ? '#E67E22' : '#27AE60'); ?>;flex-shrink:0;"></div>
                    </div>

                    <?php if($account->isTokenExpiringSoon()): ?>
                        <div style="background:rgba(230,126,34,0.1);border:1px solid rgba(230,126,34,0.3);border-radius:8px;padding:8px 12px;font-size:12px;color:#E67E22;margin-bottom:12px;">
                            ⚠ Token expiring soon — reconnect to keep posting
                        </div>
                    <?php endif; ?>

                    <?php if($account->connected_at): ?>
                        <div style="font-size:11px;color:#333;margin-bottom:14px;">Connected <?php echo e($account->created_at->format('d M Y')); ?></div>
                    <?php endif; ?>

                    <div style="display:flex;gap:8px;">
                        <?php if($account->isTokenExpiringSoon()): ?>
                            <a href="<?php echo e(route('auth.instagram')); ?>" style="flex:1;text-align:center;padding:8px;background:rgba(255,107,0,0.1);border:1px solid rgba(255,107,0,0.3);border-radius:8px;color:#FF6B00;font-size:12px;font-weight:600;text-decoration:none;">Reconnect</a>
                        <?php endif; ?>
                        <form method="POST" action="<?php echo e(route('accounts.disconnect', $account)); ?>" style="flex:1;" onsubmit="return confirm('Disconnect this account? Scheduled posts will be moved to draft.')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" style="width:100%;padding:8px;background:none;border:1px solid #22223A;border-radius:8px;color:#666;font-size:12px;cursor:pointer;">Disconnect</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    
    <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:12px;color:#666;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;">Add Account</div>

    <div style="background:#0E0E18;border:1px solid #18182A;border-radius:16px;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:16px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:16px;">
            <span style="font-size:36px;">📸</span>
            <div>
                <div style="font-size:15px;font-weight:700;color:#DDD;margin-bottom:4px;">Instagram Business</div>
                <div style="font-size:12px;color:#555;max-width:300px;">Requires an Instagram Business or Creator account linked to a Facebook Page.</div>
            </div>
        </div>
        <?php if($accounts->count() < $user->account_limit): ?>
            <a href="<?php echo e(route('auth.instagram')); ?>" class="pp-btn" style="background:linear-gradient(135deg,#E1306C,#C13584);color:#fff;flex-shrink:0;">Connect Instagram</a>
        <?php else: ?>
            <button disabled style="padding:10px 20px;background:#1A1A28;border:none;border-radius:10px;color:#444;font-size:13px;font-weight:700;cursor:not-allowed;">Limit Reached</button>
        <?php endif; ?>
    </div>

    <div style="background:#0A0A14;border:1px solid #0F0F1E;border-radius:12px;padding:18px 20px;">
        <div style="font-size:13px;font-weight:600;color:#666;margin-bottom:12px;">How to connect Instagram</div>
        <?php $__currentLoopData = ['Convert your Instagram to a Business or Creator account (Settings → Account → Switch to Professional)', 'Link it to a Facebook Page (Instagram Settings → Account → Linked Accounts)', 'Click "Connect Instagram" above and authorize PostPilot']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="display:flex;gap:12px;font-size:12px;color:#555;line-height:1.6;margin-bottom:8px;">
                <span style="width:20px;height:20px;border-radius:50%;background:rgba(255,107,0,0.15);color:#FF6B00;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?php echo e($i+1); ?></span>
                <span><?php echo e($step); ?></span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postpilot-laravel\resources\views/dashboard/accounts.blade.php ENDPATH**/ ?>