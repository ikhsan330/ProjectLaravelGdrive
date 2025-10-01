<x-guest-layout>
    @if (session('status'))
        <div class="alert alert-info mb-4">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <h4 class="mb-3">Hello! let's get started</h4>
        <h6 class="fw-light mb-4">Sign in to continue.</h6>

        <div class="mb-3">
            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email"
                name="email" value="{{ old('email') }}" required autofocus placeholder="Email">
            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="mb-3">
            <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                id="password" name="password" required placeholder="Password">
            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="remember" id="remember_me">
                <label class="form-check-label text-muted" for="remember_me">
                    Keep me signed in
                </label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link text-black">Forgot password?</a>
            @endif
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg font-weight-medium">
                SIGN IN
            </button>
            <a href="/" class="btn btn-secondary btn-lg font-weight-medium">
                KEMBALI
            </a>
        </div>
    </form>
</x-guest-layout>
