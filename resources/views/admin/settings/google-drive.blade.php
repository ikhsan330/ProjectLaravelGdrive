{{-- resources/views/settings/google-drive.blade.php --}}
<x-app-layout>
    {{-- Tambahan CSS untuk styling --}}
    @push('styles')
    <style>
        .settings-section {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            background-color: #fdfdfd;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pengaturan Sistem</h4>
                    <p class="card-description">
                        Kelola pengaturan integrasi untuk Google Drive dan Email (SMTP) di sini.
                    </p>

                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <form action="{{ route('admin.settings.google.update') }}" method="POST">
                        @csrf

                        {{-- ====================================================== --}}
                        {{-- BAGIAN PENGATURAN GOOGLE DRIVE --}}
                        {{-- ====================================================== --}}
                        <div class="settings-section">
                            <h5>Pengaturan Google Drive ⚙️</h5>
                            <hr class="mt-2 mb-4">

                            {{-- REKOMENDASI JIKA KREDENSIAL KOSONG --}}
                            @if (empty($settings->client_id) || empty($settings->client_secret))
                            <div class="alert alert-info" role="alert">
                                <h6 class="alert-heading">Butuh Kredensial Google Drive?</h6>
                                <p>Untuk mengintegrasikan Google Drive, Anda memerlukan Client ID dan Client Secret dari Google Cloud Platform.</p>
                                <hr>
                                <p class="mb-0">
                                    Anda dapat membuatnya melalui <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="alert-link">Google Cloud Console</a>. Pastikan Anda membuat **OAuth 2.0 Client ID** dan menambahkan URI redirect yang diizinkan jika diperlukan.
                                </p>
                            </div>
                            @endif


                            <div class="form-group">
                                <label for="client_id">Client ID</label>
                                <input type="text" class="form-control" id="client_id" name="client_id" placeholder="Masukkan Google Client ID" value="{{ old('client_id', $settings->client_id) }}" required>
                            </div>

                            {{-- LOGIKA UNTUK CLIENT SECRET --}}
                            <div class="form-group">
                                <label for="client_secret">Client Secret</label>
                                @if(!empty($settings->client_secret))
                                    <div class="input-group" id="client_secret_filled">
                                        <input type="text" class="form-control" value="••••••••••••••••••••••••" disabled>
                                        <button class="btn btn-outline-secondary toggle-edit-btn" type="button" data-target-input="client_secret_input_container">Ubah</button>
                                    </div>
                                    <div id="client_secret_input_container" style="display: none;">
                                        <input type="text" class="form-control" name="client_secret" placeholder="Masukkan Client Secret baru">
                                        <small class="form-text text-muted">Masukkan nilai baru untuk memperbarui. Biarkan kosong untuk tidak mengubah.</small>
                                    </div>
                                @else
                                    <input type="text" class="form-control" name="client_secret" placeholder="Masukkan Google Client Secret" required>
                                @endif
                            </div>

                            {{-- LOGIKA UNTUK REFRESH TOKEN --}}
                            <div class="form-group">
                                <label for="refresh_token">Refresh Token</label>
                                @if(!empty($settings->refresh_token))
                                    <div class="input-group" id="refresh_token_filled">
                                        <input type="text" class="form-control" value="••••••••••••••••••••••••" disabled>
                                        <button class="btn btn-outline-secondary toggle-edit-btn" type="button" data-target-input="refresh_token_input_container">Ubah</button>
                                    </div>
                                    <div id="refresh_token_input_container" style="display: none;">
                                        <input type="text" class="form-control" name="refresh_token" placeholder="Masukkan Refresh Token baru">
                                        <small class="form-text text-muted">Masukkan nilai baru untuk memperbarui.</small>
                                    </div>
                                @else
                                    <input type="text" class="form-control" name="refresh_token" placeholder="Masukkan Google Refresh Token" required>
                                @endif
                            </div>
                        </div>


                        {{-- ====================================================== --}}
                        {{-- BAGIAN PENGATURAN EMAIL (SMTP) --}}
                        {{-- ====================================================== --}}
                        <div class="settings-section">
                            <h5>Pengaturan Email (SMTP) 📧</h5>
                            <hr class="mt-2 mb-4">
                            <div class="form-group">
                                <label for="mail_username">Mail Username</label>
                                <input type="email" class="form-control" name="mail_username" placeholder="contoh: user@gmail.com" value="{{ old('mail_username', $settings->mail_username) }}">
                            </div>

                            {{-- LOGIKA UNTUK MAIL PASSWORD --}}
                            <div class="form-group">
                                <label for="mail_password">Mail Password</label>
                                @if(!empty($settings->mail_password))
                                    <div class="input-group" id="mail_password_filled">
                                        <input type="text" class="form-control" value="••••••••••••" disabled>
                                        <button class="btn btn-outline-secondary toggle-edit-btn" type="button" data-target-input="mail_password_input_container">Ubah</button>
                                    </div>
                                    <div id="mail_password_input_container" style="display: none;">
                                        <input type="password" class="form-control" name="mail_password" placeholder="Masukkan Password baru">
                                        <small class="form-text text-muted">Isi hanya jika Anda ingin mengubah password yang sudah tersimpan.</small>
                                    </div>
                                @else
                                    <input type="password" class="form-control" name="mail_password" placeholder="Masukkan Password Email">
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="mail_encryption">Mail Encryption</label>
                                <select class="form-control" name="mail_encryption">
                                    <option value="tls" {{ $settings->mail_encryption == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ $settings->mail_encryption == 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="mail_from_address">Mail From Address</label>
                                <input type="email" class="form-control" name="mail_from_address" placeholder="contoh: support@website.com" value="{{ old('mail_from_address', $settings->mail_from_address) }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Simpan Semua Pengaturan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk tombol "Ubah" --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-edit-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const targetContainerId = this.dataset.targetInput;
                    const targetContainer = document.getElementById(targetContainerId);

                    if (targetContainer) {
                        // Sembunyikan container "filled"
                        this.closest('.input-group').style.display = 'none';

                        // Tampilkan container input yang sebenarnya
                        targetContainer.style.display = 'block';

                        // Fokus ke input field yang baru ditampilkan
                        const inputField = targetContainer.querySelector('input');
                        if (inputField) {
                            inputField.focus();
                        }
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
