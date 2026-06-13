@extends('layouts.dashboard')
@section('title', 'Personal List')

@section('content')
<div class="min-h-screen p-8" style="background:#F7F5F0;">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="font-display text-3xl font-bold" style="color:#1A1A2E;">Personal List</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola tugas pribadi dan jadwal milik akun kamu.</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('google.calendar.redirect') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-white border border-gray-200 text-gray-700 hover:bg-gray-50">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                {{ auth()->user()->hasGoogleCalendarConnected() ? 'Google Connected' : 'Connect Google' }}
            </a>
            <button onclick="openModal('task-create-modal')" class="btn-primary">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                New Task
            </button>
            <button onclick="openModal('schedule-create-modal')" class="btn-primary">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                New Schedule
            </button>
        </div>
    </div>

    @foreach(['success' => 'green', 'warning' => 'yellow', 'error' => 'red'] as $key => $color)
        @if(session($key))
            <div class="mb-4 rounded-xl bg-{{ $color }}-50 border border-{{ $color }}-100 px-4 py-3 text-sm text-{{ $color }}-700">
                {{ session($key) }}
            </div>
        @endif
    @endforeach

    @if($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-5">
        <section class="xl:col-span-3 bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-display font-bold text-lg" style="color:#1A1A2E;">Personal Tasks</h2>
                    <p class="text-xs text-gray-400">{{ $tasks->count() }} task tersimpan</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase w-10">Done</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Task</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Deadline</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Progress</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($tasks as $task)
                            <tr id="task-row-{{ $task->id }}" class="hover:bg-gray-50/70">
                                <td class="px-5 py-4">
                                    <input type="checkbox"
                                           class="task-checkbox w-4 h-4 rounded cursor-pointer accent-pink-600"
                                           data-task-id="{{ $task->id }}"
                                           @checked($task->status === 'completed')>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="task-title font-semibold text-gray-800 {{ $task->status === 'completed' ? 'line-through text-gray-400' : '' }}">{{ $task->title }}</p>
                                    @if($task->description)
                                        <p class="text-xs text-gray-400 mt-1 max-w-md">{{ $task->description }}</p>
                                    @endif
                                    <span id="task-status-{{ $task->id }}" class="inline-flex mt-2 text-xs font-semibold px-2 py-0.5 rounded-md {{ $task->status === 'completed' ? 'bg-green-100 text-green-700' : ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">
                                        {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-gray-600">
                                    {{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2 min-w-32">
                                        <div class="w-24 bg-gray-100 rounded-full h-2">
                                            <div id="task-progress-bar-{{ $task->id }}" class="h-2 rounded-full" style="width:{{ $task->progress }}%; background:{{ $task->progress >= 100 ? '#22C55E' : '#C8216B' }};"></div>
                                        </div>
                                        <span id="task-progress-text-{{ $task->id }}" class="text-xs font-semibold text-gray-500 w-9 text-right">{{ $task->progress }}%</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button onclick="openModal('task-edit-modal-{{ $task->id }}')" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-pink-50 text-gray-400 hover:text-pink-600">
                                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                        </button>
                                        <form action="{{ route('personal.tasks.destroy', $task) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-red-50 text-gray-400 hover:text-red-500">
                                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-12 text-gray-400">Belum ada personal task.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="xl:col-span-2 bg-white rounded-2xl shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-display font-bold text-lg" style="color:#1A1A2E;">Calendar</h2>
                    <p class="text-xs text-gray-400">{{ $schedules->count() }} schedule tersimpan</p>
                </div>
                <button onclick="openModal('schedule-create-modal')" class="btn-primary px-3 py-2">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    New
                </button>
            </div>
            <div id="personal-calendar"></div>
        </section>
    </div>

    <section class="mt-5 bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="font-display font-bold text-lg" style="color:#1A1A2E;">Personal Schedules</h2>
            <button onclick="openModal('schedule-create-modal')" class="btn-primary">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                New Schedule
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Title</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Date</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Time</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Location</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($schedules as $schedule)
                        <tr class="hover:bg-gray-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-800">{{ $schedule->title }}</p>
                                @if($schedule->google_calendar_html_link)
                                    <a href="{{ $schedule->google_calendar_html_link }}" target="_blank" class="text-xs text-pink-600 hover:underline">Open in Google Calendar</a>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-gray-600">{{ $schedule->date->format('d M Y') }}</td>
                            <td class="px-4 py-4 text-gray-600">{{ $schedule->time }}{{ $schedule->end_time ? ' - '.$schedule->end_time : '' }}</td>
                            <td class="px-4 py-4 text-gray-600">{{ $schedule->location ?: '-' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button onclick="openModal('schedule-edit-modal-{{ $schedule->id }}')" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-pink-50 text-gray-400 hover:text-pink-600">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                    </button>
                                    <form action="{{ route('personal.schedules.destroy', $schedule) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-red-50 text-gray-400 hover:text-red-500">
                                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-12 text-gray-400">Belum ada personal schedule.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

@push('modals')
<x-personal-task-modal id="task-create-modal" title="New Personal Task" action="{{ route('personal.tasks.store') }}" />

@foreach($tasks as $task)
    <x-personal-task-modal id="task-edit-modal-{{ $task->id }}" title="Edit Personal Task" action="{{ route('personal.tasks.update', $task) }}" method="PUT" :task="$task" />
@endforeach

<x-personal-schedule-modal id="schedule-create-modal" title="New Personal Schedule" action="{{ route('personal.schedules.store') }}" />

@foreach($schedules as $schedule)
    <x-personal-schedule-modal id="schedule-edit-modal-{{ $schedule->id }}" title="Edit Personal Schedule" action="{{ route('personal.schedules.update', $schedule) }}" method="PUT" :schedule="$schedule" />
@endforeach
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
    function openModal(id) {
        document.getElementById(id)?.classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id)?.classList.add('hidden');
    }

    function openNewModal() {
        openModal('schedule-create-modal');
    }

    document.querySelectorAll('.task-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', async event => {
            const input = event.currentTarget;
            const taskId = input.dataset.taskId;
            const previous = !input.checked;
            input.disabled = true;

            try {
                const response = await fetch(`/personal/tasks/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ completed: input.checked })
                });

                if (!response.ok) {
                    throw new Error('Request failed');
                }

                const data = await response.json();
                const status = document.getElementById(`task-status-${taskId}`);
                const progressBar = document.getElementById(`task-progress-bar-${taskId}`);
                const progressText = document.getElementById(`task-progress-text-${taskId}`);
                const title = document.querySelector(`#task-row-${taskId} .task-title`);

                status.textContent = data.status.replace('_', ' ');
                status.className = data.status === 'completed'
                    ? 'inline-flex mt-2 text-xs font-semibold px-2 py-0.5 rounded-md bg-green-100 text-green-700'
                    : 'inline-flex mt-2 text-xs font-semibold px-2 py-0.5 rounded-md bg-yellow-100 text-yellow-700';
                progressBar.style.width = `${data.progress}%`;
                progressBar.style.background = data.progress >= 100 ? '#22C55E' : '#C8216B';
                progressText.textContent = `${data.progress}%`;
                title.classList.toggle('line-through', data.status === 'completed');
                title.classList.toggle('text-gray-400', data.status === 'completed');
            } catch (error) {
                input.checked = previous;
                alert('Gagal memperbarui status task.');
            } finally {
                input.disabled = false;
            }
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const calendarEl = document.getElementById('personal-calendar');

        if (!calendarEl || !window.FullCalendar) {
            return;
        }

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 520,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            events: '{{ route('personal.schedules.events') }}',
            eventColor: '#C8216B',
            eventDisplay: 'block'
        });

        calendar.render();
    });
</script>
@endpush
@endsection
