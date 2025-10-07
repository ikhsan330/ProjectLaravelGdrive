<x-app-layout>
    <div class="container py-4">

        {{-- Navigasi Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('kaprodi.dokumen.index') }}">Tinjauan Dokumen Dosen</a></li>
                @foreach ($breadcrumbs as $crumb)
                    <li class="breadcrumb-item"><a href="{{ route('kaprodi.folder.show', $crumb->folder_id) }}">{{ $crumb->name }}</a></li>
                @endforeach
                <li class="breadcrumb-item active" aria-current="page">{{ $folder->name }}</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center mb-4">
            <i class="bi bi-folder-fill text-primary me-3 fs-1"></i>
            <div>
                <h2 class="mb-0">{{ $folder->name }}</h2>
                <p class="text-muted fs-7 mb-0">Menampilkan semua dokumen dari semua dosen di dalam folder ini.</p>
            </div>
        </div>

        {{-- Notifikasi --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        @endif
        @if(session('error'))
             <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                @if($subfolders->isEmpty() && $documents->isEmpty())
                    <div class="text-center p-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <h5 class="mt-3">Folder Ini Kosong</h5>
                        <p class="text-muted">Belum ada dosen yang mengunggah dokumen ke folder ini.</p>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        {{-- Tampilkan Sub-folder --}}
                        @foreach($subfolders as $subfolder)
                            <li class="list-group-item list-group-item-action d-flex flex-wrap align-items-center justify-content-between gap-3 py-3">
                                <div class="d-flex align-items-center flex-grow-1 me-auto" style="min-width: 250px;">
                                    <i class="bi bi-folder-fill text-warning me-3 fs-4"></i>
                                    <div>
                                        <span class="fw-bold">{{ $subfolder->name }}</span>
                                        {{-- MODIFIKASI: Menambahkan badge komentar untuk subfolder --}}
                                        @if ($subfolderCommentMap->contains($subfolder->folder_id))
                                            <span class="badge bg-info text-dark ms-1">Ada Komentar</span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('kaprodi.folder.show', $subfolder->folder_id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Buka
                                </a>
                            </li>
                        @endforeach

                        {{-- Tampilkan Dokumen --}}
                        @foreach ($documents as $document)
                            <li class="list-group-item d-flex flex-wrap align-items-center gap-3 py-3">
                                <div class="d-flex align-items-center flex-grow-1 me-auto" style="min-width: 250px;">
                                    <i class="bi bi-file-earmark-text me-3 fs-4"></i>
                                    <div>
                                        <strong class="d-block">{{ $document->file_name }}</strong>
                                        <small class="text-muted">Pemilik: {{ $document->user->name ?? 'Tidak diketahui' }}</small>

                                        @if ($document->comments->count() > 0)
                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle ms-1">
                                                {{ $document->comments->count() }} Komentar
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    @if ($document->verified)
                                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle"><i class="bi bi-check-circle-fill"></i> Terverifikasi</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="bi bi-hourglass-split"></i> Belum Diverifikasi</span>
                                    @endif
                                </div>
                                <div class="btn-group flex-shrink-0" role="group">
                                    @if ($document->comments->count() > 0)
                                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewCommentsModal-{{ $document->id }}" title="Lihat Komentar">
                                            <i class="bi bi-chat-left-dots"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('kaprodi.document.download', $document->id) }}" class="btn btn-outline-secondary btn-sm" title="Unduh"><i class="bi bi-download"></i></a>
                                    <a href="{{ route('kaprodi.document.preview', $document->id) }}" target="_blank" class="btn btn-outline-primary btn-sm" title="Lihat"><i class="bi bi-eye"></i></a>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#verifyDocumentModal-{{ $document->id }}" title="Verifikasi Dokumen">
                                        <i class="bi bi-check-circle"></i> Verifikasi
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal untuk Verifikasi Dokumen --}}
    @foreach ($documents as $document)
        <div class="modal fade" id="verifyDocumentModal-{{ $document->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Verifikasi Dokumen</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                    <form action="{{ route('kaprodi.document.verify', $document->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <p>Ubah status verifikasi untuk dokumen: <strong class="d-block">{{ $document->file_name }}</strong></p>
                            <p>Pemilik: <strong>{{ $document->user->name ?? 'Tidak Diketahui' }}</strong></p>
                            <div class="mb-3">
                                <label for="verified_{{ $document->id }}" class="form-label">Status Verifikasi</label>
                                <select class="form-select" name="verified" id="verified_{{ $document->id }}">
                                    <option value="1" @if($document->verified) selected @endif>Terverifikasi</option>
                                    <option value="0" @if(!$document->verified) selected @endif>Belum Diverifikasi</option>
                                </select>
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
    @endforeach

    {{-- Modal untuk Melihat Komentar --}}
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
