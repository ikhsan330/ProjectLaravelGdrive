<section class="relative pt-28 pb-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="z-10">
                <div
                    class="inline-flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium mb-6 animate-fade-in-down">
                    <span class="w-2 h-2 bg-blue-600 rounded-full mr-2"></span>
                    Sistem Arsip Digital Terpadu
                </div>
                <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight animate-fade-in-down"
                    style="animation-delay: 0.2s;">
                    Arsip Dokumen
                    <span class="animated-gradient-text">Akreditasi</span> Digital
                </h1>
                <p class="text-xl text-gray-600 mb-8 leading-relaxed animate-fade-in-down"
                    style="animation-delay: 0.4s;">
                    Platform terintegrasi untuk mengelola dan mengarsipkan seluruh dokumen akreditasi Politeknik Negeri
                    Bengkalis dengan aman dan efisien.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 animate-fade-in-down" style="animation-delay: 0.6s;">
                    <a href="/login"
                        class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl font-medium text-center transform hover:scale-105">
                        Mulai Sekarang
                    </a>
                    <a href="#features"
                        class="border-2 border-blue-200 text-blue-700 px-8 py-4 rounded-xl hover:bg-blue-50 transition-colors font-medium text-center">
                        Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>

            <div class="animate-float">
                <div class="relative">
                    <div class="bg-white/60 backdrop-blur-sm rounded-2xl shadow-2xl p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-3 h-3 bg-red-400 rounded-full mr-2"></div>
                            <div class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></div>
                            <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                        </div>

                      {{-- File: resources/views/banner.blade.php (atau di mana pun kode ini berada) --}}
{{-- =================== BAGIAN YANG DIGANTI =================== --}}
<div class="space-y-4">
    @php
        // Array untuk mendefinisikan warna dan ikon agar dinamis
        $styles = [
            ['color' => 'from-blue-500 to-indigo-600', 'icon' => '<path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" />'],
            ['color' => 'from-emerald-500 to-teal-600', 'icon' => '<path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" /><path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a2 2 0 00-2 2v6a2 2 0 002 2h8a2 2 0 002-2V6a2 2 0 00-2-2V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd" />'],
            ['color' => 'from-purple-500 to-pink-600', 'icon' => '<path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />'],
        ];
    @endphp
    @forelse ($featuredFolders as $folder)
        @php $style = $styles[$loop->index % count($styles)]; @endphp
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-gradient-to-br {{ $style['color'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    {!! $style['icon'] !!}
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 truncate">{{ $folder->name }}</h3>
                {{-- Angka ini sekarang akan menampilkan jumlah total yang benar --}}
                <p class="text-sm text-gray-500">{{ $folder->total_files }} file tersimpan</p>
            </div>
        </div>
    @empty
        <div class="text-center text-gray-500 py-4">
            <p>Belum ada folder yang bisa ditampilkan.</p>
        </div>
    @endforelse
</div>
{{-- ================= END BAGIAN YANG DIGANTI ================= --}}
                    </div>
                    <div
                        class="absolute -top-4 -right-4 w-16 h-16 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl opacity-80 animate-pulse">
                    </div>
                    <div
                        class="absolute -bottom-6 -left-6 w-12 h-12 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full opacity-60 animate-bounce">
                    </div>
                </div>
            </div>


        </div>
    </div>
</section>
