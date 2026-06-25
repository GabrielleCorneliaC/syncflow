<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SyncFlow</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}">

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
        body { font-family: 'Inter', sans-serif; }
        h1,h2,h3,h4,h5 { font-family: 'Poppins', sans-serif; }

        /* Sidebar slide */
        #sidebar { transition: transform 0.25s cubic-bezier(.4,0,.2,1); }

        /* Nav active state */
        .nav-active {
            background: #fff7ad;
            color: #49473a;
        }
        /* Nav hover */
        .nav-item:not(.nav-active):hover {
            background: rgba(255,247,173,0.45);
        }

        /* Scrollbar tipis */
        ::-webkit-scrollbar       { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbc7b6; border-radius: 99px; }

        /* Page fade-in */
        .page-content { animation: fadeUp .25s ease both; }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(8px); }
            to   { opacity:1; transform:translateY(0); }
        }
    </style>

    @stack('head')
</head>

<body class="h-full min-h-screen bg-[#fffce8]" x-data="{ sidebarOpen: false }">

{{-- ══════════════════════════════════════════════════
     OVERLAY (mobile sidebar backdrop)
══════════════════════════════════════════════════ --}}
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-20 bg-black/30 lg:hidden">
</div>

{{-- ══════════════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════════════ --}}
<aside id="sidebar"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-30
              w-64 flex flex-col
              bg-[#f8f3eb] border-r border-[#cbc7b6]
              drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)]
              lg:translate-x-0">

    {{-- ── Logo ─────────────────────────────────────── --}}
    <div class="px-6 py-12 shrink-0">
        <div class="flex items-center gap-3">
            {{-- Logo SVG --}}
            <div class="w-8 h-8 shrink-0">
                <img src="{{ asset('assets/logo.png') }}" alt="Logo SyncFlow" class="w-full h-full object-contain">
            </div>
            <span class="font-montserrat font-bold text-[#656026] text-2xl leading-8 tracking-tight">SyncFlow</span>

            {{-- Close btn mobile --}}
            <button @click="sidebarOpen = false"
                    class="ml-auto lg:hidden text-[#9b9887] hover:text-[#49473a]">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ── Main Nav ─────────────────────────────────── --}}
    {{--
        Nav item anatomy (Figma):
        px-[12px] py-[8px] rounded-[8px] gap-[12px]
        icon 15px · label Poppins Medium 16px #49473a tracking-[0.4px]
        active: bg #fff7ad
    --}}
    <nav class="flex-1 overflow-y-auto px-2 flex flex-col gap-1">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg transition
                  {{ request()->routeIs('dashboard') ? 'nav-active' : '' }}">
            <svg class="w-[15px] h-[15px] shrink-0 text-[#49473a]" viewBox="0 0 15 15" fill="none">
                <rect x="1" y="1" width="5.5" height="5.5" rx="1" stroke="currentColor" stroke-width="1.3"/>
                <rect x="8.5" y="1" width="5.5" height="5.5" rx="1" stroke="currentColor" stroke-width="1.3"/>
                <rect x="1" y="8.5" width="5.5" height="5.5" rx="1" stroke="currentColor" stroke-width="1.3"/>
                <rect x="8.5" y="8.5" width="5.5" height="5.5" rx="1" stroke="currentColor" stroke-width="1.3"/>
            </svg>
            <span class="font-poppins font-medium text-[#49473a] text-base leading-6 tracking-[0.4px]">Dashboard</span>
        </a>

        {{-- Personal List --}}
        <a href="#"
           class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg transition
                  {{ request()->routeIs('personal.*') ? 'nav-active' : '' }}">
            <svg class="w-[15px] h-[15px] shrink-0 text-[#49473a]" viewBox="0 0 15 15" fill="none">
                <path d="M1 3h13M1 7.5h13M1 12h8" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            <span class="font-poppins font-medium text-[#49473a] text-base leading-6">Personal List</span>
        </a>

        {{-- Collaboration --}}
        <a href="{{ route('workspaces.index') }}"
        class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('workspaces.*') ? 'nav-active' : '' }}">
            <svg class="w-[18px] h-[13px] shrink-0 text-[#767135]" viewBox="0 0 18 13" fill="none">
                <circle cx="6" cy="4.5" r="3" stroke="currentColor" stroke-width="1.3"/>
                <circle cx="13" cy="4.5" r="2.5" stroke="currentColor" stroke-width="1.3"/>
                <path d="M1 12c0-2.76 2.24-5 5-5s5 2.24 5 5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                <path d="M13.5 7.5c2.21 0 4 1.79 4 4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            <span class="font-poppins font-medium text-[#767135] text-base leading-6">Collaboration</span>
        </a>
        {{-- Profile --}}
        <a href="{{ route('profile.show') }}"
           class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg transition
                  {{ request()->routeIs('profile.*') ? 'nav-active' : '' }}">
            <svg class="w-[13px] h-[13px] shrink-0 text-[#49473a]" viewBox="0 0 13 13" fill="none">
                <circle cx="6.5" cy="3.5" r="2.5" stroke="currentColor" stroke-width="1.3"/>
                <path d="M1 12c0-3.04 2.46-5.5 5.5-5.5S12 8.96 12 12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            <span class="font-poppins font-medium text-[#49473a] text-base leading-6">Profile</span>
        </a>

        {{-- Slot nav tambahan dari anggota lain --}}
        @yield('sidebar-items')
      
        {{-- Admin --}}
        @if(Auth::user()->isAdmin())
        <div class="mt-3 pt-3 border-t border-[#cbc7b6]">
            <a href="{{ route('admin.users.index') }}"
               class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg transition
                      {{ request()->routeIs('admin.*') ? 'nav-active' : '' }}">
                <svg class="w-[15px] h-[15px] shrink-0 text-[#49473a]" viewBox="0 0 15 15" fill="none">
                    <path d="M10.5 9.5c2.5 0 4 1.5 4 3.5H.5c0-2 1.5-3.5 4-3.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                    <circle cx="7.5" cy="4.5" r="3" stroke="currentColor" stroke-width="1.3"/>
                </svg>
                <span class="font-poppins font-medium text-[#49473a] text-base leading-6">Kelola User</span>
            </a>
        </div>
        @endif
    </nav>

    {{-- ── Footer: tombol + New ─────────────────────── --}}
    {{--
        Figma: border-t #cbc7b6 · pb-6 pt-[25px] px-3
        btn: bg #b30084 · shadow [4px_4px_0_#6a1452] · rounded-[8px]
        icon + / "New" · Poppins Medium 16px white
    --}}
    <div class="shrink-0 border-t border-[#cbc7b6] py-6 px-3">
        <button class="w-full flex items-center justify-center gap-2
                       bg-[#b30084] hover:bg-[#8c0067]
                       shadow-[4px_4px_0px_#6a1452]
                       rounded-lg py-3
                       font-poppins font-medium text-white text-base leading-6
                       transition active:translate-y-px active:shadow-[2px_2px_0_#6a1452]">
            <svg class="w-[14px] h-[14px] shrink-0" viewBox="0 0 14 14" fill="none">
                <path d="M7 1v12M1 7h12" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span>New</span>
        </button>
    </div>
</aside>

{{-- ══ MAIN WRAPPER ════════════════════════════════════════════ --}}
<div class="flex flex-col min-h-screen lg:pl-64">

    {{-- ── Topbar (mobile only) ────────────────────── --}}
    <header class="lg:hidden sticky top-0 z-10
                   h-14 bg-[#fffce8]/90 backdrop-blur-sm border-b border-[#cbc7b6]
                   flex items-center px-4 gap-3">
        <button @click="sidebarOpen = true"
                class="p-2 rounded-lg hover:bg-[#f2ede5] text-[#49473a] transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
        </button>
        <span class="font-montserrat font-bold text-[#656026] text-lg">SyncFlow</span>

        <div class="ml-auto flex items-center gap-2">
            {{-- Avatar --}}
            <a href="{{ route('profile.show') }}" class="flex items-center">
                <img src="{{ Auth::user()->avatar_url }}"
                     alt="{{ Auth::user()->name }}"
                     class="w-8 h-8 rounded-full object-cover ring-2 ring-[#b30084]/20">
            </a>
        </div>
    </header>

    {{-- ── Flash messages ───────────────────────────── --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(()=>show=false,4000)"
         class="mx-4 mt-3 flex items-center gap-2
                bg-green-50 border border-green-200 text-green-700
                text-sm font-medium rounded-xl px-4 py-3">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(()=>show=false,5000)"
         class="mx-4 mt-3 flex items-center gap-2
                bg-red-50 border border-red-200 text-red-700
                text-sm font-medium rounded-xl px-4 py-3">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ── Main content ────────────────────────────── --}}
    <main class="flex-1 overflow-y-auto">
        <div class="page-content p-5 sm:p-8 lg:p-10 max-w-[1200px] mx-auto">
            @yield('content')
        </div>
    </main>
</div>

@stack('scripts')
</body>
</html>
