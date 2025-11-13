<div class="space-y-6">
  {{-- Filter bar --}}
  <div class="flex flex-col sm:flex-row gap-3 items-center">
    <input
      type="text"
      wire:model.live.debounce.300ms="q"
      class="w-full sm:max-w-md rounded-xl border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
      placeholder="Cari nama/email..."
    />

    <select wire:model.live="role" class="rounded-xl border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
      <option value="all">Semua role</option>
      <option value="admin">admin</option>
      <option value="teknisi">teknisi</option>
      <option value="client">client</option>
    </select>
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="p-3 text-left">Nama</th>
          <th class="p-3 text-left">Email</th>
          <th class="p-3 text-left">Role</th>
          <th class="p-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($users as $u)
          <tr class="border-t">
            <td class="p-3">{{ $u->name }}</td>
            <td class="p-3">{{ $u->email }}</td>
            <td class="p-3"><span class="px-2 py-1 rounded bg-slate-100">{{ $u->role }}</span></td>
            <td class="p-3">
              <div class="flex flex-wrap gap-2 justify-end">
                <button wire:click="edit({{ $u->id }})"
                        class="px-3 py-1.5 rounded-xl border hover:bg-slate-50">Edit</button>

                <button wire:click="openChangePassword({{ $u->id }})"
                        class="px-3 py-1.5 rounded-xl border hover:bg-slate-50">Ubah Password</button>

                <button wire:click="resetRandom({{ $u->id }})"
                        class="px-3 py-1.5 rounded-xl border text-amber-700 hover:bg-amber-50">Reset (Acak)</button>

                <button wire:click="confirmDelete({{ $u->id }})"
                        class="px-3 py-1.5 rounded-xl border text-rose-700 hover:bg-rose-50">Hapus</button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td class="p-6 text-center text-slate-500" colspan="4">Belum ada pengguna.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="p-3">{{ $users->links() }}</div>
  </div>

  {{-- =================== MODALS TANPA KOMPONEN KUSTOM =================== --}}

  {{-- Edit User --}}
  @if ($showEdit)
    <div class="fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40" wire:click="$set('showEdit', false)"></div>
      <div class="absolute inset-x-0 top-24 mx-auto w-[95%] max-w-2xl bg-white rounded-2xl shadow-lg">
        <div class="px-5 py-4 border-b font-semibold">Edit Akun</div>

        <div class="p-5 grid sm:grid-cols-2 gap-4">
          <div>
            <label class="text-xs font-medium text-slate-600">Nama</label>
            <input type="text" wire:model.defer="name"
                   class="mt-1 w-full rounded-xl border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('name') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="text-xs font-medium text-slate-600">Email</label>
            <input type="email" wire:model.defer="email"
                   class="mt-1 w-full rounded-xl border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('email') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="text-xs font-medium text-slate-600">Telepon</label>
            <input type="text" wire:model.defer="phone"
                   class="mt-1 w-full rounded-xl border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('phone') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="text-xs font-medium text-slate-600">Role</label>
            <select wire:model.defer="editRole"
                    class="mt-1 w-full rounded-xl border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="admin">admin</option>
              <option value="teknisi">teknisi</option>
              <option value="client">client</option>
            </select>
            @error('editRole') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="px-5 py-4 border-t flex justify-end gap-2">
          <button class="px-4 py-2 rounded-xl border" wire:click="$set('showEdit', false)">Batal</button>
          <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white" wire:click="save">Simpan</button>
        </div>
      </div>
    </div>
  @endif

  {{-- Ubah Password --}}
  @if ($showPwd)
    <div class="fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40" wire:click="$set('showPwd', false)"></div>
      <div class="absolute inset-x-0 top-28 mx-auto w-[95%] max-w-xl bg-white rounded-2xl shadow-lg">
        <div class="px-5 py-4 border-b font-semibold">Ubah Password</div>

        <div class="p-5 grid sm:grid-cols-2 gap-4">
          <div>
            <label class="text-xs font-medium text-slate-600">Password Baru</label>
            <input type="password" wire:model.defer="new_password"
                   class="mt-1 w-full rounded-xl border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('new_password') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
          </div>
          <div>
            <label class="text-xs font-medium text-slate-600">Konfirmasi Password</label>
            <input type="password" wire:model.defer="new_password_confirmation"
                   class="mt-1 w-full rounded-xl border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          </div>
        </div>

        <div class="px-5 py-4 border-t flex justify-end gap-2">
          <button class="px-4 py-2 rounded-xl border" wire:click="$set('showPwd', false)">Batal</button>
          <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white" wire:click="changePassword">Simpan</button>
        </div>
      </div>
    </div>
  @endif

  {{-- Hapus User --}}
  @if ($showDelete)
    <div class="fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40" wire:click="$set('showDelete', false)"></div>
      <div class="absolute inset-x-0 top-32 mx-auto w-[95%] max-w-lg bg-white rounded-2xl shadow-lg">
        <div class="px-5 py-4 border-b font-semibold">Hapus Akun</div>

        <div class="p-5">
          <p class="text-sm text-slate-600">
            Aksi ini tidak dapat dibatalkan. Yakin ingin menghapus akun ini?
          </p>
        </div>

        <div class="px-5 py-4 border-t flex justify-end gap-2">
          <button class="px-4 py-2 rounded-xl border" wire:click="$set('showDelete', false)">Batal</button>
          <button class="px-4 py-2 rounded-xl bg-rose-600 text-white" wire:click="destroy">Hapus</button>
        </div>
      </div>
    </div>
  @endif
</div>
