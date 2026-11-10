@extends('layouts.auth')

@section('title', 'Verifikasi OTP - MNCU Future Leader Scholarship')

@section('content')
<div class="w-full">

    <!-- Mobile Logo (Small screens) -->
    <div class="lg:hidden mb-8">
        <a href="/" class="inline-flex items-center gap-2.5">
            <img src="{{ asset('icon/logoo.png') }}" alt="Logo MFLS" class="h-9 w-auto">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">MFLS Ajaran 2027</span>
        </a>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-500 flex items-center justify-center mb-5">
            <span class="iconify text-2xl" data-icon="solar:shield-check-bold-duotone"></span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            Verifikasi Email
        </h1>
        <p class="mt-2 text-sm text-slate-500 font-medium leading-relaxed">
            Kode OTP 6-digit telah dikirimkan ke alamat email: <br>
            <span class="font-bold text-slate-800">{{ $email }}</span>
        </p>
    </div>

    <!-- Session Alerts -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3 text-xs font-semibold">
            <span class="iconify text-lg text-emerald-600 flex-shrink-0" data-icon="solar:check-circle-bold"></span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl flex items-center gap-3 text-xs font-semibold">
            <span class="iconify text-lg text-rose-600 flex-shrink-0" data-icon="solar:danger-triangle-bold"></span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form Verifikasi OTP -->
    <form action="{{ route('verify.otp.post') }}" method="POST" class="space-y-6" id="otpForm">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">

        <div>
            <label for="otp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                Masukkan Kode OTP
            </label>
            <div class="relative group">
                <input type="text" 
                       name="otp" 
                       id="otp" 
                       maxlength="6" 
                       required 
                       autofocus
                       inputmode="numeric"
                       autocomplete="one-time-code"
                       class="w-full px-4 py-4 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 font-black text-2xl tracking-[0.5em] text-center placeholder:text-slate-300 placeholder:tracking-normal focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none transition-all"
                       placeholder="••••••">
            </div>
            @error('otp')
                <p class="mt-2 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-[11px] text-slate-400">
                Masukkan 6 angka yang tercantum pada pesan email masuk atau folder spam Anda.
            </p>
        </div>

        <button type="submit" 
                id="verifyBtn"
                class="w-full py-3.5 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-sm tracking-wide transition-all shadow-md hover:shadow-lg active:scale-[0.99] flex items-center justify-center gap-2 group">
            <span>Verifikasi & Masuk Dashboard</span>
            <span class="iconify text-lg group-hover:translate-x-0.5 transition-transform" data-icon="solar:arrow-right-linear"></span>
        </button>
    </form>

    <!-- Resend Section -->
    <div class="mt-8 pt-6 border-t border-slate-100 text-center">
        <p class="text-xs text-slate-500 font-medium mb-3">Tidak menerima email kode verifikasi?</p>
        
        <form action="{{ route('resend.otp') }}" method="POST" id="resendForm">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <button type="submit" 
                    id="resendBtn" 
                    class="text-amber-600 hover:text-amber-700 font-bold text-xs transition-colors inline-flex items-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="iconify text-base" data-icon="solar:restart-bold"></span>
                <span>Kirim Ulang Kode OTP</span>
            </button>
        </form>

        <div id="countdown" class="text-xs text-slate-400 mt-2 font-medium hidden">
            Kirim ulang dapat dilakukan dalam <span id="timer" class="font-bold text-slate-700">60</span> detik
        </div>
    </div>

    <!-- Back to Login -->
    <div class="mt-8 text-center">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-slate-700 transition-colors">
            <span class="iconify" data-icon="solar:arrow-left-linear"></span>
            <span>Kembali ke Halaman Masuk</span>
        </a>
    </div>

</div>

<script>
    // Hanya izinkan input angka pada field OTP
    const otpInput = document.getElementById('otp');
    otpInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Loading state saat submit verifikasi
    document.getElementById('otpForm').addEventListener('submit', function() {
        const btn = document.getElementById('verifyBtn');
        btn.disabled = true;
        btn.innerHTML = `<span class="iconify animate-spin text-lg" data-icon="solar:refresh-bold"></span> <span>Memverifikasi...</span>`;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
    });

    // Countdown kirim ulang OTP
    const resendBtn = document.getElementById('resendBtn');
    const countdown = document.getElementById('countdown');
    const timerSpan = document.getElementById('timer');
    let timeLeft = 60;

    document.getElementById('resendForm').addEventListener('submit', function() {
        resendBtn.disabled = true;
        countdown.classList.remove('hidden');
        let counter = setInterval(() => {
            timeLeft--;
            timerSpan.innerText = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(counter);
                resendBtn.disabled = false;
                countdown.classList.add('hidden');
                timeLeft = 60;
            }
        }, 1000);
    });
</script>
@endsection
