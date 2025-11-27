<?php

namespace App\Livewire\Admin\Technicians;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\TechnicianProfile;
use App\Support\Role;
use Illuminate\Support\Carbon;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';
    public bool $showModal = false;
    public ?int $editingId = null;

    public array $form = [
        'team_name'      => '',
        'leader_email'   => '',
        'leader_phone'   => '',
        'member_1_name'  => '',
        'member_2_name'  => '',
        'status'         => 'aktif',
        'is_active'      => true,
        'address'        => '',
    ];

    protected $rules = [
        'form.team_name'     => 'required|string|max:190',
        'form.leader_email'  => 'required|email',
        'form.leader_phone'  => 'nullable|string|max:50',
        'form.member_1_name' => 'nullable|string|max:190',
        'form.member_2_name' => 'nullable|string|max:190',
        'form.status'        => 'required|in:aktif,sedang_bertugas,cuti,nonaktif',
        'form.is_active'     => 'boolean',
        'form.address'       => 'nullable|string',
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }

    public function openCreate()
    {
        $this->reset('form', 'editingId');
        $this->form['status'] = 'aktif';
        $this->form['is_active'] = true;
        $this->showModal = true;
    }

    public function openEdit(int $id)
    {
        $p = TechnicianProfile::with('user')->findOrFail($id);

        $this->editingId = $id;
        $this->form = [
            'team_name'     => $p->team_name,
            'leader_email'  => $p->user->email,
            'leader_phone'  => $p->phone,
            'member_1_name' => $p->member_1_name,
            'member_2_name' => $p->member_2_name,
            'status'        => $p->status,
            'is_active'     => $p->is_active,
            'address'       => $p->address,
        ];

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            // Update
            $p = TechnicianProfile::with('user')->findOrFail($this->editingId);

            $p->user->update([
                'name'  => $this->form['team_name'],
                'email' => $this->form['leader_email'],
            ]);

            $p->update([
                'team_name'     => $this->form['team_name'],
                'phone'         => $this->form['leader_phone'],
                'member_1_name' => $this->form['member_1_name'],
                'member_2_name' => $this->form['member_2_name'],
                'status'        => $this->form['status'],
                'is_active'     => $this->form['is_active'],
                'address'       => $this->form['address'],
            ]);

            $msg = "Tim teknisi diperbarui.";
        } else {
            // Create
            $u = User::create([
                'name'     => $this->form['team_name'],
                'email'    => $this->form['leader_email'],
                'password' => bcrypt('password123'),
                'role'     => Role::TEKNISI,
            ]);

            TechnicianProfile::create([
                'user_id'       => $u->id,
                'team_name'     => $this->form['team_name'],
                'phone'         => $this->form['leader_phone'],
                'member_1_name' => $this->form['member_1_name'],
                'member_2_name' => $this->form['member_2_name'],
                'status'        => $this->form['status'],
                'is_active'     => $this->form['is_active'],
                'address'       => $this->form['address'],
            ]);

            $msg = "Tim teknisi dibuat.";
        }

        $this->showModal = false;
        $this->dispatch('toast', message: $msg, type: 'ok');
    }

    public function toggleActive(int $id)
    {
        $p = TechnicianProfile::findOrFail($id);
        $p->update([ 'is_active' => !$p->is_active ]);

        $this->dispatch('toast', message: 'Status diperbarui.', type: 'ok');
    }

    public function delete(int $id)
    {
        $p = TechnicianProfile::with('user')->findOrFail($id);

        if ($p->user) $p->user->delete();
        else $p->delete();

        $this->dispatch('toast', message: 'Teknisi dihapus.', type: 'ok');
    }

    public function render()
    {
        /** 
         * FILTER MENGGUNAKAN QUERY BUILDER (AGAR PAGINATE BISA)
         */
        $profiles = TechnicianProfile::with('user')
            ->whereHas('user', fn($q) => $q->where('role', Role::TEKNISI))
            ->when($this->search, function ($q) {
                $s = "%{$this->search}%";
                $q->where('team_name', 'like', $s)
                  ->orWhere('member_1_name', 'like', $s)
                  ->orWhere('member_2_name', 'like', $s)
                  ->orWhereHas('user', fn($u)=>$u->where('email','like',$s));
            })
            ->when($this->statusFilter !== 'all', function ($q) {
    if ($this->statusFilter === 'nonaktif') {
        $q->where('is_active', false);
    } else {
        $q->where('status', $this->statusFilter)->where('is_active', true);
    }
})

            ->orderBy('team_name')
            ->paginate(10);

        return view('livewire.admin.technicians.index', [
            'teams' => $profiles
        ])->layout('layouts.app', [
            'title'  => 'Tim Teknisi',
            'header' => 'Admin • Tim Teknisi',
        ]);
    }
}
