{{-- resources/views/auth/forgot-password.blade.php --}}
<x-guest-layout>
    @if (session('status'))
        {{-- Menggunakan alert-success untuk feedback positif --}}
        <div class="alert alert-success mb-4">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <h4 class="mb-3">Lupa Password?</h4>
        <h6 class="fw-light mb-4">Tidak masalah. Masukkan email Anda dan kami akan mengirimkan link untuk mengatur ulang password.</h6>

        <div class="mb-3">
            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email"
                name="email" value="{{ old('email') }}" required autofocus placeholder="Alamat Email">
            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="mt-4 d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg font-weight-medium">
                KIRIM LINK RESET PASSWORD
            </button>
            <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg font-weight-medium">
                KEMBALI KE LOGIN
            </a>
        </div>
    </form>
</x-guest-layout>
