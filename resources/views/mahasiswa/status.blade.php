<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Laporan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10">
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow p-8">
        <h1 class="text-2xl font-bold text-center text-blue-700 mb-6">Cek Status Laporan</h1>

        <form method="GET" action="{{ route('mahasiswa.status') }}" class="mb-6">
            <div class="flex gap-2">
                <input type="text" name="no_laporan" value="{{ request('no_laporan') }}"
                    placeholder="Masukkan No. Laporan (contoh: LPR-20240101-0001)"
                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Cari</button>
            </div>
        </form>

        @if(request('no_laporan') && !$laporan)
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                Laporan dengan nomor tersebut tidak ditemukan.
            </div>
        @endif

        @if($laporan)
            <div class="border border-gray-200 rounded-lg p-4 space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">No. Laporan</span>
                    <span class="font-semibold">{{ $laporan->no_laporan }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Tanggal Lapor</span>
                    <span>{{ $laporan->tgl_lapor->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Lab</span>
                    <span>{{ $laporan->penugasan?->lab?->nm_lab ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Kategori</span>
                    <span>{{ $laporan->kategori }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Status Approval</span>
                    <span class="px-2 py-1 rounded text-xs font-semibold
                        {{ $laporan->approval === 'menunggu' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $laporan->approval === 'disetujui' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $laporan->approval === 'ditolak' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($laporan->approval) }}
                    </span>
                </div>
                @if($laporan->perbaikan)
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Status Perbaikan</span>
                        <span class="px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-700">
                            {{ str_replace('_', ' ', ucfirst($laporan->perbaikan->status_perbaikan)) }}
                        </span>
                    </div>
                @endif
            </div>
        @endif

        <div class="mt-6 text-center">
            <a href="{{ route('mahasiswa.form') }}" class="text-blue-600 hover:underline text-sm">
                ← Kembali ke Form Laporan
            </a>
        </div>
    </div>
</body>
</html>