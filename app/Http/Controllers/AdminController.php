<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Bagian ini sudah benar dan tidak perlu diubah
        $totalDocuments = Document::count();
        $totalFolders = Folder::whereNull('parent_id')->count();
        $totalDosen = User::where('role', 'dosen')->count();
        $pendingVerification = Document::where('verified', false)->count();

        // =================================================================
        // DATA UNTUK DOUGHNUT CHART (Total Dokumen per Folder Induk & Subfolder)
        // PENINGKATAN: Menggunakan Recursive CTE untuk akurasi data
        // =================================================================

        $cte = "
            WITH RECURSIVE folder_hierarchy (id, folder_id, root_id, root_name) AS (
                -- Anchor: Memulai dari semua folder induk (root)
                SELECT id, folder_id, folder_id as root_id, name as root_name
                FROM folders
                WHERE parent_id IS NULL

                UNION ALL

                -- Recursive Member: Mencari semua anak dari folder di atasnya
                SELECT f.id, f.folder_id, h.root_id, h.root_name
                FROM folders f
                JOIN folder_hierarchy h ON f.parent_id = h.folder_id
            )
        ";

        // Query utama yang menggunakan CTE untuk menghitung dokumen
        $folderDocumentStats = DB::select("
            {$cte}
            SELECT
                h.root_name as folder_name,
                COUNT(d.id) as total_documents
            FROM folder_hierarchy h
            LEFT JOIN documents d ON h.folder_id = d.folderid
            GROUP BY h.root_name
            HAVING COUNT(d.id) > 0 -- Hanya ambil folder yang ada isinya
            ORDER BY total_documents DESC
            LIMIT 7
        ");

        // Proses hasil query mentah menjadi koleksi untuk chart
        $folderStatsCollection = collect($folderDocumentStats);
        $folderLabels = $folderStatsCollection->pluck('folder_name');
        $folderDocumentCounts = $folderStatsCollection->pluck('total_documents');


        // =================================================================
        // DATA UNTUK LINE CHART (Statistik Upload Dokumen per Waktu)
        // Bagian ini sudah benar dan tidak perlu diubah.
        // =================================================================
        $documentStats = Document::select(
                DB::raw('COUNT(*) as count'),
                DB::raw("TO_CHAR(created_at, 'Mon YYYY') as month_year") // Untuk PostgreSQL. Ganti jika DB Anda beda.
                // Untuk MySQL: DB::raw("DATE_FORMAT(created_at, '%b %Y') as month_year")
            )
            ->where('created_at', '>=', now()->subYear()) // Lebih sederhana pakai subYear()
            ->groupBy('month_year')
            ->orderByRaw("MIN(created_at)")
            ->get();

        $documentTimeLabels = $documentStats->pluck('month_year');
        $documentTimeCounts = $documentStats->pluck('count');


        // Kirim semua data yang sudah diproses ke view
        return view('admin.dashboard', compact(
            'totalDocuments',
            'totalFolders',
            'totalDosen',
            'pendingVerification',
            'folderLabels',
            'folderDocumentCounts',
            'documentTimeLabels',
            'documentTimeCounts'
        ));
    }
}
