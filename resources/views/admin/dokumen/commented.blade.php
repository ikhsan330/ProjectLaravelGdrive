<x-app-layout>
    <div class="container py-4">
        {{-- Navigasi Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dokumen.index') }}">Manajemen Folder</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dokumen Berkomentar</li>
            </ol>
        </nav>

        <h2 class="mb-4">Semua Dokumen yang Memiliki Komentar</h2>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nama Dokumen</th>
                                <th>Pemilik (Dosen)</th>
                                <th>Lokasi Folder</th>
                                <th class="text-center">Jml. Komentar</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($commentedDocuments as $document)
                                <tr>
                                    <td>
                                        <strong>{{ $document->file_name }}</strong><br>
                                        <small class="text-muted">{{ $document->name }}</small>
                                    </td>
                                    <td>{{ $document->user->name ?? 'Tidak diketahui' }}</td>
                                    <td>
                                        <a href="{{ route('admin.folder.show', $document->folder->folder_id) }}" class="text-decoration-none">
                                            <i class="bi bi-folder"></i> {{ $document->folder->name ?? 'Folder tidak ditemukan' }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info rounded-pill">{{ $document->comments->count() }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewCommentsModal-{{ $document->id }}" title="Lihat Komentar">
                                                <i class="bi bi-chat-left-dots"></i> Lihat
                                            </button>
                                            <a href="{{ route('admin.dokumen.show', $document->id) }}" target="_blank" class="btn btn-outline-primary btn-sm" title="Lihat Dokumen"><i class="bi bi-eye"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-4">
                                        <p class="mb-0 mt-2">Belum ada dokumen yang dikomentari.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal untuk Melihat Komentar --}}
    @foreach ($commentedDocuments as $document)
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
    @endforeach
</x-app-layout>
