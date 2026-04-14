<!DOCTYPE html>
<html>
<head><meta charset="utf-8">
<style>
body{font-family:Arial,sans-serif;background:#0A0A0F;color:#E0E0F0;margin:0;padding:0}
.wrap{max-width:600px;margin:0 auto;padding:40px 20px}
.card{background:#111118;border:1px solid #C0392B;border-radius:16px;padding:32px;margin-bottom:24px}
.title{font-size:20px;font-weight:700;color:#C0392B;margin-bottom:16px}
.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #1E1E2E;font-size:13px}
.key{color:#666}.val{color:#AAA}
.footer{font-size:11px;color:#444;text-align:center;margin-top:24px;border-top:1px solid #1E1E2E;padding-top:16px}
</style>
</head>
<body>
<div class="wrap">
    <div style="font-size:22px;font-weight:800;color:#FF6B00;margin-bottom:32px;">⚡ PostPilot Admin</div>
    <div class="card">
        <div class="title">Post Publish Failed — #{{ $post->id }}</div>
        <div class="row"><span class="key">Post ID</span><span class="val">#{{ $post->id }}</span></div>
        <div class="row"><span class="key">User Email</span><span class="val">{{ $post->user->email ?? '—' }}</span></div>
        <div class="row"><span class="key">User ID</span><span class="val">{{ $post->user_id }}</span></div>
        <div class="row"><span class="key">Platforms</span><span class="val">{{ implode(', ', $post->platforms ?? []) }}</span></div>
        <div class="row"><span class="key">Retry Count</span><span class="val">{{ $post->retry_count }}/3</span></div>
        <div class="row"><span class="key">Scheduled At</span><span class="val">{{ $post->scheduledAtIst() }}</span></div>
        <div style="margin-top:16px;padding:12px;background:rgba(192,57,43,0.1);border:1px solid rgba(192,57,43,0.3);border-radius:8px;font-size:12px;color:#C0392B;">
            <strong>Failure Reason:</strong><br>{{ $reason }}
        </div>
    </div>
    <div class="footer">PostPilot — Internal Admin Alert</div>
</div>
</body>
</html>
