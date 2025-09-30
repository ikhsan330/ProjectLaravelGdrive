<x-app-layout>
    <div class="row">
        <div class="col-sm-12">
            <div class="home-tab">

                {{-- Isi dari Konten Tab --}}
                <div class="tab-content tab-content-basic">
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">


                        <div class="row">
                            <div class="col-sm-12">
                                <div class="statistics-details d-flex align-items-center justify-content-between">
                                  <div>
                                        <p class="statistics-title">Total Dokumen</p>
                                        <h3 class="rate-percentage">{{ $totalDocuments }}</h3>
                                        <p class="text-muted d-flex">Semua dokumen Anda</p>
                                    </div>
                                    <div>
                                        <p class="statistics-title">Terverifikasi</p>
                                        <h3 class="rate-percentage">{{ $verifiedDocuments }}</h3>
                                        <p class="text-success d-flex">Dokumen disetujui</p>
                                    </div>
                                    <div>
                                        <p class="statistics-title">Belum Diverifikasi</p>
                                        <h3 class="rate-percentage">{{ $unverifiedDocuments }}</h3>
                                        <p class="text-warning d-flex">Menunggu persetujuan</p>
                                    </div>
                                    <div class="d-none d-md-block">
                                        <p class="statistics-title">Folder Induk</p>
                                        <h3 class="rate-percentage">{{ $totalFolders }}</h3>
                                        <p class="text-muted d-flex">Jumlah folder utama</p>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-lg-8 d-flex flex-column">
                                <div class="row flex-grow">
                                    <div class="col-12 grid-margin stretch-card">
                                        <div class="card card-rounded">
                                            <div class="card-body">
                                                <div class="d-sm-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h4 class="card-title card-title-dash">Performance Line Chart
                                                        </h4>
                                                        <h5 class="card-subtitle card-subtitle-dash">Lorem Ipsum is
                                                            simply dummy text</h5>
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
                                    <div class="col-12 grid-margin stretch-card">
                                        <div class="card card-rounded">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                            <h4 class="card-title card-title-dash">Type By Amount</h4>
                                                        </div>
                                                        <canvas class="my-auto" id="doughnutChart"
                                                            height="200"></canvas>
                                                        <div id="doughnut-chart-legend" class="mt-5 text-center"></div>
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
