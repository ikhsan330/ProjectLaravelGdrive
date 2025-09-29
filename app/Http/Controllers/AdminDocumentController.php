<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\FolderController;

class AdminDocumentController extends Controller
{
    public function create()
    {
        $folderController = new FolderController();
        $folders = $folderController->listFoldersRecursive();
        return view('dosen.dokumen.create', compact('folders'));
    }

// app/Http/Controllers/AdminDocumentController.php

public function store(Request $request)
{
    $request->validate([
        'file' => 'required|file',
        'file_name' => 'required|string|max:255',
        'folderid' => 'required|string',
        'user_id' => 'required|exists:users,id', // TAMBAHKAN VALIDASI INI
    ]);

    $accessToken = (new TokenDriveController)->token();
    if (!$accessToken) {
        return back()->with('error', 'Gagal mendapatkan token akses.');
    }

    $file = $request->file('file');
    $originalFileName = $file->getClientOriginalName();
    $filePath = $file->getPathname();
    $folderId = $request->input('folderid');

    try {
        $metadata = ['name' => $originalFileName];
        if ($folderId) {
            $metadata['parents'] = [$folderId];
        }

        $response = Http::withToken($accessToken)
            ->attach(
                'metadata',
                json_encode($metadata), 'metadata.json',
                ['Content-Type' => 'application/json; charset=UTF-8']
            )
            ->attach('data', file_get_contents($filePath), $originalFileName)
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

        $response->throw();
        $file_id = $response->json('id');

        $document = new Document;
        $document->file_name = $request->input('file_name');
        $document->name = $originalFileName;
        $document->fileid = $file_id;
        $document->folderid = $folderId;
        $document->user_id = $request->input('user_id'); // <-- TAMBAHKAN BARIS INI
        $document->save();

        return back()->with('success', 'File berhasil diunggah!');
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
            'file' => 'nullable|file', // File dibuat opsional
        ]);

        try {
            $document = Document::findOrFail($id);
            $accessToken = (new TokenDriveController())->token();

            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }

            // Jika ada file baru yang diunggah untuk menggantikan yang lama
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $newOriginalFileName = $file->getClientOriginalName();
                $filePath = $file->getPathname();

                // 1. Unggah file baru ke folder yang sama di Google Drive
                $response = Http::withToken($accessToken)
                    ->attach(
                        'metadata',
                        json_encode([
                            'name' => $newOriginalFileName,
                            'parents' => [$document->folderid] // Upload ke folder yang sama
                        ]),
                        'metadata.json',
                        ['Content-Type' => 'application/json; charset=UTF-8']
                    )
                    ->attach('data', file_get_contents($filePath), $newOriginalFileName)
                    ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

                $response->throw();
                $newFileId = $response->json('id');

                // 2. Hapus file lama dari Google Drive (jika upload baru berhasil)
                Http::withToken($accessToken)
                    ->delete("https://www.googleapis.com/drive/v3/files/{$document->fileid}");
                    // Kita tidak menggunakan throw() di sini agar proses lanjut mesmo error

                // 3. Perbarui database dengan info file baru
                $document->fileid = $newFileId;
                $document->name = $newOriginalFileName; // Perbarui juga nama asli file
            }

            // Perbarui nama custom file di Google Drive agar sesuai dengan sistem
            // (Hanya jika tidak ada file baru yang diupload, untuk menghindari request ganda)
            if (!$request->hasFile('file') && $document->file_name !== $request->input('file_name')) {
                 Http::withToken($accessToken)
                    ->patch("https://www.googleapis.com/drive/v3/files/{$document->fileid}", [
                        'name' => $request->input('file_name')
                    ]);
            }

            // Perbarui nama custom dan status verifikasi di database
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
}
