<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    /**
     * Menyiapkan data lengkap untuk halaman utama (welcome).
     */
    public function index()
    {
        // ==================================================================
        // BAGIAN 1: Mengambil data hierarki folder untuk sidebar direktori
        // ==================================================================
        $query = "
            WITH RECURSIVE all_dosen_folders AS (
                -- Anchor: Memulai dari semua folder induk (root) milik dosen
                SELECT
                    f.id,
                    f.name,
                    f.folder_id,
                    f.parent_id,
                    f.user_id,
                    u.name as user_name
                FROM folders f
                JOIN users u ON f.user_id = u.id
                WHERE f.parent_id IS NULL AND u.role = 'dosen'

                UNION ALL

                -- Recursive: Mencari semua anak, menghubungkan parent_id (string) dengan folder_id (string)
                SELECT
                    f_child.id,
                    f_child.name,
                    f_child.folder_id,
                    f_child.parent_id,
                    f_child.user_id,
                    adf.user_name
                FROM folders f_child
                JOIN all_dosen_folders adf ON f_child.parent_id = adf.folder_id
            )
            SELECT * FROM all_dosen_folders;
        ";

        // Jalankan kueri, ubah ke collection, dan pastikan tidak ada duplikat baris
        $allFolders = collect(DB::select($query))->unique('id');

        // Siapkan data untuk view:
        // 1. Peta anak folder (digunakan oleh view rekursif)
        $groupedFolders = $allFolders->groupBy('parent_id');
        // 2. Folder induk tingkat atas yang dikelompokkan berdasarkan nama (untuk tampilan awal sidebar)
        $groupedRootFolders = $allFolders->where('parent_id', null)->groupBy('name');


        // ==================================================================
        // BAGIAN 2: Mengambil data untuk "Featured Folders" di banner
        // ==================================================================

        // 1. Ambil SEMUA folder induk, diurutkan dari yang paling baru
        $allRootFolders = Folder::whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Gunakan method collection `unique()` untuk memfilter berdasarkan 'name' dan ambil 3 folder pertama.
        $featuredFolders = $allRootFolders->unique('name')->values()->take(3);

        // 3. Untuk 3 folder unik tersebut, hitung total file di dalamnya (termasuk semua subfolder).
        $featuredFolders->each(function ($folder) {
            // Lakukan Eager Loading relasi yang dibutuhkan HANYA untuk folder ini.
            // Ini jauh lebih efisien daripada memuat semuanya di awal.
            $folder->load('documents', 'childrenRecursive');

            // Panggil method dari Model untuk melakukan perhitungan rekursif.
            $folder->total_files = $folder->getTotalFileCount();
        });


        // ==================================================================
        // BAGIAN 3: Mengirim semua data ke view
        // ==================================================================
        return view('welcome', [
            'groupedRootFolders' => $groupedRootFolders,
            'groupedFolders'     => $groupedFolders,
            'featuredFolders'    => $featuredFolders // Sekarang membawa data total_files
        ]);
    }

    /**
     * Mengambil isi dari folder spesifik (subfolder dan dokumen).
     * (Tidak perlu diubah)
     */
    public function getFolderContents(Folder $folder)
    {
        $subfolders = Folder::where('parent_id', $folder->folder_id)
            ->where('user_id', $folder->user_id)
            ->orderBy('name')
            ->get();

        $documents = Document::where('folderid', $folder->folder_id)
            ->where('user_id', $folder->user_id)
            ->orderBy('name')
            ->get();

        return view('_folder_contents', [
            'selectedFolder' => $folder,
            'subfolders'     => $subfolders,
            'documents'      => $documents,
        ]);
    }
}
