<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PostPilot — Sign In</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: #080810; color: #E0E0F0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #0E0E18; border: 1px solid #18182A; border-radius: 24px; padding: 48px 40px; max-width: 420px; width: 100%; text-align: center; }
        .logo { font-size: 52px; filter: drop-shadow(0 0 20px #FF6B00); margin-bottom: 16px; }
        h1 { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 36px; color: #fff; margin-bottom: 8px; }
        .sub { font-size: 15px; color: #555; margin-bottom: 32px; line-height: 1.6; }
        .features { display: flex; flex-direction: column; gap: 10px; margin-bottom: 32px; text-align: left; }
        .feature { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: #888; }
        .check { color: #27AE60; font-weight: 800; flex-shrink: 0; }
        .login-btn {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; padding: 14px;
            background: #fff; border: none; border-radius: 12px;
            color: #111; font-size: 15px; font-weight: 600;
            cursor: pointer; font-family: 'DM Sans', sans-serif;
            text-decoration: none; margin-bottom: 16px;
            transition: opacity 0.2s;
        }
        .login-btn:hover { opacity: 0.9; }
        .error { font-size: 13px; color: #C0392B; margin-bottom: 12px; }
        .terms { font-size: 11px; color: #333; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">⚡</div>
        <h1>PostPilot</h1>
        <p class="sub">AI-powered social media scheduling for local businesses</p>

        <div class="features">
            <?php $__currentLoopData = ['Schedule Instagram posts automatically', 'AI writes captions in Hindi, English, Hinglish', 'Indian festival calendar pre-loaded', 'Start free — 5 posts, no credit card needed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="feature"><span class="check">✓</span><span><?php echo e($f); ?></span></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($errors->any()): ?>
            <div class="error"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>

        <a href="<?php echo e(route('auth.google')); ?>" class="login-btn">
            <svg width="18" height="18" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Sign in with Google
        </a>

        <p class="terms">By signing in, you agree to our Terms of Service and Privacy Policy.</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\postpilot-laravel\resources\views/auth/login.blade.php ENDPATH**/ ?>