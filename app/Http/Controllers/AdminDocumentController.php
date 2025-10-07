<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminDocumentController extends Controller
{
    // ... (method create, listFoldersRecursive, store, update, show, download, destroy tidak berubah) ...
     public function create()
    {
        $folders = $this->listFoldersRecursive();
        return view('dosen.dokumen.create', compact('folders'));
    }
    private function listFoldersRecursive($parentId = null, $prefix = '')
    {
        $folders = Folder::where('parent_id', $parentId)->orderBy('name')->get();
        $result = [];
        foreach ($folders as $folder) {
            $result[] = [
                'id' => $folder->folder_id,
                'name' => $prefix . $folder->name,
            ];
            $children = $this->listFoldersRecursive($folder->folder_id, $prefix . '— ');
            $result = array_merge($result, $children);
        }
        return $result;
    }
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480',
            'file_name' => 'required|string|max:255',
            'folderid' => 'required|string|exists:folders,folder_id',
        ]);
        $accessToken = (new TokenDriveController)->token();
        if (!$accessToken) {
            return back()->with('error', 'Gagal mendapatkan token akses Google Drive.');
        }
        $file = $request->file('file');
        $originalFileName = $file->getClientOriginalName();
        $filePath = $file->getPathname();
        $folderId = $request->input('folderid');
        try {
            $metadata = [
                'name' => $originalFileName,
                'parents' => [$folderId]
            ];
            $response = Http::withToken($accessToken)
                ->attach('metadata', json_encode($metadata), 'metadata.json', ['Content-Type' => 'application/json; charset=UTF-8'])
                ->attach('data', file_get_contents($filePath), $originalFileName)
                ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');
            $response->throw();
            $file_id = $response->json('id');
            $document = new Document;
            $document->file_name = $request->input('file_name');
            $document->name = $originalFileName;
            $document->fileid = $file_id;
            $document->folderid = $folderId;
            $document->user_id = Auth::id();
            $document->save();
            return back()->with('success', 'File berhasil diunggah!');
        } catch (\Exception $e) {
            Log::error('Gagal upload file: ' . $e->getMessage());
            return back()->with('error', 'Upload gagal. Terjadi kesalahan pada server. Detail: ' . $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
            'verified' => 'required|boolean',
            'file' => 'nullable|file',
        ]);
        try {
            $document = Document::findOrFail($id);
            $accessToken = (new TokenDriveController())->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $newOriginalFileName = $file->getClientOriginalName();
                $filePath = $file->getPathname();
                $response = Http::withToken($accessToken)
                    ->attach('metadata', json_encode(['name' => $newOriginalFileName, 'parents' => [$document->folderid]]), 'metadata.json', ['Content-Type' => 'application/json; charset=UTF-8'])
                    ->attach('data', file_get_contents($filePath), $newOriginalFileName)
                    ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');
                $response->throw();
                $newFileId = $response->json('id');
                Http::withToken($accessToken)->delete("https://www.googleapis.com/drive/v3/files/{$document->fileid}");
                $document->fileid = $newFileId;
                $document->name = $newOriginalFileName;
            }
            if (!$request->hasFile('file') && $document->file_name !== $request->input('file_name')) {
                Http::withToken($accessToken)
                    ->patch("https://www.googleapis.com/drive/v3/files/{$document->fileid}", [
                        'name' => $request->input('file_name')
                    ]);
            }
            $document->file_name = $request->input('file_name');
            $document->verified = $request->input('verified');
            $document->save();
            return back()->with('success', 'Dokumen berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui dokumen: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui dokumen.');
        }
    }
    public function show($id)
    {
        try {
            $document = Document::findOrFail($id);
            $previewUrl = "https://drive.google.com/file/d/{$document->fileid}/view?usp=sharing";
            return redirect()->away($previewUrl);
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan pratinjau file: ' . $e->getMessage());
            return back()->with('error', 'File tidak ditemukan atau terjadi kesalahan.');
        }
    }
    public function download($id)
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
            return back()->with('error', 'File tidak ditemukan atau terjadi kesalahan saat mengunduh.');
        }
    }
    public function destroy($id)
    {
        try {
            $document = Document::findOrFail($id);
            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }
            Http::withToken($accessToken)
                ->delete("https://www.googleapis.com/drive/v3/files/{$document->fileid}")
                ->throw();
            $document->delete();
            return back()->with('success', 'File berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus file: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus file.');
        }
    }

    // FUNGSI BARU: Untuk menampilkan semua dokumen yang memiliki komentar
    public function showCommented()
    {
        // Ambil semua dokumen yang memiliki setidaknya satu komentar
        // Eager load relasi 'user', 'folder', dan 'comments' untuk efisiensi
        $commentedDocuments = Document::has('comments')
            ->with(['user', 'folder', 'comments.user'])
            ->latest('updated_at') // Tampilkan yang terbaru dikomentari di atas
            ->get();

        // Kirim data ke view baru
        return view('admin.dokumen.commented', compact('commentedDocuments'));
    }
}
