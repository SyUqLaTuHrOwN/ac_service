<div class="space-y-4">
  <div class="flex items-center gap-2">
    <input type="text" wire:model.live.debounce.400ms="q" placeholder="Cari komentar…" class="input w-64">
    <select wire:model.live="rating" class="input">
      <option value="">Semua rating</option>
      @for($i=5;$i>=1;$i--) <option value="{{$i}}">{{$i}}★</option> @endfor
    </select>
    <select wire:model.live="vis" class="input">
      <option value="">Semua visibilitas</option>
      <option value="published">Dipublikasikan</option>
      <option value="hidden">Disembunyikan</option>
    </select>
  </div>

  <div class="rounded-xl border overflow-hidden bg-white">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="p-3 text-left">Tanggal</th>
          <th class="p-3 text-left">Klien/Lokasi</th>
          <th class="p-3 text-left">Rating</th>
          <th class="p-3 text-left">Komentar</th>
          <th class="p-3 text-left">Status</th>
          <th class="p-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $f)
          @php $sch = $f->report?->schedule; @endphp
          <tr class="border-t">
            <td class="p-3">{{ $f->created_at?->format('d M Y') }}</td>
            <td class="p-3">
              <div class="font-medium">{{ $sch?->client?->company_name ?? '-' }}</div>
              <div class="text-xs text-slate-500">{{ $sch?->location?->name ?? '-' }}</div>
            </td>
            <td class="p-3">{{ $f->rating }}★</td>
            <td class="p-3">{{ $f->comment }}</td>
            <td class="p-3">
              @if($f->is_public)
                <span class="badge bg-emerald-50 text-emerald-700">published</span>
              @else
                <span class="badge bg-slate-100">hidden</span>
              @endif
            </td>
            <td class="p-3 text-center space-x-2">
              <button class="btn-outline" wire:click="togglePublish({{ $f->id }})">
                {{ $f->is_public ? 'Sembunyikan' : 'Publikasikan' }}
              </button>
              <button class="btn-outline text-rose-600" wire:click="delete({{ $f->id }})">Hapus</button>
            </td>
          </tr>
        @empty
          <tr><td class="p-4 text-center text-slate-500" colspan="6">Belum ada ulasan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div>{{ $items->links() }}</div>
</div>
