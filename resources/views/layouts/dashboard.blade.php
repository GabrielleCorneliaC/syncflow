<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SyncFlow – @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --pink-primary: #C8216B;
            --pink-light:   #F9D6E7;
            --pink-soft:    #FFF0F6;
            --yellow-bg:    #FEFAE8;
            --dark:         #1A1A2E;
            --gray-text:    #6B7280;
        }
        body { font-family: 'DM Sans', sans-serif; background: #F7F5F0; }
        .font-display { font-family: 'Sora', sans-serif; }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-gray-500 transition-all duration-200; }
        .sidebar-link:hover { background: var(--pink-light); color: var(--pink-primary); }
        .sidebar-link.active { background: var(--pink-light); color: var(--pink-primary); font-weight: 600; }
        .btn-primary {
            background: var(--pink-primary);
            color: white;
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary:hover { background: #a8195a; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(200,33,107,0.3); }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="w-52 bg-white flex flex-col justify-between py-6 px-3 shadow-sm flex-shrink-0">
        {{-- Logo --}}
        <div>
            <div class="flex items-center gap-2 px-4 mb-8">
                <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background: var(--pink-primary);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                        <path d="M8 12h8M12 8v8"/>
                    </svg>
                </div>
                <span class="font-display font-bold text-base" style="color: var(--dark);">SyncFlow</span>
            </div>

            <nav class="flex flex-col gap-1">
                <a href="/dashboard" class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                    Dashboard
                </a>
                <a href="/personal" class="sidebar-link {{ request()->is('personal*') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
                    Personal List
                </a>
                <a href="/workspaces" class="sidebar-link {{ request()->is('workspaces*') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                    Collaboration
                </a>
                <a href="/profile" class="sidebar-link {{ request()->is('profile*') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                    Profile
                </a>
            </nav>
        </div>

        {{-- New Button --}}
        <div class="px-1">
            <button onclick="openNewModal()" class="btn-primary w-full justify-center">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                New
            </button>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="flex-1 overflow-y-auto">
        @yield('content')
    </main>

    @stack('modals')
    @stack('scripts')
</body>
</html>