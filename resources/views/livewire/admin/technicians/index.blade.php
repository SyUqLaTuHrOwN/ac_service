<div x-data="{ open:@entangle('showModal') }" class="space-y-6">
  <div class="flex items-center gap-3">
    <input type="text" wire:model.live.debounce.500ms="search"
           class="w-64 rounded-lg border px-3 py-2" placeholder="Cari nama/email…">
    <button class="ml-auto px-3 py-2 rounded-lg bg-indigo-600 text-white"
            wire:click="openCreate">+ Teknisi Baru</button>
  </div>

  <div class="overflow-hidden rounded-xl border bg-white">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="p-3 text-left">Nama</th>
          <th class="p-3 text-left">Email</th>
          <th class="p-3 text-left">No HP</th>
          <th class="p-3 text-left">Alamat</th>
          <th class="p-3 text-left">Status</th>
          <th class="p-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($techs as $t)
          @php $p = $t->technicianProfile; @endphp
          <tr class="border-t">
            <td class="p-3">{{ $t->name }}</td>
            <td class="p-3">{{ $t->email }}</td>
            <td class="p-3">{{ $p->phone ?? '-' }}</td>
            <td class="p-3">{{ $p->address ?? '-' }}</td>
            <td class="p-3">
              <span class="px-2 py-1 rounded
                {{ ($p->status ?? 'aktif')==='aktif' ? 'bg-emerald-50 text-emerald-700' :
                   (($p->status ?? '')==='cuti' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                {{ $p->status ?? 'aktif' }}
              </span>
            </td>
            <td class="p-3 text-center space-x-2">
              <button class="px-2 py-1 rounded border" wire:click="openEdit({{ $t->id }})">Edit</button>
              <button class="px-2 py-1 rounded border" wire:click="toggleActive({{ $t->id }})">Toggle</button>
              <button class="px-2 py-1 rounded border text-rose-600" wire:click="delete({{ $t->id }})">Hapus</button>
            </td>
          </tr>
        @empty
          <tr><td class="p-4 text-center text-slate-500" colspan="7">Belum ada teknisi.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div>{{ $techs->links() }}</div>

  {{-- Modal sederhana (Alpine) --}}
  <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/30" @click="open=false"></div>
    <div class="relative w-full max-w-2xl rounded-xl bg-white shadow-lg p-6">
      <div class="text-lg font-semibold mb-4">
        {{ $editing_id ? 'Edit Teknisi' : 'Teknisi Baru' }}
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm">Nama</label>
          <input type="text" class="w-full rounded-lg border px-3 py-2" wire:model.defer="name">
          @error('name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="text-sm">Email</label>
          <input type="email" class="w-full rounded-lg border px-3 py-2" wire:model.defer="email">
          @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="text-sm">No HP</label>
          <input type="text" class="w-full rounded-lg border px-3 py-2" wire:model.defer="phone">
        </div>
        <div>
          <label class="text-sm">Status</label>
          <select class="w-full rounded-lg border px-3 py-2" wire:model.defer="status">
            <option value="aktif">aktif</option>
            <option value="cuti">cuti</option>
            <option value="nonaktif">nonaktif</option>
          </select>
        </div>
        <div class="md:col-span-2">
          <label class="text-sm">Alamat</label>
          <input type="text" class="w-full rounded-lg border px-3 py-2" wire:model.defer="address">
        </div>
        <label class="inline-flex items-center gap-2 mt-1">
          <input type="checkbox" class="rounded border" wire:model.defer="is_active">
          <span>Aktif</span>
        </label>
      </div>

      <div class="mt-6 flex justify-end gap-2">
        <button class="px-3 py-2 rounded border" @click="open=false">Batal</button>
        <button class="px-3 py-2 rounded bg-indigo-600 text-white" wire:click="save">Simpan</button>
      </div>
    </div>
  </div>
</div>
