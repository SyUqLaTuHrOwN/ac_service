<div class="max-w-4xl mx-auto">
  <div class="rounded-xl border bg-white p-6">
    <div class="text-lg font-semibold mb-4">Biodata</div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <div class="text-sm text-slate-500">Nama</div>
        <div class="mt-1 font-medium">{{ $user->name }}</div>
      </div>

      <div>
        <div class="text-sm text-slate-500">Email</div>
        <div class="mt-1 font-medium">{{ $user->email }}</div>
      </div>

      <div>
        <div class="text-sm text-slate-500">Telepon</div>
        <div class="mt-1 font-medium">
          {{ $user->technicianProfile?->phone ?? '—' }}
        </div>
      </div>

      <div>
        <div class="text-sm text-slate-500">Status</div>
        <div class="mt-1">
          @php $st = $user->technicianProfile?->status ?? 'aktif'; @endphp
          <span class="px-2 py-1 rounded text-sm
            {{ $st==='aktif' ? 'bg-emerald-50 text-emerald-700' : ($st==='cuti' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
            {{ $st }}
          </span>
        </div>
      </div>

      <div class="md:col-span-2">
        <div class="text-sm text-slate-500">Alamat</div>
        <div class="mt-1 font-medium">
          {{ $user->technicianProfile?->address ?? '—' }}
        </div>
      </div>
    </div>

    <hr class="my-6">

    <p class="text-sm text-slate-500">
      Perubahan biodata & reset password hanya dapat dilakukan oleh <span class="font-semibold">Admin</span>.
      Silakan hubungi admin jika ada pembaruan data.
    </p>
  </div>
</div>
