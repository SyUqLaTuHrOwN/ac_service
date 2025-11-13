{{-- resources/views/livewire/teknisi/leave/index.blade.php --}}
<div class="grid lg:grid-cols-2 gap-6">
  <div class="card p-5">
    <div class="text-lg font-semibold mb-3">Form Cuti</div>
    <div class="space-y-3">
      <div>
        <label class="text-sm">Mulai</label>
        <input type="date" class="input w-full" wire:model.defer="start_date">
        @error('start_date') <p class="text-rose-600 text-xs">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="text-sm">Selesai</label>
        <input type="date" class="input w-full" wire:model.defer="end_date">
        @error('end_date') <p class="text-rose-600 text-xs">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="text-sm">Alasan (opsional)</label>
        <input type="text" class="input w-full" placeholder="Sakit/urusan keluarga…" wire:model.defer="reason">
      </div>
      <div>
        <label class="text-sm">Bukti (foto/pdf, maks 2MB)</label>
        <input type="file" class="w-full" wire:model="proof" accept=".jpg,.jpeg,.png,.webp,.pdf">
        <div wire:loading wire:target="proof" class="text-xs text-slate-500">Mengunggah…</div>
      </div>
      <div class="pt-2">
        <button class="btn" wire:click="submit">Kirim Pengajuan</button>
      </div>
    </div>
  </div>

  <div class="card p-5 overflow-x-auto">
    <div class="flex items-center justify-between mb-3">
      <div class="text-lg font-semibold">Riwayat Pengajuan</div>
      <select class="input" wire:model.live="filter_status">
        <option value="all">Semua</option>
        <option value="pending">pending</option>
        <option value="approved">approved</option>
        <option value="rejected">rejected</option>
      </select>
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50">
        <tr>
          <th class="p-2 text-left">Rentang</th>
          <th class="p-2">Status</th>
          <th class="p-2">Bukti</th>
        </tr>
      </thead>
      <tbody>
        @forelse($leaves as $lv)
          <tr class="border-t">
            <td class="p-2">{{ $lv->start_date->format('d M Y') }} – {{ $lv->end_date->format('d M Y') }}</td>
            <td class="p-2">
              <span class="badge {{ $lv->status==='approved'?'bg-emerald-100 text-emerald-700':($lv->status==='rejected'?'bg-rose-100 text-rose-700':'bg-amber-100 text-amber-700') }}">
                {{ $lv->status }}
              </span>
            </td>
            <td class="p-2">
              @if($lv->proof_path)
                <a class="link" href="{{ Storage::url($lv->proof_path) }}" target="_blank">Lihat</a>
              @else
                -
              @endif
            </td>
          </tr>
        @empty
          <tr><td class="p-4 text-center text-slate-500" colspan="3">Belum ada pengajuan.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="mt-3">{{ $leaves->links() }}</div>
  </div>
</div>
