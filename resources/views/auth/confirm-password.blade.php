{{-- resources/views/auth/confirm-password.blade.php --}}
<x-guest-layout>
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <h4 class="mb-3">Konfirmasi Password</h4>
        <h6 class="fw-light mb-4">Ini adalah area aman. Mohon konfirmasi password Anda sebelum melanjutkan.</h6>

        <div class="mb-3">
            <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                id="password" name="password" required autocomplete="current-password" placeholder="Password">
            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="mt-4 d-grid">
            <button type="submit" class="btn btn-primary btn-lg font-weight-medium">
                KONFIRMASI
            </button>
        </div>
    </form>
</x-guest-layout>
