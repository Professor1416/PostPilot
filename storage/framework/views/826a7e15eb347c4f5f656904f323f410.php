


<!DOCTYPE html><html><head><meta charset="utf-8">
<style>
body{font-family:Arial,sans-serif;background:#0A0A0F;color:#E0E0F0;margin:0;padding:0}
.wrap{max-width:600px;margin:0 auto;padding:40px 20px}
.logo{font-size:22px;font-weight:800;color:#FF6B00;margin-bottom:32px}
.card{background:#111118;border:1px solid #1E1E2E;border-radius:16px;padding:32px;margin-bottom:24px}
.title{font-size:22px;font-weight:700;color:#fff;margin-bottom:12px}
.text{font-size:14px;color:#AAA;line-height:1.8;margin-bottom:12px}
.row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #1E1E2E;font-size:13px}
.key{color:#666}.val{color:#fff;font-weight:600}
.btn{display:inline-block;background:linear-gradient(135deg,#FF6B00,#FF3D00);color:#fff;padding:13px 28px;border-radius:10px;text-decoration:none;font-weight:700;font-size:14px;margin-top:16px}
.footer{font-size:11px;color:#444;text-align:center;margin-top:24px;border-top:1px solid #1E1E2E;padding-top:16px}
</style></head>
<body><div class="wrap">
<div class="logo">⚡ PostPilot</div>
<div class="card">
    <div class="title">Payment Confirmed ✓</div>
    <div class="text">Thank you, <?php echo e($user->name); ?>! Your <strong style="color:#FF6B00;"><?php echo e(config("postpilot.plans.{$payment->plan}.label")); ?></strong> is now active.</div>
    <div class="row"><span class="key">Plan</span><span class="val"><?php echo e(config("postpilot.plans.{$payment->plan}.label")); ?></span></div>
    <div class="row"><span class="key">Amount Paid</span><span class="val" style="color:#FF6B00;"><?php echo e($payment->amountInr()); ?></span></div>
    <div class="row"><span class="key">Payment ID</span><span style="font-size:11px;color:#888;"><?php echo e($payment->payment_id); ?></span></div>
    <a href="<?php echo e(config('app.url')); ?>/dashboard/calendar" class="btn">Start Scheduling →</a>
</div>
<div class="footer">PostPilot — AI Social Media Scheduling for Indian Businesses</div>
</div></body></html>





<!DOCTYPE html><html><head><meta charset="utf-8">
<style>
body{font-family:Arial,sans-serif;background:#0A0A0F;color:#E0E0F0;margin:0;padding:0}
.wrap{max-width:600px;margin:0 auto;padding:40px 20px}
.card{background:#111118;border:1px solid #C0392B;border-radius:16px;padding:32px;margin-bottom:24px}
.title{font-size:20px;font-weight:700;color:#C0392B;margin-bottom:16px}
.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #1E1E2E;font-size:13px}
.key{color:#666}.val{color:#AAA}
</style></head>
<body><div class="wrap">
<div style="font-size:22px;font-weight:800;color:#FF6B00;margin-bottom:32px;">⚡ PostPilot Admin</div>
<div class="card">
    <div class="title">[Admin] Post Publish Failed — #<?php echo e($post->id); ?></div>
    <div class="row"><span class="key">Post ID</span><span class="val">#<?php echo e($post->id); ?></span></div>
    <div class="row"><span class="key">User</span><span class="val"><?php echo e($post->user->email ?? '—'); ?></span></div>
    <div class="row"><span class="key">Retries</span><span class="val"><?php echo e($post->retry_count); ?>/3</span></div>
    <div class="row"><span class="key">Scheduled</span><span class="val"><?php echo e($post->scheduledAtIst()); ?></span></div>
    <div style="margin-top:16px;padding:12px;background:rgba(192,57,43,0.1);border-radius:8px;font-size:12px;color:#C0392B;">
        <strong>Failure reason:</strong><br><?php echo e($reason); ?>

    </div>
</div>
</div></body></html>
<?php /**PATH C:\xampp\htdocs\postpilot-laravel\resources\views/emails/payment-receipt.blade.php ENDPATH**/ ?>