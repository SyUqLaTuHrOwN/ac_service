<div class="rounded-xl border overflow-hidden bg-white">
  <table class="min-w-full text-sm">
    <thead class="bg-slate-50 text-slate-600">
      <tr>
        <th class="p-3 text-left">Tanggal</th>
        <th class="p-3 text-left">Klien/Lokasi</th>
        <th class="p-3 text-left">Rating</th>
        <th class="p-3 text-left">Komentar</th>
        <th class="p-3 text-left">Publik?</th>
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
          <td class="p-3">{!! $f->is_public ? '<span class="badge bg-emerald-50 text-emerald-700">ya</span>' : '<span class="badge">tidak</span>' !!}</td>
        </tr>
      @empty
        <tr><td class="p-4 text-center text-slate-500" colspan="5">Belum ada ulasan.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
