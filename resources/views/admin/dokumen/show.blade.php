<x-app-layout>
    <div class="container">
        <div class="form-container">
            <h2>Dokumen di Folder: {{ $folder->name }}</h2>
            <a href="{{ route('admin.dokumen.index') }}" class="btn btn-secondary mb-3">Kembali ke Daftar Folder</a>

            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                Buat Sub-folder
            </button>

            @if(count($subfolders) > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h4>Sub-folder</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama Sub-folder</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subfolders as $subfolder)
                                        <tr>
                                            <td><i class="fas fa-folder text-warning me-2"></i>{{ $subfolder->name }}</td>
                                            <td>
                                                <a href="{{ route('admin.dosen.folder.show', ['dosen_id' => $subfolder->user_id, 'folder_id' => $subfolder->folder_id]) }}" class="btn btn-info btn-sm">Buka</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if (count($documents) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nama Dokumen</th>
                                <th>Nama File Asli</th>
                                <th>Status Verifikasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($documents as $document)
                                <tr>
                                    <td>{{ $document->file_name }}</td>
                                    <td>{{ $document->name }}</td>
                                    <td>
                                        @if ($document->verified)
                                            <span class="badge bg-success">Terverifikasi</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Belum Diverifikasi</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.dokumen.show', $document->id) }}" class="btn btn-info btn-sm">Lihat</a>
                                        <a href="{{ route('admin.dokumen.download', $document->id) }}" class="btn btn-success btn-sm">Unduh</a>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editDocumentModal-{{ $document->id }}">Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteDocumentModal-{{ $document->id }}">Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    Folder ini belum memiliki dokumen.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createFolderModalLabel">Buat Sub-folder di "{{ $folder->name }}"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.folder.store-subfolder') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="folder_name" class="form-label">Nama Folder Baru</label>
                        <input type="text" class="form-control" id="folder_name" name="folder_name" placeholder="Contoh: Dokumen Mahasiswa" required>
                    </div>
                    <input type="hidden" name="parent_folder_id" value="{{ $folder->folder_id }}">
                    <input type="hidden" name="parent_dosen_id" value="{{ $folder->user_id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Buat Folder</button>
                </div>
            </form>
        </div>
    </div>
</div>
