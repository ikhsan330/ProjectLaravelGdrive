{{-- ======================================================================= --}}
{{-- SEMUA MODAL YANG DIPERLUKAN --}}
{{-- ======================================================================= --}}

<div class="modal fade" id="createFolderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Sub-folder di "{{ $folder->name }}"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.folder.store-subfolder') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="folder_name_create" class="form-label">Nama Folder Baru</label>
                        <input type="text" class="form-control" id="folder_name_create" name="folder_name"
                            placeholder="Contoh: Dokumen Mahasiswa" required>
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

<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Dokumen ke "{{ $folder->name }}"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="folderid" value="{{ $folder->folder_id }}">
                    {{-- INPUT BARU: Input tersembunyi untuk user_id pemilik folder --}}
                    <input type="hidden" name="user_id" value="{{ $folder->user_id }}">
                    <div class="mb-3">
                        <label for="file_name_upload" class="form-label">Nama Dokumen</label>
                        <input type="text" class="form-control" id="file_name_upload" name="file_name"
                            placeholder="Contoh: Laporan Bulanan" required>
                        <small class="form-text text-muted">Ini adalah nama yang akan ditampilkan di sistem.</small>
                    </div>
                    <div class="mb-3">
                        <label for="file_upload" class="form-label">Pilih File</label>
                        <input type="file" class="form-control" id="file_upload" name="file" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modals untuk setiap item di dalam loop --}}
@foreach ($subfolders as $subfolder)
    <div class="modal fade" id="editSubfolderModal-{{ $subfolder->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Nama Sub-folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.folder.update', $subfolder->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <label for="folder_name_{{ $subfolder->id }}" class="form-label">Nama Folder Baru</label>
                        <input type="text" class="form-control" name="folder_name"
                            id="folder_name_{{ $subfolder->id }}" value="{{ $subfolder->name }}" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteSubfolderModal-{{ $subfolder->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Konfirmasi Hapus Sub-folder</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus sub-folder <strong>{{ $subfolder->name }}</strong>?</p>
                    <p class="text-danger fw-bold">Tindakan ini akan menghapusnya secara permanen dari Google Drive dan
                        tidak dapat diurungkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('admin.subfolder.destroy', $subfolder->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Ya, Hapus Permanen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

@foreach ($documents as $document)
    <div class="modal fade" id="editDocumentModal-{{ $document->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.dokumen.update', $document->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file_name_{{ $document->id }}" class="form-label">Nama Dokumen</label>
                            <input type="text" class="form-control" id="file_name_{{ $document->id }}"
                                name="file_name" value="{{ $document->file_name }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="verified_{{ $document->id }}" class="form-label">Status Verifikasi</label>
                            <select class="form-select" name="verified" id="verified_{{ $document->id }}">
                                <option value="1" @if ($document->verified) selected @endif>Terverifikasi
                                </option>
                                <option value="0" @if (!$document->verified) selected @endif>Belum
                                    Diverifikasi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="file_{{ $document->id }}" class="form-label">Ganti File (Opsional)</label>
                            <input type="file" class="form-control" id="file_{{ $document->id }}"
                                name="file">
                            <small class="text-muted">Kosongkan jika tidak ingin mengganti file.</small>
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

    <div class="modal fade" id="deleteDocumentModal-{{ $document->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Konfirmasi Hapus Dokumen</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus dokumen <strong>{{ $document->file_name }}</strong>?</p>
                    <p class="text-danger fw-bold">Tindakan ini akan menghapusnya secara permanen dari Google Drive dan
                        tidak dapat diurungkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('admin.dokumen.destroy', $document->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Ya, Hapus Permanen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
