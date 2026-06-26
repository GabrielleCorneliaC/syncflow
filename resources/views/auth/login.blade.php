@extends('layouts.auth')
@section('title', 'Log In — SyncFlow')

@section('content')

{{--
    ════════════════════════════════════════════════════════
    SyncFlow Login Page
    Ref Figma: node 1:2 (desktop) · node 8:12 (mobile)
    Fonts  : Montserrat Bold (heading) · Poppins 500 (button/toggle) · Inter (body)
    Colors : #f2ede5 input-bg · #cbc7b6 border · #b30084 primary
             #1d1c17 text · #656026 heading · #49473a soft-text
    ════════════════════════════════════════════════════════
--}}

{{-- ── PAGE BODY ──────────────────────────────────────── --}}
<div class="relative w-full max-w-[440px] mx-auto">

    {{-- Blob kuning kiri atas --}}
    <div class="blob blob-yellow"></div>
    {{-- Blob pink kanan bawah --}}
    <div class="blob blob-pink"></div>

    {{-- ══ GLASS CARD ══ --}}
    <div class="glass-card relative z-10 w-full
                border-2 border-[#cbc7b6] rounded-xl overflow-hidden
                flex flex-col gap-6
                p-[34px]
                sm:p-[34px]">

        {{-- ── HEADER ── --}}
        <div class="flex flex-col items-center gap-2 pb-2">
            {{-- Logo SyncFlow --}}
            <div class="w-16 h-16 flex items-center justify-center">
                <img src="{{ asset('assets/logo.png') }}" alt="Logo SyncFlow" class="w-full h-full object-contain">
            </div>

            {{-- Heading --}}
            <h1 class="font-montserrat font-bold text-2xl leading-8 text-[#656026] text-center mt-2">
                Welcome Back
            </h1>

            {{-- Subtext --}}
            <p class="font-inter text-sm leading-5 text-[#49473a] text-center">
                Step into the flow state.
            </p>
        </div>

        {{-- ── TOGGLE LOG IN / SIGN UP ── --}}
        <div class="relative bg-[#e6e2da] rounded-lg p-1 flex">
            {{-- Active pill --}}
            <span class="tab-active flex-1 text-center font-poppins font-medium text-base leading-6 py-2 rounded-md cursor-default">
                Log In
            </span>
            <a href="{{ route('register') }}"
               class="tab-inactive flex-1 text-center font-poppins font-medium text-base leading-6 py-2 rounded-md hover:text-[#1d1c17] transition-colors">
                Sign Up
            </a>
        </div>

        {{-- ── ERROR / SUCCESS MESSAGES ── --}}
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

        @if (session('success'))
        <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-lg px-3.5 py-3">
            <svg class="w-4 h-4 text-green-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
        @endif

        {{-- ── FORM ── --}}
        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-4 pt-2" novalidate>
            @csrf

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
                <div class="flex items-center justify-between">
                    <label for="password"
                           class="font-inter font-semibold text-xs leading-4 text-[#1d1c17] uppercase tracking-[0.6px]">
                        Password
                    </label>
                    <a href="{{ route('password.request') }}"
                       class="font-inter font-medium text-[11px] leading-[14px] text-[#b30084] hover:underline">
                        Forgot?
                    </a>
                </div>
                <div class="relative">
                    <input id="password" type="password" name="password"
                           placeholder="••••••••"
                           autocomplete="current-password" required
                           class="sf-input w-full bg-[#f2ede5] border-2 border-[#cbc7b6] rounded-lg
                                  px-[18px] py-4 font-inter text-base text-[#1d1c17]
                                  placeholder:text-[rgba(73,71,58,0.5)] transition pr-12
                                  {{ $errors->has('password') ? 'error' : '' }}">
                    {{-- Toggle show/hide password --}}
                    <button type="button" onclick="togglePwd('password', this)"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-[#9b9887] hover:text-[#49473a] transition"
                            aria-label="Toggle password visibility">
                        <svg id="eye-login" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember me --}}
            <label class="flex items-center gap-2 cursor-pointer -mt-1">
                <input type="checkbox" name="remember" value="1"
                       class="w-4 h-4 rounded border-[#cbc7b6] accent-[#b30084] cursor-pointer">
                <span class="font-inter text-sm text-[#49473a] select-none">Remember me</span>
            </label>

            {{-- Enter Flow button --}}
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2
                           bg-[#b30084] hover:bg-[#8c0067] active:scale-[0.98]
                           border-2 border-transparent rounded-lg
                           px-[18px] py-[14px]
                           font-poppins font-medium text-base leading-6 text-white
                           transition duration-150">
                Enter Flow
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none">
                    <path d="M3 8H13M13 8L9 4M13 8L9 12"
                          stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </form>

        {{-- ── DIVIDER ── --}}
        <div class="flex items-center gap-4">
            <span class="flex-1 border-t-2 border-[#cbc7b6]"></span>
            <span class="font-inter font-medium text-[11px] text-[#49473a] uppercase tracking-[1.1px] whitespace-nowrap">
                Or Access Via
            </span>
            <span class="flex-1 border-t-2 border-[#cbc7b6]"></span>
        </div>

        {{-- ── GOOGLE OAUTH BUTTON ── --}}
        <a href="{{ route('auth.google') }}"
           class="w-full flex items-center justify-center gap-3
                  bg-white hover:bg-[#f2ede5] active:scale-[0.98]
                  border-2 border-[#cbc7b6] rounded-lg
                  px-[18px] py-[14px]
                  transition duration-150">
            {{-- Google logo SVG --}}
            <svg class="w-5 h-5 shrink-0" viewBox="0 0 20 20">
                <path d="M19.6 10.23c0-.68-.06-1.36-.17-2H10v3.79h5.4a4.6 4.6 0 0 1-2 3.02v2.5h3.23c1.9-1.74 2.97-4.3 2.97-7.31Z" fill="#4285F4"/>
                <path d="M10 20c2.7 0 4.96-.9 6.63-2.43l-3.23-2.5c-.9.6-2.04.95-3.4.95-2.6 0-4.81-1.75-5.6-4.12H1.07v2.58A10 10 0 0 0 10 20Z" fill="#34A853"/>
                <path d="M4.4 11.9A5.97 5.97 0 0 1 4.08 10c0-.66.12-1.3.32-1.9V5.52H1.07A10 10 0 0 0 0 10c0 1.61.39 3.13 1.07 4.48L4.4 11.9Z" fill="#FBBC05"/>
                <path d="M10 3.98c1.47 0 2.79.5 3.83 1.5l2.86-2.86C14.96.9 12.7 0 10 0A10 10 0 0 0 1.07 5.52L4.4 8.1C5.19 5.73 7.4 3.98 10 3.98Z" fill="#EA4335"/>
            </svg>
            <span class="font-poppins font-medium text-base leading-6 text-[#1d1c17]">Google</span>
        </a>

        {{-- ── TERMS ── --}}
        <p class="font-inter font-medium text-[11px] leading-[14px] text-[#49473a] text-center">
            By entering, you agree to our
            <a href="#" class="underline underline-offset-1 hover:text-[#b30084] transition-colors">Terms</a>
            &amp;
            <a href="#" class="underline underline-offset-1 hover:text-[#b30084] transition-colors">Privacy</a>.
        </p>

    </div>
    {{-- /GLASS CARD --}}

</div>
{{-- /outer --}}

@endsection

@push('scripts')
<script>
function togglePwd(inputId, btn) {
    const input = document.getElementById(inputId);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    // Swap icon (eye / eye-off)
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
