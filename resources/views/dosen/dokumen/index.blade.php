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
            {{-- PERUBAHAN: Teks disesuaikan karena folder bersifat publik --}}
            <h2 class="mb-0">Folder Dokumen Publik</h2>
            <p class="text-muted">Telusuri folder untuk mengelola dan mengunggah dokumen Anda.</p>
        </div>

        {{-- ========================================================== --}}
        {{-- BAGIAN DAFTAR FOLDER (Tampilan Grid Kartu) --}}
        {{-- ========================================================== --}}
        <div class="row g-4">
            {{-- PERUBAHAN: Menggunakan variabel $rootFolders dari controller --}}
            @forelse ($rootFolders as $folder)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 text-center">
                        <div class="card-body d-flex flex-column p-4">
                            <div class="mb-3">
                                <i class="bi bi-folder2-open text-primary" style="font-size: 4rem;"></i>
                            </div>

                            <h5 class="card-title">{{ $folder->name }}</h5>

                            {{-- PERUBAHAN: Logika untuk menampilkan notifikasi verifikasi disesuaikan --}}
                            @if(isset($unverifiedCounts[$folder->folder_id]) && $unverifiedCounts[$folder->folder_id] > 0)
                                <div class="mt-2">
                                    <span class="badge bg-warning text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        Memerlukan Verifikasi
                                    </span>
                                </div>
                            @else
                                <div class="mt-2">
                                     <span class="badge bg-success text-success-emphasis border border-success-subtle rounded-pill px-3 py-2">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        Dokumen Terverifikasi
                                    </span>
                                </div>
                            @endif

                            <div class="mt-auto pt-4">
                                {{-- Tautan ini sudah benar, mengarah ke DosenFolderController@show --}}
                                <a href="{{ route('dosen.folder.show', $folder->folder_id) }}" class="btn btn-primary w-100">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Buka Folder
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    {{-- PERUBAHAN: Teks disesuaikan untuk kasus folder publik kosong --}}
                    <div class="text-center p-5 bg-light rounded">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <h5 class="mt-3">Belum Ada Folder Publik</h5>
                        <p class="text-muted">Folder akan muncul di sini setelah dibuat oleh Admin.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
