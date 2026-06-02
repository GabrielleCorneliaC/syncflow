<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SyncFlow')</title>

    {{-- Fonts: Montserrat (heading) · Poppins (button/toggle) · Inter (body) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Poppins:wght@400;500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary:       '#b30084',
                        'primary-hov': '#8c0067',
                        cream:         '#f2ede5',
                        border:        '#cbc7b6',
                        toggle:        '#e6e2da',
                        'text-main':   '#1d1c17',
                        'text-soft':   '#49473a',
                        'text-hint':   'rgba(73,71,58,0.5)',
                        heading:       '#656026',
                    },
                    fontFamily: {
                        montserrat: ['Montserrat', 'sans-serif'],
                        poppins:    ['Poppins',    'sans-serif'],
                        inter:      ['Inter',      'sans-serif'],
                    },
                    backdropBlur: { card: '12px' },
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; background: #ffffff; }

        /* Blob animasi */
        .blob {
            position: absolute;
            width: 192px; height: 192px;
            border-radius: 9999px;
            mix-blend-mode: multiply;
            opacity: 0.70;
            filter: blur(20px);
            pointer-events: none;
            animation: blobFloat 10s ease-in-out infinite;
        }
        .blob-yellow { background: #fff7ad; top: -48px; left: -48px; }
        .blob-pink   { background: #ffb3ae; bottom: -48px; right: -48px; animation-delay: -5s; }
        @keyframes blobFloat {
            0%,100% { transform: translate(0,0) scale(1); }
            40%     { transform: translate(10px,-10px) scale(1.04); }
            70%     { transform: translate(-7px,7px) scale(0.97); }
        }

        /* Card */
        .glass-card {
            background: rgba(255,255,255,0.70);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        /* Toggle pill aktif */
        .tab-active  { background: #ffffff; color: #1d1c17; box-shadow: 0 1px 2px rgba(0,0,0,.05); }
        .tab-inactive{ background: transparent; color: #49473a; }

        /* Focus input */
        .sf-input:focus { border-color: #b30084; background: #ffffff; outline: none; }
        .sf-input.error { border-color: #f87171; background: #fef2f2; }

        /* Scrollbar tipis di mobile */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #cbc7b6; border-radius: 99px; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 py-16
             sm:px-10 sm:py-[84px]">

    @yield('content')

</body>
</html>
