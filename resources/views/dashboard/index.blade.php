@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
{{-- ── Greeting ─────────────────────────────────── --}}
<div class="mb-6">
    <h2 class="font-heading font-bold text-xl sm:text-2xl text-textmain">
        Halo, {{ Auth::user()->name }}! 👋
    </h2>
    <p class="text-sm text-texthint mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
</div>

{{-- ── Stat Cards ───────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">

    {{-- Total Users --}}
    <div class="bg-white border border-border rounded-2xl p-4 sm:p-5 flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-widest text-texthint">Total User</span>
            <div class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0"/>
                </svg>
            </div>
        </div>
        <p class="font-heading font-bold text-2xl sm:text-3xl text-textmain">{{ $stats['total_users'] }}</p>
        <p class="text-xs text-texthint">Terdaftar di platform</p>
    </div>

    {{-- New This Month --}}
    <div class="bg-white border border-border rounded-2xl p-4 sm:p-5 flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-widest text-texthint">Baru Bulan Ini</span>
            <div class="w-8 h-8 rounded-xl bg-green-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
            </div>
        </div>
        <p class="font-heading font-bold text-2xl sm:text-3xl text-textmain">{{ $stats['new_this_month'] }}</p>
        <p class="text-xs text-texthint">Registrasi {{ now()->translatedFormat('F Y') }}</p>
    </div>

    {{-- Placeholder card 3 --}}
    <div class="bg-white border border-border rounded-2xl p-4 sm:p-5 flex flex-col gap-3 opacity-60">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-widest text-texthint">Task Aktif</span>
            <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </div>
        </div>
        <p class="font-heading font-bold text-2xl sm:text-3xl text-textmain">—</p>
        <p class="text-xs text-texthint">Data dari modul Task</p>
    </div>

    {{-- Placeholder card 4 --}}
    <div class="bg-white border border-border rounded-2xl p-4 sm:p-5 flex flex-col gap-3 opacity-60">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-widest text-texthint">Diskusi</span>
            <div class="w-8 h-8 rounded-xl bg-yellow-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/>
                </svg>
            </div>
        </div>
        <p class="font-heading font-bold text-2xl sm:text-3xl text-textmain">—</p>
        <p class="text-xs text-texthint">Data dari modul Diskusi</p>
    </div>
</div>

{{-- ── Charts Row ───────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

    {{-- Line chart: Registrasi per bulan --}}
    <div class="lg:col-span-2 bg-white border border-border rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-heading font-bold text-base text-textmain">Registrasi User</h3>
                <p class="text-xs text-texthint">6 bulan terakhir</p>
            </div>
            <span class="text-xs bg-primary/10 text-primary font-semibold px-3 py-1 rounded-full">2025</span>
        </div>
        <div class="relative h-48 sm:h-56">
            <canvas id="registrasiChart"></canvas>
        </div>
    </div>

    {{-- Doughnut chart: Distribusi role --}}
    <div class="bg-white border border-border rounded-2xl p-5">
        <div class="mb-4">
            <h3 class="font-heading font-bold text-base text-textmain">Distribusi Role</h3>
            <p class="text-xs text-texthint">Admin vs Member</p>
        </div>
        <div class="relative h-48 sm:h-56 flex items-center justify-center">
            <canvas id="roleChart"></canvas>
        </div>
    </div>
</div>

{{-- ── Quick Action / Info ─────────────────────── --}}
<div class="bg-gradient-to-r from-primary to-primary-dark rounded-2xl p-5 sm:p-6 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-white/70 mb-1">Profil Kamu</p>
        <h3 class="font-heading font-bold text-lg">Lengkapi data profilmu</h3>
        <p class="text-sm text-white/80 mt-0.5">Tambahkan foto profil dan perbarui informasi akunmu.</p>
    </div>
    <a href="{{ route('profile.show') }}"
       class="shrink-0 bg-white text-primary font-semibold text-sm px-5 py-2.5 rounded-xl hover:bg-white/90 transition shadow active:scale-[0.98]">
        Buka Profil
    </a>
</div>
@endsection

@push('scripts')
<script>
    // ── Warna brand ───────────────────
    const BRAND     = '#b30084';
    const BRAND_20  = 'rgba(179,0,132,0.15)';
    const WARM_GRAY = '#cbc7b6';

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color       = '#9b9887';

    // ── Line Chart: Registrasi ────────
    const ctxLine = document.getElementById('registrasiChart').getContext('2d');
    const gradLine = ctxLine.createLinearGradient(0, 0, 0, 220);
    gradLine.addColorStop(0, 'rgba(179,0,132,0.25)');
    gradLine.addColorStop(1, 'rgba(179,0,132,0)');

    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            datasets: [{
                label: 'Registrasi',
                data: [3, 7, 5, 12, 9, {{ $stats['new_this_month'] }}],
                borderColor: BRAND,
                backgroundColor: gradLine,
                borderWidth: 2.5,
                pointBackgroundColor: BRAND,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1d1c17',
                    titleColor: '#fff',
                    bodyColor: '#cbc7b6',
                    padding: 10,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: '#f2ede5' },
                    ticks: { precision: 0 }
                }
            }
        }
    });

    // ── Doughnut Chart: Role ──────────
    new Chart(document.getElementById('roleChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Member', 'Admin'],
            datasets: [{
                data: [
                    {{ $stats['total_users'] - 1 > 0 ? $stats['total_users'] - 1 : 0 }},
                    {{ $stats['total_users'] > 0 ? 1 : 0 }}
                ],
                backgroundColor: [BRAND, '#ffd6f0'],
                borderColor: ['#fff', '#fff'],
                borderWidth: 3,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        usePointStyle: true,
                        pointStyleWidth: 8,
                    }
                },
                tooltip: {
                    backgroundColor: '#1d1c17',
                    titleColor: '#fff',
                    bodyColor: '#cbc7b6',
                    padding: 10,
                    cornerRadius: 8,
                }
            }
        }
    });
</script>
@endpush
