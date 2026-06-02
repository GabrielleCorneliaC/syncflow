@extends('layouts.auth')
@section('title', 'Sign Up — SyncFlow')

@section('content')

{{--
    ════════════════════════════════════════════════════════
    SyncFlow Sign Up Page
    Ref Figma: node 72:14 (desktop) · node 72:92 (mobile)
    Fields  : First Name · Last Name · Email · Password
    Button  : "Join The Magic →"
    ════════════════════════════════════════════════════════
--}}

<div class="relative w-full max-w-[440px] mx-auto">

    <div class="blob blob-yellow"></div>
    <div class="blob blob-pink"></div>

    {{-- ══ GLASS CARD ══ --}}
    <div class="glass-card relative z-10 w-full
                border-2 border-[#cbc7b6] rounded-xl overflow-hidden
                flex flex-col gap-6
                p-[34px]">

        {{-- ── HEADER ── --}}
        <div class="flex flex-col items-center gap-2 pb-2">
            <div class="w-16 h-16 flex items-center justify-center">
                <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-16 h-16">
                    <circle cx="32" cy="32" r="26" stroke="#b30084" stroke-width="3"/>
                    <path d="M20 32 C20 24.3 26.3 18 34 18"
                          stroke="#b30084" stroke-width="3" stroke-linecap="round" fill="none"/>
                    <path d="M34 18 L40 18 M40 18 L40 24"
                          stroke="#b30084" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    <path d="M44 32 C44 39.7 37.7 46 30 46"
                          stroke="#d400a0" stroke-width="3" stroke-linecap="round" fill="none"/>
                    <path d="M30 46 L24 46 M24 46 L24 40"
                          stroke="#d400a0" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
            </div>

            <h1 class="font-montserrat font-bold text-2xl leading-8 text-[#656026] text-center mt-2">
                Create Account
            </h1>
            <p class="font-inter text-sm leading-5 text-[#49473a] text-center">
                Step into the flow state.
            </p>
        </div>

        {{-- ── TOGGLE ── --}}
        <div class="relative bg-[#e6e2da] rounded-lg p-1 flex">
            <a href="{{ route('login') }}"
               class="tab-inactive flex-1 text-center font-poppins font-medium text-base leading-6 py-2 rounded-md hover:text-[#1d1c17] transition-colors">
                Log In
            </a>
            <span class="tab-active flex-1 text-center font-poppins font-medium text-base leading-6 py-2 rounded-md cursor-default">
                Sign Up
            </span>
        </div>

        {{-- ── ERRORS ── --}}
        @if ($errors->any())
        <div class="flex items-start gap-2 bg-red-50 border border-red-200 rounded-lg px-3.5 py-3">
            <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <ul class="text-sm text-red-600 space-y-0.5 list-none m-0 p-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- ── FORM ── --}}
        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-4 pt-2" novalidate>
            @csrf

            {{-- First Name + Last Name (2 kolom di tablet/desktop, 1 kolom di mobile) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- First Name --}}
                <div class="flex flex-col gap-1">
                    <label for="first_name"
                           class="font-inter font-semibold text-xs leading-4 text-[#1d1c17] uppercase tracking-[0.6px]">
                        First Name
                    </label>
                    <input id="first_name" type="text" name="first_name"
                           value="{{ old('first_name') }}"
                           placeholder="Alex"
                           autocomplete="given-name" required
                           class="sf-input w-full bg-[#f2ede5] border-2 border-[#cbc7b6] rounded-lg
                                  px-[18px] py-4 font-inter text-base text-[#1d1c17]
                                  placeholder:text-[rgba(73,71,58,0.5)] transition
                                  {{ $errors->has('first_name') ? 'error' : '' }}">
                    @error('first_name')
                        <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Last Name --}}
                <div class="flex flex-col gap-1">
                    <label for="last_name"
                           class="font-inter font-semibold text-xs leading-4 text-[#1d1c17] uppercase tracking-[0.6px]">
                        Last Name
                    </label>
                    <input id="last_name" type="text" name="last_name"
                           value="{{ old('last_name') }}"
                           placeholder="Mercer"
                           autocomplete="family-name" required
                           class="sf-input w-full bg-[#f2ede5] border-2 border-[#cbc7b6] rounded-lg
                                  px-[18px] py-4 font-inter text-base text-[#1d1c17]
                                  placeholder:text-[rgba(73,71,58,0.5)] transition
                                  {{ $errors->has('last_name') ? 'error' : '' }}">
                    @error('last_name')
                        <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Email Address --}}
            <div class="flex flex-col gap-1">
                <label for="email"
                       class="font-inter font-semibold text-xs leading-4 text-[#1d1c17] uppercase tracking-[0.6px]">
                    Email Address
                </label>
                <input id="email" type="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="you@campus.edu"
                       autocomplete="email" required
                       class="sf-input w-full bg-[#f2ede5] border-2 border-[#cbc7b6] rounded-lg
                              px-[18px] py-4 font-inter text-base text-[#1d1c17]
                              placeholder:text-[rgba(73,71,58,0.5)] transition
                              {{ $errors->has('email') ? 'error' : '' }}">
                @error('email')
                    <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="flex flex-col gap-1">
                <label for="password"
                       class="font-inter font-semibold text-xs leading-4 text-[#1d1c17] uppercase tracking-[0.6px]">
                    Password
                </label>
                <div class="relative">
                    <input id="password" type="password" name="password"
                           placeholder="••••••••"
                           autocomplete="new-password" required
                           class="sf-input w-full bg-[#f2ede5] border-2 border-[#cbc7b6] rounded-lg
                                  px-[18px] py-4 font-inter text-base text-[#1d1c17]
                                  placeholder:text-[rgba(73,71,58,0.5)] transition pr-12
                                  {{ $errors->has('password') ? 'error' : '' }}">
                    <button type="button" onclick="togglePwd('password', this)"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-[#9b9887] hover:text-[#49473a] transition"
                            aria-label="Toggle password visibility">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-[rgba(73,71,58,0.6)] mt-0.5">Minimal 8 karakter.</p>
            </div>

            {{-- Confirm Password --}}
            <div class="flex flex-col gap-1">
                <label for="password_confirmation"
                       class="font-inter font-semibold text-xs leading-4 text-[#1d1c17] uppercase tracking-[0.6px]">
                    Confirm Password
                </label>
                <div class="relative">
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           placeholder="••••••••"
                           autocomplete="new-password" required
                           class="sf-input w-full bg-[#f2ede5] border-2 border-[#cbc7b6] rounded-lg
                                  px-[18px] py-4 font-inter text-base text-[#1d1c17]
                                  placeholder:text-[rgba(73,71,58,0.5)] transition pr-12">
                    <button type="button" onclick="togglePwd('password_confirmation', this)"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-[#9b9887] hover:text-[#49473a] transition"
                            aria-label="Toggle confirm password visibility">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Join The Magic button --}}
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2
                           bg-[#b30084] hover:bg-[#8c0067] active:scale-[0.98]
                           border-2 border-transparent rounded-lg
                           px-[18px] py-[14px]
                           font-poppins font-medium text-base leading-6 text-white
                           transition duration-150 mt-1">
                Join The Magic
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none">
                    <path d="M3 8H13M13 8L9 4M13 8L9 12"
                          stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </form>

        {{-- ── TERMS ── --}}
        <p class="font-inter font-medium text-[11px] leading-[14px] text-[#49473a] text-center">
            By entering, you agree to our
            <a href="#" class="underline underline-offset-1 hover:text-[#b30084] transition-colors">Terms</a>
            &amp;
            <a href="#" class="underline underline-offset-1 hover:text-[#b30084] transition-colors">Privacy</a>.
        </p>

    </div>
</div>

@endsection

@push('scripts')
<script>
function togglePwd(inputId, btn) {
    const input = document.getElementById(inputId);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.innerHTML = isText
        ? `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
               <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
               <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
           </svg>`
        : `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
               <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
           </svg>`;
}
</script>
@endpush
