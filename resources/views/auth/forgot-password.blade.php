@extends('layouts.auth')
@section('title', 'Reset Password — SyncFlow')

@section('content')

{{--  PAGE BODY --}}
<div class="relative w-full max-w-[440px] mx-auto">

    <div class="blob blob-yellow"></div>
    <div class="blob blob-pink"></div>

    <div class="glass-card relative z-10 w-full
                border-2 border-[#cbc7b6] rounded-xl overflow-hidden
                flex flex-col gap-6
                p-[34px] sm:p-[34px]">

        {{--  HEADER  --}}
        <div class="flex flex-col items-center gap-2 pb-2">
            <div class="w-16 h-16 flex items-center justify-center">
                <img src="{{ asset('assets/logo.png') }}" alt="Logo SyncFlow" class="w-full h-full object-contain">
            </div>
            <h1 class="font-montserrat font-bold text-2xl leading-8 text-[#656026] text-center mt-2">
                Reset Password
            </h1>
            <p class="font-inter text-sm leading-5 text-[#49473a] text-center">
                Masukkan email kampusmu dan buat password baru langsung di sini.
            </p>
        </div>

        {{-- Pesan Error --}}
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

        {{--  FORM  --}}
        <form method="POST" action="{{ route('password.update.direct') }}" class="flex flex-col gap-4 pt-2" novalidate>
            @csrf

            {{-- Email Address --}}
            <div class="flex flex-col gap-1">
                <label class="font-inter font-semibold text-xs leading-4 text-[#1d1c17] uppercase tracking-[0.6px]">
                    Email Address
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="you@campus.edu"
                       class="sf-input w-full bg-[#fdf9f0] border-2 border-[#cbc7b6] rounded-lg
                              px-[18px] py-3.5 font-inter text-sm text-[#1d1c17]
                              placeholder:text-[rgba(73,71,58,0.5)] transition focus:outline-none focus:border-[#b30084]
                              {{ $errors->has('email') ? 'border-red-500' : '' }}">
            </div>

            {{-- New Password --}}
            <div class="flex flex-col gap-1">
                <label class="font-inter font-semibold text-xs leading-4 text-[#1d1c17] uppercase tracking-[0.6px]">
                    Password Baru
                </label>
                <input type="password" name="password" required
                       placeholder="Minimal 8 karakter"
                       class="sf-input w-full bg-[#fdf9f0] border-2 border-[#cbc7b6] rounded-lg
                              px-[18px] py-3.5 font-inter text-sm text-[#1d1c17]
                              placeholder:text-[rgba(73,71,58,0.5)] transition focus:outline-none focus:border-[#b30084]
                              {{ $errors->has('password') ? 'border-red-500' : '' }}">
            </div>

            {{-- Confirm Password --}}
            <div class="flex flex-col gap-1">
                <label class="font-inter font-semibold text-xs leading-4 text-[#1d1c17] uppercase tracking-[0.6px]">
                    Konfirmasi Password
                </label>
                <input type="password" name="password_confirmation" required
                       placeholder="Ulangi password baru"
                       class="sf-input w-full bg-[#fdf9f0] border-2 border-[#cbc7b6] rounded-lg
                              px-[18px] py-3.5 font-inter text-sm text-[#1d1c17]
                              placeholder:text-[rgba(73,71,58,0.5)] transition focus:outline-none focus:border-[#b30084]">
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 mt-2
                           bg-[#b30084] hover:bg-[#8c0067] active:scale-[0.98]
                           border-2 border-transparent rounded-lg
                           px-[18px] py-[12px]
                           font-poppins font-medium text-base leading-6 text-white
                           transition duration-150">
                Ganti Password
            </button>
        </form>

        {{--  BACK TO LOGIN  --}}
        <div class="text-center mt-1">
            <a href="{{ route('login') }}"
               class="inline-flex items-center justify-center gap-1.5 font-inter font-medium text-sm text-[#49473a] hover:text-[#b30084] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Batal & Kembali ke Log In
            </a>
        </div>

    </div>
</div>
@endsection