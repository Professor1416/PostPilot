<!DOCTYPE html>
<html>
<head><meta charset="utf-8">
<style>
body{font-family:Arial,sans-serif;background:#0A0A0F;color:#E0E0F0;margin:0;padding:0}
.wrap{max-width:600px;margin:0 auto;padding:40px 20px}
.card{background:#111118;border:1px solid #1E1E2E;border-radius:16px;padding:32px;margin-bottom:24px}
.title{font-size:20px;font-weight:700;color:#fff;margin-bottom:16px}
.row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #1E1E2E;font-size:13px}
.key{color:#666}.val{color:#fff;font-weight:600}
.footer{font-size:11px;color:#444;text-align:center;margin-top:24px;border-top:1px solid #1E1E2E;padding-top:16px}
</style>
</head>
<body>
<div class="wrap">
    <div style="font-size:22px;font-weight:800;color:#FF6B00;margin-bottom:32px;">⚡ PostPilot Admin</div>
    <div class="card">
        <div class="title"><?php echo e($alertTitle); ?></div>
        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="row">
                <span class="key"><?php echo e($key); ?></span>
                <span class="val"><?php echo e($value); ?></span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="footer">PostPilot — Internal Admin Alert</div>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\postpilot-laravel\resources\views/emails/admin-alert.blade.php ENDPATH**/ ?>