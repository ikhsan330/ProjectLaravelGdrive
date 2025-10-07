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

        {{-- Header Halaman --}}
        <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-4">
            <h2 class="mb-3 mb-md-0">Daftar Semua Dokumen</h2>
            <a href="{{ route('admin.dokumen.create') }}" class="btn btn-primary">
                <i class="bi bi-upload me-1"></i> Upload Dokumen Baru
            </a>
        </div>

        {{-- Daftar Dokumen dalam Accordion --}}
        <div class="accordion" id="documentAccordion">
            @forelse ($foldersWithDocuments as $folder)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-{{ $folder->id }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse-{{ $folder->id }}" aria-expanded="false"
                            aria-controls="collapse-{{ $folder->id }}">
                            <div class="d-flex justify-content-between w-100 align-items-center pe-3">
                                <span class="fw-bold fs-6">
                                    <i class="bi bi-folder2-open me-2"></i>
                                    {{ $folder->name }}
                                </span>
                                <span class="badge bg-primary rounded-pill">
                                    {{ $folder->documents->count() }} Dokumen
                                </span>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse-{{ $folder->id }}" class="accordion-collapse collapse"
                        aria-labelledby="heading-{{ $folder->id }}" data-bs-parent="#documentAccordion">
                        <div class="accordion-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Nama Dokumen</th>
                                            <th scope="col">Pemilik</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($folder->documents as $document)
                                            <tr>
                                                <td>
                                                    <strong class="d-block">{{ $document->file_name }}</strong>
                                                    <small class="text-muted">{{ $document->name }}</small>
                                                </td>
                                                <td>
                                                    {{-- Tampilkan nama pemilik dokumen --}}
                                                    {{ $document->user->name ?? 'Tidak diketahui' }}
                                                </td>
                                                <td>
                                                    @if ($document->verified)
                                                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">
                                                            Terverifikasi
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                                            Belum Diverifikasi
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.dokumen.download', $document->id) }}" class="btn btn-outline-secondary btn-sm" title="Unduh"><i class="bi bi-download"></i></a>
                                                        <a href="{{ route('admin.dokumen.show', $document->id) }}" target="_blank" class="btn btn-outline-primary btn-sm" title="Lihat"><i class="bi bi-eye"></i></a>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editDocumentModal-{{ $document->id }}" title="Edit"><i class="bi bi-pencil"></i></button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteDocumentModal-{{ $document->id }}" title="Hapus"><i class="bi bi-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center p-5">
                        <i class="bi bi-file-earmark-x fs-1 text-muted"></i>
                        <h5 class="mt-3">Belum Ada Dokumen</h5>
                        <p class="text-muted">Sistem belum memiliki dokumen apapun. Silakan upload dokumen baru.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- BAGIAN MODAL-MODAL --}}
    {{-- ========================================================== --}}
    @foreach ($foldersWithDocuments as $folder)
        @foreach ($folder->documents as $document)
            {{-- Modal Edit Dokumen --}}
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
                                    <label for="file_name" class="form-label">Nama Dokumen</label>
                                    <input type="text" class="form-control" name="file_name" value="{{ $document->file_name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="verified" class="form-label">Status Verifikasi</label>
                                    <select name="verified" class="form-select">
                                        <option value="1" @if($document->verified) selected @endif>Terverifikasi</option>
                                        <option value="0" @if(!$document->verified) selected @endif>Belum Diverifikasi</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="file" class="form-label">Ganti File (Opsional)</label>
                                    <input type="file" class="form-control" name="file">
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

            {{-- Modal Hapus Dokumen --}}
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
    @endforeach

</x-app-layout>
