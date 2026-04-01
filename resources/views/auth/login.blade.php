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
              glow: "0 0 40px rgba(147,194,28,0.35)"
            }
          }
        },
        corePlugins: { preflight: true }
      }
    };
  </script>
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Three.js -->
  <script src="https://unpkg.com/three@0.160.0/build/three.min.js"></script>

  <style>
    html, body { height: 100%; }

    body {
      margin: 0;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "SF Pro Text", "Segoe UI", sans-serif;
      background:
        radial-gradient(900px 900px at 0% 0%, rgba(116,178,212,0.25), transparent 60%),
        radial-gradient(900px 900px at 100% 100%, rgba(147,194,28,0.28), transparent 60%),
        linear-gradient(135deg, #050816, #0b1020 50%, #050816);
      overflow: hidden;
      color: #e5e7eb;
    }

    /* Subtle grid overlay */
    .grid-overlay {
      position: fixed;
      inset: 0;
      pointer-events: none;
      opacity: 0.12;
      background-image:
        linear-gradient(to right, rgba(148,163,184,0.35) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(148,163,184,0.35) 1px, transparent 1px);
      background-size: 46px 46px;
      mask-image: radial-gradient(circle at center, black, transparent 85%);
    }

    /* Card */
    .glass {
      position: relative;
      border-radius: 26px;
      background:
        radial-gradient(circle at top left, rgba(255,255,255,0.12), rgba(15,23,42,0.97));
      box-shadow:
        0 22px 55px rgba(15,23,42,0.9),
        0 0 60px rgba(0,0,0,0.7);
      border: 1px solid rgba(148,163,184,0.6);
      backdrop-filter: blur(22px);
      -webkit-backdrop-filter: blur(22px);
      overflow: hidden;
    }

    /* Animated gradient border ring */
    .glass::before {
      content: "";
      position: absolute;
      inset: -1px;
      border-radius: inherit;
      padding: 1px;
      background: conic-gradient(
        from 160deg,
        rgba(147,194,28,0.0),
        rgba(147,194,28,0.65),
        rgba(116,178,212,0.7),
        rgba(147,194,28,0.0)
      );
      -webkit-mask:
        linear-gradient(#000 0 0) content-box,
        linear-gradient(#000 0 0);
      -webkit-mask-composite: xor;
              mask-composite: exclude;
      opacity: 0.85;
      pointer-events: none;
      animation: border-spin 16s linear infinite;
    }

    @keyframes border-spin {
      0%   { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Inner shell to avoid gradient overlaying content */
    .glass-inner {
      position: relative;
      border-radius: 24px;
      background:
        radial-gradient(circle at 20% -20%, rgba(148,163,184,0.28), transparent 55%),
        radial-gradient(circle at 110% 120%, rgba(147,194,28,0.32), transparent 60%),
        linear-gradient(135deg, rgba(15,23,42,0.96), rgba(15,23,42,0.99));
      box-shadow: inset 0 0 0 1px rgba(15,23,42,0.9);
    }

    /* Background canvases */
    #matrixCanvas, #cursorGlow {
      position: fixed; inset: 0; width: 100vw; height: 100vh; pointer-events: none;
    }

    #cursorGlow {
      mix-blend-mode: screen;
      background: radial-gradient(220px 220px at var(--mx,50%) var(--my,50%), rgba(147,194,28,0.22), transparent 60%);
      transition: background-position 0.06s linear;
    }

    /* 3D avatar container */
    .model-wrap {
      position: relative;
      border-radius: 999px;
      overflow: hidden;
      background:
        radial-gradient(circle at 20% 0%, rgba(116,178,212,0.55), transparent 65%),
        radial-gradient(circle at 80% 100%, rgba(147,194,28,0.55), transparent 60%);
      box-shadow:
        0 0 0 1px rgba(15,23,42,1),
        0 0 0 1px rgba(148,163,184,0.5),
        0 16px 35px rgba(15,23,42,0.9);
    }

    .model-wrap::after {
      content: "";
      position: absolute;
      inset: 12%;
      border-radius: inherit;
      border: 1px solid rgba(255,255,255,0.28);
      opacity: 0.8;
    }

    .model-wrap canvas {
      position: absolute;
      inset: 0;
      width: 100% !important;
      height: 100% !important;
      display: block;
    }

    /* Tiny label pill */
    .pill {
      border-radius: 999px;
      border: 1px solid rgba(148,163,184,0.4);
      background: radial-gradient(circle at top left, rgba(148,163,184,0.25), rgba(15,23,42,0.96));
    }

    @media (max-width: 640px) {
      body {
        overflow: auto;
      }
    }
  </style>
</head>

<body class="relative text-slate-100">
  <!-- Background effects -->
  <canvas id="matrixCanvas"></canvas>
  <div id="cursorGlow"></div>
  <div class="grid-overlay"></div>

  <!-- Content -->
  <main class="relative z-10 min-h-screen flex items-center justify-center p-4">
    <section id="card" class="glass w-full max-w-md">
      <div class="glass-inner p-6 sm:p-8">
        <!-- Brand / top row -->
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-2xl bg-slate-900/80 flex items-center justify-center shadow-glow border border-slate-500/70">
              <!-- Minimal “SA” logo -->
              <span class="text-[11px] tracking-[0.18em] font-semibold uppercase text-accentSoft">SA</span>
            </div>
            <div class="flex flex-col leading-tight">
              <span class="text-[11px] tracking-[0.18em] uppercase text-slate-400">Solar Aspekt</span>
              <span class="text-sm text-slate-200/90">Access Portal</span>
            </div>
          </div>
          <div class="hidden sm:flex pill px-2.5 py-1 text-[10px] items-center gap-1.5 text-slate-200/90">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>Online</span>
          </div>
        </div>

        <!-- Profile + Title -->
        <div class="flex flex-col items-center text-center gap-4">
          <div class="model-wrap relative aspect-square w-24 sm:w-28 md:w-32"></div>

          <div class="space-y-1">
            <h1 class="text-xl sm:text-2xl font-semibold tracking-tight">
              Willkommen
            </h1>
            <p class="text-slate-300/80 text-xs sm:text-sm tracking-wide uppercase">
              Solar Aspekt • Secure Login
            </p>
          </div>
        </div>

        @if ($errors->any())
          <div class="mt-5 text-sm text-red-300 bg-red-900/35 border border-red-500/40 rounded-xl p-3.5">
            <ul class="list-disc pl-5 space-y-0.5">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- Login Form (Laravel) -->
        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
          @csrf
          <div>
            <label class="block text-xs font-medium tracking-wide mb-1.5 text-slate-300" for="email">
              E-Mail
            </label>
            <input
              id="email"
              name="email"
              type="email"
              autocomplete="username"
              required
              class="w-full rounded-xl bg-slate-950/70 border border-slate-700/70 px-4 py-3 text-sm outline-none
                     focus:ring-2 focus:ring-accent/80 focus:border-accent/70 placeholder-slate-500"
              placeholder="you@example.com"
              value="{{ old('email') }}"
            />
            @error('email')
              <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label class="block text-xs font-medium tracking-wide mb-1.5 text-slate-300" for="password">
              Passwort
            </label>
            <div class="relative">
              <input
                id="password"
                name="password"
                type="password"
                autocomplete="current-password"
                required
                class="w-full rounded-xl bg-slate-950/70 border border-slate-700/70 px-4 py-3 pr-12 text-sm outline-none
                       focus:ring-2 focus:ring-accent/80 focus:border-accent/70 placeholder-slate-500"
                placeholder="••••••••"
              />
              <button
                type="button"
                id="togglePassword"
                class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400/85 hover:text-slate-100 focus:outline-none"
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
              <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
            @enderror
          </div>

          <div class="flex items-center justify-between text-xs">
            <label class="inline-flex items-center gap-2">
              <input
                type="checkbox"
                name="remember"
                class="h-4 w-4 rounded border-slate-600/90 bg-slate-950/80 text-accent focus:ring-accent"
              />
              <span class="text-slate-300">Angemeldet bleiben</span>
            </label>
            <a
              href="{{ route('password.request') }}"
              class="text-sky1 hover:text-sky1/90 hover:underline"
            >
              Passwort vergessen?
            </a>
          </div>

          <button
            type="submit"
            class="w-full mt-1 rounded-xl bg-gradient-to-r from-accent via-accentSoft to-sky1
                   text-slate-950 font-semibold text-sm py-3
                   shadow-glow hover:brightness-105 transition
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-950 focus:ring-accent/80"
          >
            Anmelden
          </button>
        </form>

        <!-- Footer -->
        <div class="mt-6 flex items-center justify-between text-[10px] sm:text-xs text-slate-400/90">
          <div class="flex items-center gap-1.5">
            <span>© {{ date('Y') }}</span>
            <span>Solar Aspekt</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="h-1.5 w-1.5 rounded-full bg-accentSoft"></span>
            <span>v1 • Internal Use</span>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Interactions, Matrix, Three.js model, and password toggle -->
  <script>
    // Mouse glow + parallax tilt
    (function(){
      const card = document.getElementById('card');
      const glow = document.getElementById('cursorGlow');
      let w = window.innerWidth, h = window.innerHeight;
      const clamp = (n,min,max)=> Math.max(min, Math.min(max, n));
      function onMove(e){
        const x = e.clientX, y = e.clientY;
        glow.style.setProperty('--mx', (x / w * 100) + '%');
        glow.style.setProperty('--my', (y / h * 100) + '%');
        const rx = clamp(((h/2 - y) / h) * 10, -10, 10);
        const ry = clamp(((x - w/2) / w) * 10, -10, 10);
        card.style.transform = `perspective(1000px) rotateX(${rx}deg) rotateY(${ry}deg) translateZ(0)`;
      }
      window.addEventListener('mousemove', onMove);
      window.addEventListener('resize', ()=>{ w = innerWidth; h = innerHeight; });
    })();

    // Matrix Rain (custom words)
    (function(){
      const canvas = document.getElementById('matrixCanvas');
      const ctx = canvas.getContext('2d');
      const WORDS = ["Ramin", "Sadid", "Solar", "Aspekt", "SA-CRM"]; 
      let w, h, cols, fontSize, drops, mouse = {x:-9999,y:-9999};
      function resize(){
        w = canvas.width = window.innerWidth;
        h = canvas.height = window.innerHeight;
        fontSize = Math.max(14, Math.floor(w < 640 ? 12 : 16));
        ctx.font = `${fontSize}px monospace`;
        cols = Math.floor(w / fontSize);
        drops = Array(cols).fill(0).map(()=> Math.floor(Math.random()*h));
      }
      function draw(){
        ctx.fillStyle = "rgba(0, 0, 0, 0.10)";
        ctx.fillRect(0,0,w,h);
        for(let i=0; i<cols; i++){
          const word = WORDS[Math.floor(Math.random()*WORDS.length)];
          const ch = word[Math.floor(Math.random()*word.length)];
          const x = i * fontSize;
          const y = drops[i] * fontSize;
          const dx = (x - mouse.x), dy = (y - mouse.y);
          const dist = Math.sqrt(dx*dx + dy*dy);
          const near = dist < 140 ? 1 : 0;
          ctx.fillStyle = near ? "rgba(255,255,255,0.95)" : "rgba(147,194,28,0.9)";
          ctx.fillText(ch, x, y);
          if(y > h || Math.random() > 0.975) drops[i] = 0;
          const slow = near ? 0.45 : 1;
          drops[i] += slow;
        }
        requestAnimationFrame(draw);
      }
      window.addEventListener('resize', resize);
      window.addEventListener('mousemove', (e)=>{ mouse.x = e.clientX; mouse.y = e.clientY; });
      resize(); draw();
    })();

    // Three.js rotating model (same logic, just styled holder)
    (function () {
      const wrap = document.querySelector('.model-wrap');
      if (!wrap || typeof THREE === 'undefined') return;

      const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'high-performance' });
      renderer.outputColorSpace = THREE.SRGBColorSpace;
      renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
      renderer.setSize(16, 16, false);
      wrap.appendChild(renderer.domElement);

      const scene = new THREE.Scene();
      const camera = new THREE.PerspectiveCamera(28, 1, 0.1, 100);
      camera.position.set(0, 0.2, 3);

      const hemi = new THREE.HemisphereLight(0xffffff, 0x223344, 0.8);
      scene.add(hemi);
      const key = new THREE.PointLight(0xffffff, 1.2, 0, 2);
      key.position.set(2.5, 3, 4);
      scene.add(key);
      const rim = new THREE.PointLight(0x93c21c, 0.8, 0, 2);
      rim.position.set(-3, -1, -2);
      scene.add(rim);

      const geo = new THREE.TorusKnotGeometry(0.7, 0.24, 180, 32, 2, 3);
      const mat = new THREE.MeshPhysicalMaterial({
        color: 0xffffff,
        metalness: 0.65,
        roughness: 0.18,
        clearcoat: 1.0,
        clearcoatRoughness: 0.08,
        sheen: 0.5,
        emissive: 0x1a2a0d,
        emissiveIntensity: 0.28
      });
      const knot = new THREE.Mesh(geo, mat);
      scene.add(knot);

      function resizeRendererToDisplaySize() {
        const rect = wrap.getBoundingClientRect();
        let w = Math.max(1, Math.floor(rect.width));
        let h = Math.max(1, Math.floor(rect.height));

        const needResize = renderer.domElement.width !== Math.floor(w * renderer.getPixelRatio())
          || renderer.domElement.height !== Math.floor(h * renderer.getPixelRatio());

        if (needResize) {
          renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
          renderer.setSize(w, h, false);
          camera.aspect = Math.max(1e-6, w / h);
          camera.updateProjectionMatrix();
        }
      }

      if ('ResizeObserver' in window) {
        const ro = new ResizeObserver(() => resizeRendererToDisplaySize());
        ro.observe(wrap);
      } else {
        window.addEventListener('resize', resizeRendererToDisplaySize, { passive: true });
        window.addEventListener('orientationchange', () => setTimeout(resizeRendererToDisplaySize, 60), { passive: true });
      }

      let t = 0;
      function animate() {
        t += 0.01;
        knot.rotation.x += 0.008;
        knot.rotation.y += 0.011;

        key.position.x = Math.cos(t * 0.8) * 3.0;
        key.position.y = 2.2 + Math.sin(t * 0.6) * 1.2;
        rim.position.z = -2 + Math.sin(t * 0.7);

        resizeRendererToDisplaySize();
        renderer.render(scene, camera);
        requestAnimationFrame(animate);
      }

      const kick = () => { resizeRendererToDisplaySize(); };
      kick(); setTimeout(kick, 0); setTimeout(kick, 120);

      animate();
    })();

    // Password show/hide
    (function(){
      const btn = document.getElementById('togglePassword');
      const input = document.getElementById('password');
      const eyeOpen = document.getElementById('eyeOpen');
      const eyeClosed = document.getElementById('eyeClosed');
      if (btn && input && eyeOpen && eyeClosed) {
        btn.addEventListener('click', ()=>{
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
