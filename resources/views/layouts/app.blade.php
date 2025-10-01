<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ArsipDokumenPolbeng</title>
    {{-- CSS Utama --}}
    <link rel="stylesheet" href="{{ asset('vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  {{-- CSS untuk styling folder yang aktif --}}
    <style>
        .folder-item.active {
            background-color: #e0f2fe; /* blue-50 */
            color: #1e40af; /* blue-800 */
            font-weight: 600;
        }
        .folder-item.active .text-gray-700 {
            color: #1e40af; /* blue-800 */
        }
        .folder-item.active .arrow-icon {
            color: #3b82f6; /* blue-500 */
        }
        .folder-item.active .folder-icon {
            color: #3b82f6; /* blue-500 */
        }
        .folder-item:hover {
            background-color: #f3f4f6; /* gray-100 */
        }
    </style>
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" />
</head>

<body>
    <div class="container-scroller">
        @include('layouts.partials._navbar')

        <div class="container-fluid page-body-wrapper">

            @include('layouts.partials._sidebar')

            <div class="main-panel">
                <div class="content-wrapper">
                    {{-- KONTEN UTAMA HALAMAN AKAN DIMASUKKAN DI SINI --}}
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

    {{-- JS Utama --}}
    <script src="{{ asset('vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('vendors/progressbar.js/progressbar.min.js') }}"></script>
    <script src="{{ asset('js/off-canvas.js') }}"></script>
    <script src="{{ asset('js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('js/template.js') }}"></script>
    <script src="{{ asset('js/settings.js') }}"></script>
    <script src="{{ asset('js/todolist.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/Chart.roundedBarCharts.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>


    {{-- Placeholder untuk skrip spesifik dari setiap halaman --}}
    @stack('scripts')
    {{-- PERUBAHAN 2: Menambahkan kembali library SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            $('#usersTable').DataTable();

            // PERUBAHAN 3: Script untuk menampilkan alert konfirmasi hapus
            $('.delete-form').on('submit', function(e) {
                e.preventDefault(); // Mencegah form untuk langsung di-submit
                var form = this;

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "User yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Jika dikonfirmasi, maka jalankan submit form
                    }
                })
            });
        });
    </script>
</body>

</html>
