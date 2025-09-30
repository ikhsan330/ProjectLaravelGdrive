<x-app-layout>
    <div class="container">
        <div class="form-container">
            <h2 class="mb-4">Folder Dokumen Anda</h2>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Folder Induk</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse ($folders as $folder)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="fs-4 me-3"><i class="bi bi-folder-fill text-primary"></i></span>
                                    <div>
                                        <div class="fw-bold fs-5">{{ $folder->name }}</div>

                                        {{-- BAGIAN BARU: Tampilkan badge jika ada dokumen yang belum diverifikasi --}}
                                        @if($folder->unverified_documents_count > 0)
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                                {{ $folder->unverified_documents_count }} Dokumen Perlu Diperiksa
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('dosen.folder.show', $folder->folder_id) }}" class="btn btn-info">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Buka Folder
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item text-center p-4">
                                <p class="mb-0 text-muted">Anda belum memiliki folder. Folder akan ditugaskan oleh Admin.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
