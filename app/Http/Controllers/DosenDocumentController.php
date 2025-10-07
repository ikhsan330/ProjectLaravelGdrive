<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder; // Import Folder untuk validasi
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
        // PERBAIKAN: Menambahkan validasi 'exists' untuk memastikan folder tujuan itu ada.
        $request->validate([
            'file' => 'required|file|max:20480', // Batas file 20MB
            'file_name' => 'required|string|max:255',
            'folderid' => 'required|string|exists:folders,folder_id', // Validasi ke tabel folders
        ]);

        $userId = Auth::id();

        // Validasi kepemilikan folder tidak diperlukan, karena dosen bisa upload ke folder mana saja.

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
            $document->user_id = $userId; // Simpan user_id dari sesi login
            $document->save();

            return back()->with('success', 'File berhasil diunggah!');
        } catch (\Exception $e) {
            Log::error('Gagal upload file oleh user ' . $userId . ': ' . $e->getMessage());
            return back()->with('error', 'Upload gagal. Terjadi kesalahan.');
        }
    }

    /**
     * Memperbarui dokumen milik dosen.
     * PENTING: Validasi kepemilikan di sini sudah benar dan dipertahankan.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
            'file' => 'nullable|file|max:20480', // Dosen tidak bisa mengubah status verifikasi
        ]);

        try {
            $userId = Auth::id();
            // Cari dokumen berdasarkan ID DAN pastikan pemiliknya adalah user yang sedang login
            $document = Document::where('id', $id)->where('user_id', $userId)->firstOrFail();

            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses.');
            }

            // Logika untuk mengganti file (jika ada)
            if ($request->hasFile('file')) {
                // ... (logika penggantian file sudah benar)
                $file = $request->file('file');
                $newOriginalFileName = $file->getClientOriginalName();
                $response = Http::withToken($accessToken)
                    ->attach('metadata', json_encode(['name' => $newOriginalFileName, 'parents' => [$document->folderid]]), 'metadata.json', ['Content-Type' => 'application/json; charset=UTF-8'])
                    ->attach('data', file_get_contents($file->getPathname()), $newOriginalFileName)
                    ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');
                $response->throw();
                $newFileId = $response->json('id');
                Http::withToken($accessToken)->delete("https://www.googleapis.com/drive/v3/files/{$document->fileid}");
                $document->fileid = $newFileId;
                $document->name = $newOriginalFileName;
            }

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
     * PENTING: Validasi kepemilikan di sini sudah benar dan dipertahankan.
     */
    public function show($id)
    {
        try {
            $document = Document::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            $previewUrl = "https://drive.google.com/file/d/{$document->fileid}/view?usp=sharing";
            return redirect()->away($previewUrl);
        } catch (\Exception $e) {
            return back()->with('error', 'File tidak ditemukan atau Anda tidak memiliki izin.');
        }
    }

    /**
     * Mengunduh dokumen milik dosen.
     * PENTING: Validasi kepemilikan di sini sudah benar dan dipertahankan.
     */
    public function download($id)
    {
        try {
            $document = Document::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) return back()->with('error', 'Gagal mendapatkan token akses.');

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
     * PENTING: Validasi kepemilikan di sini sudah benar dan dipertahankan.
     */
    public function destroy($id)
    {
        try {
            $document = Document::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            $accessToken = (new TokenDriveController)->token();
            if (!$accessToken) return back()->with('error', 'Gagal mendapatkan token akses.');

            Http::withToken($accessToken)->delete("https://www.googleapis.com/drive/v3/files/{$document->fileid}");
            $document->delete();

            return back()->with('success', 'File berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus file: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus file.');
        }
    }
}
