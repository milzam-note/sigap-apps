<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Surat;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. MENGAMBIL DATA STATISTIK
        $totalDokumen = Surat::count();
        $totalUmum = Surat::where('sifat_surat', 'umum')->count();
        $totalRahasia = Surat::where('sifat_surat', 'rahasia')->count();

        // Menghitung total interaksi (jumlah dilihat + jumlah diunduh)
        $totalInteraksi = Surat::sum('jumlah_dilihat') + Surat::sum('jumlah_unduh');

        // 2. MENGAMBIL DATA DOKUMEN TERBARU (RECENT DOCUMENTS)
        // Kita ambil 5 data terakhir beserta relasi jenis surat dan pembuatnya
        $recentDocuments = Surat::with(['jenisSurat', 'pembuat'])
            ->latest()
            ->take(5)
            ->get();

        // 3. MENGIRIM DATA KE VUE (FRONTEND)
        return Inertia::render('Dashboard', [
            'stats' => [
                'total' => $totalDokumen,
                'umum' => $totalUmum,
                'rahasia' => $totalRahasia,
                'interaksi' => $totalInteraksi,
            ],
            'recent_documents' => $recentDocuments,
            'userRole' => $user->role,
        ]);
    }
}
