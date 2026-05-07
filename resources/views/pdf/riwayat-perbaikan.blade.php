<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Perbaikan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #222; }
        h2 { text-align: center; font-size: 15px; margin-bottom: 4px; }
        .subtitle { text-align: center; font-size: 11px; color: #555; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #1d4ed8; color: white; padding: 6px 8px; text-align: left; }
        td { padding: 5px 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background: #f0f4ff; }
        .footer { margin-top: 20px; font-size: 10px; color: #888; text-align: right; }
    </style>
</head>
<body>
    <h2>Riwayat Perbaikan Laboratorium</h2>
    <p class="subtitle">
        {{ $lab ? 'Lab: ' . $lab->nm_lab : 'Semua Lab' }}
        @if($dari || $sampai)
            | Periode: {{ $dari ?? '-' }} s/d {{ $sampai ?? '-' }}
        @endif
    </p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>No. Laporan</th>
                <th>Lab</th>
                <th>Pelapor</th>
                <th>Kategori</th>
                <th>Tgl Ubah</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayats as $i => $rw)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $rw->perbaikan?->id_laporan ?? '-' }}</td>
                <td>{{ $rw->perbaikan?->laporan?->penugasan?->lab?->nm_lab ?? '-' }}</td>
                <td>{{ $rw->perbaikan?->laporan?->nm_pelapor ?? '-' }}</td>
                <td>{{ $rw->perbaikan?->laporan?->kategori ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($rw->tgl_ubah)->format('d/m/Y') }}</td>
                <td>{{ str_replace('_', ' ', $rw->perbaikan?->status_perbaikan ?? '-') }}</td>
                <td>{{ $rw->catatan_rw }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center; color:#888;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <p class="footer">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>