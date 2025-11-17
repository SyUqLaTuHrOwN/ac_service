<?php

namespace App\Livewire\Admin\Technicians;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\TechnicianProfile;
use App\Support\Role;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editing_id = null;

    public $name, $email, $phone, $address;
    public $is_active = true;

    protected $rules = [
        'name'      => 'required|string|min:3',
        'email'     => 'required|email',
        'phone'     => 'nullable|string|max:50',
        'address'   => 'nullable|string|max:255',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        $techs = User::with(['technicianProfile', 'approvedLeaves'])
            ->where('role', Role::TEKNISI)
            ->where(function($q){
                $q->where('name','like',"%{$this->search}%")
                  ->orWhere('email','like',"%{$this->search}%");
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.technicians.index', compact('techs'))
            ->layout('layouts.app', [
                'title' => 'Teknisi',
                'header' => 'Operasional • Teknisi'
            ]);
    }

    /** 
     * Status teknisi otomatis:
     * - nonaktif → jika is_active == false
     * - cuti → jika hari ini masuk rentang tanggal cuti
     * - aktif → default
     */
    public function getStatus($tech)
    {
        // nonaktif
        if (!$tech->technicianProfile || !$tech->technicianProfile->is_active) {
            return 'nonaktif';
        }

        // cuti
        $today = now()->toDateString();
        $hasLeaveToday = $tech->approvedLeaves()
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->exists();

        if ($hasLeaveToday) {
            return 'cuti';
        }

        // aktif
        return 'aktif';
    }

    public function openCreate()
    {
        $this->reset(['editing_id','name','email','phone','address','is_active']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEdit($userId)
    {
        $u = User::with('technicianProfile')->findOrFail($userId);

        $this->editing_id = $u->id;
        $this->name       = $u->name;
        $this->email      = $u->email;
        $this->phone      = $u->technicianProfile->phone ?? '';
        $this->address    = $u->technicianProfile->address ?? '';
        $this->is_active  = (bool)($u->technicianProfile->is_active ?? true);

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $user = $this->editing_id
            ? User::where('role', Role::TEKNISI)->findOrFail($this->editing_id)
            : new User(['role' => Role::TEKNISI]);

        $user->name  = $this->name;
        $user->email = $this->email;

        if (! $user->exists) {
            $user->password = bcrypt('password');
        }
        $user->save();

        $profile = $user->technicianProfile ?: new TechnicianProfile(['user_id' => $user->id]);
        $profile->phone     = $this->phone;
        $profile->address   = $this->address;
        $profile->is_active = (bool)$this->is_active;
        $profile->save();

        $this->dispatch('toast', message: 'Data teknisi tersimpan.', type: 'ok');
        $this->showModal = false;
    }

    public function toggleActive($userId)
    {
        $p = TechnicianProfile::firstOrCreate(['user_id'=>$userId], [
            'is_active'=>true
        ]);

        $p->is_active = ! $p->is_active;
        $p->save();
    }

    public function delete($userId)
    {
        $u = User::where('role', Role::TEKNISI)->findOrFail($userId);
        $u->delete();

        $this->dispatch('toast', message: 'Teknisi dihapus.', type: 'ok');
    }
}
