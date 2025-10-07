<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AssesorDocumentController extends Controller
{
    /**
     * Menampilkan semua folder induk publik untuk Assesor.
     * Tidak perlu menghitung notifikasi verifikasi.
     */
    public function index()
    {
        $rootFolders = Folder::whereNull('parent_id')->orderBy('name')->get();

        return view('asesor.dokumen.index', compact('rootFolders'));
    }

    /**
     * Menampilkan isi folder publik.
     * FOKUS UTAMA: Hanya menampilkan dokumen yang sudah terverifikasi.
     */
    public function show($folder_id)
    {
        $folder = Folder::where('folder_id', $folder_id)->firstOrFail();
        $breadcrumbs = $this->getFolderAncestry($folder);

        // Ambil semua subfolder
        $subfolders = Folder::where('parent_id', $folder->folder_id)->orderBy('name')->get();

        // PERUBAHAN KUNCI: Ambil dokumen HANYA yang sudah terverifikasi (verified = true)
        // Eager load relasi 'user' (pemilik) dan 'comments.user' (pemberi komentar)
        $documents = Document::with(['user', 'comments.user'])
            ->where('folderid', $folder->folder_id)
            ->where('verified', true) // <-- Filter utama untuk Assesor
            ->get();

        return view('asesor.dokumen.show', compact('folder', 'documents', 'breadcrumbs', 'subfolders'));
    }

    /**
     * Helper untuk mendapatkan leluhur folder (breadcrumbs).
     */
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

    /**
     * FUNGSI BARU: Menyimpan komentar dari Assesor pada sebuah dokumen.
     */
    public function storeComment(Request $request, $document_id)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        try {
            // Pastikan dokumen ada
            $document = Document::findOrFail($document_id);

            // Buat komentar baru
            $comment = new Comment();
            $comment->document_id = $document->id;
            $comment->user_id = Auth::id(); // ID Assesor yang sedang login
            $comment->content = $request->input('content');
            $comment->save();

            return back()->with('success', 'Komentar berhasil ditambahkan!');

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan komentar oleh user ' . Auth::id() . ': ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menambahkan komentar.');
        }
    }

    public function previewDocument($id)
    {
        try {
            // Assesor hanya bisa melihat dokumen yang sudah terverifikasi
            $document = Document::where('id', $id)->where('verified', true)->firstOrFail();

            // Arahkan ke URL pratinjau Google Drive
            $previewUrl = "https://drive.google.com/file/d/{$document->fileid}/view?usp=sharing";
            return redirect()->away($previewUrl);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Dokumen tidak ditemukan atau belum diverifikasi.');
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan pratinjau file: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mencoba melihat file.');
        }
    }
}
