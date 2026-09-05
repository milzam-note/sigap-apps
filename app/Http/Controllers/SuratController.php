<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Surat;
use App\Models\JenisSurat;
use App\Models\RiwayatSurat; // Tambahkan import Model RiwayatSurat
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SuratController extends Controller
{
    /**
     * Halaman Publik (Pengunjung Tanpa Login)
     */
    public function publicIndex(Request $request)
    {
        // Mulai merakit query untuk surat UMUM
        $query = Surat::with('jenisSurat')->where('sifat_surat', 'umum');

        // Filter Pencarian (Nama Surat & Nomor Surat)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_surat', 'like', '%' . $request->search . '%')
                    ->orWhere('nomor_surat', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Tanggal Terbit
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_surat', $request->tanggal);
        }

        // Filter Tahun Terbit
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_surat', $request->tahun);
        }

        return Inertia::render('Public/SuratPublik', [
            'surats'  => $query->latest()->get(),
            'filters' => $request->only(['search', 'tanggal', 'tahun']), // Kirim kembali input ke Vue
        ]);
    }

    /**
     * Halaman Internal & Admin (Wajib Login)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Mulai merakit query untuk SEMUA surat beserta relasi Log & Riwayat
        $query = Surat::with(['jenisSurat', 'pembuat', 'pengubah', 'riwayat.pengubah', 'riwayat.jenisSuratLama']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_surat', 'like', '%' . $request->search . '%')
                    ->orWhere('nomor_surat', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_surat', $request->tanggal);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_surat', $request->tahun);
        }

        $jenisSurats = JenisSurat::all();

        return Inertia::render('Surat/Index', [
            'surats'       => $query->latest()->get(),
            'jenis_surats' => $jenisSurats,
            'userRole'     => $user->role,
            'filters'      => $request->only(['search', 'tanggal', 'tahun']),
        ]);
    }

    /**
     * Menyimpan Surat Baru (Khusus Admin)
     */
    public function store(Request $request)
    {
        // Proteksi Tambahan Sisi Server
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Administrator yang dapat menambah data.');
        }

        $validated = $request->validate([
            'nama_surat'     => 'required|string|max:255',
            'tanggal_surat'  => 'required|date',
            'nomor_surat'    => 'required|string|max:100',
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
            'sifat_surat'    => 'required|in:umum,rahasia',
            'file_pdf'       => 'required|file|mimes:pdf|max:10240', // Maks 10MB
        ], [
            'file_pdf.max'   => 'Ukuran file PDF tidak boleh melebihi 10 MB.',
            'file_pdf.mimes' => 'Format berkas wajib berupa PDF.',
        ]);

        if ($request->hasFile('file_pdf')) {
            $path = $request->file('file_pdf')->store('surat_pdf', 'public');
            $validated['file_pdf'] = $path;
        }

        // CATAT SIAPA PEMBUATNYA (Log Aktivitas)
        $validated['created_by'] = Auth::id();

        Surat::create($validated);

        return redirect()->back()->with('message', 'Dokumen berhasil disimpan.');
    }

    /**
     * Memperbarui Data Surat (Khusus Admin)
     */
    public function update(Request $request, $id)
    {
        // Proteksi Sisi Server
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Administrator yang dapat mengubah data.');
        }

        $surat = Surat::findOrFail($id);

        // Validasi Input
        $validated = $request->validate([
            'nama_surat'     => 'required|string|max:255',
            'tanggal_surat'  => 'required|date',
            'nomor_surat'    => 'required|string|max:100',
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
            'sifat_surat'    => 'required|in:umum,rahasia',
            'file_pdf'       => 'nullable|file|mimes:pdf|max:10240', // File bersifat opsional saat edit
        ], [
            'file_pdf.max'   => 'Ukuran file PDF tidak boleh melebihi 10 MB.',
            'file_pdf.mimes' => 'Format berkas wajib berupa PDF.',
        ]);

        // 1. BACKUP DATA LAMA KE TABEL RIWAYAT
        RiwayatSurat::create([
            'surat_id'            => $surat->id,
            'nama_surat_lama'     => $surat->nama_surat,
            'tanggal_surat_lama'  => $surat->tanggal_surat,
            'nomor_surat_lama'    => $surat->nomor_surat,
            'jenis_surat_id_lama' => $surat->jenis_surat_id,
            'sifat_surat_lama'    => $surat->sifat_surat,
            'file_pdf_lama'       => $surat->file_pdf,
            'diubah_oleh'         => Auth::id(), // Siapa yang merevisi
        ]);

        // 2. CEK FILE BARU (Penting: Tanpa Menghapus File Lama dari Storage!)
        if ($request->hasFile('file_pdf')) {
            $path = $request->file('file_pdf')->store('surat_pdf', 'public');
            $validated['file_pdf'] = $path;
        } else {
            // Jika tidak ada file baru yang diunggah, pertahankan file lama
            unset($validated['file_pdf']);
        }

        // 3. UPDATE DATA UTAMA
        $validated['updated_by'] = Auth::id(); // Siapa yang update

        // Perbarui data di database
        $surat->update($validated);

        return redirect()->back()->with('message', 'Data surat berhasil diperbarui. Versi lama tersimpan di riwayat.');
    }

    /**
     * Menghapus Surat (Khusus Admin)
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Administrator yang dapat menghapus data.');
        }

        // Ambil data surat beserta seluruh riwayatnya
        $surat = Surat::with('riwayat')->findOrFail($id);

        // Kumpulkan semua file PDF (versi terbaru + semua versi riwayat)
        $pdfsToDelete = [$surat->file_pdf];
        foreach ($surat->riwayat as $riwayat) {
            $pdfsToDelete[] = $riwayat->file_pdf_lama;
        }

        // Hapus fisik file dari storage secara menyeluruh
        foreach (array_unique($pdfsToDelete) as $pdf) {
            if ($pdf && Storage::disk('public')->exists($pdf)) {
                Storage::disk('public')->delete($pdf);
            }
        }

        // Menghapus surat otomatis akan menghapus riwayat di database karena on-delete cascade
        $surat->delete();

        return redirect()->back()->with('message', 'Dokumen beserta seluruh riwayat revisinya berhasil dihapus.');
    }

    /**
     * Fitur 1: Membaca/Melihat PDF di Browser
     */
    public function lihat($id)
    {
        $surat = Surat::findOrFail($id);

        // Perbaikan: Gunakan !Auth::check() agar pengunjung (tamu) tidak bisa mengakses surat rahasia
        if ($surat->sifat_surat === 'rahasia' && !Auth::check()) {
            abort(403, 'Akses ditolak. Silakan login untuk melihat dokumen rahasia.');
        }

        $surat->increment('jumlah_dilihat');

        // Buka file di browser
        return redirect(asset('storage/' . $surat->file_pdf));
    }

    /**
     * Fitur 2: Memaksa Download PDF ke Komputer
     */
    public function unduh($id)
    {
        $surat = Surat::findOrFail($id);

        // Perbaikan: Gunakan !Auth::check() agar pengunjung (tamu) tidak bisa mengakses surat rahasia
        if ($surat->sifat_surat === 'rahasia' && !Auth::check()) {
            abort(403, 'Akses ditolak. Silakan login untuk melihat dokumen rahasia.');
        }

        $surat->increment('jumlah_unduh');

        // Path absolut fisik file di server Laravel
        $filePath = storage_path('app/public/' . $surat->file_pdf);

        // Memaksa browser mengunduh file dengan nama asli surat
        return response()->download($filePath, $surat->nama_surat . '.pdf');
    }

    /**
     * Fitur 3: Melihat File PDF Riwayat Versi Lama (Tanpa Hitung Statistik)
     */
    public function lihatRiwayat($id)
    {
        $riwayat = RiwayatSurat::findOrFail($id);

        if ($riwayat->sifat_surat_lama === 'rahasia' && !Auth::check()) {
            abort(403, 'Akses ditolak.');
        }

        return redirect(asset('storage/' . $riwayat->file_pdf_lama));
    }

    /**
     * Fitur 4: Mengunduh File PDF Riwayat Versi Lama (Tanpa Hitung Statistik)
     */
    public function unduhRiwayat($id)
    {
        $riwayat = RiwayatSurat::findOrFail($id);

        if ($riwayat->sifat_surat_lama === 'rahasia' && !Auth::check()) {
            abort(403, 'Akses ditolak.');
        }

        $filePath = storage_path('app/public/' . $riwayat->file_pdf_lama);

        return response()->download($filePath, $riwayat->nama_surat_lama . '_V-LAMA.pdf');
    }
}
