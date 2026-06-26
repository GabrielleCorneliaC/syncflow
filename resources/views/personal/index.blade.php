@extends('layouts.app')
@section('title', 'Personal List — SyncFlow')
@section('page-title', 'Personal List')

@section('content')
{{--
    ════════════════════════════════════════════════════════════════
    SyncFlow Halaman List Personal
    Ref Figma:
      Desktop  → node 1:55   — sidebar + 2 section (Task + Schedule)
      Mobile   → node 29:2   — bottom nav + FAB magenta + scroll horizontal tabel

    Sections:
      [A] List Task (Daftar Tugas)
          Table: checkbox · Task Name & Progress bar · Deadline · Status badge · ⋮
      [B] List Schedule (Daftar Jadwal Acara)
          Table: dot · Schedule Name · Date · Time · Location · ⋮
    ════════════════════════════════════════════════════════════════
--}}

{{-- ════════════════════════════════════════════════════════════
     FLASH MESSAGES
════════════════════════════════════════════════════════════ --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(()=>show=false,3500)"
     class="mb-6 flex items-center gap-2 bg-green-50 border border-green-200
            text-green-700 text-sm rounded-xl px-4 py-3">
    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
    </svg>
    {{ session('success') }}
</div>
@endif

{{-- ── Page Header ────────────────────────────────────────────── --}}
<div class="flex items-end justify-between
            border-b border-[rgba(203,199,182,0.3)] pb-[13px]
            mb-6">

    {{-- Judul: Montserrat ExtraBold 48px #1d1c17 --}}
    <h1 class="font-montserrat font-extrabold text-[#1d1c17] tracking-[-0.02em]
               text-3xl sm:text-4xl lg:text-5xl leading-tight">
        Your Workflow
    </h1>

    {{-- Tombol Sync Google Calendar --}}
    @if(Auth::user()->hasGoogleCalendarConnected())
        {{-- Tampilan jika sudah tersambung --}}
        <div class="flex items-center gap-1 bg-green-50 border border-green-200 text-green-700 rounded-full px-5 py-[13px] font-inter font-semibold text-xs uppercase tracking-[0.6px] drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)]">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            Connected to Google Calendar
        </div>
    @else
        {{-- Tampilan jika belum tersambung (Tetap pakai <button>) --}}
        <button onclick="window.location.href='{{ route('google.calendar.redirect') }}'" 
                class="flex items-center gap-1
                       bg-[#ece8df] border border-[rgba(203,199,182,0.5)]
                       drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)]
                       rounded-full px-5 py-[13px]
                       font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]
                       hover:bg-[#dedad2] transition whitespace-nowrap">
            <svg class="w-3 h-3 shrink-0" viewBox="0 0 12 12" fill="none">
                <path d="M6 1C3.24 1 1 3.24 1 6s2.24 5 5 5 5-2.24 5-5"
                      stroke="#49473a" stroke-width="1.4" stroke-linecap="round"/>
                <path d="M6 1 L8 3 M6 1 L4 3"
                      stroke="#49473a" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Sync with Google Calendar
        </button>
    @endif
</div>

{{-- Wrapper dengan padding bawah extra untuk bottom nav mobile --}}
<div class="flex flex-col gap-16 pb-28 md:pb-0">

{{-- ════════════════════════════════════════════════════════════
     SECTION A: LIST TASK
════════════════════════════════════════════════════════════ --}}
<section class="flex flex-col gap-6">

    {{-- Section heading --}}
    <div class="flex items-center gap-3">
        {{-- Ikon checklist --}}
        <svg class="w-5 h-5 shrink-0 text-[#b30084]" viewBox="0 0 20 20" fill="none">
            <circle cx="10" cy="10" r="9" stroke="currentColor" stroke-width="1.5"/>
            <path d="M6.5 10.5 L8.5 12.5 L13.5 7.5"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h2 class="flex items-baseline gap-2 leading-7">
            <span class="font-poppins font-semibold text-[#1d1c17] text-xl">List Task</span>
            <span class="font-inter font-normal text-[#49473a] text-sm">(Daftar Tugas)</span>
        </h2>
    </div>

    {{-- Tabel wrapper: scroll horizontal di mobile --}}
    <div class="overflow-x-auto rounded-xl pb-2">
        <div class="bg-white border border-[rgba(60,0,42,0.1)]
                    rounded-xl shadow-[0px_4px_12px_0px_rgba(106,20,82,0.05)]
                    overflow-visible min-w-[600px] min-h-[400px]">

            {{-- Table Header --}}
            <div class="bg-[#f8f3eb] border-b border-[rgba(203,199,182,0.3)]
                        flex items-center gap-6 px-6 py-3">
                <div class="w-10 shrink-0"></div>
                <div class="flex-1 font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">
                    Task Name &amp; Progress
                </div>
                <div class="w-32 shrink-0 font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">
                    Deadline
                </div>
                <div class="w-28 shrink-0 font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">
                    Status
                </div>
                <div class="w-10 shrink-0"></div>
            </div>

            {{-- Table Rows --}}
            @forelse($tasks as $task)
            <div class="flex items-center gap-6 px-6 min-h-[48px]
                        border-b border-[rgba(203,199,182,0.1)] last:border-b-0
                        group hover:bg-[#fdf9f0] transition
                        {{ $task->is_completed ? 'opacity-75' : '' }}">

                {{-- Checkbox toggle --}}
                <td class="px-5 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <input type="checkbox"
                            {{ $task->status === 'done' ? 'checked' : '' }}
                            onchange="updatePersonalTaskStatus({{ $task->id }}, this.checked)"
                            class="w-4 h-4 rounded cursor-pointer"
                            style="accent-color: {{ $task->status === 'done' ? '#22C55E' : '#b30084' }};">
                        <span id="loading-personal-{{ $task->id }}" class="hidden text-[10px] text-primary animate-pulse">Saving...</span>
                    </div>
                </td>

                {{-- Task name + progress bar --}}
                <div class="flex-1 flex flex-col justify-center py-3 min-w-0">
                    <p class="font-poppins font-medium text-[#1d1c17] text-base leading-6 truncate
                              {{ $task->is_completed ? 'line-through opacity-60' : '' }}">
                        {{ $task->title }}
                    </p>
                    {{-- Progress bar --}}
                    <div class="mt-2 flex items-center gap-2">
                        <div class="flex-1 h-[6px] bg-[rgba(60,0,42,0.1)] rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300"
                                style="width: {{ $task->progress }}%; 
                                        background: {{ $task->status === 'done' ? '#22C55E' : 'linear-gradient(to right, #b30084, #ffb3ae)' }};">
                            </div>
                        </div>
                        <span class="font-inter font-medium text-[#49473a] text-[11px] whitespace-nowrap">
                            {{ $task->progress }}%
                        </span>
                    </div>
                </div>

                {{-- Deadline --}}
                <div class="w-32 shrink-0 {{ $task->is_completed ? 'opacity-60' : '' }}">
                    <span class="font-inter text-sm leading-5"
                          style="color: {{ $task->deadline_color }}">
                        {{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}
                    </span>
                </div>

                {{-- Status badge --}}
                <div class="w-28 shrink-0">
                    <span class="inline-flex items-center justify-center font-inter font-semibold text-xs uppercase px-3 py-1 rounded-full"
                        style="background-color: {{ $task->status === 'done' ? '#DCFCE7' : '#f2ede5' }}; 
                                color: {{ $task->status === 'done' ? '#166534' : '#49473a' }}">
                        {{ ucfirst((string)$task->status) }}
                    </span>
                </div>

                {{-- Action menu (⋮) --}}
                <div class="w-10 shrink-0 flex justify-end relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="opacity-0 group-hover:opacity-100 transition p-1 rounded hover:bg-[#f2ede5] text-[#49473a]">
                        <svg class="w-1 h-4" viewBox="0 0 4 16" fill="currentColor">
                            <circle cx="2" cy="2" r="1.5"/><circle cx="2" cy="8" r="1.5"/><circle cx="2" cy="14" r="1.5"/>
                        </svg>
                    </button>
                    {{-- Dropdown --}}
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-6 z-20 w-40 bg-white border border-[#cbc7b6] rounded-xl shadow-lg py-1">
                        {{-- Edit (Panggil Modal Edit) --}}
                        <button @click="open=false; openModal('modal-edit-task-{{ $task->id }}')"
                                class="w-full text-left px-4 py-2 text-sm text-[#49473a] hover:bg-[#fdf9f0] transition">
                            Edit Tugas
                        </button>
                        {{-- Hapus (Panggil Modal Delete) --}}
                        <button @click="open=false; openModal('modal-delete-task-{{ $task->id }}')"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                            Hapus
                        </button>
                    </div>
                </div>

                {{-- Modal Edit & Delete khusus untuk baris Tugas ini --}}
                <x-personal-task-modal
                    id="modal-edit-task-{{ $task->id }}"
                    title="Edit Tugas"
                    action="{{ route('personal.tasks.update', $task) }}"
                    method="PUT"
                    :task="$task"
                />
                <x-delete-confirm-modal
                    id="modal-delete-task-{{ $task->id }}"
                    title="Hapus Tugas?"
                    message="Tugas '{{ $task->title }}' akan dihapus permanen."
                    action="{{ route('personal.tasks.destroy', $task) }}"
                />
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-14 text-center">
                <div class="w-12 h-12 rounded-2xl bg-[#fdf9f0] flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-[#cbc7b6]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="font-poppins font-medium text-[#49473a] text-sm mb-1">Belum ada tugas</p>
                <p class="font-inter text-[#7a7769] text-xs">Klik tombol + New untuk menambahkan tugas pertamamu</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════════
     SECTION B: LIST SCHEDULE
════════════════════════════════════════════════════════════ --}}
<section class="flex flex-col gap-6">

    {{-- Section heading --}}
    <div class="flex items-center gap-3">
        {{-- Ikon kalender --}}
        <svg class="w-[18px] h-5 shrink-0 text-[#b30084]" viewBox="0 0 18 20" fill="none">
            <rect x="1" y="3" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/>
            <path d="M1 7h16" stroke="currentColor" stroke-width="1.5"/>
            <path d="M5 1v4M13 1v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <h2 class="flex items-baseline gap-2 leading-7">
            <span class="font-poppins font-semibold text-[#1d1c17] text-xl">List Schedule</span>
            <span class="font-inter font-normal text-[#49473a] text-sm">(Daftar Jadwal Acara)</span>
        </h2>
    </div>

    {{-- Tabel Schedule --}}
    {{-- 1. Pindahkan overflow-x-auto ke kotak utama ini, hapus div pembungkus luar yang lama --}}
    <div class="bg-white border border-[rgba(60,0,42,0.1)] rounded-xl shadow-[0px_4px_12px_0px_rgba(106,20,82,0.05)] overflow-x-auto min-h-[400px]">
        
        {{-- 2. KUNCI UTAMA: Bungkus isi tabel dengan min-w-[800px] agar background dan garis ikut melar ditarik ke kanan --}}
        <div class="min-w-[800px]">
            
            {{-- Table Header --}}
            <div class="bg-[#f8f3eb] border-b border-[rgba(203,199,182,0.3)]
                        grid grid-cols-[269px_120px_100px_1fr] gap-6 items-center px-6 py-3">
                <div class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">
                    Schedule Name
                </div>
                <div class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">
                    Date
                </div>
                <div class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">
                    Time
                </div>
                <div class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">
                    Location
                </div>
            </div>

            {{-- Rows --}}
            @forelse($schedules as $schedule)
            <div class="grid grid-cols-[269px_120px_100px_1fr] gap-6 items-center px-6 py-3
                        border-b border-[rgba(203,199,182,0.1)] last:border-b-0
                        group hover:bg-[#fdf9f0] transition">

                {{-- Schedule name + dot --}}
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-2 h-2 rounded-full shrink-0"
                          style="background-color: {{ $schedule->color ?? '#ffb3ae' }}"></span>
                    <span class="font-poppins font-medium text-[#1d1c17] text-base leading-6 truncate">
                        {{ $schedule->title }}
                    </span>
                </div>

                {{-- Date --}}
                <div>
                    <span class="font-inter text-[#49473a] text-sm leading-5">
                        {{ \Carbon\Carbon::parse($schedule->date)->format('M d, Y') }}
                    </span>
                </div>

                {{-- Time --}}
                <div>
                    <span class="font-inter text-[#49473a] text-sm leading-5">
                        {{ \Carbon\Carbon::parse($schedule->time)->format('H:i A') }}
                    </span>
                </div>

                {{-- Location --}}
                <div class="min-w-0 flex items-center justify-between gap-2">
                    <span class="font-inter text-[#49473a] text-sm leading-5 truncate">
                        {{ $schedule->location ?? '—' }}
                    </span>

                    {{-- Action ⋮ --}}
                    <div class="relative flex-shrink-0" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="opacity-0 group-hover:opacity-100 transition p-1 rounded hover:bg-[#f2ede5] text-[#49473a]">
                            <svg class="w-1 h-4" viewBox="0 0 4 16" fill="currentColor">
                                <circle cx="2" cy="2" r="1.5"/><circle cx="2" cy="8" r="1.5"/><circle cx="2" cy="14" r="1.5"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mb-2 z-20 w-40 bg-white border border-[#cbc7b6] rounded-xl shadow-lg py-1">
                            <button @click="open=false; openModal('modal-edit-schedule-{{ $schedule->id }}')"
                                    class="w-full text-left px-4 py-2 text-sm text-[#49473a] hover:bg-[#fdf9f0] transition">
                                Edit Jadwal
                            </button>
                            <button @click="open=false; openModal('modal-delete-schedule-{{ $schedule->id }}')"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Modal Edit & Delete khusus untuk baris Jadwal ini --}}
                <x-personal-schedule-modal
                    id="modal-edit-schedule-{{ $schedule->id }}"
                    title="Edit Jadwal"
                    action="{{ route('personal.schedules.update', $schedule) }}"
                    method="PUT"
                    :schedule="$schedule"
                />
                <x-delete-confirm-modal
                    id="modal-delete-schedule-{{ $schedule->id }}"
                    title="Hapus Jadwal?"
                    message="Jadwal '{{ $schedule->title }}' akan dihapus permanen."
                    action="{{ route('personal.schedules.destroy', $schedule) }}"
                />
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-14 text-center">
                <div class="w-12 h-12 rounded-2xl bg-[#fdf9f0] flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-[#cbc7b6]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="font-poppins font-medium text-[#49473a] text-sm mb-1">Belum ada jadwal</p>
                <p class="font-inter text-[#7a7769] text-xs">Tambahkan jadwal acara kamu di sini</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

</div>{{-- /flex flex-col gap-16 --}}


{{-- ════════════════════════════════════════════════════════════
     FAB (Floating Action Button) — mobile only
     Figma: bg #b30084 · shadow-[4px_4px_0_#6a1452] · rounded-xl · 56px
     Desktop: tombol "+ New" di sidebar (sudah ada di layouts/app.blade.php)
════════════════════════════════════════════════════════════ --}}
{{-- FAB (Floating Action Button) — Mobile Only --}}
<div class="md:hidden fixed bottom-6 right-6 z-50" x-data="{ openMenu: false }">
    
    {{-- Tombol Utama FAB --}}
    <button @click="openMenu = !openMenu" 
            class="w-14 h-14 bg-[#b30084] text-white rounded-xl shadow-[4px_4px_0_#6a1452] flex items-center justify-center transition-transform active:scale-95">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M12 5v14m-7-7h14" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    {{-- Menu Pilihan (Muncul di atas tombol FAB) --}}
    <div x-show="openMenu" 
         @click.outside="openMenu = false"
         x-transition:enter="transition ease-out duration-200"
         class="absolute bottom-16 right-0 w-40 bg-white border border-border rounded-xl shadow-xl py-1 mb-2">
        
        {{-- Pilihan 1: Buka Modal Tugas --}}
        <button @click="openMenu = false; openModal('modal-add-task')" 
                class="w-full text-left px-4 py-3 text-sm font-semibold text-textmain hover:bg-muted transition">
            Tambah Tugas
        </button>
        
        {{-- Pilihan 2: Buka Modal Jadwal --}}
        <button @click="openMenu = false; openModal('modal-add-schedule')" 
                class="w-full text-left px-4 py-3 text-sm font-semibold text-textmain hover:bg-muted transition">
            Tambah Jadwal
        </button>
    </div>
</div>


{{-- ════════════════════════════════════════════════════════════
     MODAL: Tambah Tugas Baru
     Trigger: FAB mobile atau tombol + New di sidebar
════════════════════════════════════════════════════════════ --}}
{{-- Modal Tambah Tugas --}}
<x-personal-task-modal 
    id="modal-tambah-tugas" 
    title="Tambah Tugas Baru" 
    action="{{ route('personal.tasks.store') }}" 
    method="POST" 
/>

{{-- Modal Tambah Jadwal --}}
<x-personal-task-modal 
    id="modal-tambah-jadwal" 
    title="Tambah Jadwal Baru" 
    action="{{ route('personal.schedules.store') }}" 
    method="POST" 
/>

{{-- ── Hubungkan tombol + New di sidebar ke modal ── --}}
@push('scripts')
<script>
    // Sidebar "+ New" button dispatch ke event Alpine
    document.addEventListener('DOMContentLoaded', function () {
        // Fungsi untuk buka modal
        const openModal = () => {
            window.dispatchEvent(new CustomEvent('open-add-modal'));
        };

        // 1. Trigger dari Sidebar "+ New"
        const sidebarBtn = document.querySelector('[data-new-btn]');
        if (sidebarBtn) {
            sidebarBtn.addEventListener('click', openModal);
        }

        // 2. Trigger dari FAB Mobile
        const fabBtn = document.querySelector('[data-new-btn-mobile]');
        if (fabBtn) {
            fabBtn.addEventListener('click', openModal);
        }
    });
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    // Tambahkan juga fungsi untuk menutupnya jika belum ada
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function updatePersonalTaskStatus(taskId, isChecked) {
        // Tampilkan indikator loading
        const loadingSpan = document.getElementById('loading-personal-' + taskId);
        if (loadingSpan) loadingSpan.classList.remove('hidden');

        fetch(`/personal/tasks/${taskId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: isChecked })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh halaman agar badge dan progress bar update otomatis
                window.location.reload(); 
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
@endpush

@endsection
