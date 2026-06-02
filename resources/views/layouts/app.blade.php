<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SyncFlow</title>

    {{-- Google Fonts: Montserrat + Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN (untuk development; ganti ke Vite build untuk production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        /* ── Brand Colors ── */
                        primary:   '#b30084',
                        'primary-dark':  '#8c0067',
                        'primary-light': '#d400a0',
                        accent:    '#ffd6f0',
                        /* ── Neutral Warm ── */
                        surface:   '#faf9f7',
                        muted:     '#f2ede5',
                        border:    '#cbc7b6',
                        textmain:  '#1d1c17',
                        textsoft:  '#49473a',
                        texthint:  '#9b9887',
                    },
                    fontFamily: {
                        heading: ['Montserrat', 'sans-serif'],
                        body:    ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

    {{-- Alpine.js untuk interaktivitas ringan --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <style>
        /* ── Base ── */
        body { font-family: 'Inter', sans-serif; }
        h1,h2,h3,h4,h5 { font-family: 'Montserrat', sans-serif; }

        /* ── Sidebar transition ── */
        #sidebar { transition: transform 0.25s ease; }

        /* ── Active nav item ── */
        .nav-item.active {
            background: linear-gradient(135deg, #b30084 0%, #d400a0 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(179,0,132,0.35);
        }
        .nav-item.active svg { color: #fff; }
        .nav-item.active .nav-label { color: #fff; }

        /* ── Scrollbar tipis ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f2ede5; }
        ::-webkit-scrollbar-thumb { background: #cbc7b6; border-radius: 99px; }

        /* ── Smooth page load ── */
        .page-content { animation: fadeUp 0.3s ease both; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>

    @stack('head')
</head>

<body class="h-full bg-surface text-textmain" x-data="{ sidebarOpen: false }">

{{-- ══════════════════════════════════════════════════
     OVERLAY (mobile sidebar backdrop)
══════════════════════════════════════════════════ --}}
<div
    x-show="sidebarOpen"
    x-transition:enter="transition-opacity ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 z-20 bg-black/40 lg:hidden"
></div>

{{-- ══════════════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════════════ --}}
<aside
    id="sidebar"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-border flex flex-col lg:translate-x-0 lg:static lg:inset-auto"
>
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-6 h-16 border-b border-border shrink-0">
        <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
            </svg>
        </div>
        <span class="font-heading font-bold text-lg text-primary tracking-tight">SyncFlow</span>
        {{-- Tombol tutup sidebar (mobile) --}}
        <button @click="sidebarOpen = false" class="ml-auto lg:hidden text-texthint hover:text-textmain">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

        {{-- ── Main ── --}}
        <p class="px-3 mb-1 text-[10px] font-semibold uppercase tracking-widest text-texthint">Main</p>

        <a href="{{ route('dashboard') }}"
           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-textsoft hover:bg-muted transition {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
            </svg>
            <span class="nav-label">Dashboard</span>
        </a>

        <a href="{{ route('profile.show') }}"
           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-textsoft hover:bg-muted transition {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
            </svg>
            <span class="nav-label">Profil Saya</span>
        </a>

        {{-- ── Divider untuk modul anggota lain ── --}}
        <p class="px-3 pt-4 mb-1 text-[10px] font-semibold uppercase tracking-widest text-texthint">Modul</p>

        {{-- Slot untuk anggota lain menambahkan nav item mereka --}}
        @yield('sidebar-items')

        {{-- Placeholder nav items (hapus saat sudah ada halaman nyata) --}}
        <a href="#" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-texthint cursor-not-allowed opacity-60">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
            </svg>
            <span class="nav-label">Personal Task</span>
            <span class="ml-auto text-[10px] bg-muted border border-border rounded-full px-2 py-0.5 text-texthint">Soon</span>
        </a>

        <a href="#" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-texthint cursor-not-allowed opacity-60">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/>
            </svg>
            <span class="nav-label">Diskusi</span>
            <span class="ml-auto text-[10px] bg-muted border border-border rounded-full px-2 py-0.5 text-texthint">Soon</span>
        </a>

        <a href="#" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-texthint cursor-not-allowed opacity-60">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>
            </svg>
            <span class="nav-label">Workspace</span>
            <span class="ml-auto text-[10px] bg-muted border border-border rounded-full px-2 py-0.5 text-texthint">Soon</span>
        </a>

        {{-- Admin section --}}
        @if(Auth::user()->isAdmin())
        <p class="px-3 pt-4 mb-1 text-[10px] font-semibold uppercase tracking-widest text-texthint">Admin</p>
        <a href="{{ route('admin.users.index') }}"
           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-textsoft hover:bg-muted transition {{ request()->routeIs('admin.*') ? 'active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
            </svg>
            <span class="nav-label">Kelola User</span>
        </a>
        @endif
    </nav>

    {{-- User info di bawah sidebar --}}
    <div class="p-4 border-t border-border shrink-0">
        <div class="flex items-center gap-3">
            <img src="{{ Auth::user()->avatar_url }}"
                 alt="{{ Auth::user()->name }}"
                 class="w-9 h-9 rounded-full object-cover ring-2 ring-primary/20">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-textmain truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-texthint truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</aside>

{{-- ══════════════════════════════════════════════════
     MAIN WRAPPER
══════════════════════════════════════════════════ --}}
<div class="flex h-full flex-col lg:pl-64">

    {{-- ── TOPBAR ──────────────────────────────────── --}}
    <header class="sticky top-0 z-10 h-16 bg-white/90 backdrop-blur-sm border-b border-border flex items-center px-4 gap-4 shrink-0">

        {{-- Hamburger (mobile) --}}
        <button @click="sidebarOpen = true"
                class="lg:hidden p-2 rounded-xl hover:bg-muted text-textsoft transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
        </button>

        {{-- Page title --}}
        <h1 class="font-heading font-bold text-base sm:text-lg text-textmain truncate">
            @yield('page-title', 'Dashboard')
        </h1>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Flash message badge (jika ada) --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="hidden sm:flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-xs font-medium px-3 py-1.5 rounded-full">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- Avatar + dropdown --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-muted transition">
                <img src="{{ Auth::user()->avatar_url }}"
                     alt="{{ Auth::user()->name }}"
                     class="w-8 h-8 rounded-full object-cover ring-2 ring-primary/20">
                <span class="hidden sm:block text-sm font-medium text-textmain">{{ Auth::user()->name }}</span>
                <svg class="w-4 h-4 text-texthint" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Dropdown --}}
            <div x-show="open" @click.outside="open = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-48 bg-white border border-border rounded-xl shadow-lg py-1 z-50">
                <a href="{{ route('profile.show') }}"
                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-textsoft hover:bg-muted transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                    </svg>
                    Profil Saya
                </a>
                <hr class="my-1 border-border">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- ── FLASH MESSAGES (mobile) ─────────────────── --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="sm:hidden mx-4 mt-3 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm font-medium px-4 py-3 rounded-xl">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="mx-4 mt-3 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-sm font-medium px-4 py-3 rounded-xl">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ── MAIN CONTENT ─────────────────────────────── --}}
    <main class="flex-1 overflow-y-auto">
        <div class="page-content p-4 sm:p-6 max-w-7xl mx-auto">
            @yield('content')
        </div>
    </main>
</div>

@stack('scripts')
</body>
</html>
