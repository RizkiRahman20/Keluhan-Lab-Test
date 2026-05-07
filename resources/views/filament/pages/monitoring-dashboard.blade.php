<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <x-filament::card>
            <div class="flex items-center gap-4">
                <x-heroicon-o-document-text class="h-10 w-10 text-blue-500" />
                <div>
                    <p class="text-sm text-gray-500">Total Laporan</p>
                    <p class="text-3xl font-bold">{{ $totalLaporan }}</p>
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="flex items-center gap-4">
                <x-heroicon-o-clock class="h-10 w-10 text-yellow-500" />
                <div>
                    <p class="text-sm text-gray-500">Menunggu Approval</p>
                    <p class="text-3xl font-bold">{{ $laporanMenunggu }}</p>
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="flex items-center gap-4">
                <x-heroicon-o-check-circle class="h-10 w-10 text-green-500" />
                <div>
                    <p class="text-sm text-gray-500">Laporan Disetujui</p>
                    <p class="text-3xl font-bold">{{ $laporanDisetujui }}</p>
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="flex items-center gap-4">
                <x-heroicon-o-wrench-screwdriver class="h-10 w-10 text-orange-500" />
                <div>
                    <p class="text-sm text-gray-500">Perbaikan Berjalan</p>
                    <p class="text-3xl font-bold">{{ $perbaikanBerjalan }}</p>
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="flex items-center gap-4">
                <x-heroicon-o-check-badge class="h-10 w-10 text-green-700" />
                <div>
                    <p class="text-sm text-gray-500">Perbaikan Selesai</p>
                    <p class="text-3xl font-bold">{{ $perbaikanSelesai }}</p>
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="flex items-center gap-4">
                <x-heroicon-o-exclamation-circle class="h-10 w-10 text-red-500" />
                <div>
                    <p class="text-sm text-gray-500">Menunggu Validasi SPV</p>
                    <p class="text-3xl font-bold">{{ $menungguValidasi }}</p>
                </div>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>