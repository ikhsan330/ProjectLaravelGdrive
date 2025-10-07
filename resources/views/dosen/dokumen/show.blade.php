<x-app-layout>
    <div class="container py-4">

        {{-- Navigasi Breadcrumb Dinamis (sudah benar) --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dosen.dokumen.index') }}">Folder Publik</a></li>
                @foreach($breadcrumbs as $crumb)
                    <li class="breadcrumb-item">
                        <a href="{{ route('dosen.folder.show', $crumb->folder_id) }}">{{ $crumb->name }}</a>
                    </li>
                @endforeach
                <li class="breadcrumb-item active" aria-current="page">{{ $folder->name }}</li>
            </ol>
        </nav>

        <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-4">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <i class="bi bi-folder-fill text-primary me-3 fs-1"></i>
                <h2 class="mb-0">Isi Folder: {{ $folder->name }}</h2>
            </div>
            <div class="d-flex justify-content-start gap-2">
                {{-- PERUBAHAN: Tombol "Buat Sub-folder" DIHAPUS karena dosen tidak bisa membuat folder --}}
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                    <i class="bi bi-upload me-1"></i> Upload Dokumen
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                @if($subfolders->isEmpty() && $documents->isEmpty())
                    <div class="text-center p-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <h5 class="mt-3">Folder Ini Kosong</h5>
                        <p class="text-muted">Anda dapat mengunggah dokumen pertama Anda di sini.</p>
                        <button type="button" class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                            <i class="bi bi-upload me-1"></i> Upload Dokumen Sekarang
                        </button>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        {{-- Tampilkan Sub-folder --}}
                        @foreach($subfolders as $subfolder)
                            <li class="list-group-item list-group-item-action d-flex flex-wrap align-items-center justify-content-between gap-3 py-3">
                                <div class="d-flex align-items-center flex-grow-1 me-auto" style="min-width: 250px;">
                                    <i class="bi bi-folder-fill text-warning me-3 fs-4"></i>
                                    <div class="fw-bold">{{ $subfolder->name }}</div>
                                </div>
                                <div class="btn-group flex-shrink-0" role="group">
                                    <a href="{{ route('dosen.folder.show', $subfolder->folder_id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Buka
                                    </a>
                                    {{-- PERUBAHAN: Tombol "Edit" dan "Hapus" Sub-folder DIHAPUS --}}
                                </div>
                            </li>
                        @endforeach

                        {{-- Tampilkan Dokumen --}}
                        @foreach ($documents as $document)
                            <li class="list-group-item d-flex flex-wrap align-items-center gap-3 py-3">
                                <div class="d-flex align-items-center flex-grow-1 me-auto" style="min-width: 250px;">
                                    <i class="bi bi-file-earmark-text me-3 fs-4"></i>
                                    <div>
                                        <strong class="d-block">{{ $document->file_name }}</strong>
                                        {{-- Tampilkan nama pemilik dokumen --}}
                                        <small class="text-muted">Pemilik: {{ $document->user->name ?? 'Tidak diketahui' }}</small>
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
                                    {{-- Tombol Lihat & Download selalu bisa diakses --}}
                                    <a href="{{ route('dosen.document.download', $document->id) }}" class="btn btn-outline-secondary btn-sm" title="Unduh"><i class="bi bi-download"></i></a>
                                    <a href="{{ route('dosen.document.show', $document->id) }}" target="_blank" class="btn btn-outline-primary btn-sm" title="Lihat"><i class="bi bi-eye"></i></a>

                                    {{-- PERUBAHAN KUNCI: Tombol Edit & Hapus hanya muncul untuk dokumen milik sendiri --}}
                                    @if(Auth::check() && Auth::id() == $document->user_id)
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editDocumentModal-{{ $document->id }}" title="Edit"><i class="bi bi-pencil"></i></button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteDocumentModal-{{ $document->id }}" title="Hapus"><i class="bi bi-trash"></i></button>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- Memanggil file modal yang terpisah --}}
    @include('dosen.dokumen.modal')
</x-app-layout>
