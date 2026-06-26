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

{{-- 1. Outer Wrapper (Backdrop hitam) --}}
<div id="{{ $id }}" 
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    
    <div onclick="closeModal('{{ $id }}')" class="absolute inset-0"></div>

    {{-- 2. Box Modal Putih --}}
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden z-10 flex flex-col max-h-[90vh]">
        
        {{-- Modal Header --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-5 border-b border-[rgba(203,199,182,0.3)]">
            <h3 class="font-poppins font-semibold text-[#1d1c17] text-lg">{{ $title }}</h3>
            <button type="button" onclick="closeModal('{{ $id }}')" class="text-[#9b9887] hover:text-[#49473a] transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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
                    <label class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">Nama Jadwal *</label>
                    <input type="text" name="title" required value="{{ old('title', $schedule?->title) }}"
                           class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-lg px-4 py-3 font-inter text-[#1d1c17] text-sm focus:outline-none focus:border-[#b30084] transition">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">Deskripsi</label>
                    <textarea name="description" rows="3"
                              class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-lg px-4 py-3 font-inter text-[#1d1c17] text-sm focus:outline-none focus:border-[#b30084] transition resize-none">{{ old('description', $schedule?->description) }}</textarea>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">Tanggal *</label>
                        <input type="date" name="date" required value="{{ old('date', $schedule?->date?->format('Y-m-d')) }}"
                               class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-lg px-3 py-3 font-inter text-[#1d1c17] text-sm focus:outline-none focus:border-[#b30084] transition">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">Mulai *</label>
                        <input type="time" name="time" required value="{{ old('time', $timeValue) }}"
                               class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-lg px-3 py-3 font-inter text-[#1d1c17] text-sm focus:outline-none focus:border-[#b30084] transition">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">Selesai</label>
                        <input type="time" name="end_time" value="{{ old('end_time', $endTimeValue) }}"
                               class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-lg px-3 py-3 font-inter text-[#1d1c17] text-sm focus:outline-none focus:border-[#b30084] transition">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">Timezone</label>
                        <select name="timezone" class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-lg px-4 py-3 font-inter text-[#1d1c17] text-sm focus:outline-none focus:border-[#b30084] transition">
                            <option value="Asia/Jakarta" @selected(old('timezone', $schedule?->timezone) === 'Asia/Jakarta')>WIB</option>
                            <option value="Asia/Makassar" @selected(old('timezone', $schedule?->timezone) === 'Asia/Makassar')>WITA</option>
                            <option value="Asia/Jayapura" @selected(old('timezone', $schedule?->timezone) === 'Asia/Jayapura')>WIT</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">Warna</label>
                        <select name="color" class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-lg px-4 py-3 font-inter text-[#1d1c17] text-sm focus:outline-none focus:border-[#b30084] transition">
                            <option value="#ffb3ae">Pink (default)</option>
                            <option value="#b30084">Magenta</option>
                            <option value="#6a1452">Ungu Tua</option>
                            <option value="#ffd700">Kuning</option>
                            <option value="#4ade80">Hijau</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="font-inter font-semibold text-[#49473a] text-xs uppercase tracking-[0.6px]">Lokasi / Link</label>
                    <input type="text" name="location" value="{{ old('location', $schedule?->location) }}"
                           class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-lg px-4 py-3 font-inter text-[#1d1c17] text-sm focus:outline-none focus:border-[#b30084] transition">
                </div>

            {{-- 4. Tombol Footer di Paling Bawah Form --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-50 mt-auto">
                <button type="button" onclick="closeModal('{{ $id }}')"
                        class="font-poppins font-medium text-[#49473a] text-sm px-6 py-[10px] rounded-lg hover:bg-gray-100 transition">
                    Batal
                </button>
                <button type="submit"
                        class="bg-[#C8216B] hover:bg-[#a61556] shadow-[4px_4px_0px_#6a1452] rounded-lg px-6 py-[10px] font-poppins font-medium text-white text-sm transition active:translate-y-px active:shadow-[2px_2px_0_#6a1452]">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>