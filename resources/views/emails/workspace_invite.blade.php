<div style="font-family: Arial, Helvetica, sans-serif; color: #111;">
    <h2>You were invited to join workspace: {{ $workspace->name }}</h2>

    @if($workspace->description)
    <p>{{ $workspace->description }}</p>
    @endif

    <p>
        To accept the invitation, please login or register at our app and look for the workspace.
    </p>

    <p>
        <a href="{{ url('/') }}" style="display:inline-block;padding:10px 14px;background:#C8216B;color:#fff;border-radius:8px;text-decoration:none">Open SyncFlow</a>
    </p>

    <p style="font-size:12px;color:#666">If you didn't expect this email, you can ignore it.</p>
</div>
