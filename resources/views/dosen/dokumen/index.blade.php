<x-app-layout>
	<div class="row mb-4">
		<div class="col-lg-12 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center">
						<h4 class="card-title">Daftar Folder Dokumen</h4>
						<button class="btn btn-primary   btn-sm" data-bs-toggle="collapse" data-bs-target="#form-folder">Buat Folder Baru</button>
					</div>
					<div class="collapse mt-3" id="form-folder">
						<form method="POST" action="#">
							@csrf
							<div class="mb-2">
								<label for="nama_folder" class="form-label">Nama Folder</label>
								<input type="text" class="form-control" id="nama_folder" name="nama_folder" required>
							</div>
							<button type="submit" class="btn btn-primary btn-sm">Simpan Folder</button>
						</form>
					</div>
					<div class="table-responsive mt-3">
						<table class="table table-striped" id="datatable-folder">
							<thead>
								<tr>
									<th>No</th>
									<th>Nama Folder</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								@foreach([
									['nama' => 'Folder 1'],
									['nama' => 'Folder 2'],
									['nama' => 'Folder 3'],
								] as $i => $folder)
								<tr>
									<td>{{ $i + 1 }}</td>
									<td>{{ $folder['nama'] }}</td>
									<td>
										<a href="#" class="btn btn-info btn-sm">Detail</a>
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
		<div class="col-lg-12 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
					 <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Daftar Dokumen</h4>
                        <a href="{{ route('dosen.dokumen.create') }}" class="btn btn-primary btn-sm">Tambah Dokumen</a>
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
		const dataTableDokumen = new simpleDatatables.DataTable("#datatable-dokumen", {
			searchable: true,
			fixedHeight: true,
		});
		const dataTableFolder = new simpleDatatables.DataTable("#datatable-folder", {
			searchable: true,
			fixedHeight: true,
		});
	</script>
	 @endpush
</x-app-layout>
