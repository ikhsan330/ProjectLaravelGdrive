<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KaprodiDocumentController extends Controller
{
    // ... (method index tidak berubah) ...
    public function index()
    {
        $rootFolders = Folder::whereNull('parent_id')->orderBy('name')->get();
        $unverifiedCounts = collect();
        $commentCounts = collect();

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

            $unverifiedResults = DB::select("{$cte} SELECT h.root_id, COUNT(d.id) as total FROM folder_hierarchy h JOIN documents d ON h.folder_id = d.folderid WHERE d.verified = false GROUP BY h.root_id", $topLevelFolderIds->all());
            $unverifiedCounts = collect($unverifiedResults)->pluck('total', 'root_id');

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

        return view('kaprodi.dokumen.index', compact('rootFolders', 'unverifiedCounts', 'commentCounts'));
    }

    /**
     * MODIFIKASI UTAMA DI METHOD INI
     */
    public function show($folder_id)
    {
        $folder = Folder::where('folder_id', $folder_id)->firstOrFail();
        $breadcrumbs = $this->getFolderAncestry($folder);

        $documents = Document::with(['user', 'comments.user'])
            ->where('folderid', $folder->folder_id)
            ->get();

        $subfolders = Folder::where('parent_id', $folder->folder_id)->orderBy('name')->get();

        // BARU: Logika untuk menandai subfolder mana yang berisi dokumen berkomentar
        $subfolderCommentMap = collect();
        if ($subfolders->isNotEmpty()) {
            $subfolderIds = $subfolders->pluck('folder_id');
            $placeholders = implode(',', array_fill(0, count($subfolderIds), '?'));

            // CTE rekursif untuk mencari komentar di dalam setiap pohon sub-folder
            $cte = "WITH RECURSIVE h (id, root_id) AS (
                        SELECT folder_id, folder_id FROM folders WHERE folder_id IN ($placeholders)
                        UNION ALL
                        SELECT f.folder_id, h.root_id FROM folders f JOIN h ON f.parent_id = h.id
                    )";

            $results = DB::select("
                {$cte}
                SELECT DISTINCT h.root_id
                FROM h
                JOIN documents d ON h.id = d.folderid
                WHERE EXISTS (SELECT 1 FROM comments c WHERE c.document_id = d.id)
            ", $subfolderIds->all());

            $subfolderCommentMap = collect($results)->pluck('root_id');
        }

        return view('kaprodi.dokumen.show', compact('folder', 'documents', 'breadcrumbs', 'subfolders', 'subfolderCommentMap'));
    }

    // ... (sisa method: getFolderAncestry, showUnverified, updateVerification, dll tidak berubah) ...
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
    public function showUnverified()
    {
        $unverifiedDocuments = Document::with(['user', 'folder'])
            ->where('verified', false)
            ->latest()
            ->get();
        return view('kaprodi.dokumen.unverified', compact('unverifiedDocuments'));
    }
    public function updateVerification(Request $request, $id)
    {
        $request->validate(['verified' => 'required|boolean']);
        try {
            $document = Document::findOrFail($id);
            $document->verified = $request->input('verified');
            $document->save();
            return back()->with('success', 'Status verifikasi dokumen berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui verifikasi dokumen: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui status verifikasi.');
        }
    }
    public function previewDocument($id)
    {
        try {
            $document = Document::findOrFail($id);
            $previewUrl = "https://drive.google.com/file/d/{$document->fileid}/view?usp=sharing";
            return redirect()->away($previewUrl);
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan pratinjau file: ' . $e->getMessage());
            return back()->with('error', 'File tidak ditemukan.');
        }
    }
    public function downloadDocument($id)
    {
        try {
            $document = Document::findOrFail($id);
            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }
            $client = new \GuzzleHttp\Client();
            $response = $client->get("https://www.googleapis.com/drive/v3/files/{$document->fileid}?alt=media", [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken],
                'stream' => true,
            ]);
            $fileName = $document->name;
            $contentType = $response->getHeaderLine('Content-Type');
            return response()->streamDownload(function () use ($response) {
                $stream = $response->getBody();
                while (!$stream->eof()) {
                    echo $stream->read(1024);
                }
            }, $fileName, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengunduh file: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengunduh file.');
        }
    }
}
