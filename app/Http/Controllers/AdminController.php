<?php

namespace App\Http\Controllers;


use App\Models\Document;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{

    public function dashboard(){
        $totalDocuments = Document::count();
        $totalFolders = Folder::whereNull('parent_id')->count(); // Hanya folder induk
        $totalDosen = User::where('role', 'dosen')->count();
        $pendingVerification = Document::where('verified', false)->count();

        // =============================================================
        // DATA UNTUK DOUGHNUT CHART (Total Dokumen per Folder Induk)
        // =============================================================
        $foldersWithDocuments = Folder::whereNull('parent_id')
                                      ->has('documents')
                                      ->withCount('documents')
                                      ->orderBy('documents_count', 'desc')
                                      ->take(7)
                                      ->get();

        $folderLabels = $foldersWithDocuments->pluck('name');
        $folderDocumentCounts = $foldersWithDocuments->pluck('documents_count');

        // =============================================================
        // DATA UNTUK LINE CHART (Statistik Upload Dokumen per Waktu)
        // =============================================================
        $documentStats = Document::select(
                DB::raw('COUNT(*) as count'),
                DB::raw("TO_CHAR(created_at, 'Mon YYYY') as month_year") // Untuk PostgreSQL
            )
            ->where('created_at', '>=', now()->subMonths(11))
            ->groupBy('month_year')
            ->orderByRaw("MIN(created_at)")
            ->get();

        $documentTimeLabels = $documentStats->pluck('month_year');
        $documentTimeCounts = $documentStats->pluck('count');


        // Kirim semua data yang sudah diproses ke view
        return view('admin.dashboard', compact(
            'totalDocuments', // <-- Data baru
            'totalFolders',   // <-- Data baru
            'totalDosen',     // <-- Data baru
            'pendingVerification', // <-- Data baru
            'folderLabels',
            'folderDocumentCounts',
            'documentTimeLabels',
            'documentTimeCounts'
        ));
    }
}
