{{-- workspaces/show.blade.php --}}
@extends('layouts.app')
@section('title', $workspace->name . ' — Workspace')

@section('content')
{{-- Alert Notifikasi --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 relative">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 relative">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 relative">
        <strong class="font-bold">Gagal menyimpan!</strong>
        <ul class="list-disc pl-5 mt-1 text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    {{-- ══════════════════════════════════════════
         HERO HEADER dengan cover image
         Tinggi dikurangi agar proporsional di dalam
         main content yang sudah punya padding dari layouts.app
    ══════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl border border-border
                shadow-[3px_3px_0px_#cbc7b6] mb-6"
         style="height: 156px;">

        {{-- Cover image / fallback gradient brand --}}
        @if($workspace->cover_image)
            <img src="{{ $workspace->cover_image }}" alt="{{ $workspace->name }}"
                 class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-br from-accent to-primary"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-r from-black/55 via-black/20 to-transparent"></div>

        {{-- Back + Title --}}
        <div class="absolute inset-0 flex items-center px-6">
            <div>
                <a href="{{ route('workspaces.index') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-medium text-white/75
                          hover:text-white mb-2 transition-colors">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
                    </svg>
                    Back to Workspaces
                </a>
                <h1 class="font-heading font-bold text-xl text-white">{{ $workspace->name }}</h1>
                @if($workspace->description)
                <p class="text-white/70 text-xs mt-0.5">{{ $workspace->description }}</p>
                @endif
            </div>
        </div>

        {{-- Admin actions — hanya owner --}}
        @if(!empty($isOwner))
        <div class="absolute top-4 right-5 flex items-center gap-2 z-20">

            {{-- Invite --}}
            <button onclick="openInviteModal({{ $workspace->id }})"
                class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg
                       bg-white/90 text-primary hover:bg-white transition-all">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <line x1="19" y1="8" x2="19" y2="14"/>
                    <line x1="22" y1="11" x2="16" y2="11"/>
                </svg>
                Invite
            </button>

            {{-- Edit --}}
            <button onclick="openEditWorkspaceModal()"
                class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg
                       bg-white/90 text-textmain hover:bg-white transition-all">
                Edit
            </button>

            {{-- Delete --}}
            <form action="{{ route('workspaces.destroy', $workspace->id) }}" method="POST" class="m-0 p-0 flex"
                  onsubmit="return confirm('Hapus workspace ini? Tindakan tidak bisa dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg
                           bg-white/90 text-red-600 hover:bg-white transition-all">
                    Delete
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════
         TABS
         Menggunakan border-b dengan warna primary
         brand (#b30084) sesuai layouts.app
    ══════════════════════════════════════════ --}}
    <div class="border-b border-border mb-6">
        <div class="flex gap-1">

            {{-- Tab: List Task --}}
            <button onclick="switchTab('task')" id="tab-task"
                class="ws-tab flex items-center gap-2 px-5 py-3 text-sm font-semibold
                       border-b-2 -mb-px transition-all duration-150
                       border-primary text-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                    <path d="M9 12h6M9 16h4"/>
                </svg>
                List Task
            </button>

            {{-- Tab: List Schedule --}}
            <button onclick="switchTab('schedule')" id="tab-schedule"
                class="ws-tab flex items-center gap-2 px-5 py-3 text-sm font-semibold
                       border-b-2 -mb-px transition-all duration-150
                       border-transparent text-texthint hover:text-textsoft">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                List Schedule
            </button>

            {{-- Tab: Resource & Link --}}
            <button onclick="switchTab('resource')" id="tab-resource"
                class="ws-tab flex items-center gap-2 px-5 py-3 text-sm font-semibold
                       border-b-2 -mb-px transition-all duration-150
                       border-transparent text-texthint hover:text-textsoft">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/>
                    <path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/>
                </svg>
                Resource & Link
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         PANEL: LIST TASK
    ══════════════════════════════════════════ --}}
    <div id="panel-task">

        {{-- Toolbar --}}
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-texthint">
                Menampilkan
                <span class="font-semibold text-textsoft">{{ $tasks->count() }}</span>
                tugas
            </p>
            @if(!empty($isOwner))
            <button onclick="openAddTaskModal()"
                class="flex items-center gap-2 text-sm font-semibold text-white
                       px-4 py-2 rounded-xl
                       bg-primary hover:bg-primary-dark
                       shadow-[3px_3px_0px_#6a1452]
                       active:translate-y-px active:shadow-[1px_1px_0_#6a1452]
                       transition-all">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Tambah Tugas
            </button>
            @endif
        </div>

        {{-- Tabel task --}}
        <div class="bg-surface rounded-2xl border border-border
                    shadow-[2px_2px_0px_#cbc7b6] overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-texthint uppercase tracking-wider w-8">
                            Status
                        </th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-texthint uppercase tracking-wider">
                            Task Name
                        </th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-texthint uppercase tracking-wider">
                            Deadline
                        </th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-texthint uppercase tracking-wider">
                            Assignee
                        </th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-texthint uppercase tracking-wider">
                            Progress
                        </th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-texthint uppercase tracking-wider w-10">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-muted">
    @forelse($tasks as $task)
    <tr class="hover:bg-muted/50 transition-colors">

        {{-- Checkbox status --}}
        <td class="px-5 py-4">
            <div class="flex items-center gap-2">
                <input type="checkbox"
                    {{ $task->status === 'completed' ? 'checked' : '' }}
                    onchange="updateTaskStatus({{ $task->id }}, this.checked)"
                    class="w-4 h-4 rounded cursor-pointer"
                    style="accent-color: #b30084;">
                <span id="loading-{{ $task->id }}" class="hidden text-[10px] text-primary animate-pulse">Saving...</span>
            </div>
        </td>

        {{-- Nama Tugas + Badge Status --}}
        <td class="px-4 py-4">
            <p class="font-semibold text-textmain leading-tight">{{ $task->title }}</p>
            @if($task->description)
                <p class="text-xs text-textsoft mt-1 leading-relaxed">{{ $task->description }}</p>
            @endif
            <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                @php
                    $statusConfig = [
                        'pending'     => ['bg-yellow-100 text-yellow-700', 'Pending'],
                        'in_progress' => ['bg-blue-100 text-blue-700', 'In Progress'],
                        'completed'   => ['bg-green-100 text-green-700', 'Completed'],
                        'overdue'     => ['bg-red-100 text-red-600', 'Overdue'],
                    ];
                    $sc = $statusConfig[$task->status] ?? $statusConfig['pending'];
                @endphp
                <span id="badge-status-{{ $task->id }}" class="text-xs font-semibold px-2 py-0.5 rounded-md {{ $sc[0] }}">
                    {{ $sc[1] }}
                </span>
            </div>
        </td>

        {{-- Batas Waktu / Deadline --}}
        <td class="px-4 py-4">
            @php $isOverdue = $task->due_date && \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status !== 'completed'; @endphp
            <span class="flex items-center gap-1 text-sm {{ $isOverdue ? 'text-red-500 font-semibold' : 'text-textsoft' }}">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, H:i') : '—' }}
            </span>
            @if($isOverdue)
                <span class="text-xs text-red-400 block mt-0.5">⚠️ Overdue</span>
            @endif
        </td>

        {{-- Penerima Tugas / Assignee --}}
        <td class="px-4 py-4">
            <div class="flex items-center gap-2">
                @if($task->assignee)
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($task->assignee->name) }}&size=28&background=b30084&color=fff"
                         class="w-7 h-7 rounded-full border border-border object-cover"
                         title="{{ $task->assignee->name }}">
                    <span class="text-xs text-textmain font-medium">{{ $task->assignee->name }}</span>
                @else
                    <span class="text-xs text-texthint italic">Belum ada</span>
                @endif
            </div>
        </td>

        {{-- Progres Batang --}}
        <td class="px-4 py-4">
            @php
                $progress = ($task->status === 'completed') ? 100 : (($task->status === 'in_progress') ? 50 : 0);
            @endphp
            <div class="flex items-center gap-2">
                <div class="flex-1 bg-muted rounded-full h-2" style="min-width: 80px;">
                    {{-- 👇 TAMBAHKAN ID progress-bar DI SINI 👇 --}}
                    <div id="progress-bar-{{ $task->id }}" class="h-2 rounded-full transition-all"
                        style="width: {{ $progress }}%; background: {{ $progress >= 100 ? '#22C55E' : '#b30084' }};"></div>
                </div>
                {{-- 👇 TAMBAHKAN ID progress-text DI SINI 👇 --}}
                <span id="progress-text-{{ $task->id }}" class="text-xs font-semibold text-texthint w-8 text-right">
                    {{ $progress }}%
                </span>
            </div>
        </td>

        {{-- Aksi ke Detail --}}
        <td class="px-4 py-4">
            <a href="{{ route('workspaces.tasks.show', [$workspace->id, $task->id]) }}"
               class="w-8 h-8 flex items-center justify-center rounded-lg text-texthint hover:text-primary hover:bg-accent/40 transition-colors">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                </svg>
            </a>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="6" class="text-center py-12 text-texthint text-sm">
            Belum ada tugas. Tambahkan tugas pertama!
        </td>
    </tr>
    @endforelse
</tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         PANEL: LIST SCHEDULE
    ══════════════════════════════════════════ --}}
    <div id="panel-schedule" class="hidden">

        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-texthint">
                <span class="font-semibold text-textsoft">{{ $schedules->count() }}</span>
                agenda terjadwal
            </p>
            <button onclick="openAddScheduleModal()"
                class="flex items-center gap-2 text-sm font-semibold text-white
                       px-4 py-2 rounded-xl
                       bg-primary hover:bg-primary-dark
                       shadow-[3px_3px_0px_#6a1452]
                       active:translate-y-px active:shadow-[1px_1px_0_#6a1452]
                       transition-all">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Tambah Jadwal
            </button>
        </div>

        <div class="bg-surface rounded-2xl border border-border
                    shadow-[2px_2px_0px_#cbc7b6] overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-texthint uppercase tracking-wider w-4"></th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-texthint uppercase tracking-wider">Nama Agenda</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-texthint uppercase tracking-wider">Tanggal</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-texthint uppercase tracking-wider">Waktu</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-texthint uppercase tracking-wider">Tempat</th>
                    </tr>
                </thead>
               <tbody class="divide-y divide-muted">
                @forelse($schedules as $schedule)
                <tr class="hover:bg-muted/50 transition-colors">
                    <td class="px-5 py-4">
                        <span class="inline-block w-2 h-2 rounded-full bg-primary"></span>
                    </td>
                    
                    {{-- NAMA AGENDA --}}
                    <td class="px-4 py-4 font-semibold text-textmain">{{ $schedule->title }}</td>
                    
                    {{-- TANGGAL --}}
                    <td class="px-4 py-4 text-textsoft">
                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('d M Y') }}
                    </td>
                    
                    {{-- WAKTU --}}
                    <td class="px-4 py-4 text-textsoft">
                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                    </td>
                    
                    {{-- DESKRIPSI LOKASI --}}
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-accent text-primary">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
                            </svg>
                            {{ $schedule->description ?: 'Tidak ada' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-12 text-texthint text-sm">
                        Belum ada jadwal. Tambahkan agenda pertama!
                    </td>
                </tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         PANEL: RESOURCE & LINK
    ══════════════════════════════════════════ --}}
    <div id="panel-resource" class="hidden">

        <div class="flex items-center justify-between mb-5">
            <p class="text-sm text-texthint">
                <span class="font-semibold text-textsoft">{{ $resources->count() }}</span>
                resource tersimpan
            </p>
            <button onclick="openAddResourceModal()"
                class="flex items-center gap-2 text-sm font-semibold text-white
                       px-4 py-2 rounded-xl
                       bg-primary hover:bg-primary-dark
                       shadow-[3px_3px_0px_#6a1452]
                       active:translate-y-px active:shadow-[1px_1px_0_#6a1452]
                       transition-all">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Add Resource
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($resources as $resource)
            <div class="bg-surface rounded-2xl p-4
                        border border-border
                        shadow-[2px_2px_0px_#cbc7b6]
                        hover:shadow-[4px_4px_0px_#cbc7b6]
                        hover:-translate-y-0.5
                        transition-all group relative">

                {{-- Options kebab --}}
                <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="relative">
                        <button onclick="toggleResourceMenu({{ $resource->id }})"
                            class="w-7 h-7 rounded-full flex items-center justify-center
                                   text-texthint hover:bg-muted transition-colors">
                            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="5" r="1.5"/>
                                <circle cx="12" cy="12" r="1.5"/>
                                <circle cx="12" cy="19" r="1.5"/>
                            </svg>
                        </button>
                        <div id="resource-menu-{{ $resource->id }}"
                             class="hidden absolute right-0 top-8
                                    bg-surface rounded-xl border border-border
                                    shadow-[3px_3px_0px_#cbc7b6]
                                    py-1 z-10 w-32">
                            <button onclick="editResource({{ $resource->id }})"
                                class="w-full text-left px-3 py-2 text-xs text-textsoft hover:bg-muted transition-colors">
                                Edit
                            </button>
                            <form action="{{ route('workspaces.resources.destroy', [$workspace->id, $resource->id]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-full text-left px-3 py-2 text-xs text-red-500 hover:bg-red-50 transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <a href="{{ $resource->url }}" target="_blank" rel="noopener" class="block pr-8">
                    {{-- Icon container pakai warna accent brand --}}
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3 bg-accent">
                        @php
                            $icon = 'link';
                            if(str_contains($resource->url, 'drive.google'))  $icon = 'folder';
                            elseif(str_contains($resource->url, 'docs.google') || str_contains($resource->url, 'sheet')) $icon = 'doc';
                            elseif(str_contains($resource->url, 'figma'))     $icon = 'figma';
                        @endphp
                        @if($icon === 'folder')
                        <svg width="18" height="18" fill="none" stroke="#b30084" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                        </svg>
                        @elseif($icon === 'doc')
                        <svg width="18" height="18" fill="none" stroke="#b30084" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                        @else
                        <svg width="18" height="18" fill="none" stroke="#b30084" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/>
                            <path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/>
                        </svg>
                        @endif
                    </div>

                  <h3 class="font-semibold text-textmain text-sm mb-1 break-words">
                    {{ $resource->title }}
                </h3>
                <p class="text-xs text-texthint mb-3 leading-relaxed line-clamp-2">
                    {{ $resource->description }}
                </p>
                    <span class="flex items-center justify-center gap-1.5 w-full py-2
                                  rounded-xl text-xs font-semibold
                                  bg-accent text-primary
                                  hover:bg-primary hover:text-white
                                  transition-all">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                        Open Link
                    </span>
                </a>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <svg class="w-10 h-10 mx-auto mb-3 text-border" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/>
                    <path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/>
                </svg>
                <p class="text-sm text-texthint">
                    Belum ada resource. Tambahkan link atau dokumen penting!
                </p>
            </div>
            @endforelse
        </div>
    </div>

@endsection


{{-- ══════════════════════════════════════════════════════════════════
     MODALS
     Semua modal mengikuti design system layouts.app:
     bg-surface, border-border, shadow flat, tombol bg-primary
══════════════════════════════════════════════════════════════════ --}}

{{-- ════ MODAL: ADD TASK ════ --}}
<div id="modal-add-task" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div onclick="document.getElementById('modal-add-task').classList.add('hidden')" class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
    <div class="relative bg-surface rounded-2xl border border-border shadow-[6px_6px_0px_#cbc7b6] w-full max-w-md p-6 z-10">

        <div class="flex items-center justify-between mb-5">
            <h2 class="font-heading font-bold text-lg text-textmain">Tambah Tugas</h2>
            <button onclick="document.getElementById('modal-add-task').classList.add('hidden')" class="w-8 h-8 rounded-lg flex items-center justify-center text-texthint hover:text-textsoft hover:bg-muted transition-colors">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

       <form action="{{ route('workspaces.tasks.store', $workspace->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-textsoft mb-1.5">Judul Tugas</label>
                <input type="text" name="title" required placeholder="Misal: Revisi Desain"
                    class="w-full border border-border rounded-xl px-4 py-2.5 text-sm bg-white text-textmain focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-textsoft mb-1.5">Deskripsi Detail</label>
                <textarea name="description" rows="2" placeholder="Penjelasan tugas..."
                    class="w-full border border-border rounded-xl px-4 py-2 text-sm bg-white text-textmain focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-textsoft mb-1.5">Batas Waktu</label>
                    <input type="datetime-local" name="due_date"
                        class="w-full border border-border rounded-xl px-4 py-2.5 text-sm bg-white text-textmain focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-textsoft mb-1.5">Assign ke</label>
                    <select name="assignee_id"
                        class="w-full border border-border rounded-xl px-4 py-2.5 text-sm bg-white text-textmain focus:border-primary transition-all">
                        <option value="">— Pilih —</option>
                        @foreach($workspace->members as $member)
                            <option value="{{ $member->user->id }}">{{ $member->user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-3">
                <button type="button" onclick="document.getElementById('modal-add-task').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-textsoft bg-muted border border-border transition-all">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-primary hover:bg-primary-dark shadow-[4px_4px_0px_#6a1452] active:translate-y-px active:shadow-[2px_2px_0_#6a1452] transition-all">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════ MODAL: ADD SCHEDULE ════ --}}
<div id="modal-add-schedule"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div onclick="document.getElementById('modal-add-schedule').classList.add('hidden')"
         class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
    <div class="relative bg-surface rounded-2xl border border-border
                shadow-[6px_6px_0px_#cbc7b6]
                w-full max-w-md p-6 z-10">

        <div class="flex items-center justify-between mb-5">
            <h2 class="font-heading font-bold text-lg text-textmain">Tambah Jadwal</h2>
            <button onclick="document.getElementById('modal-add-schedule').classList.add('hidden')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center
                           text-texthint hover:text-textsoft hover:bg-muted transition-colors">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

<form action="{{ route('workspaces.schedules.store', $workspace->id) }}" method="POST" class="space-y-4">
    @csrf
    
    <div>
        <label class="block text-sm font-semibold text-textsoft mb-1.5">Nama Agenda</label>
        <input type="text" name="title" required
            class="w-full border border-border rounded-xl px-4 py-2.5 text-sm bg-white
                   text-textmain placeholder:text-texthint focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-semibold text-textsoft mb-1.5">Waktu Mulai</label>
            <input type="datetime-local" name="start_time" required
                class="w-full border border-border rounded-xl px-4 py-2.5 text-sm bg-white
                       text-textmain focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
        </div>
        <div>
            <label class="block text-sm font-semibold text-textsoft mb-1.5">Waktu Selesai</label>
            <input type="datetime-local" name="end_time" required
                class="w-full border border-border rounded-xl px-4 py-2.5 text-sm bg-white
                       text-textmain focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-textsoft mb-1.5">Lokasi / Keterangan</label>
        <input type="text" name="description" placeholder="e.g. Studio 4B, Google Meet..."
            class="w-full border border-border rounded-xl px-4 py-2.5 text-sm bg-white
                   text-textmain placeholder:text-texthint focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
    </div>

    <div class="flex gap-3 pt-1">
        <button type="button" onclick="document.getElementById('modal-add-schedule').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-textsoft bg-muted hover:bg-border border border-border transition-all">Batal</button>
        <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-primary hover:bg-primary-dark shadow-[4px_4px_0px_#6a1452] active:translate-y-px active:shadow-[2px_2px_0_#6a1452] transition-all">Simpan</button>
    </div>
</form>
    </div>
</div>

{{-- ════ MODAL: ADD RESOURCE ════ --}}
<div id="modal-add-resource"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div onclick="document.getElementById('modal-add-resource').classList.add('hidden')"
         class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
    <div class="relative bg-surface rounded-2xl border border-border
                shadow-[6px_6px_0px_#cbc7b6]
                w-full max-w-md p-6 z-10">

        <div class="flex items-center justify-between mb-5">
            <h2 class="font-heading font-bold text-lg text-textmain">Tambah Resource</h2>
            <button onclick="document.getElementById('modal-add-resource').classList.add('hidden')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center
                           text-texthint hover:text-textsoft hover:bg-muted transition-colors">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

       <form action="{{ route('workspaces.resources.store', $workspace->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-textsoft mb-1.5">Judul Resource</label>
                <input type="text" name="title" required placeholder="Misal: Referensi Ide"
                    class="w-full border border-border rounded-xl px-4 py-2.5 text-sm bg-white text-textmain placeholder:text-texthint focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-textsoft mb-1.5">URL / Link</label>
                <input type="url" name="url" required placeholder="https://..."
                    class="w-full border border-border rounded-xl px-4 py-2.5 text-sm bg-white
                           text-textmain placeholder:text-texthint
                           focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-textsoft mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3" placeholder="Jelaskan isi resource ini..."
                    class="w-full border border-border rounded-xl px-4 py-2.5 text-sm bg-white
                           text-textmain placeholder:text-texthint
                           focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20
                           resize-none transition-all"></textarea>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button"
                    onclick="document.getElementById('modal-add-resource').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold
                           text-textsoft bg-muted hover:bg-border border border-border transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white
                           bg-primary hover:bg-primary-dark
                           shadow-[4px_4px_0px_#6a1452]
                           active:translate-y-px active:shadow-[2px_2px_0_#6a1452]
                           transition-all">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════ MODAL: EDIT WORKSPACE ════ --}}
<div id="modal-edit-workspace"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div onclick="document.getElementById('modal-edit-workspace').classList.add('hidden')"
         class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
    <div class="relative bg-surface rounded-2xl border border-border
                shadow-[6px_6px_0px_#cbc7b6]
                w-full max-w-md p-6 z-10">

        <div class="flex items-center justify-between mb-5">
            <h2 class="font-heading font-bold text-lg text-textmain">Edit Workspace</h2>
            <button onclick="document.getElementById('modal-edit-workspace').classList.add('hidden')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center
                           text-texthint hover:text-textsoft hover:bg-muted transition-colors">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="edit-workspace-form" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-semibold text-textsoft mb-1.5">Nama Workspace</label>
                <input type="text" name="name" id="edit-ws-name" required
                    class="w-full border border-border rounded-xl px-4 py-2.5 text-sm bg-white
                           text-textmain focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-textsoft mb-1.5">Deskripsi</label>
                <input type="text" name="description" id="edit-ws-desc"
                    class="w-full border border-border rounded-xl px-4 py-2.5 text-sm bg-white
                           text-textmain focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-textsoft mb-1.5">Cover Image</label>
                <input type="url" name="cover_image" id="edit-ws-cover"
                    class="w-full border border-border rounded-xl px-4 py-2.5 text-sm bg-white
                           text-textmain focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all mb-2">
                {{-- Unsplash search inline --}}
                <div class="flex gap-2">
                    <input type="text" id="unsplash-query-edit" placeholder="Cari foto..."
                        class="flex-1 border border-border rounded-xl px-3 py-2 text-sm bg-white
                               text-textmain placeholder:text-texthint
                               focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    <button type="button" onclick="searchUnsplashEdit()"
                        class="px-3 py-2 rounded-xl text-sm font-semibold text-white
                               bg-primary hover:bg-primary-dark
                               shadow-[3px_3px_0px_#6a1452]
                               active:translate-y-px active:shadow-[1px_1px_0_#6a1452]
                               transition-all">
                        Cari
                    </button>
                </div>
                <div id="unsplash-results-edit"
                     class="grid grid-cols-3 gap-2 max-h-40 overflow-y-auto hidden mt-2"></div>
                <div id="unsplash-loading-edit" class="hidden text-center py-2 mt-2">
                    <div class="inline-block w-5 h-5 border-2 border-accent border-t-primary rounded-full animate-spin"></div>
                </div>
                <div id="edit-cover-preview" class="hidden mt-2 rounded-xl overflow-hidden border border-border" style="height: 80px;">
                    <img id="edit-cover-preview-img" src="" alt="" class="w-full h-full object-cover">
                </div>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button"
                    onclick="document.getElementById('modal-edit-workspace').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold
                           text-textsoft bg-muted hover:bg-border border border-border transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white
                           bg-primary hover:bg-primary-dark
                           shadow-[4px_4px_0px_#6a1452]
                           active:translate-y-px active:shadow-[2px_2px_0_#6a1452]
                           transition-all">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════ MODAL: INVITE MEMBER ════ --}}
<div id="modal-invite"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div onclick="closeInviteModal()"
         class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
    <div class="relative bg-surface rounded-2xl border border-border
                shadow-[6px_6px_0px_#cbc7b6]
                w-full max-w-md p-6 z-10">

        <div class="flex items-center justify-between mb-5">
            <h2 class="font-heading font-bold text-lg text-textmain">Undang Anggota</h2>
            <button onclick="closeInviteModal()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center
                           text-texthint hover:text-textsoft hover:bg-muted transition-colors">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="invite-form" method="POST" action="#" class="space-y-4">
            @csrf
            <div>
                <label for="invite-email" class="block text-sm font-semibold text-textsoft mb-1.5">
                    Alamat Email
                </label>
                <input type="email" id="invite-email" name="email"
                    placeholder="e.g., nama@email.com" required
                    class="w-full border border-border rounded-xl px-4 py-2.5 text-sm bg-white
                           text-textmain placeholder:text-texthint
                           focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeInviteModal()"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold
                           text-textsoft bg-muted hover:bg-border border border-border transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white
                           bg-primary hover:bg-primary-dark
                           shadow-[4px_4px_0px_#6a1452]
                           active:translate-y-px active:shadow-[2px_2px_0_#6a1452]
                           transition-all">
                    Kirim Undangan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>

    // ─── AUTO CLOSE ALERT NOTIFICATION ────────────────────────────────────────
    document.addEventListener("DOMContentLoaded", function() {
        // Cari elemen notifikasi sukses atau error
        const flashSuccess = document.getElementById('flash-success');
        const flashError = document.getElementById('flash-error');

        // Fungsi untuk menghilangkan elemen setelah 3 detik (3000 milidetik)
        function fadeOutAlert(element) {
            if (element) {
                setTimeout(() => {
                    element.classList.remove('opacity-100');
                    element.classList.add('opacity-0'); // Memudarkan
                    
                    // Hapus elemen dari HTML setelah animasi pudar selesai (0.5 detik)
                    setTimeout(() => element.remove(), 500); 
                }, 3000); 
            }
        }

        fadeOutAlert(flashSuccess);
        fadeOutAlert(flashError);
    });
    // ─── TAB SWITCHING ─────────────────────────────────────────────────────────
    // Menggunakan warna primary brand (#b30084) sesuai tailwind.config layouts.app
    function switchTab(active) {
        ['task', 'schedule', 'resource'].forEach(tab => {
            const panel = document.getElementById('panel-' + tab);
            const btn   = document.getElementById('tab-' + tab);
            const isActive = tab === active;

            panel.classList.toggle('hidden', !isActive);

            if (isActive) {
                btn.classList.add('border-primary', 'text-primary');
                btn.classList.remove('border-transparent', 'text-texthint', 'hover:text-textsoft');
            } else {
                btn.classList.remove('border-primary', 'text-primary');
                btn.classList.add('border-transparent', 'text-texthint', 'hover:text-textsoft');
            }
        });
    }

    // ─── MODAL OPENERS ─────────────────────────────────────────────────────────
    function openAddTaskModal()     { document.getElementById('modal-add-task').classList.remove('hidden'); }
    function openAddScheduleModal() { document.getElementById('modal-add-schedule').classList.remove('hidden'); }
    function openAddResourceModal() { document.getElementById('modal-add-resource').classList.remove('hidden'); }

    // ─── INVITE MODAL ──────────────────────────────────────────────────────────
    function openInviteModal(workspaceId) {
        let id = workspaceId;
        if (!id) {
            const parts = window.location.pathname.split('/').filter(Boolean);
            const idx   = parts.indexOf('workspaces');
            if (idx !== -1 && parts.length > idx + 1) id = parts[idx + 1];
        }
        if (!id) return alert('Workspace ID tidak ditemukan.');
        document.getElementById('invite-form').action = `/workspaces/${id}/members`;
        document.getElementById('modal-invite').classList.remove('hidden');
    }

    function closeInviteModal() {
        document.getElementById('modal-invite').classList.add('hidden');
        document.getElementById('invite-form').action = '#';
        document.getElementById('invite-form').reset();
    }

   // ─── SMART CHECKBOX (AJAX SINKRONISASI BACKEND) ───────────────────────────
    function updateTaskStatus(taskId, isChecked) {
        const loadingText = document.getElementById(`loading-${taskId}`);
        if (loadingText) loadingText.classList.remove('hidden');

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

        // Konversi boolean checkbox menjadi string status yang divalidasi controller
        const statusString = isChecked ? 'completed' : 'pending';

        fetch(`/workspaces/tasks/${taskId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: statusString })
        })
        .then(response => {
            if (!response.ok) throw new Error('Validasi server gagal atau rute salah.');
            return response.json();
        })
        .then(data => {
            if (loadingText) loadingText.classList.add('hidden');
            if (data.success) {
                // Reload halaman untuk memperbarui status badge dan progres secara otomatis
                window.location.reload();
            } else {
                alert('Gagal memperbarui status tugas.');
            }
        })
        .catch(error => {
            console.error('AJAX Error:', error);
            if (loadingText) loadingText.classList.add('hidden');
            alert('Terjadi kesalahan server saat menyimpan status.');
        });
    }

    // ─── RESOURCE KEBAB MENU ───────────────────────────────────────────────────
    function toggleResourceMenu(id) {
        const menu = document.getElementById('resource-menu-' + id);
        document.querySelectorAll('[id^="resource-menu-"]')
            .forEach(m => { if (m.id !== 'resource-menu-' + id) m.classList.add('hidden'); });
        menu.classList.toggle('hidden');
    }
    document.addEventListener('click', e => {
        if (!e.target.closest('[onclick^="toggleResourceMenu"]')) {
            document.querySelectorAll('[id^="resource-menu-"]')
                .forEach(m => m.classList.add('hidden'));
        }
    });

    // ─── UNSPLASH SEARCH (EDIT MODAL) ─────────────────────────────────────────
    async function searchUnsplashEdit() {
        const query   = document.getElementById('unsplash-query-edit').value.trim();
        if (!query) return;

        const results = document.getElementById('unsplash-results-edit');
        const loading = document.getElementById('unsplash-loading-edit');

        results.classList.add('hidden');
        loading.classList.remove('hidden');

        try {
            const res = await fetch(`/api/unsplash/search?query=${encodeURIComponent(query)}`);
            loading.classList.add('hidden');
            results.innerHTML = '';

            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                results.innerHTML = `<p class="col-span-3 text-xs text-center py-2" style="color:#b30084;">Gagal: ${err.error || res.statusText}</p>`;
                results.classList.remove('hidden');
                return;
            }

            const data   = await res.json();
            const photos = Array.isArray(data.results) ? data.results : [];

            if (!photos.length) {
                results.innerHTML = '<p class="col-span-3 text-xs text-center py-2 text-texthint">Tidak ditemukan.</p>';
                results.classList.remove('hidden');
                return;
            }

            photos.forEach(photo => {
                const img     = document.createElement('img');
                img.src       = photo.urls.small;
                img.alt       = photo.alt_description ?? query;
                img.className = 'w-full rounded-lg cursor-pointer object-cover border-2 border-transparent transition-all hover:border-primary hover:opacity-90';
                img.style.height = '64px';
                img.onclick   = () => selectCoverForEdit(photo.urls.regular, img);
                results.appendChild(img);
            });

            results.classList.remove('hidden');
        } catch (e) {
            loading.classList.add('hidden');
            results.innerHTML = '<p class="col-span-3 text-xs text-center py-2" style="color:#b30084;">Gagal memuat foto.</p>';
            results.classList.remove('hidden');
        }
    }

    function selectCoverForEdit(url, imgEl) {
        document.getElementById('edit-ws-cover').value = url;
        document.querySelectorAll('#unsplash-results-edit img')
            .forEach(i => i.classList.remove('border-primary'));
        imgEl.classList.add('border-primary');
        document.getElementById('edit-cover-preview-img').src = url;
        document.getElementById('edit-cover-preview').classList.remove('hidden');
    }

    // ─── EDIT WORKSPACE: pre-fill form ────────────────────────────────────────
    function openEditWorkspaceModal() {
        const ws = @json($workspace);

        document.getElementById('edit-ws-name').value  = ws.name         || '';
        document.getElementById('edit-ws-desc').value  = ws.description  || '';
        document.getElementById('edit-ws-cover').value = ws.cover_image  || '';
        document.getElementById('edit-workspace-form').action = `/workspaces/${ws.id}`;

        if (ws.cover_image) {
            document.getElementById('edit-cover-preview-img').src = ws.cover_image;
            document.getElementById('edit-cover-preview').classList.remove('hidden');
        } else {
            document.getElementById('edit-cover-preview').classList.add('hidden');
        }

        document.getElementById('unsplash-results-edit').innerHTML = '';
        document.getElementById('unsplash-results-edit').classList.add('hidden');
        document.getElementById('unsplash-query-edit').value = '';
        document.getElementById('modal-edit-workspace').classList.remove('hidden');
    }
</script>
@endpush