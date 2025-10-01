<x-app-layout>
    <div class="container py-4">

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

        {{-- Header Halaman --}}
        <div class="mb-4">
            <h2 class="mb-0">Folder Dokumen Anda</h2>
            <p class="text-muted">Berikut adalah folder yang ditugaskan kepada Anda oleh Admin.</p>
        </div>

        {{-- ========================================================== --}}
        {{-- BAGIAN DAFTAR FOLDER (Tampilan Grid Kartu) --}}
        {{-- ========================================================== --}}
        <div class="row g-4">
            @forelse ($folders as $folder)
                <div class="col-md-6 col-lg-4">
                    {{-- Setiap folder adalah sebuah kartu --}}
                    <div class="card h-100 shadow-sm border-0 text-center">
                        <div class="card-body d-flex flex-column p-4">
                            {{-- Ikon Folder --}}
                            <div class="mb-3">
                                <i class="bi bi-folder2-open text-primary" style="font-size: 4rem;"></i>
                            </div>

                            {{-- Nama Folder --}}
                            <h5 class="card-title">{{ $folder->name }}</h5>

                            {{-- Badge Status Verifikasi --}}
                            @if($folder->unverified_documents_count > 0)
                                <div class="mt-2">
                                    <span class="badge bg-warning text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        {{ $folder->unverified_documents_count }} Dokumen Perlu Diperiksa
                                    </span>
                                </div>
                            @else
                                <div class="mt-2">
                                     <span class="badge bg-success text-success-emphasis border border-success-subtle rounded-pill px-3 py-2">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        Semua Dokumen Lengkap
                                    </span>
                                </div>
                            @endif

                            {{-- Tombol Aksi (didorong ke bawah) --}}
                            <div class="mt-auto pt-4">
                                <a href="{{ route('dosen.folder.show', $folder->folder_id) }}" class="btn btn-primary w-100">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Buka Folder
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Tampilan jika tidak ada folder sama sekali --}}
                <div class="col-12">
                    <div class="text-center p-5 bg-light rounded">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <h5 class="mt-3">Anda Belum Memiliki Folder</h5>
                        <p class="text-muted">Folder akan muncul di sini setelah ditugaskan oleh Admin.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
