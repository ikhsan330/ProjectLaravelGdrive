<x-app-layout>
    <div class="container py-4">
        <h2 class="mb-4">Manajemen Dokumen Dosen</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @php
            // Hitung total semua dokumen yang belum diverifikasi dari semua grup
            $grandTotalUnverified = $groupedFolders->sum(fn($folders) => $folders->sum('unverified_documents_count'));
        @endphp

        @if ($grandTotalUnverified > 0)
            <div class="d-grid gap-2 mb-4">
                <a href="{{ route('kaprodi.dokumen.unverified') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-card-checklist me-2"></i>
                    Lihat Semua Dokumen Belum Diverifikasi
                    <span class="badge bg-light text-danger ms-2 rounded-pill">{{ $grandTotalUnverified }}</span>
                </a>
            </div>
        @endif

        <div class="accordion" id="folderAccordion">
            @forelse ($groupedFolders as $folderName => $folders)
                @php
                    // Hitung total dokumen yang belum diverifikasi untuk grup folder ini
                    $totalUnverified = $folders->sum('unverified_documents_count');
                @endphp
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-{{ Str::slug($folderName) }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse-{{ Str::slug($folderName) }}" aria-expanded="false"
                            aria-controls="collapse-{{ Str::slug($folderName) }}">
                            <div class="d-flex w-100 justify-content-between align-items-center pe-3">
                                <span class="fw-bold fs-5">{{ $folderName }}</span>
                                @if ($totalUnverified > 0)
                                    <span class="badge bg-danger rounded-pill">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        {{ $totalUnverified }} Item Perlu Diverifikasi
                                    </span>
                                @endif
                            </div>
                        </button>
                    </h2>
                    <div id="collapse-{{ Str::slug($folderName) }}" class="accordion-collapse collapse"
                        aria-labelledby="heading-{{ Str::slug($folderName) }}" data-bs-parent="#folderAccordion">
                        <div class="accordion-body p-0">
                            <ul class="list-group list-group-flush">
                                @foreach ($folders as $folder)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle fs-4 me-2"></i>
                                            <div>
                                                <strong>{{ $folder->user_name }}</strong>
                                                @if ($folder->unverified_documents_count > 0)
                                                    <br>
                                                    <span class="badge bg-warning text-dark">
                                                        {{ $folder->unverified_documents_count }} dokumen perlu
                                                        diperiksa
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ route('kaprodi.dokumen.show', ['dosen_id' => $folder->user_id, 'folder_id' => $folder->folder_id]) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="bi bi-folder-symlink me-1"></i> Buka Folder
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center">
                        <p class="mb-0 text-muted">Belum ada folder dosen yang ditugaskan oleh Admin.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
