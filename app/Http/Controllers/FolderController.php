<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    public function createFolderForm()
    {
        $folders = $this->listFoldersRecursive();
        return view('documents.create-folder', compact('folders'));
    }

    public function listFoldersRecursive($parentId = null, $prefix = '')
    {
        $userId = Auth::id(); // Ambil ID pengguna yang sedang login
        $folders = Folder::where('user_id', $userId) // Filter berdasarkan user_id
            ->where('parent_id', $parentId)
            ->get();
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

        $userId = Auth::id();
        $user = Auth::user(); // Get the authenticated user object
        $folderName = trim($request->input('folder_name'));
        $parentFolderId = $request->input('parent_folder');

        // CHECK IF THE USER IS A 'DOSEN' TRYING TO CREATE A ROOT FOLDER
        if ($user->role === 'dosen' && empty($parentFolderId)) {
            return back()->with('error', 'Akses ditolak. Anda tidak diizinkan membuat folder induk.');
        }

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
            $folder = Folder::where('user_id', Auth::id())->findOrFail($id);
            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }

            $response = Http::withToken($accessToken)
                ->patch("https://www.googleapis.com/drive/v3/files/{$folder->folder_id}", [
                    'name' => $request->input('folder_name')
                ]);

            $response->throw();

            $folder->name = $request->input('folder_name');
            $folder->save();

            return back()->with('success', 'Nama folder berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memperbarui folder. Detail: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $folder = Folder::where('user_id', Auth::id())->findOrFail($id);
            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }

            Http::withToken($accessToken)
                ->delete("https://www.googleapis.com/drive/v3/files/{$folder->folder_id}")
                ->throw();

            $folder->delete();

            return back()->with('success', 'Folder berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus folder: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus folder. Detail: ' . $e->getMessage());
        }
    }
}
