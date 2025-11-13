<div>
  <div class="flex gap-2 mb-4">
    <input type="date" class="input" wire:model.live="from_date">
    <span class="text-slate-400">s.d.</span>
    <input type="date" class="input" wire:model.live="to_date">
    <select class="input" wire:model.live="statusFilter">
      <option value="">Semua status</option>
      <option value="submitted">Menunggu verifikasi</option>
      <option value="disetujui">Disetujui</option>
      <option value="revisi">Revisi</option>
    </select>
    <button class="btn" wire:click="monthThis">Bulan ini</button>
  </div>

  <div class="card">
    <table class="w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="p-3">Jadwal</th>
          <th class="p-3">Klien/Lokasi</th>
          <th class="p-3">Mulai</th>
          <th class="p-3">Selesai</th>
          <th class="p-3">Status</th>
          <th class="p-3">Berkas</th>
        </tr>
      </thead>
      <tbody>
      @forelse($reports as $r)
        <tr class="border-t">
          <td class="p-3">{{ $r->schedule?->scheduled_at?->format('d M Y H:i') }}</td>
          <td class="p-3">{{ $r->schedule?->client?->name }} — {{ $r->schedule?->location?->name }}</td>
          <td class="p-3">{{ $r->started_at?->format('d M Y H:i') ?? '—' }}</td>
          <td class="p-3">{{ $r->finished_at?->format('d M Y H:i') ?? '—' }}</td>
          <td class="p-3"><span class="badge">{{ $r->status }}</span></td>
          <td class="p-3 space-x-2">
            @if($r->start_photo_path)
              <a class="link" href="{{ Storage::url($r->start_photo_path) }}" target="_blank">Foto Mulai</a>
            @endif
            @if($r->end_photo_path)
              <a class="link" href="{{ Storage::url($r->end_photo_path) }}" target="_blank">Foto Selesai</a>
            @endif
            @if($r->receipt_path)
              <a class="link" href="{{ Storage::url($r->receipt_path) }}" target="_blank">Nota</a>
            @endif
          </td>
        </tr>
      @empty
        <tr><td class="p-6 text-center text-slate-400" colspan="6">Belum ada laporan.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
