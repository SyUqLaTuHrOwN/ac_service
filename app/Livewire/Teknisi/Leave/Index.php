<?php
// app/Livewire/Teknisi/Leave/Index.php
namespace App\Livewire\Teknisi\Leave;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\TechnicianLeave;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class Index extends Component
{
    use WithFileUploads;

    public $start_date, $end_date, $reason, $proof; // $proof = UploadedFile
    public $filter_status = 'all';

    protected function rules() {
        return [
            'start_date' => ['required','date','after_or_equal:today'],
            'end_date'   => ['required','date','after_or_equal:start_date'],
            'reason'     => ['nullable','string','max:255'],
            'proof'      => ['nullable','file','mimes:jpg,jpeg,png,webp,pdf','max:2048'],
        ];
    }

    public function submit()
    {
        $data = $this->validate();
        $path = null;
        if ($this->proof) {
            $path = $this->proof->store('leave_proofs','public');
        }

        TechnicianLeave::create([
            'user_id'    => auth()->id(),
            'start_date' => $data['start_date'],
            'end_date'   => $data['end_date'],
            'reason'     => $data['reason'] ?? null,
            'proof_path' => $path,
            'status'     => 'pending',
        ]);

        $this->reset(['start_date','end_date','reason','proof']);
        $this->dispatch('toast', message: 'Pengajuan cuti terkirim.', type: 'ok');
    }

    public function render()
    {
        $q = TechnicianLeave::where('user_id', auth()->id())->latest();

        if ($this->filter_status !== 'all') {
            $q->where('status',$this->filter_status);
        }

        return view('livewire.teknisi.leave.index', [
            'leaves' => $q->paginate(10),
        ])->layout('layouts.app', [
            'title'=>'Ajukan Cuti',
            'header'=>'Teknisi • Ajukan Cuti',
        ]);
    }
}
