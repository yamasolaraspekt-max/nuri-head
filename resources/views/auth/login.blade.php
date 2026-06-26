<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8" />
  <title>Login • Solar Aspekt</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Tailwind CDN with custom palette -->
  <script>
    tailwind = {
      config: {
        theme: {
          extend: {
            colors: {
              accent: "#93c21c",      // solar green
              accentSoft: "#c6e275",
              sky1: "#74b2d4",        // blue tint
              night: "#050816"
            },
            boxShadow: {
              glow: "0 4px 20px rgba(147,194,28,0.25)",
              card: "0 10px 40px -10px rgba(0,0,0,0.08)"
            }
          }
        },
        corePlugins: { preflight: true }
      }
    };
  </script>
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    html, body { height: 100%; }

    body {
      margin: 0;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "SF Pro Text", "Segoe UI", sans-serif;
      background-color: #e3effb; /* Requested background color */
      overflow: hidden;
      color: #334155; /* Slate 700 for better readability on light theme */
    }

    /* Subtle grid overlay tailored for light theme */
    .grid-overlay {
      position: fixed;
      inset: 0;
      pointer-events: none;
      opacity: 0.4;
      background-image:
        linear-gradient(to right, rgba(148,163,184,0.15) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(148,163,184,0.15) 1px, transparent 1px);
      background-size: 46px 46px;
      mask-image: radial-gradient(circle at center, black, transparent 90%);
      -webkit-mask-image: radial-gradient(circle at center, black, transparent 90%);
      z-index: 1;
    }

    /* Light Theme Glass Card */
    .glass {
      position: relative;
      border-radius: 24px;
      background: rgba(255, 255, 255, 0.65);
      box-shadow: 
        0 10px 40px -10px rgba(0,0,0,0.08),
        inset 0 0 0 1px rgba(255, 255, 255, 1);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.4);
      z-index: 10;
      transition: transform 0.1s ease-out;
    }

    /* Cursor glow adapted for light mode */
    #cursorGlow {
      position: fixed; 
      inset: 0; 
      width: 100vw; 
      height: 100vh; 
      pointer-events: none;
      z-index: 0;
      /* Soft white center with a subtle green outer glow */
      background: 
        radial-gradient(600px circle at var(--mx, 50%) var(--my, 50%), rgba(255, 255, 255, 0.8), transparent 40%),
        radial-gradient(800px circle at var(--mx, 50%) var(--my, 50%), rgba(147, 194, 28, 0.12), transparent 50%);
    }

    /* Icon Container Animation */
    .icon-ring {
      animation: pulse-ring 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse-ring {
      0% { transform: scale(0.9); opacity: 0.5; }
      50% { transform: scale(1.1); opacity: 0.1; }
      100% { transform: scale(0.9); opacity: 0.5; }
    }

    @media (max-width: 640px) {
      body {
        overflow: auto;
      }
    }
  </style>
</head>

<body class="relative text-slate-800">
  <!-- Background effects -->
  <div id="cursorGlow"></div>
  <div class="grid-overlay"></div>

  <!-- Content -->
  <main class="relative z-10 min-h-screen flex items-center justify-center p-4">
    <section id="card" class="glass w-full max-w-md">
      <div class="p-8 sm:p-10">
        <!-- Brand / top row -->
        <div class="flex items-center justify-between mb-8">
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-white flex items-center justify-center shadow-sm border border-slate-200">
              <!-- Minimal “SA” logo -->
              <span class="text-[12px] tracking-[0.18em] font-bold uppercase text-accent">SA</span>
            </div>
            <div class="flex flex-col leading-tight">
              <span class="text-[11px] tracking-[0.18em] uppercase text-slate-500 font-semibold">Solar Aspekt</span>
              <span class="text-sm text-slate-700 font-medium">Access Portal</span>
            </div>
          </div>
          <div class="hidden sm:flex bg-white/60 border border-slate-200 shadow-sm px-3 py-1.5 rounded-full text-[10px] items-center gap-2 text-slate-600 font-medium">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>System Online</span>
          </div>
        </div>

        <!-- Profile + Title (Replaced 3D with Professional Icon) -->
        <div class="flex flex-col items-center text-center gap-5">
          <div class="relative flex items-center justify-center h-20 w-20">
            <!-- Pulsing background ring -->
            <div class="absolute inset-0 rounded-full bg-accent icon-ring"></div>
            <!-- Clean white icon container -->
            <div class="relative h-16 w-16 bg-white rounded-full shadow-md border border-slate-100 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
            </div>
          </div>

          <div class="space-y-1.5">
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-800">
              Willkommen zurück
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm tracking-wider uppercase font-medium">
              Sicherer Login
            </p>
          </div>
        </div>

        <!-- Laravel Error Handling -->
        @if ($errors->any())
          <div class="mt-6 text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl p-4 shadow-sm">
            <ul class="list-disc pl-5 space-y-1">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- Login Form (Laravel) -->
        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
          @csrf
          <div>
            <label class="block text-xs font-semibold tracking-wide mb-2 text-slate-600" for="email">
              E-Mail Adresse
            </label>
            <input
              id="email"
              name="email"
              type="email"
              autocomplete="username"
              required
              class="w-full rounded-xl bg-white border border-slate-300 px-4 py-3.5 text-sm text-slate-900 shadow-sm outline-none
                     focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all placeholder-slate-400"
              placeholder="name@unternehmen.de"
              value="{{ old('email') }}"
            />
            @error('email')
              <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label class="block text-xs font-semibold tracking-wide mb-2 text-slate-600" for="password">
              Passwort
            </label>
            <div class="relative">
              <input
                id="password"
                name="password"
                type="password"
                autocomplete="current-password"
                required
                class="w-full rounded-xl bg-white border border-slate-300 px-4 py-3.5 pr-12 text-sm text-slate-900 shadow-sm outline-none
                       focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all placeholder-slate-400"
                placeholder="••••••••"
              />
              <button
                type="button"
                id="togglePassword"
                class="absolute inset-y-0 right-0 px-4 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none transition-colors"
                aria-label="Passwort anzeigen/ausblenden"
                aria-pressed="false"
              >
                <!-- eye-closed -->
                <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                     fill="none" class="w-5 h-5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 3l18 18M4.53 5.47A11.18 11.18 0 001.5 12s3.75 7.5 10.5 7.5c2.08 0 3.98-.5 5.64-1.38M9.88 9.88A3 3 0 0115 12c0 .64-.2 1.24-.54 1.73" />
                </svg>
                <!-- eye-open -->
                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                     fill="none" class="w-5 h-5 hidden" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                  <circle cx="12" cy="12" r="3" stroke-width="1.5" />
                </svg>
              </button>
            </div>
            @error('password')
              <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
          </div>

          <div class="flex items-center justify-between text-sm">
            <label class="inline-flex items-center gap-2 cursor-pointer group">
              <div class="relative flex items-center">
                <input
                  type="checkbox"
                  name="remember"
                  class="peer h-4 w-4 cursor-pointer appearance-none rounded border border-slate-300 bg-white checked:border-accent checked:bg-accent focus:outline-none focus:ring-2 focus:ring-accent/30 transition-all"
                />
                <svg class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 h-3 w-3 pointer-events-none opacity-0 peer-checked:opacity-100 text-white stroke-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              </div>
              <span class="text-slate-600 group-hover:text-slate-800 transition-colors">Angemeldet bleiben</span>
            </label>
            <a
              href="{{ route('password.request') }}"
              class="text-sky1 font-medium hover:text-sky1/80 hover:underline transition-colors"
            >
              Passwort vergessen?
            </a>
          </div>

          <button
            type="submit"
            class="w-full mt-2 rounded-xl bg-gradient-to-r from-accent to-accentSoft
                   text-slate-900 font-bold text-sm py-3.5
                   shadow-glow hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-accent"
          >
            Anmelden
          </button>
        </form>

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-slate-200/60 flex items-center justify-between text-[11px] sm:text-xs text-slate-500 font-medium">
          <div class="flex items-center gap-1.5">
            <span>© {{ date('Y') }}</span>
            <span class="text-slate-700">Solar Aspekt</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
            <span>Interne Nutzung</span>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Interactions -->
  <script>
    // Mouse glow + subtle parallax tilt for a professional feel
    (function(){
      const card = document.getElementById('card');
      const glow = document.getElementById('cursorGlow');
      let w = window.innerWidth, h = window.innerHeight;
      
      const clamp = (n, min, max) => Math.max(min, Math.min(max, n));
      
      function onMove(e){
        const x = e.clientX, y = e.clientY;
        
        // Update glow position (using direct pixels for smoothness)
        glow.style.setProperty('--mx', x + 'px');
        glow.style.setProperty('--my', y + 'px');
        
        // Very subtle parallax effect suitable for business logic
        const rx = clamp(((h/2 - y) / h) * 4, -4, 4);
        const ry = clamp(((x - w/2) / w) * 4, -4, 4);
        
        // Only apply 3D tilt on larger screens
        if(w > 640) {
          card.style.transform = `perspective(1000px) rotateX(${rx}deg) rotateY(${ry}deg) translateZ(0)`;
        } else {
          card.style.transform = 'none';
        }
      }
      
      window.addEventListener('mousemove', onMove);
      window.addEventListener('resize', () => { w = innerWidth; h = innerHeight; });
      
      // Reset card tilt when mouse leaves window
      document.addEventListener('mouseleave', () => {
        card.style.transform = `perspective(1000px) rotateX(0deg) rotateY(0deg) translateZ(0)`;
      });
    })();

    // Password show/hide toggle logic
    (function(){
      const btn = document.getElementById('togglePassword');
      const input = document.getElementById('password');
      const eyeOpen = document.getElementById('eyeOpen');
      const eyeClosed = document.getElementById('eyeClosed');
      
      if (btn && input && eyeOpen && eyeClosed) {
        btn.addEventListener('click', () => {
          const show = input.type === 'password';
          input.type = show ? 'text' : 'password';
          btn.setAttribute('aria-pressed', String(show));
          eyeOpen.classList.toggle('hidden', !show);
          eyeClosed.classList.toggle('hidden', show);
        });
      }
    })();
  </script>
</body>
</html>