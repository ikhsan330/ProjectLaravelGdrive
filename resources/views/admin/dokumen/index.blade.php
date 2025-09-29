<x-app-layout>
    <div class="container">
        <div class="form-container">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- TOMBOL AKSI UTAMA YANG SUDAH DIPISAH DAN DIRAPIKAN --}}
            <div class="d-flex justify-content-start gap-2 mb-3">
                {{-- Tombol untuk memicu modal Buat Folder Baru --}}
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                    <i class="bi bi-folder-plus me-1"></i> Buat Folder Baru
                </button>
                {{-- Tombol untuk memicu modal Tugaskan Folder --}}
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#reassignFolderModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Tugaskan Folder
                </button>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h4>Manajemen Folder Induk</h4>
                </div>
                <div class="card-body">

                    {{-- Awal dari struktur Accordion (Tidak ada perubahan di sini) --}}
                    <div class="accordion" id="folderAccordion">

                        @forelse ($groupedFolders as $folderName => $assignments)
                            @php
                                $firstFolder = $assignments->first();
                                $collapseId = 'collapse-' . Str::slug($folderName) . '-' . $firstFolder->folder_id;
                            @endphp

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-{{ $collapseId }}">
                                    <div class="d-flex align-items-center w-100">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                                            <i class="bi bi-folder-fill me-2 text-primary"></i>
                                            <span class="fw-bold">{{ $folderName }}</span>
                                        </button>
                                        <div class="d-flex align-items-center ps-3 pe-3 border-start">
                                            <span class="badge bg-secondary me-3">{{ $assignments->count() }} Dosen</span>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editFolderModal-{{ $firstFolder->id }}">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteMasterFolderModal-{{ $firstFolder->folder_id }}">
                                                    <i class="bi bi-trash-fill"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </h2>
                                <div id="{{ $collapseId }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $collapseId }}" data-bs-parent="#folderAccordion">
                                    <div class="accordion-body p-0">
                                        <ul class="list-group list-group-flush">
                                            @foreach ($assignments as $folder)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span><i class="bi bi-person me-2"></i>{{ $folder->user_name }}</span>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.dosen.folder.show', ['dosen_id' => $folder->user_id, 'folder_id' => $folder->folder_id]) }}" class="btn btn-info btn-sm">
                                                            <i class="bi bi-eye"></i> Lihat
                                                        </a>
                                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteFolderModal-{{ $folder->id }}">
                                                            <i class="bi bi-x-circle"></i> Hapus Tugas
                                                        </button>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal Edit Nama Folder --}}
                            <div class="modal fade" id="editFolderModal-{{ $firstFolder->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Folder: {{ $firstFolder->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.folder.update', $firstFolder->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <label for="folder_name" class="form-label">Nama Folder Baru</label>
                                                <input type="text" class="form-control" name="folder_name" value="{{ $firstFolder->name }}" required>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-warning">Perbarui</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal Hapus Permanen --}}
                            <div class="modal fade" id="deleteMasterFolderModal-{{ $firstFolder->folder_id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Konfirmasi Hapus Permanen</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="fs-5">Apakah Anda benar-benar yakin?</p>
                                            <p>Tindakan ini akan menghapus folder <strong>{{ $firstFolder->name }}</strong> secara <strong>PERMANEN</strong> dari Google Drive dan menghapus penugasannya dari <strong>SEMUA</strong> dosen.</p>
                                            <p class="text-danger fw-bold">Tindakan ini tidak dapat diurungkan.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('admin.folder.destroy.master', $firstFolder->folder_id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Ya, Hapus Permanen</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal Hapus Penugasan Folder --}}
                            @foreach ($assignments as $folder)
                                <div class="modal fade" id="deleteFolderModal-{{ $folder->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Hapus Penugasan Folder</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus folder <strong>{{ $folder->name }}</strong> dari dosen <strong>{{ $folder->user_name }}</strong>?
                                                <br><small class="text-muted">Tindakan ini hanya menghapus penugasan dari database.</small>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('admin.folder.destroy', $folder->id) }}" method="POST">
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
                            <div class="text-center p-4">
                                <p class="mb-0">Tidak ada folder yang ditemukan.</p>
                            </div>
                        @endforelse

                    </div>
                    {{-- Akhir dari struktur Accordion --}}

                </div>
            </div>

        </div>
    </div>
@include('admin.dokumen.modal')
</x-app-layout>
