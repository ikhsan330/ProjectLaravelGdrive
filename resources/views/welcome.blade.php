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
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.8s ease-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(40px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' }
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="font-inter bg-gradient-to-br from-blue-50 via-white to-indigo-50 min-h-screen">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-blue-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <!-- Polbeng Logo -->
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-lg flex items-center justify-center">
                        <svg viewBox="0 0 24 24" class="w-6 h-6 text-white" fill="currentColor">
                            <path d="M12 2L2 7V10C2 16 6 20.5 12 22C18 20.5 22 16 22 10V7L12 2M12 4.2L20 8.1V10C20 15.1 16.9 18.8 12 20C7.1 18.8 4 15.1 4 10V8.1L12 4.2M10 10L8 12L10 14L12 12L14 14L16 12L14 10L12 12L10 10Z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">ArsipGdrive</h1>
                        <p class="text-xs text-gray-500">Politeknik Negeri Bengkalis</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/login" class="text-blue-600 hover:text-blue-700 font-medium transition-colors">Masuk</a>
                    <a href="/register" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl">Daftar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-24 pb-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="animate-fade-in">
                    <div class="inline-flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium mb-6">
                        <span class="w-2 h-2 bg-blue-600 rounded-full mr-2"></span>
                        Sistem Arsip Digital Terpadu
                    </div>
                    <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                        Arsip Dokumen
                        <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            Akreditasi
                        </span>
                        Digital
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Platform terintegrasi untuk mengelola dan mengarsipkan seluruh dokumen akreditasi Politeknik Negeri Bengkalis dengan aman dan efisien.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="/login" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl font-medium text-center">
                            Mulai Sekarang
                        </a>
                        <a href="#features" class="border-2 border-blue-200 text-blue-700 px-8 py-4 rounded-xl hover:bg-blue-50 transition-colors font-medium text-center">
                            Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>

                <!-- Right Visual -->
                <div class="animate-slide-up">
                    <div class="relative">
                        <!-- Main Card -->
                        <div class="bg-white rounded-2xl shadow-2xl p-6 animate-float">
                            <div class="flex items-center mb-4">
                                <div class="w-3 h-3 bg-red-400 rounded-full mr-2"></div>
                                <div class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></div>
                                <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">Dokumen Akreditasi 2024</h3>
                                        <p class="text-sm text-gray-500">15 file tersimpan</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a2 2 0 00-2 2v6a2 2 0 002 2h8a2 2 0 002-2V6a2 2 0 00-2-2V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">Laporan Evaluasi Diri</h3>
                                        <p class="text-sm text-gray-500">8 file tersimpan</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">Bukti Pendukung</h3>
                                        <p class="text-sm text-gray-500">23 file tersimpan</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Elements -->
                        <div class="absolute -top-4 -right-4 w-16 h-16 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl opacity-80 animate-pulse"></div>
                        <div class="absolute -bottom-6 -left-6 w-12 h-12 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full opacity-60 animate-bounce"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                    Fitur Unggulan
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Solusi lengkap untuk pengelolaan dokumen akreditasi dengan teknologi terdepan
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-8 rounded-2xl hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Penyimpanan Cloud</h3>
                    <p class="text-gray-600">Integrasikan dengan Google Drive untuk penyimpanan dokumen yang aman dan dapat diakses kapan saja.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gradient-to-br from-emerald-50 to-teal-100 p-8 rounded-2xl hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H10zm-4 1a1 1 0 011-1h.01a1 1 0 110 2H7a1 1 0 01-1-1zm1-4a1 1 0 100 2h.01a1 1 0 100-2H7zm2 0a1 1 0 100 2h.01a1 1 0 100-2H9zm2 0a1 1 0 100 2h.01a1 1 0 100-2H11z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Manajemen Kategori</h3>
                    <p class="text-gray-600">Organisasi dokumen berdasarkan kategori akreditasi untuk memudahkan pencarian dan pengelolaan.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gradient-to-br from-purple-50 to-pink-100 p-8 rounded-2xl hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Multi-Role Access</h3>
                    <p class="text-gray-600">Kontrol akses berbasis peran untuk Admin, Dosen, dan Kaprodi dengan hak akses yang sesuai.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-100 p-8 rounded-2xl hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Upload & Download</h3>
                    <p class="text-gray-600">Fitur upload dan download yang mudah dengan dukungan berbagai format file dokumen.</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-gradient-to-br from-rose-50 to-red-100 p-8 rounded-2xl hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-gradient-to-br from-rose-500 to-red-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Audit Trail</h3>
                    <p class="text-gray-600">Lacak setiap aktivitas pengguna untuk transparansi dan akuntabilitas pengelolaan dokumen.</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-gradient-to-br from-cyan-50 to-blue-100 p-8 rounded-2xl hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Kolaborasi Tim</h3>
                    <p class="text-gray-600">Fitur kolaborasi yang memungkinkan tim bekerja sama dalam pengelolaan dokumen akreditasi.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-blue-600 to-indigo-700">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8 text-center">
                <div class="text-white">
                    <div class="text-4xl font-bold mb-2">500+</div>
                    <div class="text-blue-100">Dokumen Tersimpan</div>
                </div>
                <div class="text-white">
                    <div class="text-4xl font-bold mb-2">15+</div>
                    <div class="text-blue-100">Program Studi</div>
                </div>
                <div class="text-white">
                    <div class="text-4xl font-bold mb-2">100+</div>
                    <div class="text-blue-100">Pengguna Aktif</div>
                </div>
                <div class="text-white">
                    <div class="text-4xl font-bold mb-2">99.9%</div>
                    <div class="text-blue-100">Uptime Server</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                Siap untuk Memulai?
            </h2>
            <p class="text-xl text-gray-600 mb-8">
                Bergabunglah dengan sistem arsip digital Politeknik Negeri Bengkalis dan kelola dokumen akreditasi dengan lebih efisien.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/register" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl font-medium">
                    Daftar Sekarang
                </a>
                <a href="mailto:admin@polbeng.ac.id" class="border-2 border-blue-200 text-blue-700 px-8 py-4 rounded-xl hover:bg-blue-50 transition-colors font-medium">
                    Hubungi Admin
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="col-span-2">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-lg flex items-center justify-center">
                            <svg viewBox="0 0 24 24" class="w-6 h-6 text-white" fill="currentColor">
                                <path d="M12 2L2 7V10C2 16 6 20.5 12 22C18 20.5 22 16 22 10V7L12 2M12 4.2L20 8.1V10C20 15.1 16.9 18.8 12 20C7.1 18.8 4 15.1 4 10V8.1L12 4.2M10 10L8 12L10 14L12 12L14 14L16 12L14 10L12 12L10 10Z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">ArsipGdrive</h3>
                            <p class="text-gray-400">Politeknik Negeri Bengkalis</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-4 max-w-md">
                        Sistem arsip digital terpadu untuk mengelola dokumen akreditasi dengan aman dan efisien.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/login" class="hover:text-white transition-colors">Masuk</a></li>
                        <li><a href="/register" class="hover:text-white transition-colors">Daftar</a></li>
                        <li><a href="#features" class="hover:text-white transition-colors">Fitur</a></li>
                        <li><a href="/help" class="hover:text-white transition-colors">Bantuan</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Kontak</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>Jl. Bathin Alam, Bengkalis</li>
                        <li>Riau 28711</li>
                        <li>admin@polbeng.ac.id</li>
                        <li>(0766) 21000</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 Politeknik Negeri Bengkalis. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>
</body>
</html>
