<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-info mb-3">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <h4 class="mb-3">Hello! let's get started</h4>
        <h6 class="fw-light mb-4">Sign in to continue.</h6>
        <div class="form-group">
            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Email">
            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
        <div class="form-group mt-3">
            <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Password">
            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
        <div class="my-2 d-flex justify-content-between align-items-center">
            <div class="form-check d-flex align-items-center" style="gap: 6px;">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember_me" style="margin-right:-30px;">
                    <label class="form-check-label text-muted mb-0" for="remember_me" style="padding-top:2px;">
                        Keep me signed in
                    </label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link text-black">Forgot password?</a>
            @endif
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">SIGN IN</button>
        </div>
    </form>
</x-guest-layout>
