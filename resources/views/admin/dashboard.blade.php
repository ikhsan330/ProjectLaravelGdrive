<x-app-layout>
    <div class="row">
        <div class="col-sm-12">
            <div class="home-tab">
                 {{-- ========================================================== --}}
                        {{-- == BAGIAN BARU: KARTU STATISTIK (KPI) == --}}
                        {{-- ========================================================== --}}
                        <div class="row">
                            <div class="col-md-3 mb-4 stretch-card transparent">
                                <div class="card card-tale">
                                    <div class="card-body">
                                        <p class="mb-4">Total Dokumen</p>
                                        <p class="fs-30 mb-2">{{ $totalDocuments }}</p>
                                        <p>Semua file yang terdaftar</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4 stretch-card transparent">
                                <a href="{{ route('admin.dokumen.index') }}" class="text-decoration-none w-100">
                                    <div class="card card-dark-blue">
                                        <div class="card-body">
                                            <p class="mb-4">Total Folder Induk</p>
                                            <p class="fs-30 mb-2">{{ $totalFolders }}</p>
                                            <p>Kelola semua folder</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 mb-4 stretch-card transparent">
                                <a href="{{ route('admin.users.index') }}" class="text-decoration-none w-100">
                                    <div class="card card-light-blue">
                                        <div class="card-body">
                                            <p class="mb-4">Jumlah Dosen</p>
                                            <p class="fs-30 mb-2">{{ $totalDosen }}</p>
                                            <p>Kelola semua pengguna</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 mb-4 stretch-card transparent">
                                <div class="card card-light-danger">
                                    <div class="card-body">
                                        <p class="mb-4">Pending Verifikasi</p>
                                        <p class="fs-30 mb-2">{{ $pendingVerification }}</p>
                                        <p>Dokumen perlu diperiksa</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                <div class="tab-content tab-content-basic">
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">
                        <div class="row">
                            <div class="row">
                                <div class="col-lg-8 d-flex flex-column">
                                    <div class="row flex-grow">
                                        <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card">
                                            <div class="card card-rounded">
                                                <div class="card-body">
                                                    <div class="d-sm-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h4 class="card-title card-title-dash">Performance Line
                                                                Chart</h4>
                                                            <h5 class="card-subtitle card-subtitle-dash">Lorem Ipsum is
                                                                simply dummy text of the printing</h5>
                                                        </div>
                                                        <div id="performance-line-legend"></div>
                                                    </div>
                                                    <div class="chartjs-wrapper mt-5">
                                                        <canvas id="performaneLine"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 d-flex flex-column">
                                    <div class="row flex-grow">
                                        <div class="row flex-grow">
                                            <div class="col-12 grid-margin stretch-card">
                                                <div class="card card-rounded">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-3">
                                                                    <h4 class="card-title card-title-dash">Type By
                                                                        Amount</h4>
                                                                </div>
                                                                <canvas class="my-auto" id="doughnutChart"
                                                                    height="200"></canvas>
                                                                <div id="doughnut-chart-legend"
                                                                    class="mt-5 text-center"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
