<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DosenFolderController extends Controller
{
    /**
     * Menampilkan daftar folder induk yang dimiliki oleh dosen yang sedang login.
     */
    public function index()
    {
        $userId = Auth::id();

        // HANYA AMBIL FOLDER INDUK MILIK DOSEN YANG SEDANG LOGIN
        $folders = DB::table('folders as root_folders')
            // 1. Mulai dari folder induk milik dosen yang sedang login
            ->where('root_folders.user_id', $userId)
            ->whereNull('root_folders.parent_id')

            // 2. Gabungkan (join) dengan sub-folder yang memiliki parent_id yang cocok
            ->leftJoin('folders as sub_folders', 'sub_folders.parent_id', '=', 'root_folders.folder_id')

            // 3. Gabungkan (join) dengan dokumen di dalam sub-folder tersebut,
            //    DAN HANYA hitung dokumen yang statusnya belum diverifikasi.
            ->leftJoin('documents', function ($join) {
                $join->on('documents.folderid', '=', 'sub_folders.folder_id')
                    ->where('documents.verified', '=', false);
            })

            // 4. Pilih semua data folder induk, dan hitung jumlah dokumen yang cocok
            ->select(
                'root_folders.*',
                DB::raw('COUNT(documents.id) as unverified_documents_count')
            )

            // 5. Kelompokkan hasilnya berdasarkan folder induk
            ->groupBy('root_folders.id') // Group by primary key untuk hasil yang unik
            ->orderBy('root_folders.name', 'asc')
            ->get();

        return view('dosen.dokumen.index', compact('folders'));
    }

    /**
     * Menampilkan isi dari sebuah folder (sub-folder dan dokumen) milik dosen.
     */
    public function show($folder_id)
    {
        $userId = Auth::id();

        // Validasi kepemilikan folder yang sedang dibuka (tidak berubah)
        $folder = Folder::where('folder_id', $folder_id)
            ->where('user_id', $userId)
            ->firstOrFail();
        $breadcrumbs = $this->getFolderAncestry($folder);
        // AMBIL SUB-FOLDER DAN HITUNG DOKUMEN BELUM DIVERIFIKASI DI DALAMNYA
        $subfolders = Folder::where('parent_id', $folder->folder_id)
            ->where('user_id', $userId)
            ->withCount(['documents as unverified_documents_count' => function ($query) {
                $query->where('verified', false);
            }])
            ->orderBy('name', 'asc')
            ->get();

        // Ambil dokumen yang ada di level ini (tidak berubah)
        $documents = Document::where('folderid', $folder->folder_id)
            ->where('user_id', $userId)
            ->orderBy('file_name', 'asc')
            ->get();


        // Anda perlu membuat view baru, misalnya 'dosen.folder.show'
        return view('dosen.dokumen.show', compact('folder', 'documents', 'subfolders', 'breadcrumbs'));
    }

    private function getFolderAncestry(Folder $folder)
    {
        $breadcrumbs = [];
        $current = $folder;

        // Terus berjalan mundur selama folder saat ini memiliki induk
        while ($current && $current->parent_id) {
            $parent = Folder::where('folder_id', $current->parent_id)
                ->where('user_id', Auth::user()->id)
                ->first();

            if ($parent) {
                // Masukkan ke awal array agar urutannya benar (Induk -> Anak)
                array_unshift($breadcrumbs, $parent);
                $current = $parent;
            } else {
                // Hentikan jika ada rantai yang terputus
                break;
            }
        }

        return $breadcrumbs;
    }

    /**
     * Menyimpan sub-folder baru yang dibuat oleh dosen.
     */
    public function storeSubfolder(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string|max:255',
            'parent_folder_id' => 'required|string',
        ]);

        $userId = Auth::id();
        $folderName = trim($request->input('folder_name'));
        $parentFolderId = $request->input('parent_folder_id');

        // !! PENTING: Validasi kepemilikan folder induk tempat sub-folder akan dibuat
        $parentFolder = Folder::where('folder_id', $parentFolderId)
            ->where('user_id', $userId)
            ->firstOrFail();

        // Cek duplikasi sub-folder di dalam folder induk ini untuk user ini
        $existing = Folder::where('name', $folderName)
            ->where('parent_id', $parentFolderId)
            ->where('user_id', $userId)
            ->exists();

        if ($existing) {
            return back()->with('error', 'Nama sub-folder sudah ada di dalam folder ini.');
        }

        // Buat folder di Google Drive
        $newFolderId = $this->createFolder($folderName, $parentFolderId);
        if (!$newFolderId) {
            return back()->with('error', 'Gagal membuat folder baru di Google Drive.');
        }

        // Simpan record sub-folder ke database
        $subfolder = new Folder;
        $subfolder->name = $folderName;
        $subfolder->folder_id = $newFolderId;
        $subfolder->parent_id = $parentFolderId;
        $subfolder->user_id = $userId;
        $subfolder->save();

        return back()->with('success', 'Sub-folder berhasil dibuat!');
    }

    /**
     * Memperbarui nama sub-folder milik dosen.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'folder_name' => 'required|string',
        ]);

        $userId = Auth::id();

        // !! PENTING: Cari folder berdasarkan ID dan pastikan itu milik user yang login
        $folder = Folder::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        // Logika update sama seperti di admin, karena sudah spesifik per folder
        try {
            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }

            Http::withToken($accessToken)
                ->patch("https://www.googleapis.com/drive/v3/files/{$folder->folder_id}", [
                    'name' => $request->input('folder_name')
                ])->throw();

            // Karena folder_id unik untuk setiap sub-folder yang dibuat dosen, update ini aman.
            $folder->update(['name' => $request->input('folder_name')]);

            return back()->with('success', 'Nama folder berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error saat update folder: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui folder.');
        }
    }

    /**
     * Menghapus sub-folder milik dosen.
     */
    public function destroySubfolder($id)
    {
        $userId = Auth::id();

        // !! PENTING: Cari folder berdasarkan ID dan pastikan itu milik user yang login
        $subfolder = Folder::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        // Dosen tidak boleh menghapus folder induk
        if (is_null($subfolder->parent_id)) {
            return back()->with('error', 'Anda tidak diizinkan menghapus folder induk.');
        }

        try {
            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }

            // Hapus folder dari Google Drive
            Http::withToken($accessToken)
                ->delete("https://www.googleapis.com/drive/v3/files/{$subfolder->folder_id}");

            // Hapus record dari database
            $subfolder->delete();

            return back()->with('success', 'Sub-folder berhasil dihapus secara permanen.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus sub-folder: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus sub-folder.');
        }
    }

    /**
     * Helper method untuk membuat folder di Google Drive.
     * Diambil dari AdminFolderController.
     */
    private function createFolder($folderName, $parentId = null)
    {
        $accessToken = (new TokenDriveController)->token();
        if (!$accessToken) {
            Log::error('Gagal mendapatkan token akses Google Drive.');
            return null;
        }

        $folderMetadata = [
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder'
        ];

        if ($parentId) {
            $folderMetadata['parents'] = [$parentId];
        }

        try {
            $response = Http::withToken($accessToken)
                ->post('https://www.googleapis.com/drive/v3/files', $folderMetadata);
            $response->throw();
            return $response->json('id');
        } catch (\Exception $e) {
            Log::error('Gagal membuat folder di Google Drive: ' . $e->getMessage());
            return null;
        }
    }
}
