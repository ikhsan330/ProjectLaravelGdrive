{{-- resources/views/auth/verify-email.blade.php --}}
<x-guest-layout>
    <h4 class="mb-3">Verifikasi Email Anda</h4>
    <h6 class="fw-light mb-4">
        Terima kasih telah mendaftar! Sebelum memulai, bisakah Anda memverifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan? Jika Anda tidak menerimanya, kami akan dengan senang hati mengirimkannya lagi.
    </h6>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-4">
            Link verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.
        </div>
    @endif

    <div class="mt-4 d-grid gap-2">
        {{-- Tombol Kirim Ulang Email --}}
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-lg font-weight-medium w-100">
                KIRIM ULANG EMAIL VERIFIKASI
            </button>
        </form>

        {{-- Tombol Log Out --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-lg font-weight-medium w-100">
                LOG OUT
            </button>
        </form>
    </div>
</x-guest-layout>
