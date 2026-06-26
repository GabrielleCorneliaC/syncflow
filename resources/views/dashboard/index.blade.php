@extends('layouts.app')
@section('title', 'Dashboard — SyncFlow')
@section('page-title', 'Dashboard')

@section('content')

{{-- ── Top Header  --}}
<div class="mb-10 sm:mb-12 lg:mb-16">
    <p class="font-poppins font-semibold text-[#894e4b] text-lg leading-7">Welcome back,</p>
    <h1 class="font-montserrat font-extrabold text-[#656026] text-4xl sm:text-5xl leading-[1.2] tracking-[-0.02em] mt-1">
        Halo, {{ Auth::user()->name }}
    </h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-12 gap-5 lg:gap-6">

<!-- all tugas -->
    <div class="md:col-span-8 bg-white border border-[rgba(179,0,132,0.1)] rounded-xl shadow-[0px_4px_12px_0px_rgba(106,20,82,0.08)] p-8 sm:p-10 lg:p-12 relative overflow-hidden flex flex-col gap-6">
        <div class="absolute top-0 left-0 w-32 h-2 bg-[#894e4b] opacity-80 rounded-br-lg rounded-tl-xl"></div>

        <div class="flex items-center gap-2 mt-1">
            <svg class="w-[18px] h-[18px] shrink-0 text-[#656026]" viewBox="0 0 18 18" fill="none">
                <rect x="1" y="1" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/>
                <rect x="10" y="1" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/>
                <rect x="1" y="10" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/>
                <rect x="10" y="10" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/>
            </svg>
            <h2 class="font-poppins font-semibold text-[#656026] text-xl leading-7">Keseluruhan Progres Tugas</h2>
        </div>

        <div class="flex items-center gap-6">
            <div class="flex-1 flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">Current Term</span>
                    <span class="font-poppins font-semibold text-[#b30084] text-base leading-6">
                        {{ $stats['progress_pct'] ?? 0 }}%
                    </span>
                </div>
                <div class="w-full h-4 bg-[rgba(179,0,132,0.1)] border border-[rgba(179,0,132,0.05)] rounded-full p-px overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-[#b30084] to-[#894e4b] relative overflow-hidden transition-all duration-700"
                         style="width: {{ $stats['progress_pct'] ?? 0 }}%">
                        <div class="absolute inset-0 opacity-20" style="background: repeating-linear-gradient(45deg, rgba(255,255,255,0.2) 0, rgba(255,255,255,0.2) 4px, transparent 4px, transparent 8px)"></div>
                    </div>
                </div>
            </div>

            <div class="shrink-0 w-24 h-24 rounded-full border-4 border-[#e6e2da] flex items-center justify-center shadow-[inset_0px_2px_4px_0px_rgba(0,0,0,0.05)]">
                <span class="font-montserrat font-bold text-[#656026] text-2xl leading-8">
                    {{ $stats['tasks_done'] ?? 0 }}/{{ $stats['tasks_total'] ?? 0 }}
                </span>
            </div>
        </div>
    </div>

<!-- next 7 days -->
    <div class="md:col-span-4 bg-[#ffb3ae] border border-[rgba(179,0,132,0.2)] rounded-xl shadow-[0px_4px_6px_rgba(106,20,82,0.08)] pt-[25px] pb-9 px-[25px] flex flex-col gap-6 max-h-[280px]">
        <div class="flex items-center justify-between border-b border-[rgba(137,78,75,0.2)] pb-[13px] shrink-0">
            <h2 class="font-poppins font-semibold text-[#7b4240] text-base leading-6">Next 7 Days</h2>
            <svg class="w-[18px] h-5 text-[#7b4240]" viewBox="0 0 18 20" fill="none">
                <rect x="1" y="3" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/>
                <path d="M1 7h16" stroke="currentColor" stroke-width="1.5"/>
                <path d="M5 1v4M13 1v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>

        <div class="flex flex-col gap-3 overflow-y-auto pr-1">
            @forelse($upcomingItems ?? [] as $item)
                <div class="flex items-center gap-3 bg-[rgba(255,255,255,0.6)] backdrop-blur-[2px] rounded-lg p-2 shrink-0">
                    <div class="shrink-0 w-10 h-10 rounded-md {{ $loop->first ? 'bg-[#b30084]' : 'bg-[#e6e2da]' }} flex flex-col items-center justify-center">
                        <span class="font-inter font-bold {{ $loop->first ? 'text-white' : 'text-[#1d1c17]' }} text-[10px] uppercase leading-[15px]">
                            {{ \Carbon\Carbon::parse($item->date)->format('D') }}
                        </span>
                        <span class="font-inter font-bold {{ $loop->first ? 'text-white' : 'text-[#1d1c17]' }} text-sm leading-5">
                            {{ \Carbon\Carbon::parse($item->date)->format('d') }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0 overflow-hidden">
                        <p class="font-inter text-md font-semibold text-[#1d1c17] text-xs tracking-[0.6px] truncate">{{ $item->title }}</p>

                    </div>
                </div>
            @empty
                <p class="text-sm text-[#7b4240] text-center py-2">Tidak ada jadwal 7 hari ke depan.</p>
            @endforelse
        </div>
    </div>


    
    {{-- A. Tugas Personal Belum Selesai --}}
    <div class="md:col-span-4 bg-white border border-[rgba(179,0,132,0.1)] rounded-xl shadow-[0px_4px_6px_rgba(106,20,82,0.08)] pt-10 pb-8 px-[25px] relative overflow-hidden flex flex-col gap-6 h-[400px]">
        <div class="absolute top-0 left-0 w-24 h-6 bg-[#fff1f5] border-b border-r border-[rgba(179,0,132,0.2)] rounded-br-lg rounded-tl-xl flex items-center justify-center">
            <span class="font-inter font-bold text-[#b30084] text-[10px] uppercase tracking-[0.5px]">Personal</span>
        </div>

        <div class="flex items-center justify-between shrink-0 mt-2">
            <h2 class="font-poppins font-medium text-[#656026] text-[15px] leading-6 truncate pr-2">Tugas Personal</h2>
            <a href="{{ route('personal.index') }}" class="font-poppins font-medium text-[#b30084] text-sm leading-5 hover:underline shrink-0">View all</a>
        </div>

        <div class="flex flex-col gap-3 overflow-y-auto pr-1">
            @forelse($pendingPersonalTasks ?? [] as $task)
                @php
                    $isHigh = ($task->priority === 'high');
                    $isOverdue = isset($task->due_date) && \Carbon\Carbon::parse($task->due_date)->isPast();
                @endphp
                <div class="flex items-center gap-3 {{ $isHigh ? 'bg-[rgba(255,241,245,0.3)] border-[rgba(179,0,132,0.4)]' : 'bg-[#fdf9f0] border-[rgba(203,199,182,0.4)]' }} border rounded-lg p-3 shrink-0">
                    <div class="shrink-0 w-5 h-5 rounded-full border-2 border-[#cbc7b6]"></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-inter {{ $isHigh ? 'font-medium' : '' }} text-[#1d1c17] text-[13px] leading-5 truncate">{{ $task->title ?? $task->name }}</p>
                        
                        @if($isOverdue)
                        <div class="flex items-center gap-1 mt-0.5">
                            <svg class="w-[11px] h-[9.5px] text-[#ba1a1a]" viewBox="0 0 12 10" fill="none"><path d="M6 1L11 9H1L6 1Z" stroke="currentColor" stroke-width="1.2"/><path d="M6 4v2.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/><circle cx="6" cy="8" r="0.5" fill="currentColor"/></svg>
                            <span class="font-inter text-[#ba1a1a] text-xs leading-4">Overdue</span>
                        </div>
                        @endif
                    </div>
                    <span class="shrink-0 {{ $isHigh ? 'bg-[#b30084] text-white shadow-[0px_1px_1px_rgba(0,0,0,0.05)]' : 'bg-[#f2ede5] text-[#49473a] border border-[rgba(203,199,182,0.3)] tracking-[0.5px]' }} rounded font-inter font-bold text-[9px] uppercase px-2 py-1">
                        {{ $task->status ?? 'PENDING' }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-[#49473a] text-center py-4">Semua tugas personal beres!</p>
            @endforelse
        </div>
    </div>

    {{-- B. Tugas Kolaborasi Belum Selesai --}}
    <div class="md:col-span-4 bg-white border border-[rgba(179,0,132,0.1)] rounded-xl shadow-[0px_4px_6px_rgba(106,20,82,0.08)] pt-10 pb-6 px-[25px] relative overflow-hidden flex flex-col gap-6 h-[400px]">
        <div class="absolute top-0 left-0 w-24 h-6 bg-[#ffb3ae] border-b border-r border-[rgba(137,78,75,0.2)] rounded-br-lg rounded-tl-xl flex items-center justify-center">
            <span class="font-inter font-bold text-[#894e4b] text-[10px] uppercase tracking-[0.5px]">Collab</span>
        </div>

        <div class="flex items-center justify-between shrink-0 mt-2">
            <h2 class="font-poppins font-medium text-[#656026] text-[15px] leading-6 truncate pr-2">Tugas Kolaborasi</h2>
            <a href="{{ route('workspaces.index') }}" class="font-poppins font-medium text-[#894e4b] text-sm leading-5 hover:underline shrink-0">View all</a>
        </div>

        <div class="flex flex-col gap-3 overflow-y-auto pr-1">
            @forelse($pendingCollabTasks ?? [] as $task)
                @php $isReview = strtolower($task->status) === 'review'; @endphp
                <div class="flex items-center gap-3 bg-[#fdf9f0] border border-[rgba(203,199,182,0.4)] rounded-lg p-3 shrink-0">
                    <div class="shrink-0 w-5 h-5 rounded-full border-2 border-[#cbc7b6]"></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-inter text-[#1d1c17] text-[13px] leading-5 truncate">{{ $task->name ?? $task->title }}</p>
                        <p class="font-inter text-[#894e4b] font-medium text-[10px] mt-0.5 truncate border-b border-[#894e4b]/20 pb-[1px] inline-block">
                            @ {{ $task->workspace->name ?? 'Workspace' }}
                        </p>
                        
                        @if(isset($task->assignees) && $task->assignees->count() > 0)
                        <div class="flex items-center mt-1.5">
                            @foreach($task->assignees->take(3) as $user)
                                <div class="w-4 h-4 rounded-full bg-[#{{ substr(md5($user->name), 0, 6) }}] border border-white {{ !$loop->first ? '-ml-1.5' : '' }} flex items-center justify-center font-inter text-[7px] text-white font-normal">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <span class="shrink-0 {{ $isReview ? 'bg-[#894e4b] text-white' : 'bg-[#f2ede5] text-[#49473a] border border-[rgba(203,199,182,0.3)] tracking-[0.5px]' }} rounded font-inter font-bold text-[9px] uppercase px-2 py-1">
                        {{ $task->status ?? 'PENDING' }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-[#49473a] text-center py-4">Kerja tim yang bagus, tidak menunggak!</p>
            @endforelse
        </div>
    </div>

    {{-- C. Calendar --}}
    <div class="md:col-span-4 bg-white border border-[rgba(179,0,132,0.1)] rounded-xl shadow-[0px_4px_6px_rgba(106,20,82,0.08)] pt-[25px] pb-6 px-[25px] flex flex-col h-[400px]">
        <div class="w-full flex flex-col h-full" x-data="calendarWidget()">
            <div class="flex items-center justify-between mb-4 px-2 shrink-0">
                <button @click="prev()" class="p-2 rounded-lg hover:bg-[#f2ede5] transition text-[#49473a] flex items-center justify-center">
                    <svg class="w-[8px] h-[12px]" viewBox="0 0 5 8" fill="none"><path d="M4 1L1 4L4 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <h2 class="font-poppins font-semibold text-[#656026] text-lg leading-6" x-text="monthYear"></h2>
                <button @click="next()" class="p-2 rounded-lg hover:bg-[#f2ede5] transition text-[#49473a] flex items-center justify-center">
                    <svg class="w-[8px] h-[12px]" viewBox="0 0 5 8" fill="none"><path d="M1 1L4 4L1 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <div class="w-full flex-1 flex flex-col justify-center">
                <div class="grid grid-cols-7 gap-1 mb-2 shrink-0">
                    <template x-for="d in ['S','M','T','W','T','F','S']">
                        <div class="text-center font-inter font-medium text-[#7a7769] text-[13px] leading-6" x-text="d"></div>
                    </template>
                </div>
                <div class="grid grid-cols-7 gap-y-2 gap-x-1">
                    <template x-for="(day, i) in calDays" :key="i">
                        <div class="flex flex-col items-center justify-center rounded py-[6px] text-sm leading-6 cursor-pointer transition relative"
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

</div>
{{-- /GRID --}}

@endsection


@push('scripts')
<script>

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
            const firstDay = new Date(y, m, 1).getDay();      
            const daysInMonth = new Date(y, m + 1, 0).getDate();
            const prevDays = new Date(y, m, 0).getDate();
            const days = [];

            for (let i = firstDay - 1; i >= 0; i--) {
                days.push({ d: prevDays - i, outside: true, today: false, selected: false });
            }
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
