@props([
  'href' => '#',
  'active' => false,
  'icon' => null,
])

@php
$base = 'group flex items-center gap-3 px-3 py-2.5 rounded-xl transition';
$on   = 'bg-gradient-to-r from-indigo-600/90 to-sky-500/90 text-white shadow-sm';
$off  = 'text-slate-700 hover:bg-slate-100';
@endphp

<a href="{{ $href }}" class="{{ $base }} {{ $active ? $on : $off }}">
  {{-- ikon minimalis (inline SVG) --}}
  @if($icon === 'gauge')
    <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path stroke-linecap="round" stroke-width="2" d="M12 3a9 9 0 100 18 9 9 0 000-18zM12 9v4l3 3"/>
    </svg>
  @elseif($icon === 'calendar')
    <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <rect x="3" y="5" width="18" height="16" rx="2" />
      <path stroke-linecap="round" stroke-width="2" d="M16 3v4M8 3v4M3 11h18"/>
    </svg>
  @elseif($icon === 'wrench')
    <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path stroke-linecap="round" stroke-width="2" d="M14.7 6.3a4 4 0 105.66 5.66l-7.07 7.07a2 2 0 01-2.83 0l-3.54-3.54a2 2 0 010-2.83l7.07-7.07z"/>
    </svg>
  @elseif($icon === 'users')
    <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path stroke-linecap="round" stroke-width="2" d="M17 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2"/>
      <circle cx="9" cy="7" r="4"/>
      <path stroke-linecap="round" stroke-width="2" d="M23 20v-2a4 4 0 00-3-3.87"/>
      <path stroke-linecap="round" stroke-width="2" d="M16 3.13A4 4 0 0120 7"/>
    </svg>
  @elseif($icon === 'map')
    <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path stroke-linecap="round" stroke-width="2" d="M9 20l-5-2V6l5 2 6-2 5 2v12l-5-2-6 2z"/>
    </svg>
  @elseif($icon === 'cpu')
    <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <rect x="5" y="5" width="14" height="14" rx="2"/>
      <path d="M9 1v3M15 1v3M9 20v3M15 20v3M1 9h3M1 15h3M20 9h3M20 15h3"/>
    </svg>
  @elseif($icon === 'doc')
    <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
      <path d="M14 3v5h5"/>
    </svg>
  @elseif($icon === 'gear')
    <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M10.325 4.317a1 1 0 011.35-.447l.866.433a1 1 0 00.894 0l.866-.433a1 1 0 011.35.447l.433.866a1 1 0 00.447.447l.866.433a1 1 0 010 1.788l-.866.433a1 1 0 00-.447.447l-.433.866a1 1 0 01-1.35.447l-.866-.433a1 1 0 00-.894 0l-.866.433a1 1 0 01-1.35-.447l-.433-.866a1 1 0 00-.447-.447l-.866-.433a1 1 0 010-1.788l.866-.433a1 1 0 00.447-.447l.433-.866z"/>
      <circle cx="12" cy="12" r="3"/>
    </svg>
  @elseif($icon === 'inbox')
    <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M22 12h-6l-2 3h-4l-2-3H2"/>
      <path d="M5 7l1.5-3h11L19 7l3 5v6a2 2 0 01-2 2H4a2 2 0 01-2-2v-6l3-5z"/>
    </svg>
  @elseif($icon === 'building')
    <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <rect x="3" y="3" width="18" height="18" rx="2"/>
      <path d="M7 7h2v2H7zM11 7h2v2h-2zM15 7h2v2h-2zM7 11h2v2H7zM11 11h2v2h-2zM15 11h2v2h-2zM7 15h10v4H7z"/>
    </svg>
  @elseif($icon === 'chat')
    <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M21 15a4 4 0 01-4 4H7l-4 4V7a4 4 0 014-4h10a4 4 0 014 4z"/>
    </svg>
  @elseif($icon === 'alert')
    <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13" />
      <line x1="12" y1="17" x2="12.01" y2="17" />
    </svg>
  @elseif($icon === 'user')
    <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <circle cx="12" cy="7" r="4"/>
      <path d="M6 21v-2a6 6 0 0112 0v2"/>
    </svg>
  @elseif($icon === 'plus')
    <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M12 5v14M5 12h14" />
    </svg>
  @endif

  <span class="truncate">{{ $slot }}</span>
</a>
