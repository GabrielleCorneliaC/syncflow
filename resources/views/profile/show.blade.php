@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- ── Avatar Card ──────────────────────────────── --}}
    <div class="bg-white border border-border rounded-2xl p-6">
        <h2 class="font-heading font-bold text-base text-textmain mb-4">Foto Profil</h2>

        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
            {{-- Preview avatar --}}
            <div class="relative shrink-0" id="avatarWrapper">
                <img src="{{ $user->avatar_url }}"
                     alt="{{ $user->name }}"
                     id="avatarPreview"
                     class="w-24 h-24 rounded-2xl object-cover ring-4 ring-primary/20">
                <label for="avatarInput"
                       class="absolute -bottom-2 -right-2 w-8 h-8 bg-primary rounded-xl flex items-center justify-center cursor-pointer shadow-md hover:bg-primary-dark transition">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/>
                    </svg>
                </label>
            </div>

            {{-- Form upload --}}
            <form method="POST" action="{{ route('profile.update.avatar') }}"
                  enctype="multipart/form-data" id="avatarForm" class="flex-1 w-full">
                @csrf
                @method('POST')

                <p class="text-sm text-textsoft mb-3">
                    Format: JPG, PNG, WEBP. Ukuran maks. <strong>2MB</strong>.
                </p>

                <input type="file" name="avatar" id="avatarInput" accept="image/*" class="hidden">

                <div id="avatarFilename" class="hidden mb-3 flex items-center gap-2 bg-muted border border-border rounded-xl px-3 py-2 text-sm text-textsoft">
                    <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/>
                    </svg>
                    <span id="avatarFilenameText">—</span>
                </div>

                @error('avatar')
                <p class="text-xs text-red-500 mb-2">{{ $message }}</p>
                @enderror

                <button type="submit" id="avatarSubmit"
                        class="hidden sf-btn sf-btn-primary text-sm">
                    Simpan Foto
                </button>
            </form>
        </div>
    </div>

    {{-- ── Info Card ─────────────────────────────────── --}}
    <div class="bg-white border border-border rounded-2xl p-6">
        <h2 class="font-heading font-bold text-base text-textmain mb-4">Informasi Akun</h2>

        @if(session('success'))
        <div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('profile.update.info') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            {{-- Nama --}}
            <div>
                <label class="sf-label">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="sf-input @error('name') sf-input-error @enderror">
                @error('name')
                <p class="sf-error-msg">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="sf-label">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="sf-input @error('email') sf-input-error @enderror">
                @error('email')
                <p class="sf-error-msg">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit" class="sf-btn sf-btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    {{-- ── Password Card ────────────────────────────── --}}
    <div class="bg-white border border-border rounded-2xl p-6">
        <h2 class="font-heading font-bold text-base text-textmain mb-1">Ganti Password</h2>
        <p class="text-xs text-texthint mb-4">Gunakan password minimal 8 karakter.</p>

        <form method="POST" action="{{ route('profile.update.password') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="sf-label">Password Saat Ini</label>
                <input type="password" name="current_password"
                       class="sf-input @error('current_password') sf-input-error @enderror">
                @error('current_password')
                <p class="sf-error-msg">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="sf-label">Password Baru</label>
                <input type="password" name="password"
                       class="sf-input @error('password') sf-input-error @enderror">
                @error('password')
                <p class="sf-error-msg">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="sf-label">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="sf-input">
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit" class="sf-btn sf-btn-secondary">Perbarui Password</button>
            </div>
        </form>
    </div>

    {{-- ── Danger Zone ──────────────────────────────── --}}
    <div class="bg-white border border-red-200 rounded-2xl p-6" x-data="{ confirm: false }">
        <h2 class="font-heading font-bold text-base text-red-600 mb-1">Danger Zone</h2>
        <p class="text-xs text-texthint mb-4">Tindakan ini permanen dan tidak bisa dibatalkan.</p>

        <button @click="confirm = !confirm"
                class="sf-btn sf-btn-danger text-sm">
            Hapus Akun Saya
        </button>

        {{-- Konfirmasi --}}
        <div x-show="confirm" x-transition class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
            <p class="text-sm text-red-700 font-medium mb-3">
                Ketik password kamu untuk konfirmasi penghapusan akun.
            </p>
            <form method="POST" action="{{ route('profile.destroy') }}" class="flex gap-2">
                @csrf
                @method('DELETE')
                <input type="password" name="password" placeholder="Password"
                       class="flex-1 sf-input text-sm">
                <button type="submit" class="sf-btn sf-btn-danger text-sm">Hapus</button>
            </form>
            @error('password')
            <p class="sf-error-msg mt-2">{{ $message }}</p>
            @enderror
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Preview avatar sebelum upload
    const avatarInput    = document.getElementById('avatarInput');
    const avatarPreview  = document.getElementById('avatarPreview');
    const avatarFilename = document.getElementById('avatarFilename');
    const avatarFilenameText = document.getElementById('avatarFilenameText');
    const avatarSubmit   = document.getElementById('avatarSubmit');

    avatarInput?.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        // Preview
        const reader = new FileReader();
        reader.onload = e => { avatarPreview.src = e.target.result; };
        reader.readAsDataURL(file);

        // Filename
        avatarFilenameText.textContent = file.name;
        avatarFilename.classList.remove('hidden');
        avatarFilename.classList.add('flex');

        // Tombol submit
        avatarSubmit.classList.remove('hidden');
    });
</script>
@endpush

{{-- ── Komponen CSS (standar untuk seluruh project) ── --}}
<style>
    /* ══════════════════════════════════════════
       STANDAR KOMPONEN — SyncFlow Design System
       Dipakai oleh seluruh anggota tim.
       Salin class-class ini ke halaman masing-masing.
    ══════════════════════════════════════════ */

    /* Label form */
    .sf-label {
        @apply block text-xs font-semibold uppercase tracking-widest text-textmain mb-1.5;
    }

    /* Input field */
    .sf-input {
        @apply w-full bg-muted border-2 border-border rounded-xl px-4 py-3 text-sm text-textmain
               placeholder-texthint focus:outline-none focus:border-primary focus:bg-white transition;
    }
    .sf-input-error {
        @apply border-red-400 bg-red-50;
    }

    /* Pesan error field */
    .sf-error-msg {
        @apply mt-1 text-xs text-red-500;
    }

    /* Tombol primary (magenta) */
    .sf-btn-primary {
        @apply bg-primary hover:bg-primary-dark text-white shadow-md shadow-primary/20;
    }

    /* Tombol secondary (outline) */
    .sf-btn-secondary {
        @apply bg-white border-2 border-primary text-primary hover:bg-primary/5;
    }

    /* Tombol danger (merah) */
    .sf-btn-danger {
        @apply bg-red-600 hover:bg-red-700 text-white;
    }

    /* Base tombol */
    .sf-btn {
        @apply inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm
               transition duration-150 active:scale-[0.97];
    }

    /* Badge */
    .sf-badge {
        @apply inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full;
    }
    .sf-badge-primary  { @apply bg-primary/10 text-primary; }
    .sf-badge-success  { @apply bg-green-100 text-green-700; }
    .sf-badge-warning  { @apply bg-yellow-100 text-yellow-700; }
    .sf-badge-danger   { @apply bg-red-100 text-red-700; }
    .sf-badge-neutral  { @apply bg-muted text-textsoft border border-border; }

    /* Card container */
    .sf-card {
        @apply bg-white border border-border rounded-2xl p-5;
    }

    /* Tabel standar */
    .sf-table { @apply w-full text-sm; }
    .sf-table thead tr { @apply border-b border-border; }
    .sf-table th {
        @apply text-left text-xs font-semibold uppercase tracking-widest text-texthint py-3 px-4;
    }
    .sf-table tbody tr {
        @apply border-b border-muted hover:bg-surface transition;
    }
    .sf-table td { @apply py-3 px-4 text-textsoft; }
</style>
