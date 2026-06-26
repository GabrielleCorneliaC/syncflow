@props([
    'id',
    'title',
    'action',
    'method' => 'POST',
    'task' => null,
])

{{-- 1. Outer Wrapper (Backdrop latar hitam) --}}
<div id="{{ $id }}" 
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    
    <div onclick="closeModal('{{ $id }}')" class="absolute inset-0"></div>

    {{-- 2. Box Modal Putih --}}
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden z-10 flex flex-col max-h-[90vh]">
        
        {{-- Modal Header  --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-5 border-b border-[rgba(203,199,182,0.3)]">
            <h3 class="font-poppins font-semibold text-[#1d1c17] text-lg">{{ $title }}</h3>
            <button type="button" onclick="closeModal('{{ $id }}')" class="text-[#9b9887] hover:text-[#49473a] transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- 3. Form Body --}}
        <form action="{{ $action }}" method="POST" class="flex-1 overflow-y-auto p-6 flex flex-col gap-4">
            @csrf
            @if($method !== 'POST')
                @method($method)
            @endif

                <div class="flex flex-col gap-1">
                    <label class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">Nama Tugas *</label>
                    <input type="text" name="title" required value="{{ old('title', $task?->title) }}"
                           class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-lg px-4 py-3 font-inter text-[#1d1c17] text-sm focus:outline-none focus:border-[#b30084] transition">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">Deskripsi</label>
                    <textarea name="description" rows="3"
                              class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-lg px-4 py-3 font-inter text-[#1d1c17] text-sm focus:outline-none focus:border-[#b30084] transition resize-none">{{ old('description', $task?->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">Deadline</label>
                        <input type="date" name="due_date" value="{{ old('due_date', $task?->due_date?->format('Y-m-d')) }}"
                               class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-lg px-4 py-3 font-inter text-[#1d1c17] text-sm focus:outline-none focus:border-[#b30084] transition">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">Prioritas</label>
                        <select name="priority" class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-lg px-4 py-3 font-inter text-[#1d1c17] text-sm focus:outline-none focus:border-[#b30084] transition">
                            <option value="low" @selected(old('priority', $task?->priority) === 'low')>Low</option>
                            <option value="medium" @selected(old('priority', $task?->priority ?? 'medium') === 'medium')>Medium</option>
                            <option value="high" @selected(old('priority', $task?->priority) === 'high')>High</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">Progress (%)</label>
                        <input type="number" name="progress" min="0" max="100" value="{{ old('progress', $task?->progress ?? 0) }}"
                               class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-lg px-4 py-3 font-inter text-[#1d1c17] text-sm focus:outline-none focus:border-[#b30084] transition">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">Status</label>
                        <select name="status" class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-lg px-4 py-3 font-inter text-[#1d1c17] text-sm focus:outline-none focus:border-[#b30084] transition">
                            <option value="todo" @selected(old('status', $task?->status) === 'todo')>To Do</option>
                            <option value="in_progress" @selected(old('status', $task?->status) === 'in_progress')>In Progress</option>
                            <option value="done" @selected(old('status', $task?->status) === 'done')>Done</option>
                        </select>
                    </div>
                </div>

            {{-- 4. Tombol Footer di Paling Bawah Form --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-50 mt-auto">
                <button type="button" onclick="closeModal('{{ $id }}')"
                        class="font-poppins font-medium text-[#49473a] text-sm px-6 py-[10px] rounded-lg hover:bg-gray-100 transition">
                    Batal
                </button>
                <button type="submit"
                        class="bg-[#b30084] hover:bg-[#8c0067] shadow-[4px_4px_0px_#6a1452] rounded-lg px-6 py-[10px] font-poppins font-medium text-white text-sm transition active:translate-y-px active:shadow-[2px_2px_0_#6a1452]">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>