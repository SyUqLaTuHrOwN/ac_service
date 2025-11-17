<div
  x-data="{
      open:false,
      init(){
        window.addEventListener('open-upload', ()=>{ this.open=true })
        window.addEventListener('close-upload', ()=>{ this.open=false })
      }
  }"
  class="space-y-5"
>
  {{-- Filter rentang tanggal --}}
  <div class="flex items-center gap-3">
    <input type="date" wire:model.live="from_date" class="rounded-lg border px-3 py-2">
    <span class="text-slate-500">s.d.</span>
    <input type="date" wire:model.live="to_date" class="rounded-lg border px-3 py-2">
    <button class="px-3 py-2 rounded-lg bg-indigo-600 text-white" wire:click="monthThis">Bulan ini</button>
  </div>

  {{-- Tabel tugas --}}
  <div class="overflow-hidden rounded-xl border bg-white">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="p-3 text-left">Tanggal</th>
          <th class="p-3 text-left">Klien/Lokasi</th>
          <th class="p-3 text-left">Unit</th>
          <th class="p-3 text-left">Status</th>
          <th class="p-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($schedules as $s)
          @php
            $started = optional($s->report)->started_at;
            $canStartNow = $s->canStart(); // pakai helper di Model
            $canFinish   = filled($started);
            $statusMap = [
              'menunggu'       => 'bg-slate-100',
              'dalam_proses'   => 'bg-amber-100',
              'selesai_servis' => 'bg-emerald-100',
            ];
          @endphp
          <tr class="border-t">
            <td class="p-3 whitespace-nowrap">
              {{ $s->scheduled_at?->timezone('Asia/Jakarta')->format('d M Y H:i') }}
            </td>
            <td class="p-3">
              {{ $s->client?->company_name ?? '-' }} — {{ $s->location?->name ?? '-' }}
            </td>
            <td class="p-3">
              @if($s->units->isNotEmpty())
                {{ $s->units->map(fn($u)=> "{$u->brand} {$u->model} (".$u->serial_number.")")->implode(', ') }}
              @else
                —
              @endif
            </td>
            <td class="p-3">
              <span class="px-2 py-1 rounded {{ $statusMap[$s->status] ?? 'bg-slate-100' }}">
                {{ $s->status }}
              </span>
            </td>
            <td class="p-3 text-center space-x-2">
              {{-- Mulai: hanya muncul kalau belum pernah start --}}
              @if(!$started && $s->status !== 'selesai_servis' && $s->status !== 'selesai')
                <button
                  wire:click="start({{ $s->id }})"
                  @class([
                    'px-3 py-1.5 rounded-lg border',
                    'opacity-40 cursor-not-allowed' => !$canStartNow,
                  ])
                  @disabled(!$canStartNow)
                >Mulai</button>
              @endif

              <button
                class="px-3 py-1.5 rounded-lg border"
                wire:click="openUpload({{ $s->id }})"
              >Foto Mulai / Selesai / Nota</button>

              <button
                wire:click="finish({{ $s->id }})"
                @class([
                  'px-3 py-1.5 rounded-lg bg-indigo-600 text-white',
                  'opacity-40 cursor-not-allowed' => !$canFinish,
                ])
                @disabled(!$canFinish)
              >Selesaikan</button>
            </td>
          </tr>
        @empty
          <tr>
            <td class="p-6 text-center text-slate-500" colspan="5">Tidak ada tugas dalam rentang ini.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Modal Upload --}}
  <div
    x-show="open" x-transition
    class="fixed inset-0 z-50 flex items-center justify-center"
  >
    <div class="absolute inset-0 bg-black/30" @click="open=false"></div>
    <div class="relative w-full max-w-2xl rounded-xl bg-white shadow-lg p-6">
      <div class="text-lg font-semibold mb-4">Unggah Berkas</div>

      <div class="grid gap-4">
        {{-- Foto Mulai --}}
        <div class="border rounded-lg p-4">
          <div class="font-medium mb-2">Foto Mulai</div>
          <input type="file" wire:model="start_photo" accept="image/*" class="block w-full">
          @error('start_photo') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
          <div class="mt-2">
            <button class="px-3 py-2 rounded-lg bg-indigo-600 text-white" wire:click="saveStartPhoto">Simpan Foto Mulai</button>
          </div>
        </div>

        {{-- Foto Selesai --}}
        <div class="border rounded-lg p-4">
          <div class="font-medium mb-2">Foto Selesai</div>
          <input type="file" wire:model="end_photo" accept="image/*" class="block w-full">
          @error('end_photo') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
          <div class="mt-2">
            <button class="px-3 py-2 rounded-lg bg-indigo-600 text-white" wire:click="saveEndPhoto">Simpan Foto Selesai</button>
          </div>
        </div>

        {{-- Nota/Struk --}}
        <div class="border rounded-lg p-4">
          <div class="font-medium mb-2">Nota / Struk</div>
          <input type="file" wire:model="receipt_file" accept="image/*" class="block w-full">
          @error('receipt_file') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
          <div class="mt-2">
            <button class="px-3 py-2 rounded-lg bg-indigo-600 text-white" wire:click="saveReceipt">Simpan Nota</button>
          </div>
        </div>

        {{-- Catatan opsional (akan ikut saat Selesaikan) --}}
        <div class="border rounded-lg p-4">
          <div class="font-medium mb-2">Catatan (opsional)</div>
          <textarea wire:model.defer="note" rows="3" class="w-full rounded-lg border px-3 py-2" placeholder="Tambahkan catatan pekerjaan…"></textarea>
        </div>
      </div>

      <div class="mt-6 flex justify-end gap-2">
        <button class="px-3 py-2 rounded border" @click="open=false">Tutup</button>
      </div>
    </div>
  </div>
</div>
