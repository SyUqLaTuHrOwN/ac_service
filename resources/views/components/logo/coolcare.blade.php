@props(['class' => 'h-9 w-9 text-indigo-600'])

<svg {{ $attributes->merge(['class' => $class]) }}
     viewBox="0 0 64 64" fill="none" role="img" aria-label="CoolCare">
  <!-- outer gauge -->
  <circle cx="32" cy="32" r="28" class="stroke-current" stroke-width="2" opacity="0.25"/>
  <path d="M10 32a22 22 0 1 1 44 0"
        class="stroke-current" stroke-width="4" stroke-linecap="round" opacity="0.45"/>
  <!-- ticks -->
  <g class="stroke-current" stroke-linecap="round" opacity="0.45">
    <path d="M32 10v4"/>
    <path d="M18 15l2.5 3.2"/>
    <path d="M46 15l-2.5 3.2"/>
  </g>
  <!-- thermometer -->
  <g transform="translate(40,34)">
    <rect x="-6" y="-18" width="12" height="26" rx="6"
          class="stroke-current" stroke-width="2" fill="none"/>
    <circle cx="0" cy="10" r="8" class="stroke-current" stroke-width="2" fill="currentColor" opacity="0.15"/>
    <clipPath id="cc-liquid"><rect x="-6" y="-18" width="12" height="38" rx="6"/></clipPath>
    <g clip-path="url(#cc-liquid)">
      <rect x="-6" y="2" width="12" height="18" rx="6" fill="currentColor"/>
      <circle cx="0" cy="10" r="7" fill="currentColor"/>
    </g>
    <circle cx="0" cy="10" r="3" fill="currentColor"/>
  </g>
  <!-- needle -->
  <g transform="rotate(-25 32 32)">
    <circle cx="32" cy="32" r="2.5" fill="currentColor"/>
    <path d="M32 32 L53 24" class="stroke-current" stroke-width="3" stroke-linecap="round"/>
  </g>
</svg>
