<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Models\LaporanKeluhan;
use App\Models\RiwayatPerbaikan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{
    // GET /internal/pdf/riwayat-perbaikan
    public function cetakRiwayat(Request $request)
    {
        $request->validate([
            'id_lab' => 'nullable|exists:labs,id_lab',
            'dari'   => 'nullable|date',
            'sampai' => 'nullable|date|after_or_equal:dari',
        ]);

        $query = RiwayatPerbaikan::with([
            'perbaikan.laporan.penugasan.lab',
        ]);

        if ($request->filled('id_lab')) {
            $query->whereHas('perbaikan.laporan.penugasan', fn ($q) =>
                $q->where('id_lab', $request->id_lab)
            );
        }

        if ($request->filled('dari')) {
            $query->whereDate('tgl_ubah', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('tgl_ubah', '<=', $request->sampai);
        }

        $riwayats = $query->orderBy('tgl_ubah', 'desc')->get();
        $lab      = $request->filled('id_lab') ? Lab::find($request->id_lab) : null;

        // ✅ BENAR — semua variabel dalam satu array di argumen pertama
        $pdf = Pdf::loadView('pdf.riwayat-perbaikan', [
            'riwayats'    => $riwayats,
            'lab'         => $lab,
            'dari'        => $request->dari,    // ← langsung di sini
            'sampai'      => $request->sampai,  // ← langsung di sini
            'dicetakOleh' => Auth::user()->nm_user,
        ])->setPaper('a4', 'portrait');

        $filename = 'riwayat-perbaikan-'
            . ($lab ? $lab->kd_lab . '-' : 'semua-')
            . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    // GET /internal/pdf/laporan/{no_laporan}
    public function cetakDetailLaporan(string $noLaporan)
    {
        $laporan = LaporanKeluhan::with([
            'perbaikan.riwayatPerbaikans',
            'penugasan.lab',
            'penugasan.user',
            'pic',
        ])->where('no_laporan', $noLaporan)->firstOrFail();

        // ✅ Semua variabel dalam satu array
        $pdf = Pdf::loadView('pdf.detail-laporan', [
            'laporan'     => $laporan,
            'dicetakOleh' => Auth::user()->nm_user,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-' . $noLaporan . '.pdf');
    }

    // GET /internal/pdf/rekap-lab/{id_lab}
    public function cetakRekapLab(Request $request, int $idLab)
    {
        $request->validate([
            'dari'   => 'nullable|date',
            'sampai' => 'nullable|date|after_or_equal:dari',
        ]);

        $lab = Lab::findOrFail($idLab);

        $query = LaporanKeluhan::with(['perbaikan', 'penugasan.lab'])
            ->whereHas('penugasan', fn ($q) => $q->where('id_lab', $idLab));

        if ($request->filled('dari')) {
            $query->whereDate('tgl_lapor', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('tgl_lapor', '<=', $request->sampai);
        }

        $laporans = $query->orderBy('tgl_lapor', 'desc')->get();

        // ✅ Semua variabel dalam satu array
        $pdf = Pdf::loadView('pdf.rekap-lab', [
            'lab'         => $lab,
            'laporans'    => $laporans,
            'dari'        => $request->dari,
            'sampai'      => $request->sampai,
            'dicetakOleh' => Auth::user()->nm_user,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('rekap-' . $lab->kd_lab . '-' . now()->format('Ymd') . '.pdf');
    }
}