<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
       public function index()
    {
        // Kueri CTE sudah benar
        $query = "
            WITH RECURSIVE all_dosen_folders AS (
                SELECT f.id, f.name, f.folder_id, f.parent_id, f.user_id, u.name as user_name
                FROM folders f JOIN users u ON f.user_id = u.id
                WHERE f.parent_id IS NULL AND u.role = 'dosen'
                UNION ALL
                SELECT f_child.id, f_child.name, f_child.folder_id, f_child.parent_id, f_child.user_id, adf.user_name
                FROM folders f_child
                JOIN all_dosen_folders adf ON f_child.parent_id = adf.folder_id AND f_child.user_id = adf.user_id
            )
            SELECT * FROM all_dosen_folders;
        ";
        $allFolders = collect(DB::select($query));

        // ======================= PERBAIKAN LOGIKA PENGELOMPOKAN =======================
        // Kelompokkan folder anak berdasarkan KUNCI GABUNGAN (parent_id dan user_id)
        // Ini akan membuat grup terpisah untuk setiap dosen, misal: 'gdrive_id_parent_user_id_afis'
        $groupedFolders = $allFolders->groupBy(function ($item) {
            // Kita hanya kelompokkan jika item adalah subfolder
            if ($item->parent_id) {
                return $item->parent_id . '_' . $item->user_id;
            }
        });
        // ============================================================================

        $groupedRootFolders = $allFolders->where('parent_id', null)->groupBy('name');

        // ... Logika untuk featuredFolders tidak berubah dan sudah benar ...
        $allRootFolders = Folder::whereNull('parent_id')->orderBy('created_at', 'desc')->get();
        $featuredFolders = $allRootFolders->unique('name')->values()->take(3);
        $featuredFolders->each(function ($folder) {
            $folder->load('documents', 'childrenRecursive');
            $folder->total_files = $folder->getTotalFileCount();
        });

        return view('welcome', [
            'groupedRootFolders' => $groupedRootFolders,
            'groupedFolders'     => $groupedFolders,
            'featuredFolders'    => $featuredFolders
        ]);
    }

    /**
     * Mengambil isi folder (dokumen dan subfolder).
     * Metode ini sudah benar dan aman.
     */
    public function getFolderContents(Folder $folder)
    {
        $subfolders = Folder::where('parent_id', $folder->folder_id)
            ->where('user_id', $folder->user_id)
            ->orderBy('name')
            ->get();

        $documents = Document::where('folderid', $folder->folder_id)
            ->where('user_id', $folder->user_id)
            ->orderBy('name')
            ->get();

        return view('partials._folder_contents', [
            'selectedFolder' => $folder,
            'subfolders'     => $subfolders,
            'documents'      => $documents,
        ]);
    }
}
