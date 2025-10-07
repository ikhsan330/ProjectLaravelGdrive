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

        {{-- Navigasi Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dokumen.index') }}">Folder Publik</a></li>
                @foreach ($breadcrumbs as $crumb)
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.folder.show', $crumb->folder_id) }}">{{ $crumb->name }}</a>
                    </li>
                @endforeach
                <li class="breadcrumb-item active" aria-current="page">{{ $folder->name }}</li>
            </ol>
        </nav>

        {{-- Header Halaman --}}
        <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-4">
            <h2 class="mb-3 mb-md-0 d-flex align-items-center">
                <i class="bi bi-folder2-open me-2"></i> {{ $folder->name }}
            </h2>
            <div class="d-flex justify-content-start gap-2">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                    <i class="bi bi-upload me-1"></i> Upload Dokumen
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSubfolderModal">
                    <i class="bi bi-folder-plus me-1"></i> Buat Sub-folder
                </button>
            </div>
        </div>

        {{-- Daftar Isi Folder --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                @if ($subfolders->isEmpty() && $documents->isEmpty())
                    <div class="text-center p-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <h5 class="mt-3">Folder Ini Kosong</h5>
                        <p class="text-muted">Anda bisa membuat sub-folder atau mengunggah dokumen baru.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <tbody>
                                {{-- Daftar Sub-folder --}}
                                @foreach ($subfolders as $subfolder)
                                    <tr>
                                        <td class="w-100">
                                            <a href="{{ route('admin.folder.show', $subfolder->folder_id) }}" class="text-decoration-none text-dark d-flex align-items-center">
                                                <i class="bi bi-folder-fill me-3 fs-4 text-primary"></i>
                                                <div>
                                                    <span class="fw-bold">{{ $subfolder->name }}</span>
                                                     @if ($unverifiedSubfolderMap->contains($subfolder->folder_id))
                                                        <span class="badge bg-warning text-dark ms-1">Butuh Verifikasi</span>
                                                     @endif
                                                     {{-- MODIFIKASI: Menampilkan notifikasi komentar untuk subfolder --}}
                                                     @if ($subfolderCommentMap->contains($subfolder->folder_id))
                                                        <span class="badge bg-info text-dark ms-1">Ada Komentar</span>
                                                     @endif
                                                </div>
                                            </a>
                                        </td>
                                        <td class="text-nowrap">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.folder.show', $subfolder->folder_id) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-box-arrow-in-right me-1"></i> Buka
                                                </a>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editSubfolderModal-{{ $subfolder->id }}" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSubfolderModal-{{ $subfolder->folder_id }}" title="Hapus">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Daftar Dokumen --}}
                                @foreach ($documents as $document)
                                    <tr>
                                        <td class="w-100">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-text me-3 fs-4 text-muted"></i>
                                                <div>
                                                    <strong class="d-block">{{ $document->file_name }}</strong>
                                                    <small class="text-muted">Pemilik: {{ $document->user->name ?? 'Tidak diketahui' }}</small>
                                                    @if (!$document->verified)
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle ms-1">Belum Diverifikasi</span>
                                                    @else
                                                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle ms-1">Terverifikasi</span>
                                                    @endif
                                                    {{-- MODIFIKASI: Menampilkan jumlah komentar pada dokumen --}}
                                                    @if ($document->comments->count() > 0)
                                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle ms-1">
                                                            {{ $document->comments->count() }} Komentar
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-nowrap">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.dokumen.download', $document->id) }}" class="btn btn-outline-secondary btn-sm" title="Unduh"><i class="bi bi-download"></i></a>
                                                <a href="{{ route('admin.dokumen.show', $document->id) }}" target="_blank" class="btn btn-outline-primary btn-sm" title="Lihat"><i class="bi bi-eye"></i></a>

                                                {{-- BARU: Tombol untuk melihat komentar --}}
                                                @if ($document->comments->count() > 0)
                                                    <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewCommentsModal-{{ $document->id }}" title="Lihat Komentar">
                                                        <i class="bi bi-chat-left-dots"></i>
                                                    </button>
                                                @endif

                                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editDocumentModal-{{ $document->id }}" title="Edit"><i class="bi bi-pencil"></i></button>
                                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteDocumentModal-{{ $document->id }}" title="Hapus"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Memanggil Modal untuk membuat sub-folder dan upload dokumen --}}
    @include('admin.dokumen.modal.modal-show')

    {{-- Modal Edit dan Hapus untuk Subfolder --}}
    @foreach ($subfolders as $subfolder)
        <div class="modal fade" id="editSubfolderModal-{{ $subfolder->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.folder.update', $subfolder->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Sub-folder</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <label for="folder_name_{{ $subfolder->id }}" class="form-label">Nama Sub-folder Baru</label>
                            <input type="text" id="folder_name_{{ $subfolder->id }}" class="form-control" name="folder_name" value="{{ $subfolder->name }}" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="deleteSubfolderModal-{{ $subfolder->folder_id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Konfirmasi Hapus Sub-folder</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus sub-folder <strong>{{ $subfolder->name }}</strong> secara permanen?</p>
                        <p class="text-danger">Semua dokumen dan sub-folder di dalamnya juga akan terhapus.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form action="{{ route('admin.folder.destroy', $subfolder->folder_id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modal Edit dan Hapus untuk Dokumen --}}
    @foreach ($documents as $document)
        <div class="modal fade" id="editDocumentModal-{{ $document->id }}" tabindex="-1" aria-hidden="true">
             <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.dokumen.update', $document->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Dokumen: {{ $document->file_name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="file_name_{{ $document->id }}" class="form-label">Nama Dokumen</label>
                                <input type="text" id="file_name_{{ $document->id }}" class="form-control" name="file_name" value="{{ $document->file_name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="verified_{{ $document->id }}" class="form-label">Status Verifikasi</label>
                                <select name="verified" id="verified_{{ $document->id }}" class="form-select">
                                    <option value="1" @if($document->verified) selected @endif>Terverifikasi</option>
                                    <option value="0" @if(!$document->verified) selected @endif>Belum Diverifikasi</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="file_{{ $document->id }}" class="form-label">Ganti File (Opsional)</label>
                                <input type="file" id="file_{{ $document->id }}" class="form-control" name="file">
                                <small class="text-muted">Kosongkan jika tidak ingin mengganti file.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="deleteDocumentModal-{{ $document->id }}" tabindex="-1" aria-hidden="true">
             <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus dokumen <strong>{{ $document->file_name }}</strong> secara permanen?</p>
                        <p class="text-danger fw-bold">Tindakan ini tidak dapat diurungkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form action="{{ route('admin.dokumen.destroy', $document->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- BARU: Modal untuk Melihat Komentar Dokumen --}}
    @foreach ($documents as $document)
        @if ($document->comments->count() > 0)
            <div class="modal fade" id="viewCommentsModal-{{ $document->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Komentar untuk: {{ $document->file_name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @foreach ($document->comments as $comment)
                                <div class="card mb-2 shadow-sm">
                                    <div class="card-body p-2">
                                        <p class="card-text mb-1">{{ $comment->content }}</p>
                                        <small class="text-muted">
                                            Oleh: <strong>{{ $comment->user->name ?? 'User Dihapus' }}</strong> ({{ $comment->user->role ?? '' }})
                                            pada {{ $comment->created_at->format('d M Y, H:i') }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</x-app-layout>
