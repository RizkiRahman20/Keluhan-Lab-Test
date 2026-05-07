<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Models\RiwayatPerbaikan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{
    public function cetakRiwayat(Request $request)
    {
        // Hanya SPV/admin yang terautentikasi
        if (!Auth::check()) {
            abort(403);
        }

        $query = RiwayatPerbaikan::with([
            'perbaikan.laporan.penugasan.lab',
            'perbaikan.laporan'
        ]);

        // Filter per lab
        if ($request->filled('id_lab')) {
            $query->whereHas('perbaikan.laporan.penugasan', fn ($q) => 
                $q->where('id_lab', $request->id_lab)
            );
        }

        // Filter tanggal
        if ($request->filled('dari')) {
            $query->whereDate('tgl_ubah', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tgl_ubah', '<=', $request->sampai);
        }

        $riwayats = $query->orderBy('tgl_ubah', 'desc')->get();
        $lab = $request->filled('id_lab') ? Lab::find($request->id_lab) : null;

        $pdf = Pdf::loadView('pdf.riwayat-perbaikan', [
            'riwayats' => $riwayats,
            'lab' => $lab,
            'dari' => $request->dari,
            'sampai' => $request->sampai,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('riwayat-perbaikan-' . now()->format('Ymd') . '.pdf');
    }
}