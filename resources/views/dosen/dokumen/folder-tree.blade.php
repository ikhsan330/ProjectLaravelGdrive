<ul class="folder-list list-unstyled">
    @foreach ($folders as $folder)
        <li class="folder-item mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center p-2">
                    <button class="btn btn-sm p-0 flex-grow-1 text-start" type="button" data-bs-toggle="collapse"
                        data-bs-target="#folder-{{ $folder->id }}" aria-expanded="false"
                        aria-controls="folder-{{ $folder->id }}">
                        <i class="fas fa-folder me-2"></i>
                        <span>{{ $folder->name }}</span>
                    </button>

                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#updateFolderModal-{{ $folder->id }}" title="Edit Folder">
                            Edit
                        </button>
                        <form action="{{ route('dosen.folder.destroy', $folder->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit" title="Hapus Folder"
                                onclick="return confirm('Yakin mau hapus folder ini? Semua isi di dalamnya akan terhapus juga.')">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>

                <div class="collapse" id="folder-{{ $folder->id }}">
                    <div class="card-body p-2">
                        @if ($folder->documents->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0 datatable-docs"
                                    id="docs-table-{{ $folder->id }}">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama File</th>
                                            <th width="100">Status</th>
                                            <th width="200">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($folder->documents as $doc)
                                            <tr>
                                                <td>
                                                    <i class="fas fa-file-alt me-1 text-muted"></i>
                                                    {{ $doc->file_name }}
                                                </td>
                                                <td>
                                                    @if ($doc->verified)
                                                        <span class="badge bg-success">Valid</span>
                                                    @else
                                                        <span class="badge bg-danger">Invalid</span>
                                                    @endif
                                                </td>
                                                <td>
                                                      <div class="btn-group" role="group">
                                                        <a href="{{ route('dosen.dokumen.show', $doc->id) }}"
                                                            class="btn btn-sm btn-info" title="Preview">
                                                            Preview
                                                        </a>
                                                        <a href="{{ route('dosen.dokumen.download', $doc->id) }}"
                                                            class="btn btn-sm btn-warning" title="Download">
                                                            Download
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editDocModal{{ $doc->id }}"
                                                            title="Edit"> Edit
                                                        </button>
                                                        <form action="{{ route('dosen.dokumen.destroy', $doc->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-danger" type="submit" title="Hapus"
                                                                onclick="return confirm('Yakin mau hapus?')">
                                                                <i class="fas fa-trash-alt"></i> Hapus
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-folder-open fa-2x mb-2"></i>
                                <p class="mb-0 small">Tidak ada file di folder ini</p>
                            </div>
                        @endif

                        @if (isset($folder->children) && count($folder->children) > 0)
                            <div class="mt-3 ps-3 border-start border-2 border-light">
                                @include('dosen.dokumen.folder-tree', ['folders' => $folder->children])
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </li>
    @endforeach
</ul>

@foreach ($folders as $folder)
    @foreach ($folder->documents as $doc)
        <div class="modal fade" id="editDocModal{{ $doc->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('dosen.dokumen.update', $doc->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Update Dokumen</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="file_name_{{ $doc->id }}" class="form-label">Nama
                                    File</label>
                                <input type="text" class="form-control" id="file_name_{{ $doc->id }}"
                                    name="file_name" value="{{ $doc->file_name }}">
                            </div>
                            <div class="mb-3">
                                <label for="verified_{{ $doc->id }}" class="form-label">Status</label>
                                <select class="form-select" id="verified_{{ $doc->id }}" name="verified">
                                    <option value="1" @if ($doc->verified) selected @endif>
                                        Valid</option>
                                    <option value="0" @if (!$doc->verified) selected @endif>
                                        Invalid
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="file" class="form-label">Pilih
                                    Dokumen</label>
                                <input class="form-control @error('file') is-invalid @enderror" type="file"
                                    id="file" name="file">
                                <div class="form-text">Format
                                    yang diperbolehkan: PDF,
                                    DOC, DOCX, XLSX, dsb.</div>
                                @error('file')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endforeach
