@extends('layouts.app')
@section('title', 'Profil — SyncFlow')
@section('page-title', 'Profile')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-12 gap-5 lg:gap-6 pb-24 md:pb-0">

    <div class="md:col-span-8 pt-6 flex flex-col">

        {{-- Folder tab "IDENTITY MAP" --}}
        <div class="self-start relative z-10">
            <div class="h-[33px] px-6 py-[9px]
                        bg-[#ffdad7] border border-[#894e4b]
                        rounded-tl-lg rounded-tr-lg
                        border-b-0
                        inline-flex items-center">
                <span class="font-inter font-semibold text-[#894e4b] text-xs uppercase tracking-[0.6px]">
                    Identity Map
                </span>
            </div>
        </div>

        {{-- ── Main identity card  --}}
        <div class="flex-1
                    bg-[#ffdad7] border border-[#894e4b]
                    rounded-bl-xl rounded-br-xl rounded-tr-xl
                    shadow-[0px_4px_4px_0px_rgba(137,78,75,0.05)]
                    overflow-hidden relative
                    flex flex-col gap-8
                    pt-12 pb-16 px-12">

            {{-- Glow dekoratif kanan atas --}}
            <div class="pointer-events-none absolute -top-20 -right-20 w-64 h-64
                        bg-white/20 rounded-full blur-[32px]"></div>

            {{-- ── Avatar + nama + email  --}}
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 sm:gap-12 relative z-10">

                {{-- Avatar --}}
                <div class="relative shrink-0 self-center">
                    {{-- Lingkaran foto --}}
                    <div class="w-[136px] h-[136px] rounded-full
                                bg-[#f2ede5] border-4 border-[#fdf9f0]
                                shadow-[4px_4px_0px_0px_#894e4b]
                                overflow-hidden p-1 relative">
                        <img id="avatarPreview"
                             src="{{ Auth::user()->avatar_url }}"
                             alt="{{ Auth::user()->name }}"
                             class="w-full h-full rounded-full object-cover">
                    </div>

                    {{-- Tombol edit avatar --}}
                    <label for="avatarInput"
                           class="absolute -bottom-1 -right-1
                                  w-[33px] h-[33px] rounded-full
                                  bg-[#b30084] border border-[#894e4b]
                                  shadow-[4px_4px_0px_#894e4b]
                                  flex items-center justify-center
                                  cursor-pointer hover:bg-[#8c0067] transition
                                  group/editbtn"
                           title="Ganti foto profil">
                        <svg class="w-[10.5px] h-[10.5px] text-white" viewBox="0 0 11 11" fill="none">
                            <path d="M7.5 1.5 L9.5 3.5 L3 10 L1 10 L1 8 L7.5 1.5Z"
                                  stroke="white" stroke-width="1.2" stroke-linejoin="round" fill="none"/>
                            <path d="M6.5 2.5 L8.5 4.5" stroke="white" stroke-width="1.2"/>
                        </svg>
                    </label>

                    {{-- Input file --}}
                    <input id="avatarInput"
                           type="file"
                           name="avatar"
                           accept="image/jpeg,image/png,image/webp"
                           class="hidden"
                           onchange="previewAvatar(this)"
                           form="infoForm">
                </div>

                {{-- Nama + email --}}
                <div class="py-8 flex flex-col gap-3 text-center sm:text-left">
                    <h1 class="font-poppins font-semibold text-[#894e4b]
                               text-3xl sm:text-4xl lg:text-5xl
                               leading-[1.1] break-words" id="displayName">
                        {{ Auth::user()->name }}
                    </h1>
                    <p class="font-inter text-[#7b4240] text-base sm:text-lg lg:text-xl leading-5">
                        {{ Auth::user()->email }}
                    </p>

                    <div id="avatarChangeBanner"
                         class="hidden items-center gap-2 bg-white/70 border border-[#894e4b]
                                rounded-lg px-3 py-2 text-xs text-[#894e4b] font-inter font-medium mt-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/>
                        </svg>
                        <span id="avatarBannerText">Foto baru dipilih — klik Save Changes</span>
                    </div>
                </div>
            </div>

            {{-- ── Divider  --}}
            <div class="border-t border-[rgba(137,78,75,0.2)] relative z-10"></div>

            {{-- ── Form: Update Info  --}}
            @if(session('success_info'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(()=>show=false,4000)"
                 class="flex items-center gap-2 bg-green-50 border border-green-200
                        text-green-700 text-sm rounded-lg px-4 py-3">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                {{ session('success_info') }}
            </div>
            @endif

            <form method="POST"
                  action="{{ route('profile.update.info') }}"
                  enctype="multipart/form-data"
                  class="relative z-10 flex flex-col gap-6"
                  id="infoForm">
                @csrf
                @method('PATCH')

                {{-- First Name + Last Name --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-1">
                        <label class="font-inter font-semibold text-[#894e4b] text-xs uppercase tracking-[0.6px] leading-4">
                            First Name
                        </label>
                        <input type="text" name="first_name"
                               value="{{ old('first_name', explode(' ', Auth::user()->name)[0] ?? '') }}"
                               placeholder="Alex"
                               class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-md
                                      px-[17px] py-[13px] font-inter text-[#1d1c17] text-base leading-6
                                      focus:outline-none focus:border-[#894e4b] focus:bg-white transition
                                      @error('first_name') border-red-400 @enderror">
                        @error('first_name')
                            <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="font-inter font-semibold text-[#894e4b] text-xs uppercase tracking-[0.6px] leading-4">
                            Last Name
                        </label>
                        <input type="text" name="last_name"
                               value="{{ old('last_name', implode(' ', array_slice(explode(' ', Auth::user()->name), 1)) ?: '') }}"
                               placeholder="Mercer"
                               class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-md
                                      px-[17px] py-[13px] font-inter text-[#1d1c17] text-base leading-6
                                      focus:outline-none focus:border-[#894e4b] focus:bg-white transition
                                      @error('last_name') border-red-400 @enderror">
                        @error('last_name')
                            <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Email --}}
                <div class="flex flex-col gap-1">
                    <label class="font-inter font-semibold text-[#894e4b] text-xs uppercase tracking-[0.6px] leading-4">
                        Email Address
                    </label>
                    <input type="email" name="email"
                           value="{{ old('email', Auth::user()->email) }}"
                           placeholder="you@campus.edu"
                           class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-md
                                  px-[17px] py-[13px] font-inter text-[#1d1c17] text-base leading-6
                                  focus:outline-none focus:border-[#894e4b] focus:bg-white transition
                                  @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Save Changes --}}
                <div class="flex justify-end pt-2">
                    <button type="submit"
                            class="bg-[#656026] border border-[#894e4b]
                                   shadow-[4px_4px_0px_#894e4b]
                                   hover:bg-[#4e4a1e] active:translate-y-px active:shadow-[2px_2px_0_#894e4b]
                                   rounded-lg px-6 py-[9px]
                                   font-poppins font-medium text-white text-base leading-6
                                   transition duration-150 cursor-pointer">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
        {{-- /identity card --}}

    </div>
    {{-- /kolom kiri --}}


    <div class="md:col-span-4 flex flex-col gap-5 lg:gap-6 pt-6 md:pt-6">

        {{-- ── Card: Account Control ─────────────────────── --}}
        <div class="bg-[#f8f3eb] border border-[#cbc7b6] rounded-xl
                    shadow-[0px_1px_1px_rgba(0,0,0,0.05)]
                    p-[25px] flex flex-col gap-4">

            <h2 class="font-poppins font-medium text-[#1d1c17] text-base leading-6">
                Account Control
            </h2>

            <p class="font-inter text-[#49473a] text-sm leading-5">
                Need to take a break? Make sure to save your work before logging out.
            </p>

            {{-- Log Out button --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2
                               border-2 border-[#ba1a1a] rounded-lg
                               px-[18px] py-[14px]
                               font-poppins font-medium text-[#ba1a1a] text-base leading-6
                               hover:bg-red-50 active:scale-[0.98]
                               transition duration-150">
                    {{-- Log out icon --}}
                    <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 18 18" fill="none">
                        <path d="M12 12.75 L15.75 9 L12 5.25" stroke="#ba1a1a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15.75 9H7.5" stroke="#ba1a1a" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M7.5 15.75H3.75A1.5 1.5 0 0 1 2.25 14.25V3.75A1.5 1.5 0 0 1 3.75 2.25H7.5" stroke="#ba1a1a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Log Out
                </button>
            </form>
        </div>

        {{-- ── Card: Security Settings ───────────────────── --}}
        <div class="bg-white border border-[#cbc7b6] rounded-xl
                    shadow-[0px_1px_1px_rgba(0,0,0,0.05)]
                    pt-[25px] pb-10 px-[25px]
                    flex flex-col gap-6">

            {{-- Header --}}
            <div class="flex items-center gap-3 pb-[17px] border-b border-[rgba(203,199,182,0.4)]">
                {{-- Lock icon --}}
                <div class="w-8 h-[37px] shrink-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-[#894e4b]" viewBox="0 0 32 38" fill="none">
                        <rect x="3" y="16" width="26" height="19" rx="3"
                              fill="#ffdad7" stroke="#894e4b" stroke-width="1.5"/>
                        <path d="M9 16V11C9 7.686 12.134 5 16 5C19.866 5 23 7.686 23 11V16"
                              stroke="#894e4b" stroke-width="1.5" stroke-linecap="round"/>
                        <circle cx="16" cy="25.5" r="3" fill="#894e4b"/>
                        <path d="M16 28.5V31.5" stroke="#894e4b" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <h2 class="font-poppins font-medium text-[#1d1c17] text-base leading-6">
                    Security Settings
                </h2>
            </div>

            {{-- Password form --}}
            @if(session('success_password'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(()=>show=false,4000)"
                 class="flex items-center gap-2 bg-green-50 border border-green-200
                        text-green-700 text-sm rounded-lg px-4 py-3">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                {{ session('success_password') }}
            </div>
            @endif

            <form method="POST" action="{{ route('profile.update.password') }}"
                  class="flex flex-col gap-6">
                @csrf
                @method('PATCH')

                {{-- Current Password --}}
                <div class="flex flex-col gap-1">
                    <label class="font-inter font-semibold text-[#7a7769] text-xs
                                  uppercase tracking-[0.6px] leading-4">
                        Current Password
                    </label>
                    <input type="password" name="current_password"
                           placeholder="••••••••"
                           class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-md
                                  px-[17px] py-[15px]
                                  font-inter text-[#1d1c17] text-base
                                  focus:outline-none focus:border-[#894e4b] focus:bg-white
                                  transition
                                  @error('current_password') border-red-400 @enderror">
                    @error('current_password')
                        <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New Password --}}
                <div class="flex flex-col gap-1">
                    <label class="font-inter font-semibold text-[#7a7769] text-xs
                                  uppercase tracking-[0.6px] leading-4">
                        New Password
                    </label>
                    <input type="password" name="password"
                           placeholder="Enter new password"
                           class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-md
                                  px-[17px] py-[15px]
                                  font-inter text-[#1d1c17] text-base
                                  placeholder:text-[#6b7280]
                                  focus:outline-none focus:border-[#894e4b] focus:bg-white
                                  transition
                                  @error('password') border-red-400 @enderror">
                    @error('password')
                        <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm New Password --}}
                <div class="flex flex-col gap-1">
                    <label class="font-inter font-semibold text-[#7a7769] text-xs
                                  uppercase tracking-[0.6px] leading-4">
                        Confirm New Password
                    </label>
                    <input type="password" name="password_confirmation"
                           placeholder="Repeat new password"
                           class="bg-[#fdf9f0] border border-[#cbc7b6] rounded-md
                                  px-[17px] py-[15px]
                                  font-inter text-[#1d1c17] text-base
                                  placeholder:text-[#6b7280]
                                  focus:outline-none focus:border-[#894e4b] focus:bg-white
                                  transition">
                </div>

               
                <div class="flex justify-end pt-2">
                    <button type="submit"
                            class="border-2 border-[#894e4b] rounded-lg
                                   px-[26px] py-[10px]
                                   font-poppins font-medium text-[#894e4b] text-base leading-6
                                   hover:bg-[#ffdad7] active:scale-[0.98]
                                   transition duration-150 cursor-pointer">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
        {{-- /security card --}}

        <div id="avatarUploadPanel"
             class="hidden bg-white border border-[#cbc7b6] rounded-xl
                    shadow-[0px_1px_1px_rgba(0,0,0,0.05)]
                    p-[25px] flex flex-col gap-4">
            <h2 class="font-poppins font-medium text-[#1d1c17] text-base leading-6">
                Ganti Foto Profil
            </h2>
            <p class="font-inter text-[#49473a] text-sm leading-5">
                Format JPG, PNG, WEBP. Maks. 2MB.
            </p>
            <form method="POST" action="{{ route('profile.update.avatar') }}"
                  enctype="multipart/form-data" id="avatarForm">
                @csrf
                <input type="file" name="avatar" id="avatarFileInput"
                       accept="image/jpeg,image/png,image/webp"
                       class="hidden" onchange="submitAvatarForm()">
                <div id="avatarSelectedName"
                     class="hidden mb-3 font-inter text-sm text-[#49473a] truncate"></div>
                @error('avatar')
                    <p class="text-xs text-red-500 mb-2">{{ $message }}</p>
                @enderror
                <button type="submit" id="avatarSaveBtn"
                        class="hidden w-full bg-[#b30084] hover:bg-[#8c0067]
                               rounded-lg py-3
                               font-poppins font-medium text-white text-base
                               transition active:scale-[0.98] cursor-pointer">
                    Simpan Foto
                </button>
            </form>
        </div>

    </div>
    {{-- /kolom kanan --}}

</div>
{{-- /bento grid --}}


@endsection


@push('scripts')
<script>
    function previewAvatar(input) {
        const file = input.files[0];
        if (!file) return;

        const panel = document.getElementById('avatarUploadPanel');
        panel.classList.remove('hidden');
        panel.classList.add('flex');

        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);

        const nameEl = document.getElementById('avatarSelectedName');
        nameEl.textContent = "File: " + file.name;
        nameEl.classList.remove('hidden');
        document.getElementById('avatarSaveBtn').classList.remove('hidden');
    }


    function submitAvatarForm() {
        document.getElementById('avatarForm').submit();
    }
</script>
@endpush
