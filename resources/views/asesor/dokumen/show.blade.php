<x-app-layout>
    <div class="container py-4">

        {{-- Navigasi Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('assesor.dokumen.index') }}">Tinjauan Dokumen Assesor</a>
                </li>
                @foreach ($breadcrumbs as $crumb)
                    <li class="breadcrumb-item"><a
                            href="{{ route('assesor.folder.show', $crumb->folder_id) }}">{{ $crumb->name }}</a></li>
                @endforeach
                <li class="breadcrumb-item active" aria-current="page">{{ $folder->name }}</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center mb-4">
            <i class="bi bi-folder-fill text-primary me-3 fs-1"></i>
            <div>
                <h2 class="mb-0">{{ $folder->name }}</h2>
                <p class="text-muted fs-7 mb-0">Menampilkan dokumen terverifikasi dari semua dosen.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button
                    type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button
                    type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                @if ($subfolders->isEmpty() && $documents->isEmpty())
                    <div class="text-center p-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <h5 class="mt-3">Folder Ini Kosong</h5>
                        <p class="text-muted">Belum ada dokumen terverifikasi di dalam folder ini.</p>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        {{-- Tampilkan Sub-folder --}}
                        @foreach ($subfolders as $subfolder)
                            <li
                                class="list-group-item list-group-item-action d-flex flex-wrap align-items-center justify-content-between gap-3 py-3">
                                <div class="d-flex align-items-center flex-grow-1 me-auto" style="min-width: 250px;">
                                    <i class="bi bi-folder-fill text-warning me-3 fs-4"></i>
                                    <div class="fw-bold">{{ $subfolder->name }}</div>
                                </div>
                                <a href="{{ route('assesor.folder.show', $subfolder->folder_id) }}"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Buka
                                </a>
                            </li>
                        @endforeach

                        {{-- Tampilkan Dokumen (Hanya yang Terverifikasi) --}}
                        @foreach ($documents as $document)
                            <li class="list-group-item d-flex flex-wrap align-items-center gap-3 py-3">
                                <div class="d-flex align-items-center flex-grow-1 me-auto" style="min-width: 250px;">
                                    <i class="bi bi-file-earmark-check me-3 fs-4 text-success"></i>
                                    <div>
                                        <strong class="d-block">{{ $document->file_name }}</strong>
                                        <small class="text-muted">Pemilik:
                                            {{ $document->user->name ?? 'Tidak diketahui' }}</small>
                                    </div>
                                </div>
                                <div class="btn-group flex-shrink-0" role="group">
                                    {{-- TOMBOL BARU --}}
                                    <a href="{{ route('assesor.document.preview', $document->id) }}" target="_blank"
                                        class="btn btn-outline-primary btn-sm" title="Lihat Dokumen">
                                        <i class="bi bi-eye"></i> Lihat
                                    </a>
                                    {{-- Tombol baru untuk Komentar --}}
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#commentsModal-{{ $document->id }}">
                                        <i class="bi bi-chat-left-text"></i> Komentar
                                        @if ($document->comments->count() > 0)
                                            <span
                                                class="badge bg-light text-dark ms-1">{{ $document->comments->count() }}</span>
                                        @endif
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal untuk Komentar Dokumen --}}
    @foreach ($documents as $document)
        <div class="modal fade" id="commentsModal-{{ $document->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Komentar untuk: {{ $document->file_name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Daftar Komentar yang Sudah Ada --}}
                        <div class="mb-4">
                            <h6>Diskusi</h6>
                            @forelse ($document->comments as $comment)
                                <div class="card mb-2 shadow-sm">
                                    <div class="card-body p-2">
                                        <p class="card-text mb-1">{{ $comment->content }}</p>
                                        <small class="text-muted">
                                            Oleh: <strong>{{ $comment->user->name ?? 'User Dihapus' }}</strong>
                                            pada {{ $comment->created_at->format('d M Y, H:i') }}
                                        </small>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">Belum ada komentar untuk dokumen ini.</p>
                            @endforelse
                        </div>
                        <hr>
                        {{-- Form untuk Menambah Komentar Baru --}}
                        <h6>Tinggalkan Komentar Baru</h6>
                        <form action="{{ route('assesor.document.comment.store', $document->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <textarea class="form-control" name="content" rows="4" placeholder="Tulis komentar Anda di sini..." required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Kirim Komentar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</x-app-layout>
