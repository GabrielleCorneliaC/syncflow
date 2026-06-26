<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f4ef; margin: 0; padding: 40px 0; }
        .container { max-width: 480px; margin: 0 auto; background: #fff; border-radius: 16px; border: 1px solid #cbc7b6; overflow: hidden; }
        .header { background: linear-gradient(135deg, #b30084, #6a1452); padding: 32px; text-align: center; }
        .header h1 { color: #fff; font-size: 24px; margin: 0; }
        .body { padding: 32px; }
        .body p { color: #3a3a3a; font-size: 15px; line-height: 1.6; }
        .workspace-name { font-weight: bold; color: #b30084; font-size: 18px; }
        .btn { display: inline-block; margin-top: 24px; padding: 12px 32px; background: #b30084; color: #fff; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 14px; }
        .footer { padding: 16px 32px; background: #f5f4ef; text-align: center; color: #8a8a8a; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 SyncFlow</h1>
        </div>
        <div class="body">
            <p>Halo <strong>{{ $email }}</strong>,</p>
            <p>Kamu telah diundang untuk bergabung ke workspace:</p>
            <p class="workspace-name">{{ $workspace->name }}</p>
            @if($workspace->description)
            <p style="color:#6a6a6a; font-size:13px;">{{ $workspace->description }}</p>
            @endif
            <p>Klik tombol di bawah untuk langsung masuk ke workspace:</p>
            <a href="{{ url('/workspaces/' . $workspace->id) }}" class="btn">Buka Workspace</a>
        </div>
        <div class="footer">
            Email ini dikirim otomatis oleh SyncFlow. Jangan balas email ini.
        </div>
    </div>
</body>
</html>