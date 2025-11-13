<?php
// app/Livewire/Admin/TechLeaves/Index.php
namespace App\Livewire\Admin\TechLeaves;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TechnicianLeave;
use App\Models\User;
use Illuminate\Support\Carbon;

class Index extends Component
{
    use WithPagination;

    public $status = 'pending';   // filter default
    public $search = '';

    public function approve(int $id)
    {
        $lv = TechnicianLeave::findOrFail($id);
        $lv->update([
            'status' => 'approved',
            'decided_at' => now(),
            'decided_by' => auth()->id(),
        ]);
        $this->dispatch('toast', message: 'Cuti disetujui.', type: 'ok');
    }

    public function reject(int $id)
    {
        $lv = TechnicianLeave::findOrFail($id);
        $lv->update([
            'status' => 'rejected',
            'decided_at' => now(),
            'decided_by' => auth()->id(),
        ]);
        $this->dispatch('toast', message: 'Cuti ditolak.', type: 'err');
    }

    public function render()
    {
        $q = TechnicianLeave::with('user')->latest();

        if ($this->status !== 'all') $q->where('status',$this->status);
        if ($this->search) {
            $q->whereHas('user', fn($u)=>$u->where('name','like',"%{$this->search}%")
                                          ->orWhere('email','like',"%{$this->search}%"));
        }

        return view('livewire.admin.tech-leaves.index', [
            'rows' => $q->paginate(12),
        ])->layout('layouts.app', [
            'title'=>'Permintaan Cuti Teknisi',
            'header'=>'Operasional • Permintaan Cuti Teknisi',
        ]);
    }
}
