{{-- resources/views/auth/reset-password.blade.php --}}
<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <h4 class="mb-3">Atur Ulang Password</h4>
        <h6 class="fw-light mb-4">Sekarang Anda dapat membuat password baru untuk akun Anda.</h6>

        <div class="mb-3">
            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email"
                name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="Alamat Email">
            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="mb-3">
            <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                id="password" name="password" required autocomplete="new-password" placeholder="Password Baru">
            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="mb-3">
            <input type="password" class="form-control form-control-lg" id="password_confirmation"
                name="password_confirmation" required autocomplete="new-password" placeholder="Konfirmasi Password Baru">
        </div>

        <div class="mt-4 d-grid">
            <button type="submit" class="btn btn-primary btn-lg font-weight-medium">
                RESET PASSWORD
            </button>
        </div>
    </form>
</x-guest-layout>
