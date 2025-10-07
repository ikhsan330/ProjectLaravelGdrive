<x-app-layout>
    <div class="container py-4">
        {{-- Navigasi Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('kaprodi.dokumen.index') }}">Tinjauan Dokumen Dosen</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dokumen Belum Diverifikasi</li>
            </ol>
        </nav>

        <h2 class="mb-4">Dokumen yang Perlu Diverifikasi</h2>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nama Dokumen</th>
                                <th>Pemilik (Dosen)</th>
                                <th>Lokasi Folder</th>
                                <th>Tanggal Upload</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($unverifiedDocuments as $document)
                                <tr>
                                    <td>
                                        <strong>{{ $document->file_name }}</strong><br>
                                        <small class="text-muted">{{ $document->name }}</small>
                                    </td>
                                    <td>{{ $document->user->name ?? 'Tidak diketahui' }}</td>
                                    <td>{{ $document->folder->name ?? 'Folder tidak ditemukan' }}</td>
                                    <td>{{ $document->created_at->format('d M Y, H:i') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('kaprodi.document.download', $document->id) }}" class="btn btn-outline-secondary btn-sm" title="Unduh"><i class="bi bi-download"></i></a>
                                            <a href="{{ route('kaprodi.document.preview', $document->id) }}" target="_blank" class="btn btn-outline-primary btn-sm" title="Lihat"><i class="bi bi-eye"></i></a>
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#verifyDocumentModal-{{ $document->id }}" title="Verifikasi Dokumen">
                                                <i class="bi bi-check-circle"></i> Verifikasi
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-4">
                                        <div class="text-success">
                                            <i class="bi bi-check2-circle fs-3"></i>
                                            <p class="mb-0 mt-2">Kerja bagus! Semua dokumen sudah diverifikasi.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Verifikasi (logikanya sama seperti di halaman show) --}}
    @foreach ($unverifiedDocuments as $document)
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
                                    <option value="1">Terverifikasi</option>
                                    <option value="0" selected>Belum Diverifikasi</option>
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
