<x-app-layout>
	<div class="row">
		<div class="col-lg-12 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
					 <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Daftar Dokumen</h4>

                    </div>
                    <p class="card-description">
                        Berikut adalah daftar semua dokumen yang ada di sistem.
                    </p>
					<div class="table-responsive">
						<table class="table table-striped" id="datatable-dokumen">
							<thead>
								<tr>
									<th>No</th>
									<th>Judul</th>
									<th>Deskripsi</th>
									<th>Tanggal Upload</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								@foreach([
									['judul' => 'Dokumen 1', 'deskripsi' => 'Deskripsi dokumen 1', 'tanggal' => '2025-09-01 10:00'],
									['judul' => 'Dokumen 2', 'deskripsi' => 'Deskripsi dokumen 2', 'tanggal' => '2025-09-05 14:30'],
									['judul' => 'Dokumen 3', 'deskripsi' => 'Deskripsi dokumen 3', 'tanggal' => '2025-09-10 09:15'],
								] as $i => $doc)
								<tr>
									<td>{{ $i + 1 }}</td>
									<td>{{ $doc['judul'] }}</td>
									<td>{{ $doc['deskripsi'] }}</td>
									<td>{{ $doc['tanggal'] }}</td>
									<td>
										<a href="#" class="btn btn-info btn-sm">Detail</a>
										<a href="#" class="btn btn-warning btn-sm">Edit</a>
										<a href="#" class="btn btn-danger btn-sm">Hapus</a>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
     @push('scripts')
	<script>

        const dataTable = new simpleDatatables.DataTable("#datatable-dokumen", {
            searchable: true,
            fixedHeight: true,
        });
	</script>
     @endpush
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
