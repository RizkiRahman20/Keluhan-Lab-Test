<?php

namespace App\Livewire;

use App\Models\Perbaikan;
use App\Models\RiwayatPerbaikan;
use Livewire\Component;

class KanbanPerbaikan extends Component
{
    public $columns = [
        'antrean' => 'Antrean',
        'dikerjakan' => 'Dikerjakan',
        'menunggu_sparepart' => 'Menunggu Sparepart',
        'selesai' => 'Selesai',
    ];

    public function updateStatus($id, $newStatus): void
    {
        $perbaikan = Perbaikan::findOrFail($id);
        $oldStatus = $perbaikan->status_perbaikan;
        
        $updateData = ['status_perbaikan' => $newStatus];
        if ($newStatus === 'dikerjakan' && !$perbaikan->tgl_mulai) {
            $updateData['tgl_mulai'] = now()->toDateString();
        }
        if ($newStatus === 'selesai') {
            $updateData['tgl_selesai'] = now()->toDateString();
        }
        $perbaikan->update($updateData);

        RiwayatPerbaikan::create([
            'tgl_ubah' => now()->toDateString(),
            'catatan_rw' => 'Status diubah dari ' . $oldStatus . ' ke ' . $newStatus,
            'id_perbaikan' => $perbaikan->id_perbaikan,
        ]);
    }

    public function render()
    {
        $perbaikans = [];
        foreach (array_keys($this->columns) as $status) {
            $perbaikans[$status] = Perbaikan::with(['laporan.penugasan.lab'])
                ->where('status_perbaikan', $status)
                ->get();
        }
        return view('livewire.kanban-perbaikan', ['perbaikans' => $perbaikans]);
    }
}