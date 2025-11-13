<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $title ?? 'Dashboard' }} • AC Maintenance</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  @livewireStyles

  {{-- Inter font (opsional, aman tanpa plugin) --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root{
      --brand:#6d28d9; /* indigo-700 */
      --brand2:#22c55e;/* emerald-500 */
      --brand3:#0ea5e9;/* sky-500 */
    }
    html,body{font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";}
    /* Smooth card shadow */
    .neo { box-shadow: 0 8px 24px rgba(2,6,23,.06), 0 2px 6px rgba(2,6,23,.04); }
    /* Glassy background */
    .glass { background: rgba(255,255,255,.72); backdrop-filter: saturate(160%) blur(8px); }
    /* Nice scrollbar */
    ::-webkit-scrollbar{height:10px;width:10px}
    ::-webkit-scrollbar-thumb{background:#e5e7eb;border-radius:9999px}
    ::-webkit-scrollbar-thumb:hover{background:#d1d5db}
  </style>
</head>
<body class="h-full bg-slate-50 text-slate-800">
<div x-data="{ open:false }" class="min-h-screen flex">

  {{-- SIDEBAR (desktop) --}}
  <aside class="hidden lg:flex lg:flex-col lg:w-72 border-r bg-white/90 glass">
  <div class="px-4 py-4 flex items-center gap-3">
    <x-logo.coolcare class="h-9 w-9 text-indigo-600" />
    <div class="leading-tight">
      <div class="text-base font-semibold">CoolCare AC</div>
      <div class="text-[11px] text-slate-500">Maintenance Suite</div>
    </div>
  </div>

    {{-- NAV --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-sm">
      @auth
        @php $role = auth()->user()->role ?? 'client'; @endphp

        @if ($role==='admin')
          <x-nav.link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" icon="gauge">Dashboard</x-nav.link>

          <x-nav.section title="Master Data" />
          <x-nav.link :href="route('admin.clients')"     :active="request()->routeIs('admin.clients')"     icon="building">Klien</x-nav.link>
          <x-nav.link :href="route('admin.locations')"   :active="request()->routeIs('admin.locations')"   icon="map">Lokasi</x-nav.link>
          <x-nav.link :href="route('admin.units')"       :active="request()->routeIs('admin.units')"       icon="cpu">Unit AC</x-nav.link>

          <x-nav.section title="Operasional" />
          <x-nav.link :href="route('admin.schedules')"   :active="request()->routeIs('admin.schedules')"   icon="calendar">Jadwal Maintenance</x-nav.link>
          <x-nav.link :href="route('admin.reports')"     :active="request()->routeIs('admin.reports')"     icon="doc">Laporan</x-nav.link>
          <x-nav.link :href="route('admin.requests')"    :active="request()->routeIs('admin.requests')"    icon="inbox">Permintaan</x-nav.link>
          <x-nav.link :href="route('admin.technicians')" :active="request()->routeIs('admin.technicians')" icon="wrench">Teknisi</x-nav.link>
          <x-nav.link :href="route('admin.tech-leaves')" :active="request()->routeIs('admin.tech-leaves')" icon="inbox">Permintaan Cuti Teknisi</x-nav.link>

          <x-nav.section title="Sistem" />
          <x-nav.link :href="route('admin.users')"       :active="request()->routeIs('admin.users')"       icon="users">Pengguna</x-nav.link>
          <x-nav.link :href="route('admin.settings')"    :active="request()->routeIs('admin.settings')"    icon="gear">Pengaturan</x-nav.link>
          <x-nav.link :href="route('admin.register')"    :active="request()->routeIs('admin.register')"    icon="plus">Register (buat akun)</x-nav.link>

        @elseif ($role==='teknisi')
          <x-nav.link :href="route('teknisi.dashboard')" :active="request()->routeIs('teknisi.dashboard')" icon="gauge">Dashboard</x-nav.link>
          <x-nav.link :href="route('teknisi.tasks')"     :active="request()->routeIs('teknisi.tasks')"     icon="calendar">Tugas Saya</x-nav.link>
          <x-nav.link :href="route('teknisi.reports')"   :active="request()->routeIs('teknisi.reports')"   icon="doc">Laporan</x-nav.link>
          <x-nav.link :href="route('teknisi.history')"   :active="request()->routeIs('teknisi.history')"   icon="clock">Riwayat</x-nav.link>
          <x-nav.link :href="route('teknisi.leave')"     :active="request()->routeIs('teknisi.leave')" icon="inbox">Ajukan Cuti</x-nav.link>
          <x-nav.link :href="route('teknisi.profile')"   :active="request()->routeIs('teknisi.profile')"   icon="user">Profil</x-nav.link>

        @else
          <x-nav.link :href="route('client.dashboard')"  :active="request()->routeIs('client.dashboard')"  icon="gauge">Dashboard</x-nav.link>
          <x-nav.link :href="route('client.units')"      :active="request()->routeIs('client.units')"      icon="cpu">Unit AC Saya</x-nav.link>
          <x-nav.link :href="route('client.schedules')"  :active="request()->routeIs('client.schedules')"  icon="calendar">Jadwal Maintenance</x-nav.link>
          <x-nav.link :href="route('client.reports')"    :active="request()->routeIs('client.reports')"    icon="doc">Laporan</x-nav.link>
          <x-nav.link :href="route('client.feedback')"   :active="request()->routeIs('client.feedback')"   icon="chat">Feedback</x-nav.link>
          <x-nav.link :href="route('client.requests')"   :active="request()->routeIs('client.requests')"   icon="inbox">Permintaan Maintenance</x-nav.link>
          <x-nav.link :href="route('client.complaints')" :active="request()->routeIs('client.complaints')" icon="alert">Komplain</x-nav.link>
        @endif
      @endauth

      @guest
        <div class="px-3 py-2 text-slate-500 text-sm">Silakan <a class="underline" href="{{ route('login') }}">login</a>.</div>
      @endguest
    </nav>

    {{-- Footer user --}}
    @auth
    <div class="mt-auto p-4 border-t">
      <div class="flex items-center gap-3">
        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-[var(--brand2)] to-[var(--brand3)]"></div>
        <div class="min-w-0">
          <div class="text-sm font-semibold truncate">{{ auth()->user()->name }}</div>
          <div class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</div>
        </div>
      </div>
      <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button class="w-full text-sm rounded-xl border px-3 py-2 hover:bg-slate-50">Logout</button>
      </form>
    </div>
    @endauth
  </aside>

  {{-- MOBILE TOPBAR --}}
  <div class="lg:hidden fixed inset-x-0 top-0 z-40 glass border-b">
    <div class="h-14 max-w-7xl mx-auto px-4 flex items-center justify-between">
      <button @click="open=true" class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 bg-white hover:bg-slate-50">
        {{-- icon menu --}}
        <svg class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/></svg>
        <span class="text-sm font-medium">Menu</span>
      </button>
      <div class="text-sm font-semibold">{{ $title ?? 'Dashboard' }}</div>
      <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-[var(--brand)] to-[var(--brand3)]"></div>
    </div>
  </div>

  {{-- MOBILE DRAWER SIDEBAR --}}
  <div x-show="open" x-transition.opacity class="lg:hidden fixed inset-0 z-50">
    <div @click="open=false" class="absolute inset-0 bg-slate-900/40"></div>
    <aside class="absolute left-0 top-0 h-full w-[80%] max-w-[320px] bg-white glass border-r neo"
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="-translate-x-full opacity-0"
           x-transition:enter-end="translate-x-0 opacity-100"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="translate-x-0 opacity-100"
           x-transition:leave-end="-translate-x-full opacity-0">
      <div class="px-5 pt-5 pb-4 flex items-center gap-3 border-b">
        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-[var(--brand)] to-[var(--brand3)]"></div>
        <div class="text-lg font-extrabold">CoolCare AC</div>
      </div>
      <nav class="p-3 space-y-1 text-sm max-h-[calc(100vh-120px)] overflow-y-auto">
        {{-- copy nav yang sama seperti desktop --}}
        @auth
          @php $role = auth()->user()->role ?? 'client'; @endphp

          @if ($role==='admin')
            <x-nav.link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" icon="gauge">Dashboard</x-nav.link>
            <x-nav.section title="Master Data" />
            <x-nav.link :href="route('admin.clients')"     :active="request()->routeIs('admin.clients')"     icon="building">Klien</x-nav.link>
            <x-nav.link :href="route('admin.locations')"   :active="request()->routeIs('admin.locations')"   icon="map">Lokasi</x-nav.link>
            <x-nav.link :href="route('admin.units')"       :active="request()->routeIs('admin.units')"       icon="cpu">Unit AC</x-nav.link>
            <x-nav.section title="Operasional" />
            <x-nav.link :href="route('admin.schedules')"   :active="request()->routeIs('admin.schedules')"   icon="calendar">Jadwal Maintenance</x-nav.link>
            <x-nav.link :href="route('admin.technicians')" :active="request()->routeIs('admin.technicians')" icon="wrench">Teknisi</x-nav.link>
            <x-nav.link :href="route('admin.requests')"    :active="request()->routeIs('admin.requests')"    icon="inbox">Permintaan</x-nav.link>
            <x-nav.link :href="route('admin.reports')"     :active="request()->routeIs('admin.reports')"     icon="doc">Laporan</x-nav.link>
            <x-nav.section title="Sistem" />
            <x-nav.link :href="route('admin.users')"       :active="request()->routeIs('admin.users')"       icon="users">Pengguna</x-nav.link>
            <x-nav.link :href="route('admin.settings')"    :active="request()->routeIs('admin.settings')"    icon="gear">Pengaturan</x-nav.link>
            <x-nav.link :href="route('admin.register')"    :active="request()->routeIs('admin.register')"    icon="plus">Register (buat akun)</x-nav.link>

          @elseif ($role==='teknisi')
            <x-nav.link :href="route('teknisi.dashboard')" :active="request()->routeIs('teknisi.dashboard')" icon="gauge">Dashboard</x-nav.link>
            <x-nav.link :href="route('teknisi.tasks')"     :active="request()->routeIs('teknisi.tasks')"     icon="calendar">Tugas Saya</x-nav.link>
            <x-nav.link :href="route('teknisi.reports')"   :active="request()->routeIs('teknisi.reports')"   icon="doc">Laporan</x-nav.link>
            <x-nav.link :href="route('teknisi.history')"   :active="request()->routeIs('teknisi.history')"   icon="clock">Riwayat</x-nav.link>
            <x-nav.link :href="route('teknisi.profile')"   :active="request()->routeIs('teknisi.profile')"   icon="user">Profil</x-nav.link>

          @else
            <x-nav.link :href="route('client.dashboard')"  :active="request()->routeIs('client.dashboard')"  icon="gauge">Dashboard</x-nav.link>
            <x-nav.link :href="route('client.units')"      :active="request()->routeIs('client.units')"      icon="cpu">Unit AC Saya</x-nav.link>
            <x-nav.link :href="route('client.schedules')"  :active="request()->routeIs('client.schedules')"  icon="calendar">Jadwal Maintenance</x-nav.link>
            <x-nav.link :href="route('client.reports')"    :active="request()->routeIs('client.reports')"    icon="doc">Laporan</x-nav.link>
            <x-nav.link :href="route('client.feedback')"   :active="request()->routeIs('client.feedback')"   icon="chat">Feedback</x-nav.link>
            <x-nav.link :href="route('client.requests')"   :active="request()->routeIs('client.requests')"   icon="inbox">Permintaan Maintenance</x-nav.link>
            <x-nav.link :href="route('client.complaints')" :active="request()->routeIs('client.complaints')" icon="alert">Komplain</x-nav.link>
          @endif
        @endauth
      </nav>

      <div class="mt-auto p-4 border-t">
        <button @click="open=false" class="w-full text-sm rounded-xl border px-3 py-2 hover:bg-slate-50">Tutup</button>
      </div>
    </aside>
  </div>

  {{-- MAIN --}}
  <div class="flex-1 min-w-0 w-full lg:pl-0">

    {{-- HEADER BAR (desktop) --}}
    <header class="hidden lg:block bg-gradient-to-r from-[var(--brand)] via-[var(--brand3)] to-[var(--brand2)]">
      <div class="max-w-7xl mx-auto px-6">
        <div class="h-16 flex items-center justify-between">
          <div class="text-white font-semibold">
            {{ $header ?? 'Panel' }}
          </div>
          <div class="flex items-center gap-3 text-white/90">
            @auth
              <span class="text-sm truncate max-w-[240px]">{{ auth()->user()->name }}</span>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm rounded-xl border border-white/30 px-4 py-1.5 hover:bg-white/10">Logout</button>
              </form>
            @endauth
          </div>
        </div>
      </div>
    </header>

    {{-- CONTENT --}}
    <main class="max-w-7xl mx-auto px-4 lg:px-6 pt-16 lg:pt-8 pb-10">
      {{ $slot }}
    </main>
  </div>
</div>

{{-- TOAST POPUP (Alpine, sudah dipakai di project) --}}
<div
  x-data="{ show:false, msg:'', type:'ok',
            pop(m,t='ok'){ this.msg=m; this.type=t; this.show=true; setTimeout(()=>this.show=false,2600); } }"
  x-init="
    @if(session('ok'))  pop(@js(session('ok')), 'ok');  @endif
    @if(session('err')) pop(@js(session('err')), 'err'); @endif
    window.addEventListener('toast', e => pop(e.detail.message, e.detail.type || 'ok'));
  "
  class="fixed inset-0 pointer-events-none z-[100]"
>
  <div
    x-show="show" x-transition
    class="pointer-events-auto fixed bottom-5 right-5 min-w-[260px] max-w-sm rounded-2xl neo px-4 py-3 bg-white border"
    :class="type==='ok' ? 'border-emerald-200' : 'border-rose-200'"
  >
    <div class="flex items-start gap-3">
      <div class="mt-1">
        <template x-if="type==='ok'">
          <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </template>
        <template x-if="type!=='ok'">
          <svg class="h-5 w-5 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </template>
      </div>
      <div class="text-sm font-medium text-slate-800" x-text="msg"></div>
    </div>
  </div>
</div>

@livewireScripts
</body>
</html>
