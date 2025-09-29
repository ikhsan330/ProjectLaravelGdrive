<x-app-layout>
    <div class="container">
        <div class="form-container">

            {{-- Bagian Navigasi Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dokumen.index') }}">Daftar Folder</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $folder->name }}</li>
                </ol>
            </nav>

            {{-- Bagian Header dan Tombol Aksi Utama --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Isi Folder: {{ $folder->name }}</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                    <i class="bi bi-folder-plus me-1"></i> Buat Sub-folder
                </button>
            </div>

            {{-- Card untuk Daftar Isi Folder --}}
            <div class="card shadow-sm">
                <div class="card-body p-0">

                    @if($subfolders->isEmpty() && $documents->isEmpty())
                        <div class="text-center p-5">
                            <i class="bi bi-folder2-open" style="font-size: 3rem;"></i>
                            <p class="mt-3 mb-0">Folder ini kosong.</p>
                        </div>
                    @else
                        <ul class="list-group list-group-flush">

                            {{-- Tampilkan Sub-folder dengan tombol aksi baru --}}
                            @foreach($subfolders as $subfolder)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="fw-bold">
                                        <i class="bi bi-folder-fill text-warning me-2"></i>
                                        {{ $subfolder->name }}
                                    </div>
                                    {{-- Grup Tombol Aksi untuk Sub-folder --}}
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.dosen.folder.show', ['dosen_id' => $subfolder->user_id, 'folder_id' => $subfolder->folder_id]) }}" class="btn btn-info btn-sm">
                                            <i class="bi bi-arrow-right-circle me-1"></i> Buka
                                        </a>
                                        {{-- Tombol Edit Baru --}}
                                        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editSubfolderModal-{{ $subfolder->id }}">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                        {{-- Tombol Hapus Baru --}}
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSubfolderModal-{{ $subfolder->id }}">
                                            <i class="bi bi-trash-fill"></i> Hapus
                                        </button>
                                    </div>
                                </li>

                                {{-- Modal untuk Edit Sub-folder --}}
                                <div class="modal fade" id="editSubfolderModal-{{ $subfolder->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Nama Sub-folder</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            {{-- Form ini akan menggunakan route 'admin.folder.update' yang sudah ada --}}
                                            <form action="{{ route('admin.folder.update', $subfolder->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <label for="folder_name_{{ $subfolder->id }}" class="form-label">Nama Folder Baru</label>
                                                    <input type="text" class="form-control" name="folder_name" id="folder_name_{{ $subfolder->id }}" value="{{ $subfolder->name }}" required>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning">Perbarui</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal untuk Hapus Sub-folder --}}
                                <div class="modal fade" id="deleteSubfolderModal-{{ $subfolder->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Konfirmasi Hapus Sub-folder</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menghapus sub-folder <strong>{{ $subfolder->name }}</strong>?</p>
                                                <p class="text-danger fw-bold">Tindakan ini akan menghapusnya secara permanen dari Google Drive dan tidak dapat diurungkan.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                {{-- Form ini akan menggunakan route 'admin.subfolder.destroy' yang baru dibuat --}}
                                                <form action="{{ route('admin.subfolder.destroy', $subfolder->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Ya, Hapus Permanen</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Kemudian tampilkan Dokumen (tidak ada perubahan di bagian ini) --}}
                            @foreach ($documents as $document)
                                <li class="list-group-item d-flex flex-wrap align-items-center">
                                    <div class="col-12 col-md-5 mb-2 mb-md-0">
                                        <i class="bi bi-file-earmark-text me-2"></i>
                                        <div>
                                            <strong>{{ $document->file_name }}</strong><br>
                                            <small class="text-muted">{{ $document->name }}</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 text-md-center">
                                        @if ($document->verified)
                                            <span class="badge bg-success">Terverifikasi</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Belum Diverifikasi</span>
                                        @endif
                                    </div>
                                    <div class="col-6 col-md-4 text-md-end">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.dokumen.download', $document->id) }}" class="btn btn-outline-success btn-sm" title="Unduh"><i class="bi bi-download"></i></a>
                                            <a href="{{ route('admin.dokumen.show', $document->id) }}" class="btn btn-outline-info btn-sm" title="Lihat"><i class="bi bi-eye"></i></a>
                                            <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editDocumentModal-{{ $document->id }}" title="Edit"><i class="bi bi-pencil"></i></button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteDocumentModal-{{ $document->id }}" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Modal untuk Membuat Sub-folder (tidak berubah) --}}
<div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel" aria-hidden="true">
    {{-- Isi modal ini sama seperti sebelumnya --}}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createFolderModalLabel">Buat Sub-folder di "{{ $folder->name }}"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.folder.store-subfolder') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="folder_name" class="form-label">Nama Folder Baru</label>
                        <input type="text" class="form-control" id="folder_name" name="folder_name" placeholder="Contoh: Dokumen Mahasiswa" required>
                    </div>
                    <input type="hidden" name="parent_folder_id" value="{{ $folder->folder_id }}">
                    <input type="hidden" name="parent_dosen_id" value="{{ $folder->user_id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Buat Folder</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- INGAT: Anda juga perlu menyertakan kode untuk modal 'editDocumentModal' dan 'deleteDocumentModal' agar tombol edit/hapus pada dokumen berfungsi. --}}
