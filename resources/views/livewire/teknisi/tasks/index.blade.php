<div
  x-data="{
    show:false, tab:'start',
    open(){ this.show=true },
    close(){ this.show=false }
  }"
  x-on:open-upload.window="open()"
  x-on:close-upload.window="close()"
>
  {{-- Filter rentang --}}
  <div class="flex items-center gap-2 mb-4">
    <input type="date" class="input" wire:model.live="from_date">
    <span class="text-slate-400">s.d.</span>
    <input type="date" class="input" wire:model.live="to_date">
    <button class="btn" wire:click="monthThis">Bulan ini</button>
  </div>

  {{-- Tabel --}}
  <div class="card">
    <table class="w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="p-3">Tanggal</th>
          <th class="p-3">Klien/Lokasi</th>
          <th class="p-3">Unit</th>
          <th class="p-3">Status</th>
          <th class="p-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
      @forelse($schedules as $s)
        <tr class="border-t">
          <td class="p-3 whitespace-nowrap">{{ $s->scheduled_at?->format('d M Y H:i') }}</td>
          <td class="p-3">{{ $s->client?->name }} — {{ $s->location?->name }}</td>
          <td class="p-3">{{ $s->unit_serial ?? '—' }}</td>
          <td class="p-3"><span class="badge">{{ $s->status }}</span></td>
          <td class="p-3 text-center space-x-2">
            {{-- Mulai hanya pada hari-H --}}
            <button
               wire:click="start({{ $s->id }})"
               @class(['btn-outline',
                 '!opacity-40 !cursor-not-allowed' => !app('App\\Livewire\\Teknisi\\Tasks\\Index')->canStart($s)
               ])
               @disabled(!app('App\\Livewire\\Teknisi\\Tasks\\Index')->canStart($s))
            >Mulai</button>

            <button class="btn-outline" @click="$wire.openUpload({{ $s->id }})">Foto Mulai / Selesai / Nota</button>

            <button class="btn" wire:click="finish({{ $s->id }})">Selesaikan</button>
          </td>
        </tr>
      @empty
        <tr><td class="p-6 text-center text-slate-400" colspan="5">Tidak ada tugas dalam rentang ini.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  {{-- MODAL Upload --}}
  <div class="fixed inset-0 z-40" x-show="show" x-transition>
    <div class="absolute inset-0 bg-black/30" @click="close()"></div>
    <div class="absolute top-16 left-1/2 -translate-x-1/2 w-[560px] bg-white rounded-2xl shadow-lg p-5 space-y-4">
      <div class="flex gap-2">
        <button class="tab" :class="tab==='start' && 'active'" @click="tab='start'">Foto Mulai</button>
        <button class="tab" :class="tab==='end' && 'active'" @click="tab='end'">Foto Selesai</button>
        <button class="tab" :class="tab==='receipt' && 'active'" @click="tab='receipt'">Nota/Struk</button>
      </div>

      <div x-show="tab==='start'">
        <input type="file" class="input" wire:model="start_photo" accept="image/*">
        <div class="mt-3 flex justify-end"><button class="btn" wire:click="saveStartPhoto">Simpan</button></div>
      </div>

      <div x-show="tab==='end'">
        <input type="file" class="input" wire:model="end_photo" accept="image/*">
        <div class="mt-3 flex justify-end"><button class="btn" wire:click="saveEndPhoto">Simpan</button></div>
      </div>

      <div x-show="tab==='receipt'">
        <input type="file" class="input" wire:model="receipt_file" accept="image/*">
        <div class="mt-3 flex justify-end"><button class="btn" wire:click="saveReceipt">Simpan</button></div>
      </div>

      <div class="flex justify-end">
        <button class="btn-outline" @click="close()">Tutup</button>
      </div>
    </div>
  </div>
</div>
