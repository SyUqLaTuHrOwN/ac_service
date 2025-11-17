<div>
  {{-- HERO --}}
  <section
    id="home"
    class="relative h-[650px] flex flex-col items-center justify-center text-center text-white overflow-hidden bg-fixed bg-center bg-cover"
    style="background-image: url('https://png.pngtree.com/thumb_back/fh260/back_our/20190621/ourmid/pngtree-summer-refrigerated-air-conditioning-banner-background-image_194711.jpg');">

    <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-transparent"></div>

    <div class="relative z-10 max-w-3xl px-4" data-fade>
      <h1 class="text-4xl md:text-5xl font-bold leading-tight drop-shadow-lg">
        Servis AC Cepat & Profesional untuk Kantor & Gedung
      </h1>
      <p class="mt-4 text-lg text-gray-200 drop-shadow-sm">
        Teknisi tersertifikasi, laporan digital, dan penjadwalan otomatis.
      </p>
      <div class="mt-6 flex flex-wrap justify-center gap-3">
        <a href="{{ route('login') }}"
           class="rounded-xl bg-indigo-600 text-white px-6 py-3 hover:bg-indigo-500 transition transform hover:scale-105 duration-300 hover-glow">
          Masuk untuk Kelola
        </a>
        <a href="#features" class="scroll-link rounded-xl border border-white text-white px-6 py-3 hover:bg-white hover:text-indigo-600 transition transform hover:scale-105 duration-300 hover-glow">
          Lihat Kelebihan
        </a>
      </div>
    </div>

    <div class="absolute bottom-8 z-10 animate-bounce">
      <a href="#features" class="scroll-link text-white text-3xl">⬇</a>
    </div>
  </section>

  {{-- KELEBIHAN --}}
  <section id="features" class="max-w-7xl mx-auto px-4 py-20">
    <h2 class="text-3xl font-semibold text-center" data-fade>Kelebihan Perusahaan</h2>
    <div class="mt-10 grid md:grid-cols-3 gap-6">
      <div data-fade class="p-6 rounded-2xl border bg-white hover:shadow-lg hover:-translate-y-1 transition">
        <h3 class="font-semibold text-lg">Penjadwalan Otomatis</h3>
        <p class="text-gray-600 mt-2">Reminder H-7, reschedule fleksibel, dan konfirmasi via email.</p>
      </div>
      <div data-fade class="p-6 rounded-2xl border bg-white hover:shadow-lg hover:-translate-y-1 transition">
        <h3 class="font-semibold text-lg">Teknisi Tersertifikasi</h3>
        <p class="text-gray-600 mt-2">Monitoring performa & histori pekerjaan lengkap.</p>
      </div>
      <div data-fade class="p-6 rounded-2xl border bg-white hover:shadow-lg hover:-translate-y-1 transition">
        <h3 class="font-semibold text-lg">Laporan Digital</h3>
        <p class="text-gray-600 mt-2">Foto, catatan servis, dan tanda tangan digital tersedia online.</p>
      </div>
    </div>
  </section>

  {{-- DOKUMENTASI --}}
  <section id="docs" class="max-w-7xl mx-auto px-4 py-20">
    <h2 class="text-3xl font-semibold text-center" data-fade>Dokumentasi Lapangan</h2>
    <div class="mt-10 grid sm:grid-cols-2 md:grid-cols-3 gap-4">
      @for ($i=0; $i<6; $i++)
        <img data-fade class="rounded-xl object-cover aspect-video hover:scale-[1.02] transition-all duration-300"
             src="https://images.unsplash.com/photo-1581092921461-eab62e97a780?q=80&w=800&auto=format&fit=crop" alt="doc">
      @endfor
    </div>
  </section>

  {{-- TESTIMONI (dinamis dari DB) --}}
  <section id="testi" class="max-w-7xl mx-auto px-4 py-20">
    <div class="flex items-end justify-between gap-4">
      <h2 class="text-3xl font-semibold" data-fade>Testimoni Klien</h2>

      {{-- ringkasan rating --}}
      <div class="flex items-center gap-3" data-fade>
        <div class="flex items-center">
          @php $rounded = round($avgRating); @endphp
          @for($i=1;$i<=5;$i++)
            <svg class="h-5 w-5 {{ $i <= $rounded ? 'text-amber-400' : 'text-slate-300' }}"
                 viewBox="0 0 24 24"
                 fill="{{ $i <= $rounded ? 'currentColor' : 'none' }}"
                 stroke="currentColor" stroke-width="1.5">
              <path d="M12 2l2.77 5.62 6.2.9-4.49 4.37 1.06 6.14L12 16.9 6.46 19.03l1.06-6.14L3.03 8.52l6.2-.9L12 2z"/>
            </svg>
          @endfor
        </div>
        <div class="text-sm text-slate-600">
          <span class="font-semibold">{{ number_format($avgRating,1) }}</span>
          dari {{ $countRating }} ulasan
        </div>
      </div>
    </div>

    <div class="mt-10 swiper mySwiper" data-fade>
      <div class="swiper-wrapper">
        @forelse ($testimonials as $fb)
          @php
            $company = $fb->report?->schedule?->client?->company_name ?? 'Pelanggan';
            $rating  = (int) ($fb->rating ?? 0);
            $comment = $fb->comment ?? '';
          @endphp
          <div class="swiper-slide p-6 rounded-2xl border bg-white hover:shadow-lg hover:-translate-y-1 transition">
            <div class="flex items-center mb-2">
              @for($i=1;$i<=5;$i++)
                <svg class="h-4 w-4 {{ $i <= $rating ? 'text-amber-400' : 'text-slate-300' }}"
                     viewBox="0 0 24 24"
                     fill="{{ $i <= $rating ? 'currentColor' : 'none' }}"
                     stroke="currentColor" stroke-width="1.3">
                  <path d="M12 2l2.77 5.62 6.2.9-4.49 4.37 1.06 6.14L12 16.9 6.46 19.03l1.06-6.14L3.03 8.52l6.2-.9L12 2z"/>
                </svg>
              @endfor
            </div>
            <p class="italic text-lg">“{{ $comment }}”</p>
            <div class="mt-3 text-sm text-gray-500">— {{ $company }}</div>
          </div>
        @empty
          <div class="swiper-slide p-6 rounded-2xl border bg-white">
            Belum ada testimoni dipublikasikan.
          </div>
        @endforelse
      </div>
      {{-- optional nav --}}
      <div class="mt-4 flex justify-center gap-3">
        <div class="swiper-pagination"></div>
      </div>
    </div>
  </section>

 {{-- KONTAK --}}
<section id="contact" class="max-w-5xl mx-auto px-4 py-20">
  <h2 class="text-3xl font-semibold text-center" data-fade>Kontak Admin</h2>

  @php
    $wa   = config('services.company.whatsapp');
    $mail = config('services.company.email');
    $ig   = config('services.company.instagram');

    $waText = rawurlencode('Halo CoolCare AC, saya ingin bertanya tentang layanan maintenance AC.');
    $waUrl  = "https://wa.me/{$wa}?text={$waText}";
    $mailUrl= "mailto:{$mail}?subject=" . rawurlencode('Pertanyaan layanan AC');
    $igUrl  = "https://instagram.com/{$ig}";
  @endphp

  <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    {{-- WhatsApp --}}
    <a href="{{ $waUrl }}" target="_blank" rel="noopener"
       class="group rounded-2xl border bg-white p-6 hover:shadow-lg hover:-translate-y-0.5 transition" data-fade>
      <div class="flex items-center gap-4">
        {{-- ikon WA --}}
        <svg class="h-11 w-11 text-green-500 group-hover:scale-105 transition" viewBox="0 0 24 24" fill="currentColor">
          <path d="M20.52 3.48A11.86 11.86 0 0 0 12.07 0 11.93 11.93 0 0 0 .14 11.93 11.8 11.8 0 0 0 2.7 19.2L1.2 24l4.96-1.52a11.93 11.93 0 0 0 5.91 1.58h.01A11.93 11.93 0 0 0 24 12.06a11.86 11.86 0 0 0-3.48-8.58ZM12.08 21.3h-.01a9.26 9.26 0 0 1-4.72-1.28l-.34-.2-2.96.91.93-2.88-.22-.3a9.25 9.25 0 1 1 7.31 3.75Zm5.36-6.93c-.29-.14-1.73-.85-1.99-.95s-.46-.14-.66.14-.76.95-.94 1.14-.35.21-.64.07a7.57 7.57 0 0 1-2.23-1.38 8.39 8.39 0 0 1-1.55-1.92c-.16-.28 0-.43.12-.57.12-.12.28-.3.42-.45s.2-.25.3-.42.05-.32-.02-.45-.66-1.6-.9-2.2-.47-.5-.66-.51h-.56a1.08 1.08 0 0 0-.78.36c-.27.3-1.03 1.01-1.03 2.46s1.06 2.85 1.2 3.05 2.1 3.2 5.08 4.49c.71.31 1.27.49 1.71.63.72.23 1.38.2 1.9.12.58-.09 1.73-.71 1.98-1.4.24-.68.24-1.27.17-1.39-.07-.12-.26-.2-.55-.34Z"/>
        </svg>
        <div>
          <div class="font-semibold">WhatsApp</div>
          <div class="text-sm text-gray-500">+{{ $wa }}</div>
        </div>
      </div>
      <div class="mt-4 inline-flex items-center gap-2 text-green-600 group-hover:underline">
        Chat sekarang
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M5 12h14M13 5l7 7-7 7"/></svg>
      </div>
    </a>

    {{-- Email --}}
    <a href="{{ $mailUrl }}" class="group rounded-2xl border bg-white p-6 hover:shadow-lg hover:-translate-y-0.5 transition" data-fade>
      <div class="flex items-center gap-4">
        <svg class="h-11 w-11 text-indigo-600 group-hover:scale-105 transition" viewBox="0 0 24 24" fill="currentColor">
          <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z"/>
        </svg>
        <div>
          <div class="font-semibold">Email</div>
          <div class="text-sm text-gray-500">{{ $mail }}</div>
        </div>
      </div>
      <div class="mt-4 inline-flex items-center gap-2 text-indigo-600 group-hover:underline">
        Kirim email
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M5 12h14M13 5l7 7-7 7"/></svg>
      </div>
    </a>

    {{-- Instagram --}}
    <a href="{{ $igUrl }}" target="_blank" rel="noopener"
       class="group rounded-2xl border bg-white p-6 hover:shadow-lg hover:-translate-y-0.5 transition" data-fade>
      <div class="flex items-center gap-4">
        <svg class="h-11 w-11 text-pink-500 group-hover:scale-105 transition" viewBox="0 0 24 24" fill="currentColor">
          <path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5Zm5 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm6.5-.75a1.25 1.25 0 1 0 0 2.5 1.25 1.25 0 0 0 0-2.5Z"/>
        </svg>
        <div>
          <div class="font-semibold">Instagram</div>
          <div class="text-sm text-gray-500">@coolcare.ac</div>
        </div>
      </div>
      <div class="mt-4 inline-flex items-center gap-2 text-pink-600 group-hover:underline">
        Buka profil
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M5 12h14M13 5l7 7-7 7"/></svg>
      </div>
    </a>
  </div>

  <p class="text-center text-sm text-gray-500 mt-6">
    Tap salah satu untuk langsung terhubung.
  </p>
</section>

  {{-- FLOATING WA --}}
  <a href="https://wa.me/6281234567890" target="_blank"
     class="fixed bottom-6 right-6 bg-green-500 text-white p-4 rounded-full shadow-lg hover:scale-110 transition text-xl float">
     💬
  </a>

  {{-- SCROLL TO TOP --}}
  <button id="toTopBtn" class="hidden fixed bottom-20 right-6 bg-indigo-600 text-white p-3 rounded-full shadow-lg hover:bg-indigo-500 transition text-xl">
    ↑
  </button>

  @push('styles')
    {{-- AOS + Swiper CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css"/>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css"/>
    <style>
      [data-fade]{opacity:0;transform:translateY(30px);transition:all .7s ease-out}
      [data-fade].show{opacity:1;transform:translateY(0)}
      .hover-glow:hover{box-shadow:0 0 15px rgba(99,102,241,.6)}
      @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
      .float{animation:float 3s ease-in-out infinite}
    </style>
  @endpush

  @push('scripts')
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
      // Smooth scroll super halus (untuk .scroll-link)
      function smoothScrollTo(targetY, duration = 900) {
        const startY = window.pageYOffset;
        const diff = targetY - startY;
        let start;
        function ease(t){ return t<.5 ? 4*t*t*t : 1 - Math.pow(-2*t+2,3)/2 }
        function step(ts){
          if(!start) start = ts;
          const p = Math.min((ts - start)/duration, 1);
          window.scrollTo(0, startY + diff * ease(p));
          if(p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
      }

      document.querySelectorAll('.scroll-link').forEach(a=>{
        a.addEventListener('click', e=>{
          const href = a.getAttribute('href') || '';
          if(href.startsWith('#')){
            e.preventDefault();
            const el = document.querySelector(href);
            if(el){
              const offsetTop = el.getBoundingClientRect().top + window.scrollY - 60; // header offset
              smoothScrollTo(offsetTop);
            }
          }
        });
      });

      // Fade-in observer
      const obs = new IntersectionObserver(entries=>{
        entries.forEach(en=>{ if(en.isIntersecting) en.target.classList.add('show'); });
      }, { threshold:.12 });
      document.querySelectorAll('[data-fade]').forEach(el=>obs.observe(el));

      // Scroll to top
      const toTopBtn = document.getElementById('toTopBtn');
      window.addEventListener('scroll', () => {
        if (window.scrollY > 400) toTopBtn.classList.remove('hidden');
        else toTopBtn.classList.add('hidden');
      });
      toTopBtn?.addEventListener('click', ()=> smoothScrollTo(0, 800));

      // Parallax hero
      const hero = document.getElementById('home');
      window.addEventListener('scroll', ()=> {
        const y = window.pageYOffset;
        hero.style.backgroundPositionY = (y * 0.4) + 'px';
      });

      // Init AOS + Swiper
      AOS.init({ duration: 800, once: true });
      new Swiper('.mySwiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: { delay: 3200 },
        pagination: { el: '.swiper-pagination', clickable: true },
        breakpoints: { 768: { slidesPerView: 3 } },
      });
    </script>
  @endpush
</div>
