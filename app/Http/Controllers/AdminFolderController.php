<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminFolderController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen folder admin.
     * Mengambil dan mengelompokkan folder untuk ditampilkan.
     */
    public function createFolderForm()
    {
        $folders = $this->listRootFolders();

        $dosenFoldersData = $this->getAllDosenFoldersWithNames();
        $groupedFolders = $dosenFoldersData->groupBy('name');

        $dosens = User::where('role', 'dosen')->get();

        return view('admin.dokumen.index', compact('folders', 'groupedFolders', 'dosens'));
    }

    /**
     * Menugaskan kembali folder yang sudah ada ke dosen lain.
     */
    public function reassignFolder(Request $request)
    {
        $request->validate([
            'folder_id' => 'required|string',
            'dosen_id' => 'required|string|exists:users,id',
        ]);

        $folderId = $request->input('folder_id');
        $dosenId = $request->input('dosen_id');

        // Cek apakah folder sudah ditugaskan ke dosen ini
        $existing = Folder::where('folder_id', $folderId)
            ->where('user_id', $dosenId)
            ->exists();

        if ($existing) {
            return back()->with('error', 'Folder ini sudah ditugaskan ke dosen yang dipilih.');
        }

        // Temukan folder sumber untuk mendapatkan nama dan parent_id
        $sourceFolder = Folder::where('folder_id', $folderId)->firstOrFail();

        // Buat record folder baru di database
        $newFolder = new Folder;
        $newFolder->name = $sourceFolder->name;
        $newFolder->folder_id = $folderId;
        $newFolder->parent_id = $sourceFolder->parent_id;
        $newFolder->user_id = $dosenId;
        $newFolder->save();

        return back()->with('success', 'Folder berhasil ditugaskan kembali ke dosen.');
    }

    /**
     * Mengambil daftar semua folder induk yang unik untuk digunakan di dropdown.
     */
    public function listRootFolders()
    {
        $folders = Folder::whereNull('parent_id')
            ->select('folder_id', 'name')
            ->distinct()
            ->get();

        $result = [];
        foreach ($folders as $folder) {
            $result[] = [
                'id' => $folder->folder_id,
                'name' => $folder->name,
            ];
        }
        return $result;
    }

    /**
     * Mengambil semua folder induk milik dosen beserta nama dosennya.
     */
    public function getAllDosenFoldersWithNames()
    {
        $dosenFolders = Folder::select('folders.*', 'users.name as user_name')
            ->join('users', 'folders.user_id', '=', 'users.id')
            ->where('users.role', 'dosen')
            ->whereNull('folders.parent_id')
            ->orderBy('folders.name') // Urutkan berdasarkan nama folder
            ->orderBy('users.name')   // Lalu urutkan berdasarkan nama dosen
            ->get();

        return $dosenFolders;
    }

    /**
     * Membuat struktur folder baru, baik untuk semua dosen (oleh admin)
     * atau untuk diri sendiri (oleh dosen).
     */
    public function createFolderStructure(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string',
            'parent_folder' => 'nullable|string', // Dibiarkan untuk kompatibilitas jika ada
        ]);

        $user = Auth::user();
        $folderName = trim($request->input('folder_name'));
        $parentFolderId = $request->input('parent_folder'); // Ini untuk subfolder, bukan dari modal utama

        // Jika admin membuat folder induk baru dari modal utama
        if ($user->role === 'admin' && empty($parentFolderId)) {
            $dosens = User::where('role', 'dosen')->get();

            // Cek apakah nama folder sudah ada untuk salah satu dosen
            $existingFolderForDosen = Folder::where('name', $folderName)
                ->whereNull('parent_id')
                ->whereIn('user_id', $dosens->pluck('id'))
                ->exists();

            if ($existingFolderForDosen) {
                return back()->with('error', 'Gagal membuat folder. Nama folder sudah ada.');
            }

            // Buat satu folder di Google Drive
            $newFolderId = $this->createFolder($folderName, null);
            if (!$newFolderId) {
                return back()->with('error', 'Gagal membuat folder baru di Google Drive.');
            }

            // Simpan record folder ini untuk setiap dosen
            foreach ($dosens as $dosen) {
                $folder = new Folder;
                $folder->name = $folderName;
                $folder->folder_id = $newFolderId;
                $folder->parent_id = null;
                $folder->user_id = $dosen->id;
                $folder->save();
            }

            return back()->with('success', 'Folder "' . $folderName . '" berhasil dibuat dan ditugaskan ke semua dosen!');
        }

        // Logika untuk membuat subfolder atau folder oleh non-admin
        $userId = $user->id;
        $existingFolder = Folder::where('name', $folderName)
            ->where('parent_id', $parentFolderId)
            ->where('user_id', $userId)
            ->exists();

        if ($existingFolder) {
            return back()->with('error', 'Gagal membuat folder. Nama folder sudah ada di dalam folder induk yang dipilih.');
        }

        $newFolderId = $this->createFolder($folderName, $parentFolderId);
        if (!$newFolderId) {
            return back()->with('error', 'Gagal membuat folder baru.');
        }

        $folder = new Folder;
        $folder->name = $folderName;
        $folder->folder_id = $newFolderId;
        $folder->parent_id = $parentFolderId;
        $folder->user_id = $userId;
        $folder->save();

        return back()->with('success', 'Folder "' . $folderName . '" berhasil dibuat dan disimpan!');
    }

    /**
     * Helper method untuk membuat folder di Google Drive via API.
     */
    public function createFolder($folderName, $parentId = null)
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

    /**
     * Memperbarui nama folder di Google Drive dan di database lokal.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'folder_name' => 'required|string',
        ]);

        try {
            $folder = Folder::findOrFail($id);
            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }

            // Update nama di Google Drive
            Http::withToken($accessToken)
                ->patch("https://www.googleapis.com/drive/v3/files/{$folder->folder_id}", [
                    'name' => $request->input('folder_name')
                ])->throw();

            // Update nama di semua record database yang memiliki folder_id yang sama
            Folder::where('folder_id', $folder->folder_id)
                  ->update(['name' => $request->input('folder_name')]);

            return back()->with('success', 'Nama folder berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error saat update folder: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui folder.');
        }
    }

    /**
     * Menghapus penugasan folder dari seorang dosen (hanya dari database).
     */
    public function destroy($id)
    {
        try {
            $folder = Folder::findOrFail($id);
            $folder->delete();

            return back()->with('success', 'Penugasan folder berhasil dihapus dari dosen.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus folder: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus penugasan folder.');
        }
    }

    /**
     * Menampilkan isi dari folder spesifik milik dosen tertentu (dokumen dan subfolder).
     */
    public function showDosenFolder($dosen_id, $folder_id)
    {
        $folder = Folder::where('user_id', $dosen_id)
            ->where('folder_id', $folder_id)
            ->firstOrFail();

        $documents = Document::where('folderid', $folder->folder_id)->get();

        $subfolders = Folder::where('user_id', $dosen_id)
            ->where('parent_id', $folder->folder_id)
            ->get();

        return view('admin.dokumen.show', compact('folder', 'documents', 'subfolders'));
    }

    /**
     * Menyimpan struktur subfolder baru untuk dosen tertentu.
     */
    public function storeSubfolderStructure(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string',
            'parent_folder_id' => 'required|string',
            'parent_dosen_id' => 'required|string|exists:users,id',
        ]);

        $folderName = trim($request->input('folder_name'));
        $parentFolderId = $request->input('parent_folder_id');
        $dosenId = $request->input('parent_dosen_id');

        // Pastikan folder induk ada di database untuk dosen ini
        Folder::where('folder_id', $parentFolderId)
            ->where('user_id', $dosenId)
            ->firstOrFail();

        // Cek duplikasi sub-folder
        $existingFolder = Folder::where('name', $folderName)
            ->where('parent_id', $parentFolderId)
            ->where('user_id', $dosenId)
            ->exists();

        if ($existingFolder) {
            return back()->with('error', 'Gagal membuat folder. Nama folder sudah ada di dalam folder induk ini.');
        }

        // Buat folder di Google Drive
        $newFolderId = $this->createFolder($folderName, $parentFolderId);
        if (!$newFolderId) {
            return back()->with('error', 'Gagal membuat folder baru di Google Drive.');
        }

        // Simpan record sub-folder ke database untuk dosen ini
        $newFolder = new Folder;
        $newFolder->name = $folderName;
        $newFolder->folder_id = $newFolderId;
        $newFolder->parent_id = $parentFolderId;
        $newFolder->user_id = $dosenId;
        $newFolder->save();

        return back()->with('success', 'Sub-folder "' . $folderName . '" berhasil dibuat dan disimpan!');
    }

    /**
     * Metode ini tidak digunakan di alur utama halaman admin,
     * tetapi mungkin digunakan di tempat lain.
     */
    public function listFoldersRecursive($parentId = null, $prefix = '')
    {
        $isAdmin = Auth::user()->role === 'admin';

        if ($isAdmin) {
            $folders = Folder::join('users', 'folders.user_id', '=', 'users.id')
                ->where('users.role', 'dosen')
                ->where('folders.parent_id', $parentId)
                ->select('folders.*')
                ->get();
        } else {
            $userId = Auth::id();
            $folders = Folder::where('user_id', $userId)
                ->where('parent_id', $parentId)
                ->get();
        }

        $result = [];
        foreach ($folders as $folder) {
            $result[] = [
                'id' => $folder->folder_id,
                'name' => $prefix . $folder->name,
            ];

            $children = $this->listFoldersRecursive($folder->folder_id, $prefix . $folder->name . '/');
            $result = array_merge($result, $children);
        }

        return $result;
    }

    public function destroyMasterFolder($folder_id)
    {
        try {
            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }

            // Langkah 1: Hapus folder dari Google Drive
            Http::withToken($accessToken)
                ->delete("https://www.googleapis.com/drive/v3/files/{$folder_id}")
                ->throw(); // Akan melempar exception jika gagal

            // Langkah 2: Hapus semua record folder dari database lokal
            Folder::where('folder_id', $folder_id)->delete();

            return back()->with('success', 'Folder dan semua penugasannya berhasil dihapus secara permanen.');

        } catch (\Exception $e) {
            Log::error('Gagal menghapus folder master: ' . $e->getMessage());
            // Cek jika error karena file tidak ditemukan (mungkin sudah dihapus manual)
            if ($e instanceof \Illuminate\Http\Client\RequestException && $e->response->status() == 404) {
                // Jika folder tidak ditemukan di Drive, tetap hapus dari DB
                Folder::where('folder_id', $folder_id)->delete();
                return back()->with('success', 'Folder tidak ditemukan di Google Drive, tetapi berhasil dihapus dari database.');
            }
            return back()->with('error', 'Terjadi kesalahan saat menghapus folder.');
        }
    }


      public function destroySubfolder($id)
    {
        try {
            // 1. Temukan record sub-folder di database
            $subfolder = Folder::findOrFail($id);

            // Pastikan ini adalah sub-folder (memiliki parent_id)
            if (is_null($subfolder->parent_id)) {
                return back()->with('error', 'Aksi ini hanya untuk sub-folder.');
            }

            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }

            // 2. Hapus folder dari Google Drive
            Http::withToken($accessToken)
                ->delete("https://www.googleapis.com/drive/v3/files/{$subfolder->folder_id}")
                ->throw();

            // 3. Hapus record dari database lokal
            $subfolder->delete();

            return back()->with('success', 'Sub-folder berhasil dihapus secara permanen.');

        } catch (\Exception $e) {
            Log::error('Gagal menghapus sub-folder: ' . $e->getMessage());

            if ($e instanceof \Illuminate\Http\Client\RequestException && $e->response->status() == 404) {
                // Jika folder tidak ada di Drive, paksa hapus dari DB
                Folder::findOrFail($id)->delete();
                return back()->with('success', 'Sub-folder tidak ditemukan di Google Drive, tetapi berhasil dihapus dari database.');
            }

            return back()->with('error', 'Terjadi kesalahan saat menghapus sub-folder.');
        }
    }
}
