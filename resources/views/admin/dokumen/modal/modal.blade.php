
    {{-- =================================================================== --}}
    {{-- MODAL BARU YANG SUDAH DIPISAH --}}
    {{-- =================================================================== --}}

    <div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createFolderModalLabel">Buat Folder Baru untuk Semua Dosen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.folder.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="folder_name_create" class="form-label">Nama Folder Baru</label>
                            <input type="text" class="form-control" id="folder_name_create" name="folder_name" placeholder="Contoh: Arsip {{ date('Y') }}" required>
                            <small class="form-text text-muted">Folder ini akan dibuat dan ditugaskan ke semua dosen.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Buat & Tugaskan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reassignFolderModal" tabindex="-1" aria-labelledby="reassignFolderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reassignFolderModalLabel">Tugaskan Folder ke Dosen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.folder.reassign') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="folder_id_reassign" class="form-label">Pilih Folder Induk</label>
                            <select class="form-select" id="folder_id_reassign" name="folder_id" required>
                                <option value="" disabled selected>Pilih Folder...</option>
                                @foreach ($folders as $folder)
                                    <option value="{{ $folder['id'] }}">{{ $folder['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="dosen_id_reassign" class="form-label">Pilih Dosen</label>
                            <select class="form-select" id="dosen_id_reassign" name="dosen_id" required>
                                <option value="" disabled selected>Pilih Dosen...</option>
                                @foreach ($dosens as $dosen)
                                    <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info">Tugaskan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
