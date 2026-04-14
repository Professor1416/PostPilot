{{-- ============================================================ --}}
{{-- FILE: resources/views/emails/welcome.blade.php             --}}
{{-- ============================================================ --}}
<!DOCTYPE html><html><head><meta charset="utf-8">
<style>
body{font-family:Arial,sans-serif;background:#0A0A0F;color:#E0E0F0;margin:0;padding:0}
.wrap{max-width:600px;margin:0 auto;padding:40px 20px}
.logo{font-size:22px;font-weight:800;color:#FF6B00;margin-bottom:32px}
.card{background:#111118;border:1px solid #1E1E2E;border-radius:16px;padding:32px;margin-bottom:24px}
.title{font-size:22px;font-weight:700;color:#fff;margin-bottom:12px}
.text{font-size:14px;color:#AAA;line-height:1.8;margin-bottom:12px}
.hi{color:#FF6B00;font-weight:700}
.btn{display:inline-block;background:linear-gradient(135deg,#FF6B00,#FF3D00);color:#fff;padding:13px 28px;border-radius:10px;text-decoration:none;font-weight:700;font-size:14px;margin-top:8px}
.footer{font-size:11px;color:#444;text-align:center;margin-top:24px;border-top:1px solid #1E1E2E;padding-top:16px}
</style></head>
<body><div class="wrap">
<div class="logo">⚡ PostPilot</div>
<div class="card">
    <div class="title">Hey {{ $user->name }}, you're in! 🎉</div>
    <div class="text">Your PostPilot account is ready. You have <span class="hi">5 free scheduled posts</span> — no credit card needed.</div>
    <div class="text">What to do next:<br>
        1. Connect your Instagram Business account<br>
        2. Create your first post using AI<br>
        3. Pick a date and time — PostPilot publishes it automatically
    </div>
    <a href="{{ config('app.url') }}/dashboard/calendar" class="btn">Start Scheduling →</a>
</div>
<div class="footer">PostPilot — AI Social Media Scheduling for Indian Businesses</div>
</div></body></html>
