<div style="font-family: Arial, Helvetica, sans-serif; color: #111; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <h2 style="color: #b30084;">Halo! 👋</h2>

    <p style="line-height: 1.6;">
        <strong>{{ $inviter->name }}</strong> ({{ $inviter->email }}) baru saja mengundang kamu untuk berkolaborasi di workspace <strong>"{{ $workspace->name }}"</strong>.
    </p>

    @if($workspace->description)
    <div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #C8216B; margin: 15px 0; border-radius: 4px;">
        <p style="margin: 0; color: #555; font-style: italic;">"{{ $workspace->description }}"</p>
    </div>
    @endif

    <p style="line-height: 1.6;">
        Undangan ini dikirimkan khusus untuk email kamu: <strong>{{ $email }}</strong>. <br>
        Silakan login atau daftar di aplikasi SyncFlow menggunakan email tersebut untuk mulai berkolaborasi.
    </p>

    <div style="margin-top: 30px; margin-bottom: 30px;">
        <a href="{{ url('/') }}" style="display:inline-block; padding:12px 24px; background:#C8216B; color:#fff; border-radius:8px; text-decoration:none; font-weight: bold;">Buka SyncFlow Sekarang</a>
    </div>

    <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">

    <p style="font-size:12px; color:#999; line-height: 1.5;">
        Pesan ini dikirim otomatis oleh sistem SyncFlow. Jika kamu merasa tidak mengenal pengundang atau tidak mengharapkan email ini, silakan abaikan saja ya.
    </p>
</div>