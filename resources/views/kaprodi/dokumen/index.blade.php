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

        @php
            // Hitung total semua dokumen yang belum diverifikasi dari semua grup
            $grandTotalUnverified = $groupedFolders->sum(fn($folders) => $folders->sum('unverified_documents_count'));
        @endphp

        {{-- BAGIAN HEADER HALAMAN UTAMA --}}
        <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-4">
            <h2 class="mb-3 mb-md-0">Manajemen Dokumen Dosen</h2>
            {{-- INI ADALAH TOMBOL YANG ANDA MAKSUD --}}
            @if ($grandTotalUnverified > 0)
                <a href="{{ route('kaprodi.dokumen.unverified') }}" class="btn btn-danger ">
                    <i class="bi bi-card-checklist me-2"></i>
                    Lihat {{ $grandTotalUnverified }} Dokumen Perlu Verifikasi
                </a>
            @endif
        </div>


        {{-- ========================================================== --}}
        {{-- BAGIAN DAFTAR GRUP FOLDER (Kartu + Dropdown) --}}
        {{-- ========================================================== --}}
        <div class="accordion" id="kaprodiFolderAccordion">
            @forelse ($groupedFolders as $folderName => $folders)
                @php
                    $totalUnverified = $folders->sum('unverified_documents_count');
                    $collapseId = 'collapse-' . Str::slug($folderName);
                @endphp

                {{-- Setiap item kini berfungsi seperti kartu di dalam akordeon --}}
                <div class="accordion-item card shadow-sm mb-3 border-0">
                    <h2 class="card-header bg-light py-0" id="heading-{{ Str::slug($folderName) }}">

                        {{-- Header kartu kini adalah tombol dropdown --}}
                        <button class="btn btn-link text-decoration-none w-100 p-3 text-start collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false"
                            aria-controls="{{ $collapseId }}">

                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-dark">
                                    <i class="bi bi-collection-fill me-2 text-primary"></i>
                                    {{ $folderName }}
                                </h5>
                                @if ($totalUnverified > 0)
                                    <span
                                        class="badge bg-danger text-danger-emphasis border border-danger-subtle rounded-pill fs-6">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        {{ $totalUnverified }} Item Perlu Diverifikasi
                                    </span>
                                @else
                                    <span
                                        class="badge bg-success text-success-emphasis border border-success-subtle rounded-pill">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        Semua Terverifikasi
                                    </span>
                                @endif
                            </div>
                        </button>
                    </h2>

                    {{-- Isi Kartu (Daftar Dosen) yang bisa disembunyikan --}}
                    <div id="{{ $collapseId }}" class="accordion-collapse collapse"
                        aria-labelledby="heading-{{ Str::slug($folderName) }}"
                        data-bs-parent="#kaprodiFolderAccordion">
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @foreach ($folders as $folder)
                                    <li
                                        class="list-group-item d-flex flex-wrap justify-content-between align-items-center py-3 gap-2">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle fs-4 me-3 text-muted"></i>
                                            <div>
                                                <strong class="d-block">{{ $folder->user_name }}</strong>
                                                @if ($folder->unverified_documents_count > 0)
                                                    <span
                                                        class="badge bg-warning text-warning-emphasis border border-warning-subtle">
                                                        {{ $folder->unverified_documents_count }} dokumen perlu
                                                        diperiksa
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ route('kaprodi.dokumen.show', ['dosen_id' => $folder->user_id, 'folder_id' => $folder->folder_id]) }}"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-folder-symlink me-1"></i> Buka Folder
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center p-5 bg-light rounded">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <h5 class="mt-3">Tidak Ada Dokumen untuk Ditinjau</h5>
                    <p class="text-muted">Saat ini belum ada folder dosen yang ditugaskan oleh Admin.</p>
                </div>
            @endforelse
        </div>

    </div>
</x-app-layout>
