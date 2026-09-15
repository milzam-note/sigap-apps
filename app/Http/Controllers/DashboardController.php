<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. MENGAMBIL DATA STATISTIK
        $totalDokumen = Surat::count();
        $totalUmum = Surat::where('sifat_surat', 'umum')->count();
        $totalRahasia = Surat::where('sifat_surat', 'rahasia')->count();

        // Menghitung total interaksi (jumlah dilihat + jumlah diunduh)
        $totalInteraksi = Surat::sum('jumlah_dilihat') + Surat::sum('jumlah_unduh');

        // 2. MENGAMBIL DATA DOKUMEN UTAMA (BISA DI-FILTER)
        $query = Surat::with(['jenisSurat', 'pembuat']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_surat', 'like', '%' . $request->search . '%')
                    ->orWhere('nomor_surat', 'like', '%' . $request->search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('tahun')) {
            $query->where('tahun_dokumen', $request->tahun);
        }

        if ($request->filled('search') || $request->filled('tahun')) {
            $recentDocuments = $query->latest()->get();
        } else {
            $recentDocuments = $query->latest()->take(5)->get();
        }

        // 3. LOG AKTIVITAS TERAKHIR (STATIS: MENGAMBIL 5 TERBARU TANPA FILTER)
        $recentActivities = Surat::with('pembuat')->latest()->take(5)->get();

        // 4. MENGIRIM DATA KE VUE
        return Inertia::render('Dashboard', [
            'stats' => [
                'total' => $totalDokumen,
                'umum' => $totalUmum,
                'rahasia' => $totalRahasia,
                'interaksi' => $totalInteraksi,
            ],
            'recent_documents' => $recentDocuments,
            'recent_activities' => $recentActivities, // KITA KIRIM VARIABEL BARU INI
            'userRole' => $user->role,
            'filters' => $request->only(['search', 'tahun']),
        ]);
    }
}
