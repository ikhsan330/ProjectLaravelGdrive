<p align="center">
![Banner Halaman Utama ArsipGdrive](URL_PUBLIK_GAMBAR_ANDA)
</p>

<h1 align="center">ArsipDrive - Sistem Arsip Digital Polbeng</h1>

<p align="center">
Platform terintegrasi untuk mengelola dan mengarsipkan seluruh dokumen akreditasi Politeknik Negeri Bengkalis dengan aman dan efisien.
</p>

📖 Tentang Aplikasi
ArsipDrive adalah sebuah platform aplikasi web yang dirancang khusus untuk menjawab kebutuhan manajemen dokumen di lingkungan Politeknik Negeri Bengkalis. Aplikasi ini memfasilitasi proses pengarsipan, pengelolaan, dan penelusuran dokumen-dokumen penting, terutama yang berkaitan dengan proses akreditasi.

Dibangun dengan Laravel, aplikasi ini menawarkan sintaks yang elegan dan ekspresif, dengan fokus pada pengalaman pengembangan yang menyenangkan dan kreatif. Tujuannya adalah untuk menyederhanakan tugas-tugas kompleks dalam pengelolaan arsip digital berskala institusi.

✨ Fitur Utama
Aplikasi ini dilengkapi dengan berbagai fitur untuk menunjang kebutuhan manajemen arsip digital:

🗂️ Manajemen Folder Hirarkis: Admin dapat membuat struktur folder induk dan subfolder yang dinamis untuk mengorganisir dokumen berdasarkan kategori, standar, atau kebutuhan lainnya.

☁️ Integrasi Penuh dengan Google Drive: Seluruh file dan folder secara fisik disimpan di Google Drive, sementara database aplikasi mengelola metadata, izin, dan strukturnya. Pembuatan, pembaruan nama, dan penghapusan folder tersinkronisasi secara otomatis.

👥 Sistem Multi-User (Admin & Dosen): Terdapat dua peran utama:

Admin: Memiliki kontrol penuh atas struktur folder, manajemen pengguna (dosen), dan verifikasi dokumen.

Dosen: Dapat mengunggah dan mengelola dokumen di dalam folder yang telah ditugaskan kepadanya.

🔐 Isolasi Data yang Aman: Setiap dosen hanya dapat melihat dan mengelola folder serta dokumen miliknya sendiri, memastikan privasi dan keamanan data.

📊 Dasbor Admin Terpusat: Antarmuka khusus untuk admin guna membuat "Master Folder" yang secara otomatis ditugaskan ke semua dosen, menugaskan ulang folder, dan memonitor status dokumen.

🌍 Direktori Dokumen Publik: Halaman utama yang dapat diakses publik untuk menelusuri hierarki folder dan melihat daftar dokumen yang tersedia, tanpa bisa mengunduh file sensitif.

✅ Sistem Verifikasi Dokumen: Admin dapat menandai dokumen sebagai "Terverifikasi", memberikan status validitas pada arsip yang diunggah.

🚀 Teknologi yang Digunakan
Backend: PHP, Laravel Framework

Frontend: HTML, Tailwind CSS, JavaScript

Database: PostgreSQL (atau database relasional lain yang didukung Laravel)

API Eksternal: Google Drive API

⚙️ Instalasi & Konfigurasi
Untuk menjalankan aplikasi ini secara lokal, ikuti langkah-langkah berikut:

Clone Repositori

Bash

git clone https://www.andarepository.com/
cd [nama-folder-proyek]
Install Dependensi

Bash

composer install
npm install
Konfigurasi Lingkungan

Salin file .env.example menjadi .env.

Bash

cp .env.example .env
Buat kunci aplikasi baru.

Bash

php artisan key:generate
Atur koneksi database Anda di file .env (variabel DB_*).

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=arsip_polbeng
DB_USERNAME=user
DB_PASSWORD=password
Atur kredensial untuk Google Drive API di file .env. Anda memerlukan GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REDIRECT_URI, dan GOOGLE_REFRESH_TOKEN.

Migrasi Database
Jalankan migrasi untuk membuat semua tabel yang diperlukan di database.

Bash

php artisan migrate
Jika Anda memiliki seeder, jalankan juga:

Bash

php artisan db:seed
Compile Aset Frontend

Bash

npm run dev
Jalankan Server Lokal

Bash

php artisan serve
Aplikasi sekarang akan berjalan di http://127.0.0.1:8000.

🤝 Kontribusi
Terima kasih telah mempertimbangkan untuk berkontribusi pada proyek ini! Panduan kontribusi dapat ditemukan di dokumentasi Laravel.

📄 Lisensi
Aplikasi ini merupakan perangkat lunak sumber terbuka yang dilisensikan di bawah Lisensi MIT.
