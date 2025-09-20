<x-app-layout>
	<div class="row">
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

        const dataTable = new simpleDatatables.DataTable("#datatable-dokumen", {
            searchable: true,
            fixedHeight: true,
        });
	</script>
     @endpush
</x-app-layout>
