<x-app-layout>
    <div class="container py-4">

        {{-- Notifikasi Sukses atau Error --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        {{-- Header Halaman --}}
        <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-4">

            {{-- Judul Halaman (di kiri) --}}
            <h2 class="mb-3 mb-md-0">Manajemen Folder Publik</h2>

            {{-- Grup Tombol Aksi (di kanan) --}}
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#createFolderModal">
                    <i class="bi bi-folder-plus me-1"></i> Buat Folder Induk
                </button>

                @if ($commentCounts->sum() > 0)
                    <a href="{{ route('admin.dokumen.commented') }}" class="btn btn-info">
                        <i class="bi bi-chat-left-dots-fill me-1"></i> Lihat Dokumen Berkomentar
                    </a>
                @endif
            </div>

        </div>

        {{-- Daftar Folder --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse ($rootFolders as $folder)
                        <li
                            class="list-group-item d-flex flex-wrap align-items-center justify-content-between gap-3 py-3 px-3">
                            {{-- Informasi Folder --}}
                            <div class="d-flex align-items-center flex-grow-1 me-auto" style="min-width: 250px;">
                                <i class="bi bi-folder2-open me-3 fs-4 text-primary"></i>
                                <div>
                                    <span class="fw-bold fs-6">{{ $folder->name }}</span>
                                    {{-- Notifikasi jika ada dokumen yang butuh verifikasi di dalamnya --}}
                                    @if (isset($unverifiedCounts[$folder->folder_id]) && $unverifiedCounts[$folder->folder_id] > 0)
                                        <span class="badge bg-warning text-dark ms-1">
                                            Butuh Verifikasi
                                        </span>
                                    @endif
                                    @if (isset($commentCounts[$folder->folder_id]) && $commentCounts[$folder->folder_id] > 0)
                                        <span class="badge bg-info text-dark ms-1">Ada Komentar</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.folder.show', $folder->folder_id) }}"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Buka
                                </a>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editFolderModal-{{ $folder->id }}" title="Edit Nama Folder">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#deleteMasterFolderModal-{{ $folder->folder_id }}"
                                    title="Hapus Folder Permanen">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </li>

                        {{-- Modal Edit Nama Folder --}}
                        <div class="modal fade" id="editFolderModal-{{ $folder->id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Folder: {{ $folder->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.folder.update', $folder->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <label for="folder_name" class="form-label">Nama Folder Baru</label>
                                            <input type="text" class="form-control" name="folder_name"
                                                value="{{ $folder->name }}" required>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-warning">Perbarui</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Hapus Permanen --}}
                        <div class="modal fade" id="deleteMasterFolderModal-{{ $folder->folder_id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Konfirmasi Hapus Permanen</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="fs-5">Apakah Anda benar-benar yakin?</p>
                                        <p>Tindakan ini akan menghapus folder <strong>{{ $folder->name }}</strong>
                                            secara <strong>PERMANEN</strong> dari Google Drive dan sistem.</p>
                                        <p class="text-danger fw-bold">Semua sub-folder dan dokumen di dalamnya juga
                                            akan terhapus. Tindakan ini tidak dapat diurungkan.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('admin.folder.destroy', $folder->folder_id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Ya, Hapus Permanen</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty
                        {{-- Tampilan jika tidak ada folder --}}
                        <li class="list-group-item text-center p-5">
                            <i class="bi bi-folder-x fs-1 text-muted"></i>
                            <h5 class="mt-3">Belum Ada Folder Induk</h5>
                            <p class="text-muted">Silakan buat folder baru untuk memulai.</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- Memanggil Modal untuk membuat folder induk --}}
    @include('admin.dokumen.modal.modal')
</x-app-layout>
