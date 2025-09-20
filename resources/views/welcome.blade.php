<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArsipDokumen</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="shortcut icon" href="/images/favicon.ico" />
    <style>
        body {
            background: linear-gradient(135deg, #e3f0ff 0%, #f8fafc 100%);
            min-height: 100vh;
        }

        .hero {
            padding: 80px 0 40px 0;
        }

        .feature-icon {
            font-size: 2.5rem;
            color: #0d6efd;
        }

        .card-feature {
            border: none;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <img src="{{ asset('images/logoArsipDokumen2.png') }}" alt="Logo" style="height:40px;">
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
            </div>
        </div>
    </nav>
    <section class="hero text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Sistem Arsip Dokumen Digital</h1>
            <p class="lead mb-4">Kelola dokumen, folder, dan data penting secara aman dan
                terstruktur. Akses mudah untuk Admin, Dosen, dan Kaprodi.</p>
            <a href="{{ route('login') }}" class="btn btn-lg btn-primary px-5">Mulai Sekarang</a>
        </div>
    </section>
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card card-feature p-4 text-center h-100">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Manajemen User</h5>
                        <p class="mb-0">Admin dapat membuat, mengedit, dan menghapus user
                            dosen/kaprodi. Role-based access untuk keamanan data.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-feature p-4 text-center h-100">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-folder2-open"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Manajemen Dokumen & Folder</h5>
                        <p class="mb-0">Dosen dapat membuat folder, upload dokumen, dan mengelola
                            arsip digital dengan datatable interaktif.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-feature p-4 text-center h-100">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Keamanan & Akses</h5>
                        <p class="mb-0">Akses login terpisah untuk admin, dosen, dan kaprodi. Data
                            terenkripsi dan terproteksi.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="py-4 bg-light mt-5">
        <div class="container text-center">
            <small>&copy; {{ date('Y') }} Arsip Dokumen. All rights reserved.</small>
        </div>
    </footer>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>
</html>
