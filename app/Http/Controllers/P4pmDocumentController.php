<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class P4pmDocumentController extends Controller
{
    /**
     * Menampilkan semua folder induk publik untuk P4PM.
     */
    public function index()
    {
        $rootFolders = Folder::whereNull('parent_id')->orderBy('name')->get();
        return view('p4pm.dokumen.index', compact('rootFolders'));
    }

    /**
     * Menampilkan isi folder publik.
     * Hanya menampilkan dokumen yang sudah terverifikasi.
     */
    public function show($folder_id)
    {
        $folder = Folder::where('folder_id', $folder_id)->firstOrFail();
        $breadcrumbs = $this->getFolderAncestry($folder);
        $subfolders = Folder::where('parent_id', $folder->folder_id)->orderBy('name')->get();

        // Mengambil dokumen HANYA yang sudah terverifikasi (verified = true)
        $documents = Document::with(['user', 'comments.user'])
            ->where('folderid', $folder->folder_id)
            ->where('verified', true) // <-- Filter utama
            ->get();

        return view('p4pm.dokumen.show', compact('folder', 'documents', 'breadcrumbs', 'subfolders'));
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
     * Menyimpan komentar dari P4PM pada sebuah dokumen.
     */
    public function storeComment(Request $request, $document_id)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);
        try {
            $document = Document::findOrFail($document_id);

            $comment = new Comment();
            $comment->document_id = $document->id;
            $comment->user_id = Auth::id(); // ID P4PM yang sedang login
            $comment->content = $request->input('content');
            $comment->save();

            return back()->with('success', 'Komentar berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan komentar oleh user P4PM ' . Auth::id() . ': ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menambahkan komentar.');
        }
    }

    /**
     * Menampilkan pratinjau dokumen.
     */
    public function previewDocument($id)
    {
        try {
            // P4PM hanya bisa melihat dokumen yang sudah terverifikasi
            $document = Document::where('id', $id)->where('verified', true)->firstOrFail();
            $previewUrl = "https://drive.google.com/file/d/{$document->fileid}/view?usp=sharing";
            return redirect()->away($previewUrl);
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan pratinjau file untuk P4PM: ' . $e->getMessage());
            return back()->with('error', 'Dokumen tidak ditemukan atau belum diverifikasi.');
        }
    }
}
