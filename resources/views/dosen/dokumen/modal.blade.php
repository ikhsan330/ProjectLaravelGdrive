{{-- ====================================================================== --}}
{{-- MODAL UNTUK FOLDER (BUAT, EDIT, HAPUS) SENGAJA DIHAPUS DARI FILE INI --}}
{{-- KARENA DOSEN TIDAK LAGI MEMILIKI AKSES UNTUK MENGELOLA FOLDER      --}}
{{-- ====================================================================== --}}


{{-- Modal untuk Upload Dokumen --}}
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Upload Dokumen ke "{{ $folder->name ?? '' }}"</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            {{-- Form ini mengarah ke DosenDocumentController@store --}}
            <form action="{{ route('dosen.document.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="folderid" value="{{ $folder->folder_id ?? '' }}">
                    <div class="mb-3"><label for="file_name_upload" class="form-label">Nama Dokumen</label><input type="text" class="form-control" id="file_name_upload" name="file_name" placeholder="Contoh: Laporan Bulanan" required><small class="form-text text-muted">Ini adalah nama yang akan ditampilkan di sistem.</small></div>
                    <div class="mb-3"><label for="file_upload" class="form-label">Pilih File</label><input type="file" class="form-control" id="file_upload" name="file" required></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-success">Upload</button></div>
            </form>
        </div>
    </div>
</div>


{{-- Modals untuk Edit dan Hapus Dokumen --}}
{{-- Pastikan variabel $documents ada atau loop ini tidak akan berjalan --}}
@if(isset($documents))
    @foreach ($documents as $document)
        {{-- Modal Edit Dokumen --}}
        <div class="modal fade" id="editDocumentModal-{{ $document->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Edit Dokumen</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                    <form action="{{ route('dosen.document.update', $document->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3"><label for="file_name_{{ $document->id }}" class="form-label">Nama Dokumen</label><input type="text" class="form-control" id="file_name_{{ $document->id }}" name="file_name" value="{{ $document->file_name }}" required></div>
                            <div class="mb-3"><label class="form-label">Status Verifikasi</label><input type="text" class="form-control" value="{{ $document->verified ? 'Terverifikasi' : 'Belum Diverifikasi' }}" disabled readonly></div>
                            <div class="mb-3"><label for="file_{{ $document->id }}" class="form-label">Ganti File (Opsional)</label><input type="file" class="form-control" id="file_{{ $document->id }}" name="file"><small class="text-muted">Kosongkan jika tidak ingin mengganti file.</small></div>
                        </div>
                        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-warning">Perbarui</button></div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Hapus Dokumen --}}
         <div class="modal fade" id="deleteDocumentModal-{{ $document->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Konfirmasi Hapus Dokumen</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus dokumen <strong>{{ $document->file_name }}</strong>?</p>
                        <p class="text-danger fw-bold">Tindakan ini akan menghapusnya secara permanen dari Google Drive dan tidak dapat diurungkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form action="{{ route('dosen.document.destroy', $document->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Ya, Hapus Permanen</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
