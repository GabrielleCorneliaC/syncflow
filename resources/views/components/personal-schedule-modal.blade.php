@props([
    'id',
    'title',
    'action',
    'method' => 'POST',
    'schedule' => null,
])

@php
    $timeValue = $schedule?->time ? substr((string) $schedule->time, 0, 5) : null;
    $endTimeValue = $schedule?->end_time ? substr((string) $schedule->end_time, 0, 5) : null;
@endphp

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
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Title</label>
                <input type="text" name="title" required value="{{ old('title', $schedule?->title) }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100 resize-none">{{ old('description', $schedule?->description) }}</textarea>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Date</label>
                    <input type="date" name="date" required value="{{ old('date', $schedule?->date?->format('Y-m-d')) }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Start</label>
                    <input type="time" name="time" required value="{{ old('time', $timeValue) }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">End</label>
                    <input type="time" name="end_time" value="{{ old('end_time', $endTimeValue) }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Timezone</label>
                <select name="timezone" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 bg-white">
                    @foreach(['Asia/Jakarta' => 'WIB', 'Asia/Makassar' => 'WITA', 'Asia/Jayapura' => 'WIT'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('timezone', $schedule?->timezone ?? 'Asia/Jakarta') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Location</label>
                <input type="text" name="location" value="{{ old('location', $schedule?->location) }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeModal('{{ $id }}')"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90" style="background:#C8216B;">Save</button>
            </div>
        </form>
    </div>
</div>
