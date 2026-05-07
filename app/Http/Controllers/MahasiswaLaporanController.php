<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Models\LaporanKeluhan;
use App\Models\PenugasanUserLab;
use Illuminate\Http\Request;

class MahasiswaLaporanController extends Controller
{
    public function index()
    {
        $labs = Lab::where('status_lab', 'aktif')->orderBy('nm_lab')->get();
        return view('mahasiswa.form', compact('labs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim_pelapor' => 'required|string|max:20',
            'nm_pelapor' => 'required|string|max:100',
            'fakultas_pelapor' => 'required|string|max:100',
            'id_lab' => 'required|exists:labs,id_lab',
            'kategori' => 'required|in:PC,non_PC',
            'catatan_lpr' => 'required|string',
            'file_foto' => 'nullable|image|max:2048',
        ]);

        // Cari penugasan aktif di lab tersebut (PIC = SPV)
        $penugasan = PenugasanUserLab::with('user')
            ->where('id_lab', $request->id_lab)
            ->where('status_aktif', 'aktif')
            ->whereHas('user', fn ($q) => $q->where('role_user', 'like', 'spv_%'))
            ->first();

        $filePath = null;
        if ($request->hasFile('file_foto')) {
            $filePath = $request->file('file_foto')->store('laporan', 'public');
        }

        $noLaporan = LaporanKeluhan::generateNomorLaporan();

        $laporan = LaporanKeluhan::create([
            'no_laporan' => $noLaporan,
            'tgl_lapor' => now()->toDateString(),
            'approval' => 'menunggu',
            'nim_pelapor' => $request->nim_pelapor,
            'nm_pelapor' => $request->nm_pelapor,
            'fakultas_pelapor' => $request->fakultas_pelapor,
            'kategori' => $request->kategori,
            'catatan_lpr' => $request->catatan_lpr,
            'file_foto' => $filePath,
            'id_penugasan' => $penugasan?->id_penugasan,
        ]);

        return redirect()->route('mahasiswa.status', ['no_laporan' => $noLaporan])
            ->with('success', 'Laporan berhasil dikirim! Simpan nomor laporan Anda: ' . $noLaporan);
    }

    public function status(Request $request)
    {
        $laporan = null;
        if ($request->has('no_laporan') && $request->no_laporan) {
            $laporan = LaporanKeluhan::with(['perbaikan', 'penugasan.lab'])
                ->where('no_laporan', $request->no_laporan)
                ->first();
        }
        return view('mahasiswa.status', compact('laporan'));
    }
}