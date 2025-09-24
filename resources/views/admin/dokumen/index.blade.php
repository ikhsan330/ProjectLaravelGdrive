<x-app-layout>
    <div class="container">
        <div class="form-container">
            <h2>Halaman Admin</h2>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="d-flex justify-content-start mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mainFolderModal">
                    Kelola Folder Dosen
                </button>
            </div>

            <hr class="my-4">

            <div class="card shadow-sm">
                <div class="card-header">
                    <h4>Daftar Folder Dosen</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Folder</th>
                                    <th>Nama Dosen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dosenFolders as $folder)
                                    <tr>
                                        <td>{{ $folder->name }}</td>
                                        <td>{{ $folder->user_name }}</td>
                                        <td>
                                            <a href="{{ route('admin.dosen.folder.show', ['dosen_id' => $folder->user_id, 'folder_id' => $folder->folder_id]) }}" class="btn btn-info btn-sm">Lihat</a>
                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editFolderModal-{{ $folder->id }}">Edit</button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteFolderModal-{{ $folder->id }}">Hapus</button>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="editFolderModal-{{ $folder->id }}" tabindex="-1" aria-labelledby="editFolderModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editFolderModalLabel">Edit Folder: {{ $folder->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.folder.update', $folder->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="folder_name" class="form-label">Nama Folder Baru</label>
                                                            <input type="text" class="form-control" id="folder_name" name="folder_name" value="{{ $folder->name }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-warning">Perbarui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="deleteFolderModal-{{ $folder->id }}" tabindex="-1" aria-labelledby="deleteFolderModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteFolderModalLabel">Hapus Folder: {{ $folder->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus folder ini? Menghapus folder ini juga akan menghapus semua dokumen di dalamnya di Google Drive dan dari semua dosen.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('admin.folder.destroy', $folder->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada folder yang ditemukan untuk dosen.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<div class="modal fade" id="mainFolderModal" tabindex="-1" aria-labelledby="mainFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mainFolderModalLabel">Kelola Folder Dosen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 border-end">
                        <h5 class="mb-3">Buat Struktur Folder Baru</h5>
                        <form action="{{ route('admin.folder.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="folder_name" class="form-label">Nama Folder Baru</label>
                                <input type="text" class="form-control" id="folder_name" name="folder_name" placeholder="Contoh: Arsip" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Buat Folder</button>
                        </form>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3">Tugaskan Ulang Folder</h5>
                        <form action="{{ route('admin.folder.reassign') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="reassign_folder" class="form-label">Pilih Folder</label>
                            <select class="form-control" id="parent_folder" name="parent_folder">
                                    <option value="">Buat Folder Baru</option>
                                    @foreach ($folders as $folder)
                                        <option value="{{ $folder['id'] }}">{{ $folder['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="reassign_dosen" class="form-label">Pilih Dosen</label>
                                <select class="form-control" id="reassign_dosen" name="dosen_id" required>
                                    <option value="">Pilih Dosen</option>
                                    @foreach ($dosens as $dosen)
                                        <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info">Tugaskan Ulang</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
