<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Support\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $q = '';
    public string $role = 'all';

    // edit modal
    public bool $showEdit = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $email = '';
    public ?string $phone = null;
    public string $editRole = Role::CLIENT;

    // change password modal
    public bool $showPwd = false;
    public ?int $pwdUserId = null;
    public string $new_password = '';
    public string $new_password_confirmation = '';

    // delete modal
    public bool $showDelete = false;
    public ?int $deleteId = null;

    protected function rules(): array
    {
        return [
            'name'  => ['required','string','max:120'],
            'email' => [
                'required','email','max:160',
                Rule::unique('users','email')->ignore($this->editingId),
            ],
            'phone' => ['nullable','string','max:30'],
            'editRole' => ['required', Rule::in([Role::ADMIN, Role::TEKNISI, Role::CLIENT])],
        ];
    }

    public function updatingQ()    { $this->resetPage(); }
    public function updatingRole() { $this->resetPage(); }

    public function edit(int $id): void
    {
        $u = User::findOrFail($id);
        $this->editingId = $u->id;
        $this->name      = (string) $u->name;
        $this->email     = (string) $u->email;
        $this->phone     = (string) ($u->phone ?? '');
        $this->editRole  = (string) $u->role;
        $this->showEdit  = true;
    }

    public function save(): void
    {
        $this->validate();

        $u = User::findOrFail($this->editingId);
        $u->update([
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'role'  => $this->editRole,
        ]);

        $this->showEdit = false;
        $this->dispatch('toast', message: 'Akun diperbarui.', type: 'ok');
    }

    public function openChangePassword(int $id): void
    {
        $this->pwdUserId = $id;
        $this->new_password = $this->new_password_confirmation = '';
        $this->showPwd = true;
    }

    public function changePassword(): void
    {
        $this->validate([
            'new_password' => ['required','string','min:8','confirmed'],
        ], [], ['new_password' => 'password baru']);

        $u = User::findOrFail($this->pwdUserId);
        $u->password = Hash::make($this->new_password);
        $u->save();

        $this->showPwd = false;
        $this->dispatch('toast', message: 'Password diubah.', type: 'ok');
    }

    public function resetRandom(int $id): void
    {
        $u = User::findOrFail($id);
        $plain = str()->password(10); // Laravel helper (>=10.x)
        $u->password = Hash::make($plain);
        $u->save();

        $this->dispatch('toast', message: "Password baru: {$plain}", type: 'ok');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDelete = true;
    }

    public function destroy(): void
    {
        $u = User::findOrFail($this->deleteId);

        // Larangan aman: jangan hapus diri sendiri & jangan hapus admin terakhir
        if (auth()->id() === $u->id) {
            $this->dispatch('toast', message: 'Tidak bisa menghapus akun sendiri.', type: 'err');
            $this->showDelete = false;
            return;
        }
        if ($u->role === Role::ADMIN && User::where('role', Role::ADMIN)->count() <= 1) {
            $this->dispatch('toast', message: 'Tidak bisa menghapus admin terakhir.', type: 'err');
            $this->showDelete = false;
            return;
        }

        $u->delete();
        $this->showDelete = false;
        $this->dispatch('toast', message: 'Akun dihapus.', type: 'ok');
    }

    public function render()
    {
        $users = User::query()
            ->when($this->q, fn($q) =>
                $q->where(fn($w) => $w->where('name','like',"%{$this->q}%")
                                     ->orWhere('email','like',"%{$this->q}%")))
            ->when($this->role !== 'all', fn($q) => $q->where('role', $this->role))
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.admin.users.index', compact('users'))
            ->layout('layouts.app', [
                'title'  => 'Pengguna',
                'header' => 'Sistem • Pengguna',
            ]);
    }
}
