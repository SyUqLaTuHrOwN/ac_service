<div>
  {{-- toolbar filter + flash --}}
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-2">
      <select class="rounded-xl border-gray-300" wire:model.live="clientFilter">
        <option value="">Semua Klien</option>
        @foreach($clients as $c)
          <option value="{{ $c->id }}">{{ $c->company_name }}</option>
        @endforeach
      </select>

      <select class="rounded-xl border-gray-300" wire:model.live="statusFilter">
        <option value="">Semua Status</option>
        <option value="1">menunggu</option>
        <option value="2">dalam_proses</option>
        <option value="3">selesai_servis</option>
        <option value="4">selesai</option>
      </select>

      <input type="text" wire:model.live.debounce.400ms="search" class="rounded-xl border-gray-300" placeholder="Cari catatan...">
      @if(session('ok')) <span class="text-sm text-emerald-700">{{ session('ok') }}</span> @endif
      @if(session('err')) <span class="text-sm text-red-600">{{ session('err') }}</span> @endif
    </div>

    <button wire:click="createNew" class="rounded-xl bg-indigo-600 text-white px-4 py-2">+ Buat Jadwal</button>
  </div>

  {{-- table --}}
  <div class="bg-white border rounded-2xl overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="p-3 text-left">Tanggal</th>
          <th class="p-3 text-left">Klien/Lokasi</th>
          <th class="p-3 text-left">Teknisi</th>
          <th class="p-3 text-left">Unit</th>
          <th class="p-3 text-left">Respon Klien</th>
          <th class="p-3 text-left">Status</th>
          <th class="p-3 text-left">Catatan</th>
          <th class="p-3 w-36">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($schedules as $s)
          <tr class="border-t">
            <td class="p-3">{{ $s->scheduled_at->format('d M Y H:i') }}</td>
            <td class="p-3">{{ $s->client?->company_name }} — {{ $s->location?->name }}</td>
            <td class="p-3">{{ $s->technician?->name ?? '-' }}</td>

            {{-- Kolom Unit (ringkas) --}}
            <td class="p-3">
              @php
                $labels = $s->units->map(function($u){
                  return $u->serial_number ? 'SN '.$u->serial_number : trim(($u->brand.' '.$u->model));
                });
              @endphp
              @if($labels->isEmpty())
                <span class="text-gray-400">—</span>
              @else
                {{ $labels->take(3)->join(', ') }}@if($labels->count() > 3), +{{ $labels->count() - 3 }} lagi @endif
              @endif
            </td>

            {{-- Respon Klien + usulan tanggal --}}
            <td class="p-3">
              @php
                $badgeColor = match($s->client_response) {
                  'confirmed'            => 'bg-emerald-100 text-emerald-700',
                  'reschedule_requested' => 'bg-amber-100 text-amber-700',
                  'cancelled_by_client'  => 'bg-red-100 text-red-700',
                  default                => 'bg-gray-100 text-gray-700',
                };
              @endphp
              <span class="px-2 py-1 rounded {{ $badgeColor }}">
                {{ $s->client_response_label }}
              </span>

              @if($s->has_pending_reschedule)
                <div class="text-xs text-gray-500 mt-1">
                  Usulan: <strong>{{ $s->client_requested_date?->format('d M Y H:i') }}</strong>
                  @if($s->client_response_note)
                    <div class="mt-0.5 italic">“{{ $s->client_response_note }}”</div>
                  @endif
                </div>
              @endif
            </td>

            <td class="p-3"><span class="px-2 py-1 rounded bg-gray-100">{{ $s->status }}</span></td>
            <td class="p-3">{{ \Illuminate\Support\Str::limit($s->notes, 40) }}</td>

            <td class="p-3">
              <div class="flex flex-wrap gap-2">
                <button class="px-3 py-1 rounded-lg border" wire:click="edit({{ $s->id }})">Edit</button>
                <button class="px-3 py-1 rounded-lg border text-red-600"
                        onclick="return confirm('Hapus jadwal?')"
                        wire:click="delete({{ $s->id }})">Hapus</button>

                @if($s->has_pending_reschedule)
                  <button class="px-3 py-1 rounded-lg border bg-emerald-600 text-white"
                          wire:click="approveReschedule({{ $s->id }})">Setujui Usulan</button>
                  <button class="px-3 py-1 rounded-lg border bg-red-600 text-white"
                          wire:click="rejectReschedule({{ $s->id }})">Tolak</button>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr><td class="p-3" colspan="8">Belum ada data.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-3">{{ $schedules->links() }}</div>

  {{-- Drawer Form --}}
  @if(!is_null($editingId))
    <div class="fixed inset-0 bg-black/30 z-40" wire:click="$set('editingId', null)"></div>
    <div class="fixed right-0 top-0 bottom-0 w-full max-w-lg bg-white z-50 border-l overflow-y-auto">
      <div class="p-6 border-b flex items-center justify-between">
        <div class="font-semibold">{{ $editingId ? 'Edit Jadwal' : 'Jadwal Baru' }}</div>
        <button class="text-gray-500" wire:click="$set('editingId', null)">✕</button>
      </div>

      <form wire:submit.prevent="save" class="p-6 grid gap-4">
        <div>
          <label class="text-sm">Klien</label>
          <select class="mt-1 w-full rounded-xl border-gray-300" wire:model.live="client_id" required>
            <option value="">— pilih klien —</option>
            @foreach($clients as $c)
              <option value="{{ $c->id }}">{{ $c->company_name }}</option>
            @endforeach
          </select>
          @error('client_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="text-sm">Lokasi</label>
          <select class="mt-1 w-full rounded-xl border-gray-300" wire:model.live="location_id" required>
            <option value="">— pilih lokasi —</option>
            @foreach($locations as $l)
              <option value="{{ $l->id }}">{{ $l->name }}</option>
            @endforeach
          </select>
          @error('location_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- PILIH UNIT BERDASARKAN LOKASI --}}
        @if($location_id)
          <div>
            <label class="text-sm">Unit AC (pilih satu/lebih)</label>
            <div class="mt-2 max-h-52 overflow-y-auto border rounded-xl p-3 space-y-2">
              @forelse($unitsForLocation as $u)
                <label class="flex items-center gap-3 text-sm">
                  <input type="checkbox" class="rounded border-gray-300"
                         value="{{ $u->id }}" wire:model="unit_ids">
                  <span>
                    {{ $u->brand }} {{ $u->model }}
                    <span class="text-gray-500">— SN: {{ $u->serial_number ?? $u->id }}</span>
                  </span>
                </label>
              @empty
                <div class="text-sm text-gray-500">Belum ada unit pada lokasi ini.</div>
              @endforelse
            </div>
            @error('unit_ids')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
          </div>
        @endif

        <div>
          <label class="text-sm">Waktu Kunjungan</label>
          <input type="datetime-local" class="mt-1 w-full rounded-xl border-gray-300"
                 wire:model.defer="scheduled_at" required>
          @error('scheduled_at')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

<div class="space-y-1">
  <label class="text-sm text-slate-600">Teknisi (opsional)</label>
  <select wire:model="technician_id" class="input w-full">
    <option value="">— pilih teknisi —</option>
    @foreach($techs as $t)
      <option value="{{ $t->id }}">{{ $t->name }}</option>
    @endforeach
  </select>
  @error('technician_id') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
</div>


        <div>
          <label class="text-sm">Status</label>
          <select class="mt-1 w-full rounded-xl border-gray-300" wire:model.defer="status">
            <option value="menunggu">menunggu</option>
            <option value="dalam_proses">dalam_proses</option>
            <option value="selesai_servis">selesai_servis</option>
            <option value="selesai">selesai</option>
          </select>
        </div>

        <div>
          <label class="text-sm">Catatan</label>
          <textarea rows="3" class="mt-1 w-full rounded-xl border-gray-300" wire:model.defer="notes"></textarea>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" class="px-4 py-2 rounded-xl border" wire:click="$set('editingId', null)">Batal</button>
          <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white">Simpan</button>
        </div>
      </form>
    </div>
  @endif
</div>
