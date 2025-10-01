<x-app-layout>
    <div class="container py-4">

        {{-- Notifikasi --}}
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

        {{-- ========================================================== --}}
        {{-- BAGIAN HEADER HALAMAN UTAMA --}}
        {{-- ========================================================== --}}
        <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-4">
            <h2 class="mb-3 mb-md-0">Manajemen Folder Induk</h2>
            <div class="d-flex justify-content-start gap-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                    <i class="bi bi-folder-plus me-1"></i> Buat Folder
                </button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                    data-bs-target="#reassignFolderModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Tugaskan
                </button>
            </div>
        </div>

        {{-- ========================================================== --}}
        {{-- BAGIAN DAFTAR FOLDER (ACCORDION) --}}
        {{-- ========================================================== --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="accordion" id="folderAccordion">

                    @forelse ($groupedFolders as $folderName => $assignments)
                        @php
                            $firstFolder = $assignments->first();
                            $collapseId = 'collapse-' . Str::slug($folderName) . '-' . $firstFolder->folder_id;
                        @endphp

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-{{ $collapseId }}">
                                {{-- Header kini menggunakan flexbox untuk memisahkan tombol dan aksi --}}
                                <div class="d-flex align-items-center w-100 p-2">
                                    {{-- Bagian yang bisa di-klik untuk membuka/tutup --}}
                                    <button class="accordion-button collapsed flex-grow-1" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
                                        aria-expanded="false" aria-controls="{{ $collapseId }}">
                                        <i class="bi bi-folder2-open me-2 fs-5"></i>
                                        <span class="fw-bold fs-6 me-2">{{ $folderName }}</span>
                                        @if (isset($unverifiedCounts[$firstFolder->folder_id]) && $unverifiedCounts[$firstFolder->folder_id] > 0)
                                            <span class="badge bg-warning text-dark">
                                                Butuh Verifikasi
                                            </span>
                                        @endif
                                    </button>

                                    {{-- Bagian Metadata dan Tombol Aksi (terpisah) --}}
                                    <div class="d-flex align-items-center ps-3 ms-auto gap-3">
                                        <span class="badge bg-light text-dark border">{{ $assignments->count() }} Dosen</span>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editFolderModal-{{ $firstFolder->id }}" title="Edit Nama Folder">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteMasterFolderModal-{{ $firstFolder->folder_id }}" title="Hapus Folder Permanen">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </h2>

                            <div id="{{ $collapseId }}" class="accordion-collapse collapse"
                                aria-labelledby="heading-{{ $collapseId }}" data-bs-parent="#folderAccordion">
                                <div class="accordion-body p-0">
                                    <ul class="list-group list-group-flush">
                                        @foreach ($assignments as $folder)
                                            <li class="list-group-item d-flex justify-content-between align-items-center ps-4">
                                                <span class="text-muted"><i class="bi bi-person me-2"></i>{{ $folder->user_name }}</span>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.dosen.folder.show', ['dosen_id' => $folder->user_id, 'folder_id' => $folder->folder_id]) }}"
                                                        class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-box-arrow-in-right me-1"></i> Buka
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteFolderModal-{{ $folder->id }}" title="Hapus Penugasan">
                                                        <i class="bi bi-person-x"></i>
                                                    </button>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- SEMUA MODAL TETAP SAMA, TIDAK ADA PERUBAHAN --}}
                        {{-- ========================================================== --}}

                               {{-- Modal Edit Nama Folder --}}
                            <div class="modal fade" id="editFolderModal-{{ $firstFolder->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Folder: {{ $firstFolder->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.folder.update', $firstFolder->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <label for="folder_name" class="form-label">Nama Folder Baru</label>
                                                <input type="text" class="form-control" name="folder_name"
                                                    value="{{ $firstFolder->name }}" required>
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
                            <div class="modal fade" id="deleteMasterFolderModal-{{ $firstFolder->folder_id }}"
                                tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Konfirmasi Hapus Permanen</h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="fs-5">Apakah Anda benar-benar yakin?</p>
                                            <p>Tindakan ini akan menghapus folder
                                                <strong>{{ $firstFolder->name }}</strong> secara
                                                <strong>PERMANEN</strong> dari Google Drive dan menghapus penugasannya
                                                dari <strong>SEMUA</strong> dosen.
                                            </p>
                                            <p class="text-danger fw-bold">Tindakan ini tidak dapat diurungkan.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <form
                                                action="{{ route('admin.folder.destroy.master', $firstFolder->folder_id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Ya, Hapus
                                                    Permanen</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal Hapus Penugasan Folder --}}
                            @foreach ($assignments as $folder)
                                <div class="modal fade" id="deleteFolderModal-{{ $folder->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Hapus Penugasan Folder</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus folder
                                                <strong>{{ $folder->name }}</strong> dari dosen
                                                <strong>{{ $folder->user_name }}</strong>?
                                                <br><small class="text-muted">Tindakan ini hanya menghapus penugasan
                                                    dari database.</small>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('admin.folder.destroy', $folder->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                    @empty
                        {{-- Tampilan jika tidak ada folder sama sekali --}}
                        <div class="text-center p-5">
                            <i class="bi bi-folder-x fs-1 text-muted"></i>
                            <h5 class="mt-3">Belum Ada Folder</h5>
                            <p class="text-muted">Silakan buat folder baru untuk memulai.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- Pastikan path include modal sudah benar --}}
    @include('admin.dokumen.modal.modal')
</x-app-layout>
