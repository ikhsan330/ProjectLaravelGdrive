<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * Menampilkan halaman utama (welcome page).
     * Menyiapkan daftar folder induk dan folder unggulan untuk ditampilkan.
     */
    public function index(): View
    {
        // 1. Mengambil semua folder induk (root) yang bersifat publik.
        // Logika kompleks dengan CTE dan grouping per dosen tidak diperlukan lagi.
        $rootFolders = Folder::whereNull('parent_id')
            ->orderBy('name', 'asc')
            ->get();

        // 2. Mengambil folder unggulan (misalnya, 3 folder terbaru yang dibuat).
        // Kita perlu memuat relasi untuk menghitung jumlah file di dalamnya.
        $featuredFolders = Folder::whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // 3. Eager load relasi dan hitung total file untuk setiap folder unggulan.
        // Pastikan model Folder memiliki relasi 'documents' dan 'childrenRecursive'.
        $featuredFolders->each(function ($folder) {
            // Memuat semua dokumen di semua level subfolder dan menghitungnya
            $folder->load('documents', 'childrenRecursive.documents');
            $folder->total_files = $folder->getTotalFileCount(); // Asumsi method ini ada di model Folder
        });

        // 4. Kirim data yang sudah disederhanakan ke view.
        return view('welcome', [
            'rootFolders'     => $rootFolders,
            'featuredFolders' => $featuredFolders,
        ]);
    }

    /**
     * Mengambil isi folder (dokumen dan subfolder) untuk ditampilkan secara dinamis (misalnya via AJAX).
     * Logika ini disederhanakan untuk folder publik.
     */
    public function getFolderContents(Folder $folder): View
    {
        // Ambil subfolder dari folder yang dipilih.
        // Klausa ->where('user_id', ...) dihapus.
        $subfolders = Folder::where('parent_id', $folder->folder_id)
            ->orderBy('name')
            ->get();

        // Ambil dokumen dari folder yang dipilih, beserta data pemiliknya (user).
        // Klausa ->where('user_id', ...) dihapus agar semua dokumen tampil.
        $documents = Document::with('user') // Eager load 'user' untuk menampilkan nama pemilik
            ->where('folderid', $folder->folder_id)
            ->orderBy('file_name')
            ->get();

        return view('partials._folder_contents', [
            'selectedFolder' => $folder,
            'subfolders'     => $subfolders,
            'documents'      => $documents,
        ]);
    }
}
