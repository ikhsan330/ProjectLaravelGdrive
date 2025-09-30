<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DosenDocumentController extends Controller
{
    /**
     * Menyimpan dokumen baru yang diunggah oleh dosen.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'file_name' => 'required|string|max:255',
            'folderid' => 'required|string',
        ]);

        $userId = Auth::id(); // Ambil user ID dari sesi login

        $accessToken = (new TokenDriveController)->token();
        if (!$accessToken) {
            return back()->with('error', 'Gagal mendapatkan token akses.');
        }

        $file = $request->file('file');
        $originalFileName = $file->getClientOriginalName();
        $filePath = $file->getPathname();
        $folderId = $request->input('folderid');

        try {
            $metadata = ['name' => $originalFileName, 'parents' => [$folderId]];

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
            $document->user_id = $userId; // Simpan user ID dari sesi
            $document->save();

            return back()->with('success', 'File berhasil diunggah!');
        } catch (\Exception $e) {
            Log::error('Gagal upload file oleh user ' . $userId . ': ' . $e->getMessage());
            return back()->with('error', 'Upload gagal. Terjadi kesalahan.');
        }
    }

    /**
     * Memperbarui dokumen milik dosen.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
            'file' => 'nullable|file', // Dosen tidak bisa mengubah status verifikasi
        ]);

        try {
            $userId = Auth::id();
            // !! PENTING: Validasi kepemilikan dokumen sebelum update
            $document = Document::where('id', $id)->where('user_id', $userId)->firstOrFail();

            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $newOriginalFileName = $file->getClientOriginalName();

                // 1. Unggah file baru
                $response = Http::withToken($accessToken)
                    ->attach('metadata', json_encode(['name' => $newOriginalFileName, 'parents' => [$document->folderid]]), 'metadata.json', ['Content-Type' => 'application/json; charset=UTF-8'])
                    ->attach('data', file_get_contents($file->getPathname()), $newOriginalFileName)
                    ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');
                $response->throw();
                $newFileId = $response->json('id');

                // 2. Hapus file lama dari Google Drive
                Http::withToken($accessToken)->delete("https://www.googleapis.com/drive/v3/files/{$document->fileid}");

                // 3. Perbarui database
                $document->fileid = $newFileId;
                $document->name = $newOriginalFileName;
            }

            // Perbarui nama custom di database
            $document->file_name = $request->input('file_name');
            $document->save();

            return back()->with('success', 'Dokumen berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui dokumen: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui dokumen.');
        }
    }

    /**
     * Menampilkan pratinjau dokumen milik dosen.
     */
    public function show($id)
    {
        try {
            // !! PENTING: Validasi kepemilikan dokumen
            $document = Document::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            $previewUrl = "https://drive.google.com/file/d/{$document->fileid}/view?usp=sharing";
            return redirect()->away($previewUrl);
        } catch (\Exception $e) {
            return back()->with('error', 'File tidak ditemukan atau Anda tidak memiliki izin.');
        }
    }

    /**
     * Mengunduh dokumen milik dosen.
     */
    public function download($id)
    {
        try {
            // !! PENTING: Validasi kepemilikan dokumen
            $document = Document::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }

            $client = new \GuzzleHttp\Client();
            $response = $client->get("https://www.googleapis.com/drive/v3/files/{$document->fileid}?alt=media", [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken],
                'stream' => true,
            ]);

            return response()->streamDownload(function () use ($response) {
                echo $response->getBody()->getContents();
            }, $document->name);
        } catch (\Exception $e) {
            Log::error('Gagal mengunduh file: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengunduh file.');
        }
    }

    /**
     * Menghapus dokumen milik dosen.
     */
    public function destroy($id)
    {
        try {
            // !! PENTING: Validasi kepemilikan dokumen
            $document = Document::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }

            // Hapus dari Google Drive
            Http::withToken($accessToken)->delete("https://www.googleapis.com/drive/v3/files/{$document->fileid}");

            // Hapus dari database
            $document->delete();

            return back()->with('success', 'File berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus file: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus file.');
        }
    }
}
