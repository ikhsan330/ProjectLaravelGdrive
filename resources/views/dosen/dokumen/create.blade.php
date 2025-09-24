<x-app-layout>
    <div class="container">
        <div class="form-container">
            <h2>Unggah Dokumen Baru</h2>
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <a href="{{ route('dosen.dokumen.index') }}" class="btn btn-warning mb-3">List Dokumen</a>
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                Buat Struktur Folder
            </button>

            <div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createFolderModalLabel">Buat Struktur Folder</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('dosen.folder.store') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="parent_folder" class="form-label">Pilih Folder</label>
                                    <select class="form-control" id="parent_folder" name="parent_folder">
                                        {{-- <option value="">Buat Folder Baru</option> --}}
                                        @foreach ($folders as $folder)
                                            <option value="{{ $folder['id'] }}">{{ $folder['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="folder_name" class="form-label">Nama Folder Baru</label>
                                    <input type="text" class="form-control" id="folder_name" name="folder_name" placeholder="Contoh: Arsip" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Buat Folder</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <hr>
            <div class="form-container">
                <h2 class="text-center mb-4">Unggah Dokumen Baru</h2>
                <form action="{{ route('dosen.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="folderid" class="form-label">Pilih Folder Tujuan</label>
                        <select name="folderid" id="folderid" class="form-control" required>
                            @foreach ($folders as $folder)
                                <option value="{{ $folder['id'] }}">{{ $folder['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="file_name" class="form-label">Nama Dokumen</label>
                        <input type="text" class="form-control @error('file_name') is-invalid @enderror" id="file_name" name="file_name" value="{{ old('file_name') }}" placeholder="Contoh: Laporan Keuangan 2024">
                        @error('file_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">Pilih Dokumen</label>
                        <input class="form-control @error('file') is-invalid @enderror" type="file" id="file" name="file">
                        <div class="form-text">Format yang diperbolehkan: PDF, DOC, DOCX, XLSX, dsb.</div>
                        @error('file')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Unggah Dokumen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
