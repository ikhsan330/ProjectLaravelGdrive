<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ArsipGdrive - Politeknik Negeri Bengkalis</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" />
</head>

<body class="font-inter bg-gray-50 min-h-screen overflow-x-hidden">

    <div id="blob"></div>

    @include('frontend.nav')

    @yield('content')

    @include('frontend.footer')

    <script src="{{ asset('js/welcome.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Gunakan event delegation untuk efisiensi, terutama pada konten dinamis
        const container = document.querySelector('#document-directory');

        if (container) {
            container.addEventListener('click', function(event) {
                // Cari elemen .folder-toggle terdekat dari target yang di-klik
                const toggle = event.target.closest('.folder-toggle');

                // Jika bukan .folder-toggle yang di-klik, abaikan
                if (!toggle) {
                    return;
                }

                // Dapatkan <ul> yang berisi konten folder
                const content = toggle.nextElementSibling;
                // Dapatkan ikon chevron di dalam elemen yang di-klik
                const icon = toggle.querySelector('.toggle-icon');

                if (content && content.tagName === 'UL') {
                    // Toggle class 'hidden' untuk menampilkan/menyembunyikan
                    content.classList.toggle('hidden');

                    // Toggle rotasi ikon
                    if (icon) {
                        icon.classList.toggle('rotate-90');
                    }
                }
            });
        }
    });
</script>
</body>

</html>
