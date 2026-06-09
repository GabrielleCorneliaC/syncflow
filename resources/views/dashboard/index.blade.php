@extends('layouts.app')
@section('title', 'Dashboard — SyncFlow')
@section('page-title', 'Dashboard')

@section('content')
{{--
    ════════════════════════════════════════════════════════════════
    SyncFlow Dashboard — Bento Grid Layout
    Ref Figma : node 60:1273 (desktop) · node 9:121 (mobile)
    Layout    : 12-col bento grid desktop, single-col mobile
    Sections  :
      1. Task Progress (8 cols)       ← row 1
      2. Next 7 Days (4 cols)         ← row 1
      3. Productivity Chart (8 cols)  ← row 2
      4. Calendar (4 cols)            ← row 2
      5. Tugas Personal (6 cols)      ← row 3
      6. Tugas Kolaborasi (6 cols)    ← row 3
    ════════════════════════════════════════════════════════════════
--}}

{{-- ── Top Header ──────────────────────────────────────────────── --}}
<div class="mb-10 sm:mb-12 lg:mb-16">
    <p class="font-poppins font-semibold text-[#894e4b] text-lg leading-7">Welcome back,</p>
    <h1 class="font-montserrat font-extrabold text-[#656026] text-4xl sm:text-5xl leading-[1.2] tracking-[-0.02em] mt-1">
        Halo, {{ Auth::user()->name }}
    </h1>
</div>

{{-- ══════════════════════════════════════════════════════════════
     BENTO GRID — 12 kolom desktop, 1 kolom mobile
══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 md:grid-cols-12 gap-5 lg:gap-6">

    {{-- ══════════════════════════════════════════════
         ROW 1-A │ Section 1: Keseluruhan Progres Tugas
         Desktop: col 1-8  │  Mobile: full
    ══════════════════════════════════════════════ --}}
    <div class="
        md:col-span-8
        bg-white border border-[rgba(179,0,132,0.1)] rounded-xl
        shadow-[0px_4px_12px_0px_rgba(106,20,82,0.08)]
        p-8 sm:p-10 lg:p-12
        relative overflow-hidden
        flex flex-col gap-6
    ">
        {{-- Folder tab dekorasi atas kiri --}}
        <div class="absolute top-0 left-0 w-32 h-2 bg-[#894e4b] opacity-80 rounded-br-lg rounded-tl-xl"></div>

        {{-- Heading --}}
        <div class="flex items-center gap-2 mt-1">
            <svg class="w-[18px] h-[18px] shrink-0 text-[#656026]" viewBox="0 0 18 18" fill="none">
                <rect x="1" y="1" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/>
                <rect x="10" y="1" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/>
                <rect x="1" y="10" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/>
                <rect x="10" y="10" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/>
            </svg>
            <h2 class="font-poppins font-semibold text-[#656026] text-xl leading-7">Keseluruhan Progres Tugas</h2>
        </div>

        {{-- Progress + Angka --}}
        <div class="flex items-center gap-6">
            {{-- Progress bar area --}}
            <div class="flex-1 flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">Current Term</span>
                    <span class="font-poppins font-semibold text-[#b30084] text-base leading-6">
                        {{ $stats['progress_pct'] ?? 65 }}%
                    </span>
                </div>
                {{-- Progress bar --}}
                <div class="w-full h-4 bg-[rgba(179,0,132,0.1)] border border-[rgba(179,0,132,0.05)] rounded-full p-px overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-[#b30084] to-[#894e4b]
                                relative overflow-hidden transition-all duration-700"
                         style="width: {{ $stats['progress_pct'] ?? 65 }}%">
                        {{-- Shimmer stripe --}}
                        <div class="absolute inset-0 opacity-20"
                             style="background: repeating-linear-gradient(45deg, rgba(255,255,255,0.2) 0, rgba(255,255,255,0.2) 4px, transparent 4px, transparent 8px)">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Angka bulat --}}
            <div class="shrink-0 w-24 h-24 rounded-full border-4 border-[#e6e2da]
                        flex items-center justify-center
                        shadow-[inset_0px_2px_4px_0px_rgba(0,0,0,0.05)]">
                <span class="font-montserrat font-bold text-[#656026] text-2xl leading-8">
                    {{ $stats['tasks_done'] ?? 12 }}/{{ $stats['tasks_total'] ?? 18 }}
                </span>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         ROW 1-B │ Section 2: Next 7 Days
         Desktop: col 9-12  │  Mobile: full
    ══════════════════════════════════════════════ --}}
    <div class="
        md:col-span-4
        bg-[#ffb3ae] border border-[rgba(179,0,132,0.2)] rounded-xl
        shadow-[0px_4px_6px_rgba(106,20,82,0.08)]
        pt-[25px] pb-9 px-[25px]
        flex flex-col gap-6
    ">
        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-[rgba(137,78,75,0.2)] pb-[13px]">
            <h2 class="font-poppins font-semibold text-[#7b4240] text-base leading-6">Next 7 Days</h2>
            <svg class="w-[18px] h-5 text-[#7b4240]" viewBox="0 0 18 20" fill="none">
                <rect x="1" y="3" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/>
                <path d="M1 7h16" stroke="currentColor" stroke-width="1.5"/>
                <path d="M5 1v4M13 1v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>

        {{-- List items --}}
        <div class="flex flex-col gap-3">

            {{-- Item 1 - Upcoming (magenta) --}}
            <div class="flex items-center gap-3 bg-[rgba(255,255,255,0.6)] backdrop-blur-[2px] rounded-lg p-2">
                <div class="shrink-0 w-10 h-10 rounded-md bg-[#b30084]
                            flex flex-col items-center justify-center">
                    <span class="font-inter font-bold text-white text-[10px] uppercase leading-[15px]">MON</span>
                    <span class="font-inter font-bold text-white text-sm leading-5">12</span>
                </div>
                <div class="flex-1 min-w-0 overflow-hidden">
                    <p class="font-inter font-semibold text-[#1d1c17] text-xs tracking-[0.6px] truncate">Physics Lab Report</p>
                    <p class="font-inter font-medium text-[#894e4b] text-[11px] leading-[14px]">Due 11:59 PM</p>
                </div>
            </div>

            {{-- Item 2 - Netral (beige) --}}
            <div class="flex items-center gap-3 bg-[rgba(255,255,255,0.6)] backdrop-blur-[2px] rounded-lg p-2">
                <div class="shrink-0 w-10 h-10 rounded-md bg-[#e6e2da]
                            flex flex-col items-center justify-center">
                    <span class="font-inter font-bold text-[#1d1c17] text-[10px] uppercase leading-[15px]">WED</span>
                    <span class="font-inter font-bold text-[#1d1c17] text-sm leading-5">14</span>
                </div>
                <div class="flex-1 min-w-0 overflow-hidden">
                    <p class="font-inter font-semibold text-[#1d1c17] text-xs tracking-[0.6px] truncate">Study Group</p>
                    <p class="font-inter font-medium text-[#49473a] text-[11px] leading-[14px]">Library - 3:00 PM</p>
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         ROW 2-A │ Section 3: Productivity Weekly Trend
         Desktop: col 1-8  │  Mobile: full
    ══════════════════════════════════════════════ --}}
    <div class="
        md:col-span-8
        bg-white border border-[rgba(179,0,132,0.1)] rounded-xl
        shadow-[0px_4px_6px_rgba(106,20,82,0.08)]
        p-6 relative overflow-hidden
    " style="min-height: 320px;">

        <h2 class="font-poppins font-medium text-[#656026] text-base leading-6 mb-4">Productivity Weekly Trend</h2>

        {{-- Chart.js canvas --}}
        <div class="relative" style="height: 220px;">
            <canvas id="productivityChart"></canvas>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         ROW 2-B │ Section 4: Calendar
         Desktop: col 9-12  │  Mobile: full
    ══════════════════════════════════════════════ --}}
    <div class="
        md:col-span-4
        bg-white border border-[rgba(179,0,132,0.1)] rounded-xl
        shadow-[0px_4px_6px_rgba(106,20,82,0.08)]
        pt-[25px] pb-6 px-[25px]
        flex flex-col gap-2
    ">
        {{-- Wrapper Utama Kalender (Bikin nyusun ke bawah) --}}
        <div class="w-full flex flex-col" x-data="calendarWidget()">

            {{-- Header Kalender: < Bulan Tahun > --}}
            <div class="flex items-center justify-between mb-4 px-2">
                {{-- Tombol Kiri (<) --}}
                <button @click="prev()"
                        class="p-2 rounded-lg hover:bg-[#f2ede5] transition text-[#49473a] flex items-center justify-center">
                    <svg class="w-[8px] h-[12px]" viewBox="0 0 5 8" fill="none">
                        <path d="M4 1L1 4L4 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                {{-- Teks Bulan Tahun --}}
                <h2 class="font-poppins font-semibold text-[#656026] text-lg leading-6"
                    x-text="monthYear"></h2>

                {{-- Tombol Kanan (>) --}}
                <button @click="next()"
                        class="p-2 rounded-lg hover:bg-[#f2ede5] transition text-[#49473a] flex items-center justify-center">
                    <svg class="w-[8px] h-[12px]" viewBox="0 0 5 8" fill="none">
                        <path d="M1 1L4 4L1 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>

            {{-- Bagian Hari & Tanggal --}}
            <div class="w-full">
                {{-- Nama Hari (S M T W T F S) --}}
                <div class="grid grid-cols-7 gap-1 mb-2">
                    <template x-for="d in ['S','M','T','W','T','F','S']">
                        <div class="text-center font-inter font-medium text-[#7a7769] text-sm leading-6"
                             x-text="d"></div>
                    </template>
                </div>
                
                {{-- Angka Tanggal --}}
                <div class="grid grid-cols-7 gap-1">
                    <template x-for="(day, i) in calDays" :key="i">
                        <div class="flex flex-col items-center justify-center rounded py-2 text-sm leading-6 cursor-pointer transition relative"
                             :class="{
                                 'text-[#e6e2da]': day.outside,
                                 'text-[#1d1c17]': !day.outside && !day.today && !day.selected,
                                 'bg-[#656026] text-white rounded-md font-bold shadow-md': day.selected,
                                 'font-bold text-[#b30084]': day.today && !day.selected,
                             }"
                             @click="select(day)"
                             x-text="day.d">
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         ROW 3-A │ Tugas Personal Belum Selesai
         Desktop: col 1-6  │  Mobile: full
    ══════════════════════════════════════════════ --}}
    <div class="
        md:col-span-6
        bg-white border border-[rgba(179,0,132,0.1)] rounded-xl
        shadow-[0px_4px_6px_rgba(106,20,82,0.08)]
        pt-10 pb-8 px-[25px]
        relative overflow-hidden
        flex flex-col gap-6
    ">
        {{-- Folder tab atas kiri --}}
        <div class="absolute top-0 left-0 w-24 h-6
                    bg-[#fff1f5] border-b border-r border-[rgba(179,0,132,0.2)]
                    rounded-br-lg rounded-tl-xl
                    flex items-center justify-center">
            <span class="font-inter font-bold text-[#b30084] text-[10px] uppercase tracking-[0.5px]">Personal</span>
        </div>

        {{-- Heading --}}
        <div class="flex items-center justify-between">
            <h2 class="font-poppins font-medium text-[#656026] text-base leading-6">Tugas Personal Belum Selesai</h2>
            <a href="#" class="font-poppins font-medium text-[#b30084] text-sm leading-5 hover:underline">View all</a>
        </div>

        {{-- List --}}
        <div class="flex flex-col gap-3">

            {{-- Item: Pending --}}
            <div class="flex items-center gap-4 bg-[#fdf9f0] border border-[rgba(203,199,182,0.4)] rounded-lg p-[13px]">
                <div class="shrink-0 w-5 h-5 rounded-full border-2 border-[#cbc7b6]"></div>
                <div class="flex-1 min-w-0">
                    <p class="font-inter text-[#1d1c17] text-base leading-6">Read Literature Ch 4-5</p>
                </div>
                <span class="shrink-0 bg-[#f2ede5] border border-[rgba(203,199,182,0.3)] rounded
                             font-inter font-bold text-[#49473a] text-[10px] uppercase tracking-[0.5px]
                             px-[9px] py-[5px]">
                    PENDING
                </span>
            </div>

            {{-- Item: Overdue HIGH --}}
            <div class="flex items-center gap-4 bg-[rgba(255,241,245,0.3)] border border-[rgba(179,0,132,0.4)] rounded-lg p-[13px]">
                <div class="shrink-0 w-5 h-5 rounded-full border-2 border-[#cbc7b6]"></div>
                <div class="flex-1 min-w-0">
                    <p class="font-inter font-medium text-[#1d1c17] text-base leading-6">Submit Math Assignment</p>
                    <div class="flex items-center gap-1 mt-0.5">
                        <svg class="w-[11px] h-[9.5px] text-[#ba1a1a]" viewBox="0 0 12 10" fill="none">
                            <path d="M6 1L11 9H1L6 1Z" stroke="currentColor" stroke-width="1.2"/>
                            <path d="M6 4v2.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                            <circle cx="6" cy="8" r="0.5" fill="currentColor"/>
                        </svg>
                        <span class="font-inter text-[#ba1a1a] text-xs leading-4">Overdue</span>
                    </div>
                </div>
                <span class="shrink-0 bg-[#b30084] shadow-[0px_1px_1px_rgba(0,0,0,0.05)] rounded
                             font-inter font-bold text-white text-[10px] uppercase
                             px-2 py-1">
                    HIGH
                </span>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         ROW 3-B │ Tugas Kolaborasi Belum Selesai
         Desktop: col 7-12  │  Mobile: full
    ══════════════════════════════════════════════ --}}
    <div class="
        md:col-span-6
        bg-white border border-[rgba(179,0,132,0.1)] rounded-xl
        shadow-[0px_4px_6px_rgba(106,20,82,0.08)]
        pt-10 pb-6 px-[25px]
        relative overflow-hidden
        flex flex-col gap-6
    ">
        {{-- Folder tab atas kiri (pink-red) --}}
        <div class="absolute top-0 left-0 w-24 h-6
                    bg-[#ffb3ae] border-b border-r border-[rgba(137,78,75,0.2)]
                    rounded-br-lg rounded-tl-xl
                    flex items-center justify-center">
            <span class="font-inter font-bold text-[#894e4b] text-[10px] uppercase tracking-[0.5px]">Collab</span>
        </div>

        {{-- Heading --}}
        <div class="flex items-center justify-between">
            <h2 class="font-poppins font-medium text-[#656026] text-base leading-6">Tugas Kolaborasi Belum Selesai</h2>
            <a href="#" class="font-poppins font-medium text-[#894e4b] text-sm leading-5 hover:underline">View all</a>
        </div>

        {{-- List --}}
        <div class="flex flex-col gap-3">

            {{-- Item: dengan avatar + PENDING --}}
            <div class="flex items-center gap-4 bg-[#fdf9f0] border border-[rgba(203,199,182,0.4)] rounded-lg p-[13px]">
                <div class="shrink-0 w-5 h-5 rounded-full border-2 border-[#cbc7b6]"></div>
                <div class="flex-1 min-w-0">
                    <p class="font-inter text-[#1d1c17] text-base leading-6">Draft Presentation Slides</p>
                    {{-- Avatar stack --}}
                    <div class="flex items-center mt-2">
                        <div class="w-5 h-5 rounded-full bg-[#656026] border border-white
                                    flex items-center justify-center
                                    font-inter text-[8px] text-white font-normal leading-[12px]">S</div>
                        <div class="w-5 h-5 rounded-full bg-[#b30084] border border-white -ml-2
                                    flex items-center justify-center
                                    font-inter text-[8px] text-white font-normal leading-[12px]">A</div>
                    </div>
                </div>
                <span class="shrink-0 bg-[#f2ede5] border border-[rgba(203,199,182,0.3)] rounded
                             font-inter font-bold text-[#49473a] text-[10px] uppercase tracking-[0.5px]
                             px-[9px] py-[5px]">
                    PENDING
                </span>
            </div>

            {{-- Item: REVIEW --}}
            <div class="flex items-center gap-4 bg-[#fdf9f0] border border-[rgba(203,199,182,0.4)] rounded-lg p-[13px]">
                <div class="shrink-0 w-5 h-5 rounded-full border-2 border-[#cbc7b6]"></div>
                <div class="flex-1 min-w-0">
                    <p class="font-inter text-[#1d1c17] text-base leading-6">Review Code PR</p>
                </div>
                <span class="shrink-0 bg-[#894e4b] rounded
                             font-inter font-bold text-white text-[10px] uppercase
                             px-2 py-1">
                    REVIEW
                </span>
            </div>

        </div>
    </div>

</div>
{{-- /BENTO GRID --}}

@endsection


@push('scripts')
<script>
// ─────────────────────────────────────────────────────────────
//  Chart.js — Productivity Weekly Trend
//  Bar chart sesuai Figma: warna & tinggi dari desain
// ─────────────────────────────────────────────────────────────
(function () {
    const ctx = document.getElementById('productivityChart');
    if (!ctx) return;

    // Data & warna dari Figma (M T W T F S S)
    const data   = [30, 50, 80, 40, 60, 20, 90];
    const days   = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
    const colors = [
        '#ffb3ae',          // M  – soft pink
        '#fff1f5',          // T  – lighter pink
        '#b30084',          // W  – primary magenta (paling tinggi)
        '#ffb3ae',          // T  – soft pink
        '#fff1f5',          // F  – lighter pink
        '#e6e2da',          // S  – beige netral
        '#b30084',          // S  – primary magenta (paling tinggi)
    ];
    const glows = colors.map(c => c === '#b30084' ? 'rgba(179,0,132,0.35)' : 'transparent');

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color       = '#7a7769';

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: days,
            datasets: [{
                data,
                backgroundColor: colors,
                borderRadius:    { topLeft: 2, topRight: 2 },
                borderSkipped:   false,
                barPercentage:   0.55,
                categoryPercentage: 0.7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1d1c17',
                    titleColor:      '#cbc7b6',
                    bodyColor:       '#fff',
                    padding:         8,
                    cornerRadius:    6,
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} tasks`
                    }
                }
            },
            scales: {
                x: {
                    grid:   { display: false },
                    border: { display: false },
                    ticks:  { font: { size: 12 }, color: '#7a7769' }
                },
                y: {
                    display: false,
                    beginAtZero: true,
                    grid: { color: 'rgba(203,199,182,0.3)', drawBorder: false },
                }
            }
        }
    });
})();

// ─────────────────────────────────────────────────────────────
//  Alpine.js — Calendar Widget
// ─────────────────────────────────────────────────────────────
function calendarWidget() {
    return {
        today:    new Date(),
        current:  new Date(),
        selected: new Date(),
        get monthYear() {
            return this.current.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
        },
        get calDays() {
            const y = this.current.getFullYear();
            const m = this.current.getMonth();
            const firstDay = new Date(y, m, 1).getDay();      // 0=Sun
            const daysInMonth = new Date(y, m + 1, 0).getDate();
            const prevDays = new Date(y, m, 0).getDate();
            const days = [];

            // Isi slot bulan sebelumnya (faded)
            for (let i = firstDay - 1; i >= 0; i--) {
                days.push({ d: prevDays - i, outside: true, today: false, selected: false });
            }
            // Isi hari bulan ini
            for (let d = 1; d <= daysInMonth; d++) {
                const date = new Date(y, m, d);
                days.push({
                    d,
                    outside: false,
                    today: this._same(date, this.today),
                    selected: this._same(date, this.selected),
                    date,
                });
            }
            // Isi slot bulan depan (sampai grid penuh)
            const rem = (7 - (days.length % 7)) % 7;
            for (let d = 1; d <= rem; d++) {
                days.push({ d, outside: true, today: false, selected: false });
            }
            return days;
        },
        _same(a, b) {
            return a.getFullYear() === b.getFullYear()
                && a.getMonth()    === b.getMonth()
                && a.getDate()     === b.getDate();
        },
        prev() { this.current = new Date(this.current.getFullYear(), this.current.getMonth() - 1, 1); },
        next() { this.current = new Date(this.current.getFullYear(), this.current.getMonth() + 1, 1); },
        select(day) {
            if (!day.outside && day.date) this.selected = day.date;
        },
    };
}
</script>
@endpush
