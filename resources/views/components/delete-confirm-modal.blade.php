@props([
    'id',
    'title' => 'Hapus Data?',
    'message' => 'Tindakan ini tidak dapat dibatalkan.',
    'action',
])

<div id="{{ $id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div onclick="closeModal('{{ $id }}')" class="absolute inset-0" style="background:rgba(0,0,0,0.4);"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 z-10 text-center">

        <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-red-50 flex items-center justify-center">
            <svg width="22" height="22" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>
            </svg>
        </div>

        <h2 class="font-display font-bold text-lg mb-1" style="color:#1A1A2E;">{{ $title }}</h2>
        <p class="text-sm text-gray-500 mb-5">{{ $message }}</p>

        <form action="{{ $action }}" method="POST" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeModal('{{ $id }}')"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all">
                Batal
            </button>
            <button type="submit"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition-all">
                Hapus
            </button>
        </form>
    </div>
</div>