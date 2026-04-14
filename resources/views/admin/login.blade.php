{{-- ============================================================ --}}
{{-- FILE: resources/views/admin/login.blade.php                --}}
{{-- ============================================================ --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PostPilot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: #080810; color: #E0E0F0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #0E0E18; border: 1px solid #18182A; border-radius: 20px; padding: 48px 40px; max-width: 380px; width: 100%; text-align: center; }
        h2 { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 24px; color: #fff; margin-bottom: 8px; }
        p { color: #555; font-size: 14px; margin-bottom: 28px; }
        input { width: 100%; background: #080810; border: 1px solid #22223A; border-radius: 10px; color: #DDD; padding: 12px 14px; font-size: 14px; font-family: 'DM Sans', sans-serif; margin-bottom: 12px; }
        button { width: 100%; padding: 13px; background: linear-gradient(135deg, #FF6B00, #FF3D00); border: none; border-radius: 10px; color: #fff; font-size: 14px; font-weight: 700; cursor: pointer; font-family: 'Syne', sans-serif; }
        .error { font-size: 13px; color: #C0392B; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <div style="font-size:40px;margin-bottom:16px;">🔐</div>
        <h2>Admin Access</h2>
        <p>PostPilot Admin Dashboard</p>
        @if($errors->any())
            <div class="error">{{ $errors->first('admin_key') }}</div>
        @endif
        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <input type="password" name="admin_key" placeholder="Enter admin secret key" autofocus>
            <button type="submit">Access Dashboard</button>
        </form>
    </div>
</body>
</html>
