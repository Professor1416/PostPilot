<?php $__env->startSection('title', 'Pricing'); ?>
<?php $__env->startSection('content'); ?>
<div class="pp-main" style="max-width:1100px;">
    <div style="text-align:center;margin-bottom:28px;">
        <div style="display:inline-block;background:rgba(255,107,0,0.1);border:1px solid rgba(255,107,0,0.3);border-radius:20px;color:#FF6B00;font-size:11px;font-weight:700;padding:4px 14px;margin-bottom:16px;letter-spacing:1px;text-transform:uppercase;">Simple Pricing</div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:38px;color:#fff;margin-bottom:12px;line-height:1.1;">One price. No surprises.</h1>
        <p style="color:#555;font-size:16px;line-height:1.6;">Start free with 5 scheduled posts. Upgrade when ready. Cancel anytime.</p>
    </div>

    <div style="display:flex;align-items:center;justify-content:center;gap:4px;flex-wrap:wrap;background:rgba(255,107,0,0.06);border:1px solid rgba(255,107,0,0.18);border-radius:12px;padding:14px 24px;margin-bottom:48px;font-size:14px;color:#AAA;">
        🎁 Every new account gets <strong style="color:#FF6B00;margin:0 4px;">5 free scheduled posts</strong> — no credit card required
    </div>

    <div style="display:flex;gap:20px;justify-content:center;flex-wrap:wrap;margin-bottom:40px;">
        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $planId => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($planId === 'free'): ?> <?php continue; ?> <?php endif; ?>
            <?php $isCurrent = auth()->user()->plan === $planId; $isHL = $planId === 'growth'; ?>

            <div style="background:<?php echo e($isHL ? 'rgba(255,107,0,0.04)' : '#0E0E18'); ?>;border:<?php echo e($isHL ? '2px solid rgba(255,107,0,0.45)' : '1px solid #18182A'); ?>;border-radius:20px;padding:32px 28px;width:300px;display:flex;flex-direction:column;position:relative;">
                <?php if($isHL): ?>
                    <div style="position:absolute;top:-16px;left:50%;transform:translateX(-50%);background:linear-gradient(135deg,#FF6B00,#FF3D00);color:#fff;font-size:11px;font-weight:700;padding:5px 18px;border-radius:20px;white-space:nowrap;">Most Popular</div>
                <?php endif; ?>

                <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:12px;color:#666;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:12px;"><?php echo e($plan['label']); ?></div>

                <div style="display:flex;align-items:flex-start;gap:2px;margin-bottom:20px;">
                    <span style="font-family:'Syne',sans-serif;font-weight:700;font-size:20px;color:#FF6B00;padding-top:8px;">₹</span>
                    <span style="font-family:'Syne',sans-serif;font-weight:800;font-size:52px;color:#fff;line-height:1;"><?php echo e(number_format($plan['amount_paise'] / 100)); ?></span>
                    <span style="font-size:12px;color:#444;padding-top:36px;margin-left:2px;">/mo</span>
                </div>

                <div style="height:1px;background:#16162A;margin-bottom:20px;"></div>

                <ul style="list-style:none;display:flex;flex-direction:column;gap:10px;flex:1;margin-bottom:28px;">
                    <?php
                        $features = [
                            'starter' => ['1 Instagram account','30 posts per month','AI caption generation','Festival calendar','Image upload','Email support'],
                            'growth'  => ['3 Instagram accounts','150 posts per month','AI caption generation','Festival calendar','Image upload','Priority support'],
                            'agency'  => ['10 Instagram accounts','Unlimited posts','AI caption generation','Festival calendar','Image upload','Dedicated support','Bulk scheduling'],
                        ];
                    ?>
                    <?php $__currentLoopData = $features[$planId] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li style="display:flex;align-items:flex-start;gap:10px;font-size:13px;color:#888;">
                            <span style="color:#FF6B00;font-weight:800;font-size:12px;flex-shrink:0;margin-top:1px;">✓</span>
                            <span><?php echo e($f); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>

                <?php if($isCurrent): ?>
                    <button disabled style="width:100%;padding:14px;background:#111;border:1px solid #25D366;border-radius:12px;color:#25D366;font-size:14px;font-weight:700;font-family:'Syne',sans-serif;cursor:default;">✓ Current Plan</button>
                    <p style="font-size:11px;color:#444;text-align:center;margin-top:8px;">You are on this plan</p>
                <?php else: ?>
                    <button onclick="startPayment('<?php echo e($planId); ?>', <?php echo e($plan['amount_paise']); ?>)"
                        style="width:100%;padding:14px;background:<?php echo e($isHL ? 'linear-gradient(135deg,#FF6B00,#FF3D00)' : '#1A1A2A'); ?>;border:<?php echo e($isHL ? 'none' : '1px solid #2A2A3E'); ?>;border-radius:12px;color:<?php echo e($isHL ? '#fff' : '#888'); ?>;font-size:14px;font-weight:700;font-family:'Syne',sans-serif;cursor:pointer;<?php echo e($isHL ? 'box-shadow:0 8px 24px rgba(255,107,0,0.3);' : ''); ?>">
                        Get <?php echo e($plan['label']); ?>

                    </button>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <h2 style="font-family:'Syne',sans-serif;font-weight:700;font-size:24px;color:#fff;margin-bottom:28px;text-align:center;">Common Questions</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;">
        <?php $__currentLoopData = [
            ['Can I cancel anytime?', 'Yes. Cancel before your next billing date. No lock-in, no penalties.'],
            ['What payment methods?', 'UPI, credit cards, debit cards, net banking, and wallets — all via Razorpay.'],
            ['Do I need a Facebook account?', 'Yes. Instagram Business accounts are linked to Facebook Pages.'],
            ['Is my data secure?', 'Yes. Meta tokens are AES-256 encrypted. API keys never touch the browser.'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$q, $a]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="background:#0E0E18;border:1px solid #18182A;border-radius:14px;padding:20px 22px;">
                <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:14px;color:#DDD;margin-bottom:10px;"><?php echo e($q); ?></div>
                <div style="font-size:13px;color:#555;line-height:1.7;"><?php echo e($a); ?></div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const RAZORPAY_KEY   = '<?php echo e(config("services.razorpay.key_id")); ?>';
const ORDER_URL      = '<?php echo e(route("payment.create-order")); ?>';
const VERIFY_URL     = '<?php echo e(route("payment.verify")); ?>';
const USER_NAME      = '<?php echo e(auth()->user()->name); ?>';
const USER_EMAIL     = '<?php echo e(auth()->user()->email); ?>';

async function startPayment(planId, amount) {
    try {
        const order = await apiPost(ORDER_URL, { plan: planId });
        if (order.error) { showToast(order.error, 'error'); return; }

        const rzp = new Razorpay({
            key:         RAZORPAY_KEY,
            amount:      order.amount,
            currency:    'INR',
            name:        'PostPilot',
            description: `${order.plan_label} — Monthly`,
            order_id:    order.order_id,
            prefill:     { name: USER_NAME, email: USER_EMAIL },
            theme:       { color: '#FF6B00' },
            handler: async response => {
                const result = await apiPost(VERIFY_URL, {
                    razorpay_order_id:   response.razorpay_order_id,
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_signature:  response.razorpay_signature,
                    plan:                planId,
                });
                if (result.success) {
                    showToast('Plan activated! 🎉');
                    setTimeout(() => window.location.href = '<?php echo e(route("dashboard.calendar")); ?>', 1500);
                } else {
                    showToast(result.error || 'Verification failed', 'error');
                }
            },
        });

        rzp.on('payment.failed', () => showToast('Payment failed. Please try again.', 'error'));
        rzp.open();
    } catch (e) {
        showToast('Payment setup failed', 'error');
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postpilot-laravel\resources\views/dashboard/pricing.blade.php ENDPATH**/ ?>