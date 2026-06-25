@props([
    'id',
    'title',
    'action',
    'method' => 'POST',
    'task' => null,
])

<div id="{{ $id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div onclick="closeModal('{{ $id }}')" class="absolute inset-0" style="background:rgba(0,0,0,0.4);"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-display font-bold text-lg" style="color:#1A1A2E;">{{ $title }}</h2>
            <button type="button" onclick="closeModal('{{ $id }}')" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 text-gray-400">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ $action }}" method="POST" class="space-y-4">
            @csrf
            @if($method !== 'POST')
                @method($method)
            @endif

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Task Name</label>
                <input type="text" name="title" required value="{{ old('title', $task?->title) }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100 resize-none">{{ old('description', $task?->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deadline</label>
                    <input type="date" name="due_date" value="{{ old('deadline',$task?->due_date) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Prioritas</label>
                    <select name="priority" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 bg-white">
                        <option value="low" @selected(old('priority', $task?->priority ?? 'medium') === 'low')>Low</option>
                        <option value="medium" @selected(old('priority', $task?->priority ?? 'medium') === 'medium')>Medium</option>
                        <option value="high" @selected(old('priority', $task?->priority ?? 'medium') === 'high')>High</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Progress (%)</label>
                    <input type="number" name="progress" min="0" max="100" value="{{ old('progress', $task?->progress ?? 0) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                    <select name="status" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 bg-white">
                        <option value="todo" @selected(old('status', $task?->status ?? 'todo') === 'todo')>To Do</option>
                        <option value="in_progress" @selected(old('status', $task?->status ?? 'todo') === 'in_progress')>In Progress</option>
                        <option value="done" @selected(old('status', $task?->status ?? 'todo') === 'done')>Done</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeModal('{{ $id }}')"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90" style="background:#b30084;">Save</button>
            </div>
        </form>
    </div>
</div>