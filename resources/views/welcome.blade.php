@extends('frontend.app')

@section('content')
    @include('banner')
    {{-- CSS untuk styling folder yang aktif/dipilih --}}
    <style>
        .folder-item.active {
            background-color: #eff6ff; /* blue-100 */
            color: #1d4ed8; /* blue-700 */
        }
        .folder-item.active svg {
            color: #2563eb; /* blue-600 */
        }
    </style>

    <section id="document-directory" class="py-16 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Direktori Dokumen</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Pilih folder di panel kiri untuk melihat dokumen yang tersedia di panel kanan.
                </p>
            </div>

            <div class="flex flex-col md:flex-row gap-8">
                {{-- Panel Kiri: Struktur Folder --}}
                <div class="w-full md:w-2/5 lg:w-1/3">
                    <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-200 h-full">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 pb-3 border-b">Struktur Folder</h3>
                        <ul id="folder-tree-container" class="space-y-1">
                            @include('_folder_sidebar')
                        </ul>
                    </div>
                </div>

                {{-- Panel Kanan: Tampilan Dokumen --}}
                <div class="w-full md:w-3/5 lg:w-2/3">
                    <div id="document-display-area" class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200 min-h-[400px]">
                        {{-- Konten AJAX akan dimuat di sini --}}
                        <div class="text-center text-gray-500 pt-20">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.75h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p class="mt-2 text-lg">Pilih sebuah folder untuk melihat isinya.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const folderContainer = document.getElementById('folder-tree-container');
        const documentArea = document.getElementById('document-display-area');

        folderContainer.addEventListener('click', function(e) {
            const target = e.target.closest('.folder-item');
            if (!target) return;

            // Logika untuk expand/collapse
            if (target.dataset.role === 'toggle') {
                target.nextElementSibling?.classList.toggle('hidden');
                target.querySelector('.arrow-icon')?.classList.toggle('rotate-90');
            }

            // Logika untuk fetch data (hanya jika item memiliki folder-id)
            const folderId = target.dataset.folderId;
            if (folderId) {
                e.preventDefault();

                // Hapus kelas aktif dari semua item
                folderContainer.querySelectorAll('.folder-item').forEach(item => item.classList.remove('active'));
                // Tambahkan kelas aktif ke item yang diklik
                target.classList.add('active');

                documentArea.innerHTML = `<div class="text-center p-10 text-gray-500">Memuat...</div>`;
                const url = `/folder-contents/${folderId}`;

                fetch(url)
                    .then(response => response.text())
                    .then(html => { documentArea.innerHTML = html; })
                    .catch(error => {
                        console.error('Error fetching content:', error);
                        documentArea.innerHTML = `<div class="text-center p-10 text-red-500">Gagal memuat konten.</div>`;
                    });
            }
        });
    });
</script>
    {{-- features (Asumsi file ini ada) --}}
    @include('features')
@endsection
