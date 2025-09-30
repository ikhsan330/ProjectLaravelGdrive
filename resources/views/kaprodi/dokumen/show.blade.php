<x-app-layout>
    <div class="container py-4">
        {{-- Navigasi Breadcrumb untuk Kaprodi --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('kaprodi.dokumen.index') }}">Semua Folder Dosen</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $folder->name }}</li>
            </ol>
        </nav>

        {{-- Header Halaman --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-0">Isi Folder: {{ $folder->name }}</h2>
                <p class="text-muted mb-0">Milik: <strong>{{ $folder->user->name }}</strong></p>
            </div>
            {{-- Tombol aksi (upload/buat folder) dihilangkan untuk Kaprodi --}}
        </div>

        {{-- Alert --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

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
                                    <i class="bi bi-folder-fill text-primary me-2"></i>
                                    <div class="fw-bold">{{ $subfolder->name }}</div>
                                    {{-- Badge Notifikasi untuk Sub-folder --}}
                                    @if($subfolder->unverified_documents_count > 0)
                                        <span class="badge rounded-pill bg-warning text-dark ms-2">
                                            {{ $subfolder->unverified_documents_count }} item perlu diperiksa
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('kaprodi.dokumen.show', ['dosen_id' => $subfolder->user_id, 'folder_id' => $subfolder->folder_id]) }}" class="btn btn-info btn-sm"><i class="bi bi-arrow-right-circle me-1"></i> Buka</a>
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
                                        <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Terverifikasi</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Belum Diverifikasi</span>
                                    @endif
                                </div>
                                <div class="col-6 col-md-4 text-md-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('kaprodi.dokumen.download', $document->id) }}" class="btn btn-outline-success btn-sm" title="Unduh"><i class="bi bi-download"></i></a>
                                        <a href="{{ route('kaprodi.dokumen.preview', $document->id) }}" target="_blank" class="btn btn-outline-info btn-sm" title="Lihat"><i class="bi bi-eye"></i></a>
                                        {{-- Tombol Edit dan Hapus diganti dengan Tombol Verifikasi --}}
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#verifyDocumentModal-{{ $document->id }}" title="Verifikasi Dokumen">
                                            <i class="bi bi-check-circle"></i> Verifikasi
                                        </button>
                                    </div>
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
                        @method('PATCH') {{-- Menggunakan PATCH untuk update parsial --}}
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
