<!DOCTYPE html>
<html lang="en">
<head>
    {{-- ... (bagian head tetap sama) ... --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login ArsipDokumen</title>

    <link rel="stylesheet" href="{{ asset('vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/vendor.bundle.base.css') }}">

    <link rel="stylesheet" href="{{ asset('css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom-auth.css') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" />
</head>
<body>
    {{-- GAMBAR DITAMBAHKAN DI SINI --}}
<img src="{{ asset('images/fotoPolbeng.jpeg') }}" class="login-bg-image" alt="Gedung Politeknik Negeri Bengkalis">

    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo mb-4 text-center">
                                <a href="/">
                                    <img src="{{ asset('images/logoArsipDokumen.png') }}" alt="logo" style="width:200px; height:auto;">
                                </a>
                            </div>
                            {{ $slot }}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/custom-auth.js') }}"></script>
</body>
</html>
