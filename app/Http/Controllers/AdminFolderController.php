<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;

class AdminFolderController extends Controller
{
    public function index()
    {
        $rootFolders = Folder::whereNull('parent_id')->orderBy('name')->get();
        $unverifiedCounts = collect();
        $commentCounts = collect(); // BARU: Untuk menampung data komentar

        if ($rootFolders->isNotEmpty()) {
            $topLevelFolderIds = $rootFolders->pluck('folder_id');
            $placeholders = implode(',', array_fill(0, count($topLevelFolderIds), '?'));

            $cte = "
                WITH RECURSIVE folder_hierarchy (folder_id, root_id) AS (
                    SELECT folder_id, folder_id as root_id FROM folders WHERE folder_id IN ($placeholders)
                    UNION ALL
                    SELECT f.folder_id, h.root_id FROM folders f JOIN folder_hierarchy h ON f.parent_id = h.folder_id
                )
            ";

            // Query untuk dokumen belum terverifikasi (tetap ada)
            $unverifiedResults = DB::select("{$cte} SELECT h.root_id, COUNT(d.id) as total FROM folder_hierarchy h JOIN documents d ON h.folder_id = d.folderid WHERE d.verified = false GROUP BY h.root_id", $topLevelFolderIds->all());
            $unverifiedCounts = collect($unverifiedResults)->pluck('total', 'root_id');

            // BARU: Query untuk dokumen yang memiliki komentar
            $commentResults = DB::select("
                {$cte}
                SELECT h.root_id, COUNT(DISTINCT c.document_id) as total
                FROM folder_hierarchy h
                JOIN documents d ON h.folder_id = d.folderid
                JOIN comments c ON d.id = c.document_id
                GROUP BY h.root_id
            ", $topLevelFolderIds->all());
            $commentCounts = collect($commentResults)->pluck('total', 'root_id');
        }

        return view('admin.dokumen.index', compact('rootFolders', 'unverifiedCounts', 'commentCounts'));
    }

    public function showFolder($folder_id)
    {
        $folder = Folder::where('folder_id', $folder_id)->firstOrFail();
        $breadcrumbs = $this->getFolderAncestry($folder);

        // MODIFIKASI: Eager load relasi 'comments' untuk setiap dokumen
        $documents = Document::with(['user', 'comments'])
            ->where('folderid', $folder->folder_id)
            ->get();

        $subfolders = Folder::where('parent_id', $folder->folder_id)->orderBy('name')->get();

        // Logika untuk notifikasi verifikasi pada subfolder (tetap ada)
        $unverifiedSubfolderMap = collect();
        if ($subfolders->isNotEmpty()) {
            // ... (logika unverified subfolder tidak berubah)
        }

        // BARU: Logika untuk menandai subfolder mana yang berisi dokumen berkomentar
        $subfolderCommentMap = collect();
        $subfolderIds = $subfolders->pluck('folder_id');
        if ($subfolderIds->isNotEmpty()) {
            $placeholders = implode(',', array_fill(0, count($subfolderIds), '?'));
            $cte = "WITH RECURSIVE h (id, root_id) AS (SELECT folder_id, folder_id FROM folders WHERE folder_id IN ($placeholders) UNION ALL SELECT f.folder_id, h.root_id FROM folders f JOIN h ON f.parent_id = h.id)";
            $results = DB::select("{$cte} SELECT DISTINCT h.root_id FROM h JOIN documents d ON h.id = d.folderid WHERE EXISTS (SELECT 1 FROM comments c WHERE c.document_id = d.id)", $subfolderIds->all());
            $subfolderCommentMap = collect($results)->pluck('root_id');
        }

        return view('admin.dokumen.show', compact('folder', 'documents', 'breadcrumbs', 'subfolders', 'unverifiedSubfolderMap', 'subfolderCommentMap'));
    }

    // ... (Semua method lain: create, store, update, destroy, dll tidak berubah) ...
     public function createFolderStructure(Request $request)
    {
        $request->validate(['folder_name' => 'required|string|max:255']);
        $folderName = trim($request->input('folder_name'));
        $existing = Folder::where('name', $folderName)->whereNull('parent_id')->exists();
        if ($existing) {
            return back()->with('error', 'Nama folder sudah ada di level ini.');
        }
        $newFolderId = $this->createFolderInDrive($folderName, null);
        if (!$newFolderId) {
            return back()->with('error', 'Gagal membuat folder di Google Drive. Cek log untuk detail.');
        }
        $folder = new Folder;
        $folder->name = $folderName;
        $folder->folder_id = $newFolderId;
        $folder->parent_id = null;
        $folder->save();
        return back()->with('success', 'Folder publik "' . $folderName . '" berhasil dibuat!');
    }
    public function storeSubfolderStructure(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string|max:255',
            'parent_folder_id' => 'required|string|exists:folders,folder_id',
        ]);
        $folderName = trim($request->input('folder_name'));
        $parentFolderId = $request->input('parent_folder_id');
        $existing = Folder::where('name', $folderName)->where('parent_id', $parentFolderId)->exists();
        if ($existing) {
            return back()->with('error', 'Nama sub-folder sudah ada di dalam folder ini.');
        }
        $newFolderId = $this->createFolderInDrive($folderName, $parentFolderId);
        if (!$newFolderId) {
            return back()->with('error', 'Gagal membuat sub-folder di Google Drive.');
        }
        $newFolder = new Folder;
        $newFolder->name = $folderName;
        $newFolder->folder_id = $newFolderId;
        $newFolder->parent_id = $parentFolderId;
        $newFolder->save();
        return back()->with('success', 'Sub-folder "' . $folderName . '" berhasil dibuat!');
    }
    public function update(Request $request, $id)
    {
        $request->validate(['folder_name' => 'required|string|max:255']);
        $newName = $request->input('folder_name');
        try {
            $folder = Folder::findOrFail($id);
            $existing = Folder::where('name', $newName)
                ->where('parent_id', $folder->parent_id)
                ->where('id', '!=', $id)
                ->exists();
            if ($existing) {
                return back()->with('error', 'Nama folder sudah ada di level yang sama.');
            }
            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }
            Http::withToken($accessToken)
                ->patch("https://www.googleapis.com/drive/v3/files/{$folder->folder_id}", ['name' => $newName])
                ->throw();
            $folder->name = $newName;
            $folder->save();
            return back()->with('success', 'Nama folder berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error saat update folder: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui folder.');
        }
    }
    public function destroy($folder_id)
    {
        try {
            $folder = Folder::where('folder_id', $folder_id)->firstOrFail();
            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }
            Http::withToken($accessToken)
                ->delete("https://www.googleapis.com/drive/v3/files/{$folder->folder_id}")
                ->throw();
            $folder->delete();
            return back()->with('success', 'Folder berhasil dihapus secara permanen.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus folder: ' . $e->getMessage());
            if ($e instanceof \Illuminate\Http\Client\RequestException && $e->response->status() == 404) {
                Folder::where('folder_id', $folder_id)->delete();
                return back()->with('success', 'Folder tidak ditemukan di Google Drive, tetapi berhasil dihapus dari database.');
            }
            return back()->with('error', 'Terjadi kesalahan saat menghapus folder.');
        }
    }
    private function createFolderInDrive($folderName, $parentId = null)
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
            $response = Http::withToken($accessToken)->post('https://www.googleapis.com/drive/v3/files', $folderMetadata);
            $response->throw();
            return $response->json('id');
        } catch (RequestException $e) {
            Log::error('Gagal membuat folder di Google Drive: ' . $e->getMessage());
            return null;
        }
    }
    private function getFolderAncestry(Folder $folder)
    {
        $breadcrumbs = [];
        $current = $folder;
        while ($current && $current->parent_id) {
            $parent = Folder::where('folder_id', $current->parent_id)->first();
            if ($parent) {
                array_unshift($breadcrumbs, $parent);
                $current = $parent;
            } else {
                break;
            }
        }
        return $breadcrumbs;
    }
}
