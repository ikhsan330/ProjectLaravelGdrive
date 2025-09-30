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
        $totalFolders = Folder::where('user_id', $userId)->whereNull('parent_id')->count();

        // =============================================================
        // DATA UNTUK DOUGHNUT CHART (Distribusi Dokumen per Folder)
        // =============================================================
        $foldersWithDocuments = Folder::where('user_id', $userId)
                                      ->whereNull('parent_id') // Hanya folder induk
                                      ->has('documents')
                                      ->withCount(['documents' => function ($query) use ($userId) {
                                          $query->where('user_id', $userId);
                                      }])
                                      ->get();

        $folderLabels = $foldersWithDocuments->pluck('name');
        $folderDocumentCounts = $foldersWithDocuments->pluck('documents_count');

        // =============================================================
        // DATA UNTUK LINE CHART (Aktivitas Upload per Hari - 30 hari terakhir)
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
