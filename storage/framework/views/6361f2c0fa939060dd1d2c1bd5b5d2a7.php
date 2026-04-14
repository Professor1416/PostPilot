<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'PostPilot'); ?> — AI Social Media Scheduler</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">

    <!-- Razorpay -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #080810;
            --bg-card:   #0E0E18;
            --bg-input:  #080810;
            --border:    #18182A;
            --accent:    #FF6B00;
            --accent2:   #FF3D00;
            --text:      #E0E0F0;
            --text-muted:#888899;
            --text-dim:  #444455;
            --success:   #27AE60;
            --error:     #C0392B;
            --warning:   #E67E22;
            --ig:        #E1306C;
            --fb:        #1877F2;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* Header */
        .pp-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 28px;
            border-bottom: 1px solid var(--border);
            background: rgba(8,8,16,0.97);
            backdrop-filter: blur(20px);
            position: sticky;
            top: 0;
            z-index: 200;
            flex-wrap: wrap;
            gap: 12px;
        }

        .pp-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .pp-logo-icon { font-size: 26px; filter: drop-shadow(0 0 10px var(--accent)); }

        .pp-logo-name {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 18px;
            color: #fff;
        }

        .pp-logo-badge {
            font-size: 10px;
            background: var(--accent);
            color: #fff;
            padding: 2px 6px;
            border-radius: 5px;
            font-weight: 700;
        }

        .pp-logo-sub { font-size: 10px; color: #444; text-transform: uppercase; letter-spacing: 0.5px; }

        .pp-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pp-nav a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .pp-nav a:hover,
        .pp-nav a.active {
            color: var(--accent);
            background: rgba(255,107,0,0.1);
        }

        .pp-quota {
            display: flex;
            align-items: center;
            gap: 5px;
            background: rgba(255,107,0,0.1);
            border: 1px solid rgba(255,107,0,0.25);
            border-radius: 20px;
            padding: 5px 12px;
        }

        .pp-quota-num {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 14px;
            color: var(--accent);
        }

        .pp-quota-lbl { font-size: 10px; color: var(--text-muted); }

        .pp-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid var(--accent);
            object-fit: cover;
        }

        .pp-user-name { font-size: 12px; font-weight: 600; color: #CCC; }
        .pp-plan-tag { font-size: 9px; color: var(--accent); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; }

        .pp-btn-logout {
            background: #111;
            border: 1px solid #222;
            border-radius: 7px;
            color: #555;
            font-size: 11px;
            padding: 5px 10px;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
        }

        /* Main layout */
        .pp-main { max-width: 1280px; margin: 0 auto; padding: 28px 28px; }

        /* Page title */
        .pp-page-title { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 26px; color: #fff; margin-bottom: 4px; }
        .pp-page-sub { font-size: 13px; color: var(--text-dim); margin-bottom: 24px; }

        /* Cards */
        .pp-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
        }

        /* Buttons */
        .pp-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Syne', sans-serif;
            text-decoration: none;
            transition: all 0.2s;
        }

        .pp-btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: #fff;
            box-shadow: 0 4px 16px rgba(255,107,0,0.3);
        }

        .pp-btn-secondary {
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text-muted);
        }

        .pp-btn-danger {
            background: rgba(192,57,43,0.1);
            border: 1px solid rgba(192,57,43,0.4);
            color: var(--error);
        }

        /* Form inputs */
        .pp-input, .pp-select, .pp-textarea {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid #22223A;
            border-radius: 10px;
            color: var(--text);
            padding: 11px 13px;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            margin-bottom: 14px;
        }

        .pp-input:focus, .pp-select:focus, .pp-textarea:focus {
            outline: 2px solid var(--accent);
            outline-offset: 1px;
        }

        .pp-select { appearance: none; cursor: pointer; }
        .pp-textarea { resize: vertical; min-height: 90px; line-height: 1.6; }

        .pp-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 7px;
        }

        /* Status badges */
        .pp-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pp-badge-scheduled { background: rgba(255,107,0,0.1);   color: var(--accent);  }
        .pp-badge-published  { background: rgba(39,174,96,0.1);   color: var(--success); }
        .pp-badge-failed     { background: rgba(192,57,43,0.1);   color: var(--error);   }
        .pp-badge-draft      { background: #111;                  color: #555;           }
        .pp-badge-publishing { background: rgba(52,152,219,0.1);  color: #3498DB;        }

        /* Alert flashes */
        .pp-alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .pp-alert-success { background: rgba(39,174,96,0.1);  border: 1px solid rgba(39,174,96,0.3);  color: var(--success); }
        .pp-alert-error   { background: rgba(192,57,43,0.1);  border: 1px solid rgba(192,57,43,0.3);  color: var(--error);   }
        .pp-alert-warning { background: rgba(230,126,34,0.1); border: 1px solid rgba(230,126,34,0.3); color: var(--warning); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #22223A; border-radius: 3px; }

        /* Drawer overlay */
        .pp-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            z-index: 500;
            justify-content: flex-end;
        }

        .pp-overlay.open { display: flex; }

        .pp-drawer {
            width: min(480px, 100vw);
            background: var(--bg-card);
            border-left: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            animation: slideIn 0.25s ease;
        }

        .pp-drawer-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            background: var(--bg-card);
            z-index: 10;
        }

        .pp-drawer-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 17px; color: #fff; }
        .pp-close-btn { background: none; border: none; color: #666; font-size: 18px; cursor: pointer; }
        .pp-drawer-body { padding: 20px 24px; display: flex; flex-direction: column; gap: 4px; }

        /* Toggle switch */
        .pp-toggle-wrap { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
        .pp-toggle-label { font-size: 13px; font-weight: 600; color: #CCC; }

        .pp-toggle {
            position: relative;
            width: 44px;
            height: 24px;
            appearance: none;
            background: #22223A;
            border-radius: 12px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .pp-toggle:checked { background: var(--accent); }

        .pp-toggle::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 18px;
            height: 18px;
            background: #fff;
            border-radius: 50%;
            transition: left 0.2s;
        }

        .pp-toggle:checked::after { left: 23px; }

        /* FullCalendar dark overrides */
        .fc { font-family: 'DM Sans', sans-serif !important; color: #888 !important; }
        .fc-theme-standard td, .fc-theme-standard th { border-color: var(--border) !important; }
        .fc-theme-standard .fc-scrollgrid { border-color: var(--border) !important; }
        .fc .fc-col-header-cell-cushion { color: #555 !important; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; text-decoration: none !important; }
        .fc .fc-daygrid-day-number { color: #666 !important; font-size: 13px; text-decoration: none !important; }
        .fc .fc-daygrid-day.fc-day-today { background: rgba(255,107,0,0.05) !important; }
        .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number { color: var(--accent) !important; font-weight: 700; }
        .fc .fc-button { background: var(--bg-card) !important; border-color: var(--border) !important; color: #666 !important; font-size: 12px !important; }
        .fc .fc-button:hover { background: var(--border) !important; color: #DDD !important; }
        .fc .fc-toolbar-title { font-family: 'Syne', sans-serif !important; font-weight: 700 !important; font-size: 18px !important; color: #DDD !important; }

        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeIn  { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes spin    { to { transform: rotate(360deg); } }

        /* Responsive */
        @media (max-width: 768px) {
            .pp-main { padding: 16px; }
            .pp-header { padding: 12px 16px; }
        }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>


<header class="pp-header">
    <a href="<?php echo e(route('dashboard.calendar')); ?>" class="pp-logo">
        <span class="pp-logo-icon">⚡</span>
        <div>
            <div class="pp-logo-name">PostPilot <span class="pp-logo-badge">v1.0</span></div>
            <div class="pp-logo-sub">AI Social Media Scheduler</div>
        </div>
    </a>

    <nav class="pp-nav">
        <a href="<?php echo e(route('dashboard.calendar')); ?>" class="<?php echo e(request()->routeIs('dashboard.calendar') ? 'active' : ''); ?>">📅 Calendar</a>
        <a href="<?php echo e(route('dashboard.queue')); ?>"    class="<?php echo e(request()->routeIs('dashboard.queue')    ? 'active' : ''); ?>">📋 Queue</a>
        <a href="<?php echo e(route('dashboard.accounts')); ?>" class="<?php echo e(request()->routeIs('dashboard.accounts') ? 'active' : ''); ?>">🔗 Accounts</a>
        <a href="<?php echo e(route('dashboard.pricing')); ?>"  class="<?php echo e(request()->routeIs('dashboard.pricing')  ? 'active' : ''); ?>">💳 Pricing</a>

        <?php if(auth()->guard()->check()): ?>
            <div class="pp-quota">
                <span class="pp-quota-num"><?php echo e(auth()->user()->isUnlimited() ? '∞' : auth()->user()->postsRemaining()); ?></span>
                <span class="pp-quota-lbl">posts left</span>
            </div>

            <div style="display:flex;align-items:center;gap:8px;">
                <?php if(auth()->user()->photo_url): ?>
                    <img src="<?php echo e(auth()->user()->photo_url); ?>" class="pp-avatar" alt="">
                <?php endif; ?>
                <div>
                    <div class="pp-user-name"><?php echo e(explode(' ', auth()->user()->name)[0]); ?></div>
                    <div class="pp-plan-tag"><?php echo e(auth()->user()->plan); ?></div>
                </div>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="pp-btn-logout">Sign out</button>
                </form>
            </div>
        <?php endif; ?>
    </nav>
</header>


<div style="max-width:1280px;margin:0 auto;padding:0 28px;">
    <?php if(session('success')): ?>
        <div class="pp-alert pp-alert-success" style="margin-top:16px;">✓ <?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="pp-alert pp-alert-error" style="margin-top:16px;">✕ <?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php if(session('warning')): ?>
        <div class="pp-alert pp-alert-warning" style="margin-top:16px;">⚠ <?php echo e(session('warning')); ?></div>
    <?php endif; ?>
</div>


<main>
    <?php echo $__env->yieldContent('content'); ?>
</main>


<footer style="display:flex;justify-content:space-between;align-items:center;padding:16px 28px;border-top:1px solid #0D0D1A;margin-top:40px;">
    <span style="font-size:11px;color:#1A1A2A;">PostPilot v1.0 — AI Social Media Scheduling for Indian Businesses</span>
    <a href="<?php echo e(route('admin.login')); ?>" style="font-size:11px;color:#1A1A2A;text-decoration:none;">Admin</a>
</footer>


<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
    // Global CSRF helper for fetch requests
    window.CSRF_TOKEN = '<?php echo e(csrf_token()); ?>';

    function apiPost(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify(data),
        }).then(r => r.json());
    }

    function apiDelete(url) {
        return fetch(url, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
        }).then(r => r.json());
    }

    function showToast(msg, type = 'success') {
        const t = document.createElement('div');
        t.style.cssText = `position:fixed;bottom:24px;right:24px;padding:12px 18px;border-radius:12px;font-size:13px;font-weight:600;color:#fff;z-index:9999;animation:fadeIn 0.3s ease;max-width:340px;`;
        t.style.background = type === 'error' ? '#6b1a1a' : type === 'warning' ? '#6b4a1a' : '#1a6b3a';
        t.style.border = `1px solid ${type === 'error' ? '#C0392B' : type === 'warning' ? '#E67E22' : '#27AE60'}`;
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 3500);
    }
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\postpilot-laravel\resources\views/layouts/app.blade.php ENDPATH**/ ?>