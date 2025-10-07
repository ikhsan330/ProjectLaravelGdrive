<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DosenController extends Controller
{
    public function dashboard()
    {
        $userId = Auth::id(); // Mengambil ID dosen yang sedang login

        // =============================================================
        // DATA UNTUK KARTU STATISTIK (KPI)
        // =============================================================
        $totalDocuments = Document::where('user_id', $userId)->count();
        $verifiedDocuments = Document::where('user_id', $userId)->where('verified', true)->count();
        $unverifiedDocuments = Document::where('user_id', $userId)->where('verified', false)->count();

        // PERUBAHAN: Folder bersifat publik. Hitung semua folder induk yang ada di sistem.
        $totalFolders = Folder::whereNull('parent_id')->count();

        // =============================================================
        // DATA UNTUK DOUGHNUT CHART (Distribusi Dokumen per Folder)
        // =============================================================

        // PERUBAHAN: Logika diubah.
        // Cari folder induk publik, TAPI hanya yang berisi dokumen milik dosen yang sedang login.
        $foldersWithDocuments = Folder::whereNull('parent_id') // 1. Mulai dari semua folder induk
                                      ->whereHas('documents', function ($query) use ($userId) {
                                          $query->where('user_id', $userId); // 2. Filter folder yang punya dokumen milik dosen ini
                                      })
                                      ->withCount(['documents' => function ($query) use ($userId) {
                                          $query->where('user_id', $userId); // 3. Hitung HANYA dokumen milik dosen ini
                                      }])
                                      ->get();

        $folderLabels = $foldersWithDocuments->pluck('name');
        $folderDocumentCounts = $foldersWithDocuments->pluck('documents_count');

        // =============================================================
        // DATA UNTUK LINE CHART (Logika ini sudah benar, tidak perlu diubah)
        // =============================================================
        $dbConnection = config('database.default');
        $dateFunction = ($dbConnection == 'mysql')
            ? "DATE(created_at)"
            : "TO_CHAR(created_at, 'YYYY-MM-DD')"; // Untuk PostgreSQL

        $documentStats = Document::select(
                DB::raw('COUNT(*) as count'),
                DB::raw("$dateFunction as date")
            )
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(29))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $uploadTimeLabels = $documentStats->pluck('date');
        $uploadTimeCounts = $documentStats->pluck('count');

        // Kirim semua data ke view
        return view('dosen.dashboard', compact(
            'totalDocuments',
            'verifiedDocuments',
            'unverifiedDocuments',
            'totalFolders',
            'folderLabels',
            'folderDocumentCounts',
            'uploadTimeLabels',
            'uploadTimeCounts'
        ));
    }
}
