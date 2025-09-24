<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\FolderController;

class AdminDocumentController extends Controller
{
    public function index()
    {
        $folderController = new FolderController();

        $allFolders = Folder::all();
        $allDocuments = Document::all();
        $folders = $folderController->listFoldersRecursive();
        $foldersTree = $this->buildFolderTree($allFolders);
        $foldersWithDocuments = $this->assignDocumentsToFolders($foldersTree, $allDocuments);

        return view('admin.dokumen.index', compact('folders', 'foldersWithDocuments'));
    }


    public function create()
    {
        $folderController = new FolderController();
        $folders = $folderController->listFoldersRecursive();
        return view('dosen.dokumen.create', compact('folders'));
    }

        public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'file_name' => 'required|string|max:255',
            'folderid' => 'nullable|string',
        ]);

        $accessToken = (new TokenDriveController)->token();
        if (!$accessToken) {
            return back()->with('error', 'Gagal mendapatkan token akses.');
        }

        $file = $request->file('file');
        $originalFileName = $file->getClientOriginalName();
        $filePath = $file->getPathname();

        try {
            $response = Http::withToken($accessToken)
                ->attach(
                    'metadata',
                    json_encode([
                        'name' => $originalFileName,
                        'parents' => [$request->input('folderid')]
                    ]),
                    'metadata.json',
                    ['Content-Type' => 'application/json; charset=UTF-8']
                )
                ->attach(
                    'data',
                    file_get_contents($filePath),
                    $originalFileName,
                    ['Content-Type' => $file->getMimeType()]
                )
                ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

            $response->throw();
            $file_id = $response->json('id');

            $document = new Document;
            $document->file_name = $request->input('file_name');
            $document->name = $originalFileName;
            $document->fileid = $file_id;
            $document->folderid = $request->input('folderid');
            $document->save();

            return back()->with('success', 'File berhasil diunggah ke folder yang dipilih!');
        } catch (\Exception $e) {
            Log::error('Gagal upload file: ' . $e->getMessage());
            return back()->with('error', 'Upload gagal. Detail: ' . $e->getMessage());
        }
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
            'verified' => 'required|boolean',
            'file' => 'nullable|file', // Buat file menjadi opsional
        ]);

        try {
            $document = Document::findOrFail($id); // Filter berdasarkan user_id
            $accessToken = (new TokenDriveController())->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }

            // Inisialisasi data untuk Google Drive
            $driveData = ['name' => $request->input('file_name')];
            $filePath = null;

            // Jika ada file baru yang diunggah
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filePath = $file->getPathname();
                $originalFileName = $file->getClientOriginalName();
                $mimeType = $file->getMimeType();

                // 1. Unggah file baru
                $response = Http::withToken($accessToken)
                    ->attach(
                        'metadata',
                        json_encode([
                            'name' => $originalFileName,
                            'parents' => [$document->folderid]
                        ]),
                        'metadata.json',
                        ['Content-Type' => 'application/json; charset=UTF-8']
                    )
                    ->attach(
                        'data',
                        file_get_contents($filePath),
                        $originalFileName,
                        ['Content-Type' => $mimeType]
                    )
                    ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');
                $response->throw();
                $newFileId = $response->json('id');

                // 2. Hapus file lama dari Google Drive
                Http::withToken($accessToken)
                    ->delete("https://www.googleapis.com/drive/v3/files/{$document->fileid}")
                    ->throw();

                // 3. Perbarui ID file di database dengan ID file baru
                $document->fileid = $newFileId;
                $document->name = $originalFileName; // Perbarui juga nama asli file
            } else {
                // Jika tidak ada file baru, hanya perbarui nama file di Google Drive
                $response = Http::withToken($accessToken)
                    ->patch("https://www.googleapis.com/drive/v3/files/{$document->fileid}", $driveData);
                $response->throw();
            }

            // Perbarui nama file dan status verified di database
            $document->file_name = $request->input('file_name');
            $document->verified = $request->input('verified');
            $document->save();

            return back()->with('success', 'Dokumen berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui dokumen: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui dokumen. Detail: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $document = Document::findOrFail($id); // Filter berdasarkan user_id
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
            $document = Document::findOrFail($id); // Filter berdasarkan user_id
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
            return back()->with('error', 'Terjadi kesalahan saat menghapus file. Detail: ' . $e->getMessage());
        }
    }
}
