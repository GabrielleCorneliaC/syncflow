@extends('layouts.dashboard')
@section('title', $workspace->name)

@section('content')
<div class="min-h-screen" style="background: #F7F5F0;">

    {{-- ===== HERO HEADER with cover image ===== --}}
    <div class="relative overflow-hidden" style="height: 160px;">
        @if($workspace->cover_image)
            <img src="{{ $workspace->cover_image }}" alt="" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full" style="background: linear-gradient(135deg, #F9D6E7 0%, #C8216B 100%);"></div>
        @endif
        <div class="absolute inset-0" style="background: linear-gradient(to right, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.15) 100%);"></div>

        {{-- Back + Title --}}
        <div class="absolute inset-0 flex items-center px-8">
            <div>
                <a href="{{ route('workspaces.index') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-medium text-white/80 hover:text-white mb-2 transition-colors">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                    Back to Workspaces
                </a>
                <h1 class="font-display text-2xl font-bold text-white">{{ $workspace->name }}</h1>
            </div>
        </div>

        {{-- Admin actions (only visible to owner) --}}
        @if(auth()->id() === $workspace->owner_id)
        <div class="absolute top-4 right-6 flex gap-2">
            <button onclick="openInviteModal()"
                class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg transition-all"
                style="background: rgba(255,255,255,0.9); color: #C8216B;">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                Invite Member
            </button>
        </div>
        @endif
    </div>

    {{-- ===== TABS ===== --}}
    <div class="px-8 pt-5 pb-0">
        <div class="flex gap-1 border-b border-gray-200">
            @foreach([['task','list-task','List Task'],['schedule','list-schedule','List Schedule'],['resource','resource-link','Resource & Link']] as [$key,$icon,$label])
            <button onclick="switchTab('{{ $key }}')" id="tab-{{ $key }}"
                class="ws-tab flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 transition-all duration-200
                       {{ $key === 'task' ? 'border-pink-600 text-pink-600' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                @if($key === 'task')
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
                @elseif($key === 'schedule')
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                @else
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                @endif
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- ===== PANEL: LIST TASK ===== --}}
    <div id="panel-task" class="px-8 py-6">
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-gray-500">Menampilkan <span class="font-semibold text-gray-700">{{ $tasks->count() }}</span> tugas</p>
            @if(auth()->id() === $workspace->owner_id)
            <button onclick="openAddTaskModal()"
                class="flex items-center gap-2 text-sm font-semibold text-white px-4 py-2 rounded-xl transition-all hover:opacity-90"
                style="background: #C8216B;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Tugas
            </button>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid #F3F4F6;">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider w-8">Status</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Task Name</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Deadline</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Assignee</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Progress</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider w-10">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tasks as $task)
                    <tr class="hover:bg-gray-50/70 transition-colors">
                        {{-- Smart Checkbox --}}
                        <td class="px-5 py-4">
                            <input type="checkbox"
                                {{ $task->status === 'done' ? 'checked' : '' }}
                                onchange="updateTaskStatus({{ $task->id }}, this.checked)"
                                class="w-4 h-4 rounded cursor-pointer accent-pink-600">
                        </td>

                        {{-- Task Name + badges --}}
                        <td class="px-4 py-4">
                            <p class="font-semibold text-gray-800 leading-tight">{{ $task->name }}</p>
                            <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                @php
                                    $statusConfig = [
                                        'todo'    => ['bg-gray-100 text-gray-600', 'To Do'],
                                        'pending' => ['bg-yellow-100 text-yellow-700', 'Pending'],
                                        'review'  => ['bg-blue-100 text-blue-700', 'Review'],
                                        'done'    => ['bg-green-100 text-green-700', 'Done'],
                                        'overdue' => ['bg-red-100 text-red-600', 'Overdue'],
                                    ];
                                    $sc = $statusConfig[$task->status] ?? $statusConfig['todo'];
                                @endphp
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-md {{ $sc[0] }}">{{ $sc[1] }}</span>
                                @if($task->attachments_count > 0)
                                <span class="text-xs text-gray-400 flex items-center gap-1">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                                    {{ $task->attachments_count }} Files
                                </span>
                                @endif
                            </div>
                        </td>

                        {{-- Deadline --}}
                        <td class="px-4 py-4">
                            @php $isOverdue = $task->deadline && $task->deadline->isPast() && $task->status !== 'done'; @endphp
                            <span class="flex items-center gap-1 text-sm {{ $isOverdue ? 'text-red-500 font-semibold' : 'text-gray-600' }}">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                {{ $task->deadline ? $task->deadline->format('M d') : '—' }}
                            </span>
                            @if($isOverdue)<span class="text-xs text-red-400">⚠ Overdue</span>@endif
                        </td>

                        {{-- Assignees --}}
                        <td class="px-4 py-4">
                            <div class="flex items-center -space-x-1.5">
                                @foreach($task->assignees->take(3) as $a)
                                <img src="{{ $a->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($a->name).'&size=28&background=C8216B&color=fff' }}"
                                     class="w-7 h-7 rounded-full border-2 border-white object-cover" title="{{ $a->name }}">
                                @endforeach
                            </div>
                        </td>

                        {{-- Progress bar --}}
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-100 rounded-full h-2" style="min-width: 80px;">
                                    <div class="h-2 rounded-full transition-all"
                                         style="width: {{ $task->progress }}%; background: {{ $task->progress >= 100 ? '#22C55E' : '#C8216B' }};">
                                    </div>
                                </div>
                                <span class="text-xs font-semibold text-gray-500 w-8 text-right">{{ $task->progress }}%</span>
                            </div>
                        </td>

                        {{-- Comment icon --}}
                        <td class="px-4 py-4">
                            <a href="{{ route('workspaces.tasks.show', [$workspace->id, $task->id]) }}"
                               class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-pink-50 text-gray-300 hover:text-pink-500 transition-colors">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-12 text-gray-400 text-sm">Belum ada tugas. Tambahkan tugas pertama!</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($tasks->hasPages())
            <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
                <span class="text-xs text-gray-400">Showing {{ $tasks->firstItem() }} to {{ $tasks->lastItem() }} of {{ $tasks->total() }} tasks</span>
                <div class="flex gap-1">
                    {{ $tasks->links('vendor.pagination.simple-tailwind') }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ===== PANEL: LIST SCHEDULE ===== --}}
    <div id="panel-schedule" class="hidden px-8 py-6">
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-gray-500">{{ $schedules->count() }} agenda terjadwal</p>
            <button onclick="openAddScheduleModal()"
                class="flex items-center gap-2 text-sm font-semibold text-white px-4 py-2 rounded-xl transition-all hover:opacity-90"
                style="background: #C8216B;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Jadwal
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid #F3F4F6;">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider w-4"></th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Agenda</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Waktu</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tempat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($schedules as $schedule)
                    <tr class="hover:bg-gray-50/70 transition-colors">
                        <td class="px-5 py-4">
                            <span class="inline-block w-2 h-2 rounded-full" style="background: #C8216B;"></span>
                        </td>
                        <td class="px-4 py-4 font-semibold text-gray-800">{{ $schedule->name }}</td>
                        <td class="px-4 py-4 text-gray-600">{{ $schedule->date->format('d M Y') }}</td>
                        <td class="px-4 py-4 text-gray-600">{{ $schedule->time }} – {{ $schedule->end_time ?? 'Selesai' }}</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full"
                                  style="background: #FFF0F6; color: #C8216B;">
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ $schedule->location }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-12 text-gray-400 text-sm">Belum ada jadwal. Tambahkan agenda!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== PANEL: RESOURCE & LINK ===== --}}
    <div id="panel-resource" class="hidden px-8 py-6">
        <div class="flex items-center justify-between mb-5">
            <p class="text-sm text-gray-500">{{ $resources->count() }} resource tersimpan</p>
            <button onclick="openAddResourceModal()"
                class="flex items-center gap-2 text-sm font-semibold text-white px-4 py-2 rounded-xl transition-all hover:opacity-90"
                style="background: #C8216B;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Add New Resource
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($resources as $resource)
            <div class="bg-white rounded-2xl p-4 shadow-sm hover:shadow-md transition-all group relative"
                 style="border: 1px solid #F3F4F6;">
                {{-- Options button --}}
                <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="relative">
                        <button onclick="toggleResourceMenu({{ $resource->id }})"
                            class="w-7 h-7 rounded-full flex items-center justify-center hover:bg-gray-100 text-gray-400">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                        </button>
                        <div id="resource-menu-{{ $resource->id }}"
                             class="hidden absolute right-0 top-8 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-10 w-32">
                            <button onclick="editResource({{ $resource->id }})"
                                class="w-full text-left px-3 py-2 text-xs text-gray-600 hover:bg-gray-50">Edit</button>
                            <form action="{{ route('workspaces.resources.destroy', [$workspace->id, $resource->id]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full text-left px-3 py-2 text-xs text-red-500 hover:bg-red-50">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Icon --}}
                <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3"
                     style="background: #FFF0F6;">
                    @php
                        $icon = 'link';
                        if(str_contains($resource->url, 'drive.google')) $icon = 'folder';
                        elseif(str_contains($resource->url, 'docs.google') || str_contains($resource->url, 'sheet')) $icon = 'doc';
                        elseif(str_contains($resource->url, 'figma')) $icon = 'figma';
                    @endphp
                    @if($icon === 'folder')
                    <svg width="18" height="18" fill="none" stroke="#C8216B" stroke-width="2" viewBox="0 0 24 24"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
                    @elseif($icon === 'doc')
                    <svg width="18" height="18" fill="none" stroke="#C8216B" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    @else
                    <svg width="18" height="18" fill="none" stroke="#C8216B" stroke-width="2" viewBox="0 0 24 24"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                    @endif
                </div>

                <h3 class="font-semibold text-gray-800 text-sm mb-1">{{ $resource->url | basename | split('.')[0] ?? $resource->url }}</h3>
                <p class="text-xs text-gray-400 mb-3 leading-relaxed line-clamp-2">{{ $resource->description }}</p>

                <a href="{{ $resource->url }}" target="_blank" rel="noopener"
                   class="flex items-center justify-center gap-1.5 w-full py-2 rounded-xl text-xs font-semibold transition-all hover:opacity-90"
                   style="background: #FFF0F6; color: #C8216B;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    Open Link
                </a>
            </div>
            @empty
            <div class="col-span-full text-center py-12 text-gray-400 text-sm">
                Belum ada resource. Tambahkan link atau dokumen penting!
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ===== MODALS ===== --}}
@push('modals')

{{-- ADD TASK MODAL --}}
<div id="modal-add-task" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div onclick="document.getElementById('modal-add-task').classList.add('hidden')" class="absolute inset-0" style="background:rgba(0,0,0,0.4);"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-display font-bold text-lg" style="color:#1A1A2E;">Tambah Tugas Kelompok</h2>
            <button onclick="document.getElementById('modal-add-task').classList.add('hidden')" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 text-gray-400">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('workspaces.tasks.store', $workspace->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Tugas</label>
                <input type="text" name="name" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deadline</label>
                    <input type="date" name="deadline" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                    <select name="status" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 bg-white">
                        <option value="todo">To Do</option>
                        <option value="pending">Pending</option>
                        <option value="review">Review</option>
                        <option value="done">Done</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Assign ke</label>
                <select name="assignee_id" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 bg-white">
                    <option value="">— Pilih anggota —</option>
                    @foreach($workspace->members as $member)
                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Progress (%)</label>
                <input type="number" name="progress" min="0" max="100" value="0"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('modal-add-task').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all">Batal</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90" style="background:#C8216B;">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ADD SCHEDULE MODAL --}}
<div id="modal-add-schedule" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div onclick="document.getElementById('modal-add-schedule').classList.add('hidden')" class="absolute inset-0" style="background:rgba(0,0,0,0.4);"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-display font-bold text-lg" style="color:#1A1A2E;">Tambah Jadwal</h2>
            <button onclick="document.getElementById('modal-add-schedule').classList.add('hidden')" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 text-gray-400">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('workspaces.schedules.store', $workspace->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Agenda</label>
                <input type="text" name="name" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal</label>
                    <input type="date" name="date" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Waktu</label>
                    <input type="time" name="time" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lokasi</label>
                <input type="text" name="location" placeholder="e.g. Studio 4B, Google Meet..."
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('modal-add-schedule').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all">Batal</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90" style="background:#C8216B;">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ADD RESOURCE MODAL --}}
<div id="modal-add-resource" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div onclick="document.getElementById('modal-add-resource').classList.add('hidden')" class="absolute inset-0" style="background:rgba(0,0,0,0.4);"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-display font-bold text-lg" style="color:#1A1A2E;">Tambah Resource</h2>
            <button onclick="document.getElementById('modal-add-resource').classList.add('hidden')" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 text-gray-400">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('workspaces.resources.store', $workspace->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">URL / Link</label>
                <input type="url" name="url" required placeholder="https://..."
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3" placeholder="Jelaskan isi resource ini..."
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100 resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('modal-add-resource').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all">Batal</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90" style="background:#C8216B;">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endpush

@push('scripts')
<script>
    // ─── TAB SWITCHING ────────────────────────────────────────────────
    function switchTab(active) {
        ['task','schedule','resource'].forEach(tab => {
            const panel = document.getElementById('panel-'+tab);
            const btn   = document.getElementById('tab-'+tab);
            if (tab === active) {
                panel.classList.remove('hidden');
                btn.classList.add('border-pink-600','text-pink-600');
                btn.classList.remove('border-transparent','text-gray-400');
            } else {
                panel.classList.add('hidden');
                btn.classList.remove('border-pink-600','text-pink-600');
                btn.classList.add('border-transparent','text-gray-400');
            }
        });
    }

    // ─── MODAL HELPERS ────────────────────────────────────────────────
    function openAddTaskModal()     { document.getElementById('modal-add-task').classList.remove('hidden'); }
    function openAddScheduleModal() { document.getElementById('modal-add-schedule').classList.remove('hidden'); }
    function openAddResourceModal() { document.getElementById('modal-add-resource').classList.remove('hidden'); }

    // ─── SMART CHECKBOX (AJAX) ────────────────────────────────────────
    async function updateTaskStatus(taskId, isDone) {
        const status = isDone ? 'done' : 'todo';
        await fetch(`/workspaces/tasks/${taskId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status })
        });
        // Reload to reflect changes
        window.location.reload();
    }

    // ─── RESOURCE MENU TOGGLE ─────────────────────────────────────────
    function toggleResourceMenu(id) {
        const menu = document.getElementById('resource-menu-'+id);
        // Close all others first
        document.querySelectorAll('[id^="resource-menu-"]').forEach(m => { if(m.id !== 'resource-menu-'+id) m.classList.add('hidden'); });
        menu.classList.toggle('hidden');
    }
    // Close menus on outside click
    document.addEventListener('click', e => {
        if (!e.target.closest('[onclick^="toggleResourceMenu"]')) {
            document.querySelectorAll('[id^="resource-menu-"]').forEach(m => m.classList.add('hidden'));
        }
    });
</script>
@endpush
@endsection