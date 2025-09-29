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
     public function createFolderForm()
    {
        $folders = $this->listRootFolders();
        $dosenFolders = $this->getAllDosenFoldersWithNames();
        $dosens = User::where('role', 'dosen')->get();
        return view('admin.dokumen.index', compact('folders', 'dosenFolders', 'dosens'));
    }




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

        // Temukan folder induk untuk mendapatkan nama
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



    // Metode untuk mengambil hanya folder induk
    public function listRootFolders()
    {
        $userId = Auth::id();
        $folders = Folder::where('user_id', $userId)
                         ->whereNull('parent_id') // Hanya ambil folder dengan parent_id null
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

    public function getAllDosenFoldersWithNames()
    {
        $dosenFolders = Folder::select('folders.*', 'users.name as user_name')
            ->join('users', 'folders.user_id', '=', 'users.id')
            ->where('users.role', 'dosen')
            ->whereNull('folders.parent_id') // Filter untuk hanya folder induk
            ->get();

        return $dosenFolders;
    }

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


    public function createFolderStructure(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string',
            'parent_folder' => 'nullable|string',
        ]);

        $user = Auth::user();
        $folderName = trim($request->input('folder_name'));
        $parentFolderId = $request->input('parent_folder');

        if ($user->role === 'admin' && empty($parentFolderId)) {
            $dosens = User::where('role', 'dosen')->get();

            $existingFolderForDosen = Folder::where('name', $folderName)
                                            ->where('parent_id', $parentFolderId)
                                            ->whereIn('user_id', $dosens->pluck('id'))
                                            ->first();

            if ($existingFolderForDosen) {
                return back()->with('error', 'Gagal membuat folder. Nama folder sudah ada untuk salah satu dosen.');
            }

            $newFolderId = $this->createFolder($folderName, $parentFolderId);
            if (!$newFolderId) {
                return back()->with('error', 'Gagal membuat folder baru di Google Drive.');
            }

            foreach ($dosens as $dosen) {
                $folder = new Folder;
                $folder->name = $folderName;
                $folder->folder_id = $newFolderId;
                $folder->parent_id = $parentFolderId;
                $folder->user_id = $dosen->id;
                $folder->save();
            }

            return back()->with('success', 'Folder "' . $folderName . '" berhasil dibuat dan ditugaskan ke semua dosen!');

        } else {
            $userId = $user->id;

            $existingFolder = Folder::where('name', $folderName)
                ->where('parent_id', $parentFolderId)
                ->where('user_id', $userId)
                ->first();

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
    }

    public function createFolder($folderName, $parentId = null)
    {
        $accessToken = (new TokenDriveController)->token();
        if (!$accessToken) {
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

            $response = Http::withToken($accessToken)
                ->patch("https://www.googleapis.com/drive/v3/files/{$folder->folder_id}", [
                    'name' => $request->input('folder_name')
                ]);

            $response->throw();

            Folder::where('folder_id', $folder->folder_id)->update(['name' => $request->input('folder_name')]);

            return back()->with('success', 'Nama folder berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memperbarui folder. Detail: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $folder = Folder::findOrFail($id);
            $folder->delete();

            return back()->with('success', 'Folder berhasil dihapus dari dosen.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus folder: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus folder. Detail: ' . $e->getMessage());
        }
    }

    public function showDosenFolder($dosen_id, $folder_id)
    {
        // Pastikan folder ini benar-benar milik dosen yang dipilih
        $folder = Folder::where('user_id', $dosen_id)
                        ->where('folder_id', $folder_id)
                        ->firstOrFail();

        // Ambil semua dokumen di dalam folder tersebut
        $documents = Document::where('folderid', $folder->folder_id)->get();

        // Ambil semua sub-folder di dalam folder ini
        $subfolders = Folder::where('user_id', $dosen_id)
                            ->where('parent_id', $folder->folder_id)
                            ->get();

        return view('admin.dokumen.show', compact('folder', 'documents', 'subfolders'));
    }

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
        $parentFolder = Folder::where('folder_id', $parentFolderId)
                              ->where('user_id', $dosenId)
                              ->firstOrFail();

        // Cek duplikasi sub-folder
        $existingFolder = Folder::where('name', $folderName)
                                ->where('parent_id', $parentFolderId)
                                ->where('user_id', $dosenId)
                                ->first();

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

}
