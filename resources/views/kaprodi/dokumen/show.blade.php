<x-app-layout>
    <div class="container py-4">

        {{-- ========================================================== --}}
        {{-- BAGIAN HEADER HALAMAN --}}
        {{-- ========================================================== --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('kaprodi.dokumen.index') }}">Manajemen Dokumen Dosen</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $folder->name }}</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center mb-4">
            <i class="bi bi-folder-fill text-primary me-3 fs-1"></i>
            <div>
                <h2 class="mb-0">{{ $folder->name }}</h2>
                <p class="text-muted fs-7 mb-0">Milik Dosen: <strong>{{ $folder->user->name }}</strong></p>
            </div>
        </div>

        {{-- Notifikasi --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
             <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- ========================================================== --}}
        {{-- BAGIAN DAFTAR ISI FOLDER --}}
        {{-- ========================================================== --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                @if($subfolders->isEmpty() && $documents->isEmpty())
                    <div class="text-center p-5">
                        <i class="bi bi-file-earmark-zip fs-1 text-muted"></i>
                        <h5 class="mt-3">Folder Ini Masih Kosong</h5>
                        <p class="text-muted">Dosen belum mengunggah dokumen apapun ke folder ini.</p>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        {{-- Tampilkan Sub-folder --}}
                        @foreach($subfolders as $subfolder)
                            <li class="list-group-item list-group-item-action d-flex flex-wrap align-items-center gap-3 py-3">
                                {{-- Informasi Utama Subfolder --}}
                                <div class="d-flex align-items-center flex-grow-1 me-auto" style="min-width: 250px;">
                                    <i class="bi bi-folder-fill text-warning me-3 fs-4"></i>
                                    <div class="fw-bold">
                                        {{ $subfolder->name }}
                                        @if($subfolder->unverified_documents_count > 0)
                                            <span class="badge bg-warning text-warning-emphasis border border-warning ms-2">
                                                {{ $subfolder->unverified_documents_count }} item perlu diperiksa
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                {{-- Tombol Aksi Subfolder --}}
                                <a href="{{ route('kaprodi.dokumen.show', ['dosen_id' => $subfolder->user_id, 'folder_id' => $subfolder->folder_id]) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Buka Folder
                                </a>
                            </li>
                        @endforeach

                        {{-- Tampilkan Dokumen --}}
                        @foreach ($documents as $document)
                            <li class="list-group-item d-flex flex-wrap align-items-center gap-3 py-3">
                                {{-- Informasi Utama Dokumen --}}
                                <div class="d-flex align-items-center flex-grow-1 me-auto" style="min-width: 250px;">
                                    <i class="bi bi-file-earmark-text me-3 fs-4"></i>
                                    <div>
                                        <strong class="d-block">{{ $document->file_name }}</strong>
                                        <small class="text-muted">{{ $document->name }}</small>
                                    </div>
                                </div>
                                {{-- Status Verifikasi Dokumen --}}
                                <div class="flex-shrink-0">
                                    @if ($document->verified)
                                        <span class="badge bg-success text-success-emphasis border border-success-subtle">
                                            <i class="bi bi-check-circle-fill"></i> Terverifikasi
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-warning-emphasis border border-warning-subtle">
                                            <i class="bi bi-hourglass-split"></i> Belum Diverifikasi
                                        </span>
                                    @endif
                                </div>
                                {{-- Tombol Aksi Dokumen --}}
                                <div class="btn-group flex-shrink-0" role="group">
                                    <a href="{{ route('kaprodi.dokumen.download', $document->id) }}" class="btn btn-outline-secondary btn-sm" title="Unduh"><i class="bi bi-download"></i></a>
                                    <a href="{{ route('kaprodi.dokumen.preview', $document->id) }}" target="_blank" class="btn btn-outline-primary btn-sm" title="Lihat"><i class="bi bi-eye"></i></a>

                                    {{-- Tombol verifikasi dengan gaya berbeda tergantung status --}}
                                    @if (!$document->verified)
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#verifyDocumentModal-{{ $document->id }}" title="Verifikasi Dokumen">
                                            <i class="bi bi-check-circle"></i> Verifikasi
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#verifyDocumentModal-{{ $document->id }}" title="Ubah Status Verifikasi">
                                            <i class="bi bi-pencil-square"></i> Ubah Status
                                        </button>
                                    @endif
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
                    <div class="modal-header">
                        <h5 class="modal-title">Verifikasi Dokumen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('kaprodi.dokumen.verify', $document->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <p>Anda akan mengubah status verifikasi untuk dokumen:</p>
                            <p class="fw-bold">{{ $document->file_name }}</p>
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
</x-app-layout>
