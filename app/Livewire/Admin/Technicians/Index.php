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

    // form fields
    public $name, $email, $phone, $address, $status = 'aktif', $is_active = true;

    protected $rules = [
        'name'      => 'required|string|min:3',
        'email'     => 'required|email',
        'phone'     => 'nullable|string|max:50',
        'address'   => 'nullable|string|max:255',
        'status'    => 'required|in:aktif,cuti,nonaktif',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        $techs = User::with('technicianProfile')
            ->where('role', Role::TEKNISI)
            ->where(function($q){
                $q->where('name','like',"%{$this->search}%")
                  ->orWhere('email','like',"%{$this->search}%");
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.technicians.index', compact('techs'))
            ->layout('layouts.app', ['title'=>'Teknisi','header'=>'Operasional • Teknisi']);
    }

    public function openCreate()
    {
        $this->reset(['editing_id','name','email','phone','address','status','is_active']);
        $this->status    = 'aktif';
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
        $this->status     = $u->technicianProfile->status ?? 'aktif';
        $this->is_active  = (bool)($u->technicianProfile->is_active ?? true);

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        // simpan user
        $user = $this->editing_id
            ? User::where('role', Role::TEKNISI)->findOrFail($this->editing_id)
            : new User(['role' => Role::TEKNISI]);

        $user->name  = $this->name;
        $user->email = $this->email;

        if (! $user->exists) {
            $user->password = bcrypt('password'); // admin bisa reset nanti
        }
        $user->save();

        // simpan profil
        $profile = $user->technicianProfile ?: new TechnicianProfile(['user_id' => $user->id]);
        $profile->phone     = $this->phone;
        $profile->address   = $this->address;
        $profile->status    = $this->status;
        $profile->is_active = (bool)$this->is_active;
        $profile->save();

        $this->dispatch('toast', message: 'Data teknisi tersimpan.', type: 'ok');
        $this->showModal = false;
    }

    public function toggleActive($userId)
    {
        $p = TechnicianProfile::firstOrCreate(['user_id'=>$userId], [
            'status'=>'aktif','is_active'=>true
        ]);
        $p->is_active = ! $p->is_active;
        if (!$p->is_active && $p->status === 'aktif') {
            $p->status = 'nonaktif';
        }
        $p->save();
    }

    public function delete($userId)
    {
        $u = User::where('role', Role::TEKNISI)->findOrFail($userId);
        $u->delete();
        $this->dispatch('toast', message: 'Teknisi dihapus.', type: 'ok');
    }
}
