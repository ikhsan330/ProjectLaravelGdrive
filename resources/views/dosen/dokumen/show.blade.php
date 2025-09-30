<x-app-layout>
    <div class="container">
        <div class="form-container">

            {{-- Navigasi Breadcrumb untuk Dosen --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dosen.dokumen.index') }}">Folder Saya</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $folder->name }}</li>
                </ol>
            </nav>

            {{-- Header dengan tombol aksi untuk Dosen --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Isi Folder: {{ $folder->name }}</h2>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                        <i class="bi bi-folder-plus me-1"></i> Buat Sub-folder
                    </button>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                        <i class="bi bi-upload me-1"></i> Upload Dokumen
                    </button>
                </div>
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
                            {{-- Tampilkan Sub-folder --}}
                             @foreach($subfolders as $subfolder)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-folder-fill text-warning me-2"></i>
                                        <div class="fw-bold">{{ $subfolder->name }}</div>

                                        {{-- BAGIAN BARU: Badge Notifikasi untuk Sub-folder --}}
                                        @if($subfolder->unverified_documents_count > 0)
                                            <span class="badge rounded-pill bg-warning text-dark ms-2">
                                                {{ $subfolder->unverified_documents_count }} item perlu diperiksa
                                            </span>
                                        @endif
                                    </div>

                                    <div class="btn-group" role="group">
                                        <a href="{{ route('dosen.dokumen.show', $subfolder->folder_id) }}" class="btn btn-info btn-sm"><i class="bi bi-arrow-right-circle me-1"></i> Buka</a>
                                        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editSubfolderModal-{{ $subfolder->id }}"><i class="bi bi-pencil-square"></i> Edit</button>
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSubfolderModal-{{ $subfolder->id }}"><i class="bi bi-trash-fill"></i> Hapus</button>
                                    </div>
                                </li>
                            @endforeach

                            {{-- Tampilkan Dokumen --}}
                            @foreach ($documents as $document)
                                <li class="list-group-item d-flex flex-wrap align-items-center">
                                    <div class="col-12 col-md-5 mb-2 mb-md-0 d-flex align-items-center">
                                        <i class="bi bi-file-earmark-text me-2 fs-4"></i>
                                        <div><strong>{{ $document->file_name }}</strong><br><small class="text-muted">{{ $document->name }}</small></div>
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
                                            <a href="{{ route('dosen.document.download', $document->id) }}" class="btn btn-outline-success btn-sm" title="Unduh"><i class="bi bi-download"></i></a>
                                            <a href="{{ route('dosen.document.show', $document->id) }}" class="btn btn-outline-info btn-sm" title="Lihat"><i class="bi bi-eye"></i></a>
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


<div class="modal fade" id="createFolderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Buat Sub-folder di "{{ $folder->name }}"</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <form action="{{ route('dosen.folder.store-subfolder') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label for="folder_name_create" class="form-label">Nama Folder Baru</label><input type="text" class="form-control" id="folder_name_create" name="folder_name" placeholder="Contoh: Dokumen Mahasiswa" required></div>
                    <input type="hidden" name="parent_folder_id" value="{{ $folder->folder_id }}">
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Buat Folder</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Upload Dokumen ke "{{ $folder->name }}"</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <form action="{{ route('dosen.document.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="folderid" value="{{ $folder->folder_id }}">
                    <div class="mb-3"><label for="file_name_upload" class="form-label">Nama Dokumen</label><input type="text" class="form-control" id="file_name_upload" name="file_name" placeholder="Contoh: Laporan Bulanan" required><small class="form-text text-muted">Ini adalah nama yang akan ditampilkan di sistem.</small></div>
                    <div class="mb-3"><label for="file_upload" class="form-label">Pilih File</label><input type="file" class="form-control" id="file_upload" name="file" required></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-success">Upload</button></div>
            </form>
        </div>
    </div>
</div>

@foreach ($subfolders as $subfolder)
    {{-- MODAL EDIT SUB-FOLDER (LENGKAP) --}}
    <div class="modal fade" id="editSubfolderModal-{{ $subfolder->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Nama Sub-folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('dosen.folder.update', $subfolder->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="folder_name_{{ $subfolder->id }}" class="form-label">Nama Folder Baru</label>
                            <input type="text" class="form-control" name="folder_name" id="folder_name_{{ $subfolder->id }}" value="{{ $subfolder->name }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL HAPUS SUB-FOLDER (LENGKAP) --}}
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
                    <form action="{{ route('dosen.folder.destroy', $subfolder->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Ya, Hapus Permanen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- Modals untuk Dokumen --}}
@foreach ($documents as $document)
    <div class="modal fade" id="editDocumentModal-{{ $document->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Dokumen</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <form action="{{ route('dosen.document.update', $document->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3"><label for="file_name_{{ $document->id }}" class="form-label">Nama Dokumen</label><input type="text" class="form-control" id="file_name_{{ $document->id }}" name="file_name" value="{{ $document->file_name }}" required></div>
                        <div class="mb-3"><label class="form-label">Status Verifikasi</label><input type="text" class="form-control" value="{{ $document->verified ? 'Terverifikasi' : 'Belum Diverifikasi' }}" disabled readonly></div>
                        <div class="mb-3"><label for="file_{{ $document->id }}" class="form-label">Ganti File (Opsional)</label><input type="file" class="form-control" id="file_{{ $document->id }}" name="file"><small class="text-muted">Kosongkan jika tidak ingin mengganti file.</small></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-warning">Perbarui</button></div>
                </form>
            </div>
        </div>
    </div>

     <div class="modal fade" id="deleteDocumentModal-{{ $document->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Konfirmasi Hapus Dokumen</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus dokumen <strong>{{ $document->file_name }}</strong>?</p>
                    <p class="text-danger fw-bold">Tindakan ini akan menghapusnya secara permanen dari Google Drive dan tidak dapat diurungkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('dosen.document.destroy', $document->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Ya, Hapus Permanen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

</x-app-layout>
