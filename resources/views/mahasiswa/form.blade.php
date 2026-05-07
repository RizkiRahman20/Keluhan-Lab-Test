<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Laporan Keluhan Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-8">
        <h1 class="text-2xl font-bold text-center text-blue-700 mb-2">Sistem Pelaporan Keluhan Lab</h1>
        <p class="text-center text-gray-500 mb-6">Isi form di bawah untuk melaporkan kerusakan/keluhan fasilitas laboratorium</p>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('mahasiswa.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIM <span class="text-red-500">*</span></label>
                    <input type="text" name="nim_pelapor" value="{{ old('nim_pelapor') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nm_pelapor" value="{{ old('nm_pelapor') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fakultas <span class="text-red-500">*</span></label>
                    <input type="text" name="fakultas_pelapor" value="{{ old('fakultas_pelapor') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Laboratorium <span class="text-red-500">*</span></label>
                    <select name="id_lab"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">-- Pilih Laboratorium --</option>
                        @foreach($labs as $lab)
                            <option value="{{ $lab->id_lab }}" {{ old('id_lab') == $lab->id_lab ? 'selected' : '' }}>
                                {{ $lab->nm_lab }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Kerusakan <span class="text-red-500">*</span></label>
                    <select name="kategori"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="PC" {{ old('kategori') == 'PC' ? 'selected' : '' }}>PC (Komputer)</option>
                        <option value="non_PC" {{ old('kategori') == 'non_PC' ? 'selected' : '' }}>Non PC (Fasilitas Lain)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Keluhan <span class="text-red-500">*</span></label>
                    <textarea name="catatan_lpr" rows="4"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>{{ old('catatan_lpr') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Kerusakan (Opsional)</label>
                    <input type="file" name="file_foto" accept="image/*"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maks 2MB</p>
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                    Kirim Laporan
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('mahasiswa.status') }}" class="text-blue-600 hover:underline text-sm">
                Cek status laporan saya →
            </a>
        </div>
    </div>
</body>
</html>