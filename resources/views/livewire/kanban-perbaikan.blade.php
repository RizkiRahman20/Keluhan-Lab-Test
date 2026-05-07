<div class="flex gap-4 overflow-x-auto pb-4" x-data="kanbanBoard()">
    @foreach($columns as $status => $label)
    <div class="flex-shrink-0 w-72 bg-gray-100 rounded-xl p-3"
         x-on:dragover.prevent
         x-on:drop="drop($event, '{{ $status }}')">
        <h3 class="font-bold text-gray-700 mb-3 flex items-center gap-2">
            <span class="inline-block w-3 h-3 rounded-full
                {{ $status === 'antrean' ? 'bg-gray-400' : '' }}
                {{ $status === 'dikerjakan' ? 'bg-yellow-400' : '' }}
                {{ $status === 'menunggu_sparepart' ? 'bg-blue-400' : '' }}
                {{ $status === 'selesai' ? 'bg-green-400' : '' }}
            "></span>
            {{ $label }}
            <span class="ml-auto bg-gray-300 text-gray-700 text-xs px-2 py-0.5 rounded-full">
                {{ count($perbaikans[$status]) }}
            </span>
        </h3>
        <div class="space-y-2 min-h-[100px]">
            @foreach($perbaikans[$status] as $item)
            <div class="bg-white rounded-lg p-3 shadow-sm cursor-grab border border-gray-200"
                 draggable="true"
                 x-on:dragstart="dragstart($event, {{ $item->id_perbaikan }})"
                 wire:key="perbaikan-{{ $item->id_perbaikan }}">
                <p class="text-xs font-bold text-blue-600">{{ $item->id_laporan }}</p>
                <p class="text-sm font-medium text-gray-800 mt-1">
                    {{ $item->laporan?->penugasan?->lab?->nm_lab ?? 'Lab tidak diketahui' }}
                </p>
                <p class="text-xs text-gray-500 mt-1 truncate">
                    {{ $item->laporan?->catatan_lpr ?? '-' }}
                </p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="text-xs px-2 py-0.5 rounded
                        {{ $item->laporan?->kategori === 'PC' ? 'bg-orange-100 text-orange-600' : 'bg-purple-100 text-purple-600' }}">
                        {{ $item->laporan?->kategori ?? '-' }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
    <script>
        function kanbanBoard() {
            return {
                draggedId: null,
                dragstart(event, id) {
                    this.draggedId = id;
                },
                drop(event, status) {
                    if (this.draggedId) {
                        @this.updateStatus(this.draggedId, status);
                        this.draggedId = null;
                    }
                }
            }
        }
    </script>
</div>