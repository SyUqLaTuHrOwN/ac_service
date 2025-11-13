{{-- resources/views/livewire/admin/tech-leaves/index.blade.php --}}
<div class="space-y-4">
  <div class="flex items-center gap-3">
    <input class="input w-64" placeholder="Cari nama/email…" wire:model.live.debounce.400ms="search">
    <select class="input" wire:model.live="status">
      <option value="pending">pending</option>
      <option value="approved">approved</option>
      <option value="rejected">rejected</option>
      <option value="all">semua</option>
    </select>
  </div>

  <div class="card overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50">
        <tr>
          <th class="p-3 text-left">Teknisi</th>
          <th class="p-3">Rentang</th>
          <th class="p-3">Status</th>
          <th class="p-3">Bukti</th>
          <th class="p-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $lv)
          <tr class="border-t">
            <td class="p-3">{{ $lv->user->name }} <div class="text-xs text-slate-500">{{ $lv->user->email }}</div></td>
            <td class="p-3 text-center">{{ $lv->start_date->format('d M Y') }} – {{ $lv->end_date->format('d M Y') }}</td>
            <td class="p-3 text-center">
              <span class="badge {{ $lv->status==='approved'?'bg-emerald-100 text-emerald-700':($lv->status==='rejected'?'bg-rose-100 text-rose-700':'bg-amber-100 text-amber-700') }}">{{ $lv->status }}</span>
            </td>
            <td class="p-3 text-center">
              @if($lv->proof_path)
                <a class="link" href="{{ Storage::url($lv->proof_path) }}" target="_blank">Lihat</a>
              @else - @endif
            </td>
            <td class="p-3 text-center space-x-2">
              <button class="btn-outline" wire:click="approve({{ $lv->id }})" @disabled($lv->status!=='pending')>Setujui</button>
              <button class="btn-outline" wire:click="reject({{ $lv->id }})"  @disabled($lv->status!=='pending')>Tolak</button>
            </td>
          </tr>
        @empty
          <tr><td class="p-6 text-center text-slate-500" colspan="5">Tidak ada data.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div>{{ $rows->links() }}</div>
</div>
