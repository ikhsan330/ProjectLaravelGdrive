<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
// Pastikan TokenDriveController dapat diakses
use App\Http\Controllers\TokenDriveController;

/**
 * Controller ini mengatur semua aksi yang dapat dilakukan oleh Kaprodi.
 * Fokus utamanya adalah untuk melihat, mengunduh, dan memverifikasi
 * dokumen milik semua dosen, tanpa hak untuk memodifikasi struktur folder
 * atau mengunggah file baru.
 */
class KaprodiDocumentController extends Controller
{
    public function index()
    {
        // 1. Ambil folder induk (root) milik semua dosen
        $rootFolders = Folder::select('folders.*', 'users.name as user_name')
            ->join('users', 'folders.user_id', '=', 'users.id')
            ->where('users.role', 'dosen')
            ->whereNull('folders.parent_id')
            ->orderBy('folders.name')
            ->orderBy('users.name')
            ->get();

        // 2. Ambil SEMUA folder milik dosen dan kelompokkan berdasarkan parent_id untuk pencarian rekursif yang efisien
        $allDosenFolders = Folder::whereIn('user_id', $rootFolders->pluck('user_id'))->get();
        $foldersByParent = $allDosenFolders->groupBy('parent_id');

        // 3. Untuk setiap folder induk, hitung dokumen yang belum diverifikasi di dalamnya DAN di semua sub-foldernya
        foreach ($rootFolders as $folder) {
            // Dapatkan semua ID sub-folder secara rekursif
            $descendantIds = $this->getDescendantFolderIds($folder->folder_id, $foldersByParent);

            // Gabungkan ID folder induk itu sendiri dengan semua ID sub-foldernya
            $allFolderIds = collect($descendantIds)->push($folder->folder_id);

            // Hitung dokumen yang belum diverifikasi dari semua folder tersebut
            $unverifiedCount = Document::whereIn('folderid', $allFolderIds)
            ->where('user_id', $folder->user_id)
                ->where('verified', false)
                ->count();

            // Tambahkan hasil hitungan ke objek folder
            $folder->unverified_documents_count = $unverifiedCount;
        }

        // 4. Kelompokkan hasilnya berdasarkan nama folder untuk ditampilkan di view
        $groupedFolders = $rootFolders->groupBy('name');

        return view('kaprodi.dokumen.index', compact('groupedFolders'));
    }

    /**
     * Helper function untuk mendapatkan semua ID sub-folder (keturunan) secara rekursif.
     *
     * @param string $parentId ID folder induk
     * @param \Illuminate\Support\Collection $foldersByParent Koleksi semua folder yang dikelompokkan berdasarkan parent_id
     * @return array
     */
    private function getDescendantFolderIds($parentId, $foldersByParent)
    {
        $descendants = [];
        // Cek apakah ada anak dari parentId ini
        if (!isset($foldersByParent[$parentId])) {
            return $descendants;
        }

        // Ambil anak-anak langsung
        $children = $foldersByParent[$parentId];

        foreach ($children as $child) {
            // Tambahkan ID anak ini ke dalam array
            $descendants[] = $child->folder_id;
            // Cari cucu dari anak ini (rekursif) dan gabungkan hasilnya
            $descendants = array_merge($descendants, $this->getDescendantFolderIds($child->folder_id, $foldersByParent));
        }

        return $descendants;
    }

    /**
     * Menampilkan isi dari sebuah folder spesifik milik seorang dosen.
     */
  public function showDosenFolder($dosen_id, $folder_id)
    {
        $folder = Folder::with('user')
            ->where('user_id', $dosen_id)
            ->where('folder_id', $folder_id)
            ->firstOrFail();

        $documents = Document::where('folderid', $folder->folder_id)->get();

        // Ambil sub-folder langsung
        $subfolders = Folder::where('user_id', $dosen_id)
            ->where('parent_id', $folder->folder_id)
            ->get();

        // --- Logika Baru untuk Menghitung Notifikasi Sub-folder ---
        if ($subfolders->isNotEmpty()) {
            $allDosenFolders = Folder::where('user_id', $dosen_id)->get();
            $foldersByParent = $allDosenFolders->groupBy('parent_id');

            foreach ($subfolders as $subfolder) {
                $descendantIds = $this->getDescendantFolderIds($subfolder->folder_id, $foldersByParent);
                $allFolderIds = collect($descendantIds)->push($subfolder->folder_id);
                $subfolder->unverified_documents_count = Document::whereIn('folderid', $allFolderIds)
                    ->where('user_id', $dosen_id)
                    ->where('verified', false)
                    ->count();
            }
        }

        return view('kaprodi.dokumen.show', compact('folder', 'documents', 'subfolders'));
    }
    /**
     * Mengubah status verifikasi sebuah dokumen.
     * Ini adalah satu-satunya aksi "update" yang bisa dilakukan Kaprodi.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id ID dari dokumen di database
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateVerification(Request $request, $id)
    {
        $request->validate([
            'verified' => 'required|boolean',
        ]);

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

    /**
     * Mengarahkan pengguna ke URL pratinjau Google Drive untuk dokumen yang dipilih.
     *
     * @param int $id ID dari dokumen di database
     * @return \Illuminate\Http\RedirectResponse
     */
    public function previewDocument($id)
    {
        try {
            $document = Document::findOrFail($id);
            // URL ini memungkinkan pratinjau tanpa perlu login jika file di-share dengan benar
            $previewUrl = "https://drive.google.com/file/d/{$document->fileid}/view?usp=sharing";
            return redirect()->away($previewUrl);
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan pratinjau file: ' . $e->getMessage());
            return back()->with('error', 'File tidak ditemukan atau terjadi kesalahan.');
        }
    }

    /**
     * Mengunduh file dokumen dari Google Drive.
     *
     * @param int $id ID dari dokumen di database
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function downloadDocument($id)
    {
        try {
            $document = Document::findOrFail($id);
            $accessToken = (new TokenDriveController)->token();

            if (!$accessToken) {
                return back()->with('error', 'Gagal mendapatkan token akses Google Drive.');
            }

            // Menggunakan GuzzleHttp untuk mengambil file
            $client = new \GuzzleHttp\Client();
            $response = $client->get("https://www.googleapis.com/drive/v3/files/{$document->fileid}?alt=media", [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken],
                'stream' => true,
            ]);

            $fileName = $document->name; // Menggunakan nama file asli saat diunduh
            $contentType = $response->getHeaderLine('Content-Type');

            // Mengirim file sebagai stream download ke browser
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

    public function showUnverified()
    {
        // Ambil semua dokumen dengan status verified = false
        // Eager load relasi 'user' (pemilik) dan 'folder' untuk efisiensi query
        $unverifiedDocuments = Document::with(['user', 'folder'])
            ->where('verified', false)
            ->latest() // Tampilkan yang terbaru di atas
            ->get();

        // Kirim data ke view baru
        return view('kaprodi.dokumen.unverified', compact('unverifiedDocuments'));
    }
}
