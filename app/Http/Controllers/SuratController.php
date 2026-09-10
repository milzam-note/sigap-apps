<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Surat;
use App\Models\JenisSurat;
use App\Models\RiwayatSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SuratController extends Controller
{
    public function publicIndex(Request $request)
    {
        $query = Surat::with('jenisSurat')->where('sifat_surat', 'umum');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_surat', 'like', '%' . $request->search . '%')
                    ->orWhere('nomor_surat', 'like', '%' . $request->search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('tanggal')) $query->whereDate('tanggal_surat', $request->tanggal);
        if ($request->filled('tahun')) $query->where('tahun_dokumen', $request->tahun);

        $years = Surat::where('sifat_surat', 'umum')
            ->select('tahun_dokumen')
            ->distinct()
            ->orderBy('tahun_dokumen', 'desc')
            ->pluck('tahun_dokumen');

        return Inertia::render('Public/SuratPublik', [
            'surats'  => $query->latest()->get(),
            'years'   => $years,
            'filters' => $request->only(['search', 'tanggal', 'tahun']),
        ]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Surat::with(['jenisSurat', 'pembuat', 'pengubah', 'riwayat.pengubah', 'riwayat.jenisSuratLama']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_surat', 'like', '%' . $request->search . '%')
                    ->orWhere('nomor_surat', 'like', '%' . $request->search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('tanggal')) $query->whereDate('tanggal_surat', $request->tanggal);
        if ($request->filled('tahun')) $query->where('tahun_dokumen', $request->tahun);

        $years = Surat::select('tahun_dokumen')
            ->distinct()
            ->orderBy('tahun_dokumen', 'desc')
            ->pluck('tahun_dokumen');

        return Inertia::render('Surat/Index', [
            'surats'       => $query->latest()->get(),
            'jenis_surats' => JenisSurat::all(),
            'years'        => $years,
            'userRole'     => $user->role,
            'filters'      => $request->only(['search', 'tanggal', 'tahun']),
        ]);
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $validated = $request->validate([
            'nama_surat'      => 'required|string|max:255',
            'deskripsi'       => 'required|string',
            'tahun_dokumen'   => 'required|string|max:4',
            'tanggal_surat'   => 'required|date',
            'nomor_surat'     => 'required|string|max:100',
            'jenis_surat_id'  => 'required|exists:jenis_surats,id',
            'sifat_surat'     => 'required|in:umum,rahasia',
            'keterangan'      => 'required|in:berlaku,dicabut',
            'file_pdf'        => 'required|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('file_pdf')) {
            $validated['file_pdf'] = $request->file('file_pdf')->store('surat_pdf', 'public');
        }

        $validated['created_by'] = Auth::id();
        Surat::create($validated);

        return redirect()->back()->with('message', 'Dokumen berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $surat = Surat::findOrFail($id);

        $validated = $request->validate([
            'nama_surat'      => 'required|string|max:255',
            'deskripsi'       => 'required|string',
            'tahun_dokumen'   => 'required|string|max:4',
            'tanggal_surat'   => 'required|date',
            'nomor_surat'     => 'required|string|max:100',
            'jenis_surat_id'  => 'required|exists:jenis_surats,id',
            'sifat_surat'     => 'required|in:umum,rahasia',
            'keterangan'      => 'required|in:berlaku,dicabut',
            'file_pdf'        => 'nullable|file|mimes:pdf|max:10240',
        ]);

        RiwayatSurat::create([
            'surat_id'             => $surat->id,
            'nama_surat_lama'      => $surat->nama_surat,
            'deskripsi_lama'       => $surat->deskripsi,
            'tahun_dokumen_lama'   => $surat->tahun_dokumen,
            'tanggal_surat_lama'   => $surat->tanggal_surat,
            'nomor_surat_lama'     => $surat->nomor_surat,
            'jenis_surat_id_lama'  => $surat->jenis_surat_id,
            'sifat_surat_lama'     => $surat->sifat_surat,
            'keterangan_lama'      => $surat->keterangan,
            'file_pdf_lama'        => $surat->file_pdf,
            'diubah_oleh'          => Auth::id(),
        ]);

        if ($request->hasFile('file_pdf')) {
            $validated['file_pdf'] = $request->file('file_pdf')->store('surat_pdf', 'public');
        } else {
            unset($validated['file_pdf']);
        }

        $validated['updated_by'] = Auth::id();
        $surat->update($validated);

        return redirect()->back()->with('message', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $surat = Surat::with('riwayat')->findOrFail($id);
        $pdfsToDelete = [$surat->file_pdf];
        foreach ($surat->riwayat as $riwayat) {
            $pdfsToDelete[] = $riwayat->file_pdf_lama;
        }
        foreach (array_unique($pdfsToDelete) as $pdf) {
            if ($pdf && Storage::disk('public')->exists($pdf)) {
                Storage::disk('public')->delete($pdf);
            }
        }
        $surat->delete();
        return redirect()->back()->with('message', 'Dokumen dihapus.');
    }

    public function lihat($id)
    {
        $surat = Surat::findOrFail($id);
        if ($surat->sifat_surat === 'rahasia' && !Auth::check()) abort(403);
        $surat->increment('jumlah_dilihat');
        return redirect(asset('storage/' . $surat->file_pdf));
    }

    public function unduh($id)
    {
        $surat = Surat::findOrFail($id);
        if ($surat->sifat_surat === 'rahasia' && !Auth::check()) abort(403);
        $surat->increment('jumlah_unduh');
        return response()->download(storage_path('app/public/' . $surat->file_pdf), $surat->nama_surat . '.pdf');
    }

    public function lihatRiwayat($id)
    {
        $riwayat = RiwayatSurat::findOrFail($id);
        if ($riwayat->sifat_surat_lama === 'rahasia' && !Auth::check()) abort(403);
        return redirect(asset('storage/' . $riwayat->file_pdf_lama));
    }

    public function unduhRiwayat($id)
    {
        $riwayat = RiwayatSurat::findOrFail($id);
        if ($riwayat->sifat_surat_lama === 'rahasia' && !Auth::check()) abort(403);
        return response()->download(storage_path('app/public/' . $riwayat->file_pdf_lama), $riwayat->nama_surat_lama . '_V-LAMA.pdf');
    }
}
