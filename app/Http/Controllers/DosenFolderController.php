<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DosenFolderController extends Controller
{
    /**
     * PERUBAHAN: Menampilkan semua folder induk publik.
     * Logikanya sekarang mirip dengan Admin, karena semua dosen melihat struktur yang sama.
     */
    public function index()
    {
        // Mengambil semua folder induk (yang tidak punya parent_id)
        $rootFolders = Folder::whereNull('parent_id')->orderBy('name')->get();

        // (Opsional) Menghitung dokumen yang butuh verifikasi untuk ditampilkan di view
        $unverifiedCounts = collect();
        if ($rootFolders->isNotEmpty()) {
            $topLevelFolderIds = $rootFolders->pluck('folder_id');
            $placeholders = implode(',', array_fill(0, count($topLevelFolderIds), '?'));

            // CTE rekursif untuk mencari dokumen yang belum terverifikasi di setiap pohon folder
            $cte = "
                WITH RECURSIVE folder_hierarchy (folder_id, root_id) AS (
                    SELECT folder_id, folder_id as root_id FROM folders WHERE folder_id IN ($placeholders)
                    UNION ALL
                    SELECT f.folder_id, h.root_id FROM folders f JOIN folder_hierarchy h ON f.parent_id = h.folder_id
                )
            ";
            $results = DB::select("
                {$cte}
                SELECT h.root_id, COUNT(d.id) as total
                FROM folder_hierarchy h
                JOIN documents d ON h.folder_id = d.folderid
                WHERE d.verified = false
                GROUP BY h.root_id
            ", $topLevelFolderIds->all());
            $unverifiedCounts = collect($results)->pluck('total', 'root_id');
        }

        return view('dosen.dokumen.index', compact('rootFolders', 'unverifiedCounts'));
    }

    /**
     * PERUBAHAN: Menampilkan isi folder publik.
     * Pemeriksaan kepemilikan folder ('user_id') dihilangkan.
     */
    public function show($folder_id)
    {
        // Cari folder berdasarkan folder_id uniknya, tanpa memeriksa user_id
        $folder = Folder::where('folder_id', $folder_id)->firstOrFail();

        // Dapatkan breadcrumbs
        $breadcrumbs = $this->getFolderAncestry($folder);

        // Ambil semua subfolder langsung dari folder ini
        $subfolders = Folder::where('parent_id', $folder->folder_id)->orderBy('name')->get();

        // Ambil SEMUA dokumen di dalam folder ini.
        // Kepemilikan akan dicek di level view untuk menampilkan tombol Aksi (Edit/Hapus).
        $documents = Document::with('user')
            ->where('folderid', $folder->folder_id)
            ->get();

        return view('dosen.dokumen.show', compact('folder', 'documents', 'subfolders', 'breadcrumbs'));
    }

    /**
     * PERUBAHAN: Menghilangkan pemeriksaan 'user_id' saat mengambil breadcrumbs.
     */
    private function getFolderAncestry(Folder $folder)
    {
        $breadcrumbs = [];
        $current = $folder;
        while ($current && $current->parent_id) {
            // Cari parent tanpa filter user_id
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
