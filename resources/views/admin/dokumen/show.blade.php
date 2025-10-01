<x-app-layout>
    <div class="container py-4">

        {{-- ========================================================== --}}
        {{-- BAGIAN HEADER HALAMAN --}}
        {{-- ========================================================== --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                {{-- Tautan ke halaman utama folder (selalu ada) --}}
                <li class="breadcrumb-item"><a href="{{ route('admin.dokumen.index') }}">Folder Saya</a></li>

                {{-- Loop untuk setiap folder induk dalam hierarki --}}
                @foreach ($breadcrumbs as $crumb)
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dokumen.show', $crumb->folder_id) }}">{{ $crumb->name }}</a>
                    </li>
                @endforeach

                {{-- Folder yang sedang aktif (tidak bisa diklik) --}}
                <li class="breadcrumb-item active" aria-current="page">{{ $folder->name }}</li>
            </ol>
        </nav>

        <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-4">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <i class="bi bi-folder-fill text-primary me-2 fs-2"></i>
                <h2 class="mb-0 me-2">Isi Folder: {{ $folder->name }}</h2>
                @if ($mainFolderHasUnverified)
                    <span class="badge bg-warning text-dark align-middle">Butuh Verifikasi</span>
                @endif
            </div>
            <div class="d-flex justify-content-start gap-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#createFolderModal">
                    <i class="bi bi-folder-plus me-1"></i> Buat Sub-folder
                </button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                    data-bs-target="#uploadDocumentModal">
                    <i class="bi bi-upload me-1"></i> Upload Dokumen
                </button>
            </div>
        </div>

        {{-- ========================================================== --}}
        {{-- BAGIAN DAFTAR ISI FOLDER --}}
        {{-- ========================================================== --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                @if ($subfolders->isEmpty() && $documents->isEmpty())
                    {{-- Tampilan jika folder benar-benar kosong --}}
                    <div class="text-center p-5">
                        <i class="bi bi-folder2-open fs-1 text-muted"></i>
                        <h5 class="mt-3">Folder Ini Kosong</h5>
                        <p class="text-muted">Buat sub-folder baru atau upload dokumen untuk memulai.</p>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#uploadDocumentModal">
                            <i class="bi bi-upload me-1"></i> Upload Dokumen Sekarang
                        </button>
                    </div>
                @else
                    <ul class="list-group list-group-flush">

                        {{-- BAGIAN DAFTAR SUB-FOLDER --}}
                        @foreach ($subfolders as $subfolder)
                            <li
                                class="list-group-item list-group-item-action d-flex flex-wrap align-items-center gap-3 py-3">
                                {{-- Kolom 1: Informasi Utama --}}
                                <div class="d-flex align-items-center flex-grow-1 me-auto" style="min-width: 250px;">
                                    <i class="bi bi-folder-fill text-warning me-3 fs-4"></i>
                                    <div class="fw-bold">
                                        {{ $subfolder->name }}
                                        @if ($unverifiedSubfolderMap->contains($subfolder->folder_id))
                                            <span class="badge bg-warning text-dark ms-1 fw-normal">Butuh
                                                Verifikasi</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Kolom 2: Aksi --}}
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.dosen.folder.show', ['dosen_id' => $subfolder->user_id, 'folder_id' => $subfolder->folder_id]) }}"
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Buka
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#editSubfolderModal-{{ $subfolder->id }}"
                                        title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#deleteSubfolderModal-{{ $subfolder->id }}" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </div>
                            </li>
                        @endforeach

                        {{-- BAGIAN DAFTAR DOKUMEN --}}
                        @foreach ($documents as $document)
                            <li
                                class="list-group-item list-group-item-action d-flex flex-wrap align-items-center gap-3 py-3">
                                {{-- Kolom 1: Informasi Utama --}}
                                <div class="d-flex align-items-center flex-grow-1 me-auto" style="min-width: 250px;">
                                    <i class="bi bi-file-earmark-text me-3 fs-4"></i>
                                    <div>
                                        <strong class="d-block">{{ $document->file_name }}</strong>
                                        <small class="text-muted">{{ $document->name }}</small>
                                        @if ($document->user)
                                            <span class="badge bg-primary fw-normal ms-1">
                                                <i class="bi bi-person-check-fill"></i> {{ $document->user->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Kolom 2: Status Verifikasi --}}
                                <div class="flex-shrink-0">
                                    @if ($document->verified)
                                        <span
                                            class="badge bg-success text-success-emphasis border border-success-subtle">Terverifikasi</span>
                                    @else
                                        <span
                                            class="badge bg-warning text-warning-emphasis border border-warning-subtle">Belum
                                            Diverifikasi</span>
                                    @endif
                                </div>

                                {{-- Kolom 3: Aksi --}}
                                <div class="btn-group flex-shrink-0" role="group">
                                    <a href="{{ route('admin.dokumen.download', $document->id) }}"
                                        class="btn btn-outline-secondary btn-sm" title="Unduh"><i
                                            class="bi bi-download"></i></a>
                                    <a href="{{ route('admin.dokumen.show', $document->id) }}"
                                        class="btn btn-outline-primary btn-sm" title="Lihat"><i
                                            class="bi bi-eye"></i></a>
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#editDocumentModal-{{ $document->id }}"
                                        title="Edit"><i class="bi bi-pencil"></i></button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#deleteDocumentModal-{{ $document->id }}" title="Hapus"><i
                                            class="bi bi-trash"></i></button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

    </div>

    {{-- Pastikan path include modal sudah benar --}}
    @include('admin.dokumen.modal.modal-show')
</x-app-layout>
