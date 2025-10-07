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
            <h2 class="mb-3 mb-md-0">Tinjauan Dokumen Assesor</h2>
        </div>

        {{-- Daftar Folder --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Folder Dokumen Publik</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse ($rootFolders as $folder)
                        <li class="list-group-item d-flex flex-wrap align-items-center justify-content-between gap-3 py-3 px-3">
                            <div class="d-flex align-items-center flex-grow-1 me-auto" style="min-width: 250px;">
                                <i class="bi bi-folder2-open me-3 fs-4 text-primary"></i>
                                <span class="fw-bold fs-6">{{ $folder->name }}</span>
                            </div>

                            <div class="btn-group" role="group">
                                <a href="{{ route('assesor.folder.show', $folder->folder_id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Buka
                                </a>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center p-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <h5 class="mt-3">Belum Ada Folder Publik</h5>
                            <p class="text-muted">Folder akan muncul di sini setelah dibuat oleh Admin.</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
