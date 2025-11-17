<div class="space-y-5">
  {{-- Filter rentang & status --}}
  <div class="flex items-center gap-3">
    <input type="date" wire:model.live="from_date" class="rounded-lg border px-3 py-2">
    <span class="text-slate-500">s.d.</span>
    <input type="date" wire:model.live="to_date" class="rounded-lg border px-3 py-2">

    <select wire:model.live="status" class="rounded-lg border px-3 py-2">
      <option value="">Semua status</option>
      <option value="draft">draft</option>
      <option value="submitted">submitted</option>
      <option value="disetujui">disetujui</option>
      <option value="revisi">revisi</option>
    </select>

    <button class="px-3 py-2 rounded-lg bg-indigo-600 text-white" wire:click="monthThis">Bulan ini</button>
  </div>

  <div class="overflow-hidden rounded-xl border bg-white">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="p-3 text-left">Jadwal</th>
          <th class="p-3 text-left">Klien/Lokasi</th>
          <th class="p-3 text-left">Unit</th> {{-- ⬅️ baru --}}
          <th class="p-3 text-left">Mulai</th>
          <th class="p-3 text-left">Selesai</th>
          <th class="p-3 text-left">Status</th>
          <th class="p-3 text-center">Berkas</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($reports as $r)
          @php
            $s = $r->schedule;
            $when = $s?->scheduled_at?->timezone('Asia/Jakarta');
            $units = $s?->units ?? collect();
          @endphp
          <tr class="border-t">
            <td class="p-3 whitespace-nowrap">
              {{ $when?->format('d M Y H:i') ?? '—' }}
            </td>
            <td class="p-3">
              {{ $s?->client?->company_name ?? '-' }} — {{ $s?->location?->name ?? '-' }}
            </td>
            <td class="p-3">
              @if($units->isNotEmpty())
                {{ $units->map(fn($u)=> "{$u->brand} {$u->model} (SN {$u->serial_number})")->implode(', ') }}
              @else
                —
              @endif
            </td>
            <td class="p-3 whitespace-nowrap">
              {{ $r->started_at?->timezone('Asia/Jakarta')->format('d M Y H:i') ?? '—' }}
            </td>
            <td class="p-3 whitespace-nowrap">
              {{ $r->finished_at?->timezone('Asia/Jakarta')->format('d M Y H:i') ?? '—' }}
            </td>
            <td class="p-3">
              <span class="px-2 py-1 rounded bg-slate-100">{{ $r->status }}</span>
            </td>
            <td class="p-3 text-center space-x-2">
              @if($r->start_photo_path)
                <a href="{{ Storage::url($r->start_photo_path) }}" target="_blank" class="underline text-indigo-600">Foto Mulai</a>
              @endif
              @if($r->end_photo_path)
                <a href="{{ Storage::url($r->end_photo_path) }}" target="_blank" class="underline text-indigo-600">Foto Selesai</a>
              @endif
              @if($r->receipt_path)
                <a href="{{ Storage::url($r->receipt_path) }}" target="_blank" class="underline text-indigo-600">Nota</a>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td class="p-6 text-center text-slate-500" colspan="7">Tidak ada laporan pada rentang ini.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
