@extends('frontend.app')

@section('content')
    @include('partials.banner')

    <section id="document-directory" class="py-16 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Direktori Dokumen</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Jelajahi struktur folder dan temukan dokumen yang Anda cari dengan mudah.
                </p>
            </div>

            <div class="flex flex-col md:flex-row gap-8">
                {{-- Panel Kiri: Struktur Folder --}}
                <div class="w-full md:w-2/5 lg:w-1/3">
                    <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-md border border-gray-100 h-full">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 pb-3 border-b border-gray-200">Struktur Folder</h3>
                        <ul id="folder-tree-container" class="space-y-1">
                            @include('partials._folder_sidebar')
                        </ul>
                    </div>
                </div>

                {{-- Panel Kanan: Tampilan Dokumen --}}
                <div class="w-full md:w-3/5 lg:w-2/3">
                    <div id="document-display-area" class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 min-h-[400px]">
                        <div class="text-center text-gray-500 pt-20">
                            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.75h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p class="mt-4 text-lg font-medium">Pilih sebuah folder dari panel kiri untuk melihat isinya.</p>
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

            // Toggle sub-list visibility
            if (target.dataset.role === 'toggle') {
                target.nextElementSibling?.classList.toggle('hidden');
                target.querySelector('.arrow-icon')?.classList.toggle('rotate-90');
            }

            const folderId = target.dataset.folderId;
            if (folderId) {
                e.preventDefault();

                // Remove 'active' from all folder items
                folderContainer.querySelectorAll('.folder-item').forEach(item => item.classList.remove('active'));
                // Add 'active' to the clicked folder item
                target.classList.add('active');

                // Show loading state
                documentArea.innerHTML = `<div class="text-center p-10 text-gray-500">
                                            <svg class="mx-auto h-12 w-12 animate-spin text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.992 0l-3.1-3.1A.75.75 0 012 11.25v-1.5c0-.828.672-1.5 1.5-1.5h1.5a.75.75 0 01.53-.22L7.33 6.07A4.5 4.5 0 0111.16 3H12.75c.828 0 1.5.672 1.5 1.5V6a.75.75 0 01-.22.53l-3.1 3.1a.75.75 0 01-.53.22H9.348v-.001M21.015 19.644v-4.992m0 0h-4.992m4.992 0l3.1-3.1A.75.75 0 0022 11.25v-1.5c0-.828-.672-1.5-1.5-1.5H19a.75.75 0 00-.53.22L16.67 6.07A4.5 4.5 0 0012.84 3H11.25c-.828 0-1.5.672-1.5 1.5V6a.75.75 0 00.22.53l3.1 3.1a.75.75 0 00.53.22h4.992v-.001M21.015 4.356v4.992m0 0h-4.992m4.992 0l3.1 3.1A.75.75 0 0122 12.75v1.5c0 .828-.672 1.5-1.5 1.5H19a.75.75 0 01-.53-.22l-3.1-3.1a.75.75 0 00-.53-.22H9.348v-.001M2.985 4.356v4.992m0 0h4.992m-4.992 0l-3.1 3.1A.75.75 0 002 12.75v1.5c0 .828.672 1.5 1.5 1.5h1.5a.75.75 0 01.53-.22L7.33 13.93A4.5 4.5 0 0111.16 17h1.59c.828 0 1.5.672 1.5 1.5V20a.75.75 0 01-.22.53l-3.1 3.1a.75.75 0 00-.53.22H9.348v-.001" /></svg>
                                            <p class="mt-4 text-lg">Memuat konten folder...</p>
                                        </div>`;
                const url = `/folder-contents/${folderId}`;

                fetch(url)
                    .then(response => response.text())
                    .then(html => { documentArea.innerHTML = html; })
                    .catch(error => {
                        console.error('Error fetching content:', error);
                        documentArea.innerHTML = `<div class="text-center p-10 text-red-500 font-medium">
                                                    <p>Gagal memuat konten. Silakan coba lagi.</p>
                                                </div>`;
                    });
            }
        });
    });
</script>
@include('partials.features')
@endsection
