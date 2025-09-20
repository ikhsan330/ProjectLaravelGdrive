<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Information & Delete User -->
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
                    <p class="mt-1 text-sm text-gray-600">Update your account's profile information and email address.</p>
                </header>
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white text-center">
                        <h5 class="mb-0">Edit Profil</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('patch')
                            <div class="row mb-4">
                                <div class="col-12 text-center">
                                    <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('images/default.jpg') }}" class="rounded-circle border border-5 border-primary shadow mb-4" style="width:150px; height:150px; object-fit:cover;" id="photoPreview">
                                </div>
                                <div class="col-12 text-center">
                                    <label for="photo" class="form-label fw-bold">Foto Profil</label>
                                    <input type="file" class="form-control w-auto d-inline-block" id="photo" name="photo" accept="image/*" onchange="previewPhoto(event)">
                                    @error('photo')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="name" class="form-label fw-bold">Nama</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                                    @error('name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                                    @error('email')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="role" class="form-label fw-bold">Role</label>
                                    <input type="text" class="form-control" id="role" name="role" value="{{ ucfirst($user->role) }}" readonly>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                                <button type="button" class="btn btn-outline-warning px-4" data-bs-toggle="modal" data-bs-target="#modalUpdatePassword">Ubah Password</button>
                            </div>
                        </form>
                        <hr class="my-4">
                        <!-- Delete User (gabung di bawah form edit profil) -->
                        <div class="mt-4">
                            <p class="mb-3">Setelah akun dihapus, semua data akan hilang permanen. Pastikan Anda sudah backup data penting sebelum melanjutkan.</p>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteUser">Hapus Akun</button>
                            <!-- Modal -->
                            <div class="modal fade" id="modalDeleteUser" tabindex="-1" aria-labelledby="modalDeleteUserLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="post" action="{{ route('profile.destroy') }}">
                                            @csrf
                                            @method('delete')
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalDeleteUserLabel">Konfirmasi Hapus Akun</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Masukkan password untuk konfirmasi penghapusan akun:</p>
                                                <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
                                                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Hapus Akun</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Update Password Modal -->
            <div class="modal fade" id="modalUpdatePassword" tabindex="-1" aria-labelledby="modalUpdatePasswordLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="post" action="{{ route('password.update') }}">
                            @csrf
                            @method('put')
                            <div class="modal-header bg-white text-black">
                                <h5 class="modal-title" id="modalUpdatePasswordLabel">Ubah Password</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="update_password_current_password" class="form-label fw-bold">Password Saat Ini</label>
                                    <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" />
                                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                                </div>
                                <div class="mb-3">
                                    <label for="update_password_password" class="form-label fw-bold">Password Baru</label>
                                    <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                                </div>
                                <div class="mb-3">
                                    <label for="update_password_password_confirmation" class="form-label fw-bold">Konfirmasi Password Baru</label>
                                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                                </div>
                                @if (session('status') === 'password-updated')
                                    <p class="text-success mt-3">Password berhasil diubah.</p>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-warning">Simpan Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <script>
            function previewPhoto(event) {
                const reader = new FileReader();
                reader.onload = function(){
                    document.getElementById('photoPreview').src = reader.result;
                };
                reader.readAsDataURL(event.target.files[0]);
            }
            </script>
        </div>
    </div>
</x-app-layout>
