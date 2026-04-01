<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>AI Token • Celebration Card</title>
  <script>
    tailwind = {
      config: {
        theme: {
          extend: {
            colors: { accent: "#93c21c", sky1: "#74b2d4" },
            boxShadow: { glow: "0 0 40px rgba(147,194,28,0.35)" },
            fontFamily: { display: ["Inter", "ui-sans-serif", "system-ui"] }
          }
        }
      }
    };
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Background flair */
    .bg-orb { filter: blur(60px); opacity:.35; animation: float 16s ease-in-out infinite; }
    .bg-orb:nth-child(2){ animation-duration: 20s; }
    @keyframes float { 0%,100%{ transform: translateY(0) translateX(0) scale(1);} 50%{ transform: translateY(-18px) translateX(10px) scale(1.05);} }

    /* Confetti */
    #confetti { position: fixed; inset: 0; pointer-events: none; overflow: hidden; }
    .confetti { position: absolute; will-change: transform, opacity; opacity: 0.95; border-radius: 2px; animation-name: fall; animation-timing-function: cubic-bezier(.15,.53,.51,.92); animation-fill-mode: both; }
    @keyframes fall {
      0%   { transform: translateY(-12vh) rotate(0deg);   opacity:1; }
      100% { transform: translateY(110vh) rotate(720deg); opacity:0; }
    }

    /* Shine across the number */
    .shiny {
      position: relative; display: inline-block; isolation: isolate;
      background: linear-gradient(90deg, #111, #111);
      -webkit-background-clip: text; background-clip: text; color: transparent;
    }
    .shiny::after {
      content: ""; position: absolute; inset: -40% -60%; pointer-events:none;
      background: linear-gradient(120deg, transparent 45%, rgba(255,255,255,.65), transparent 55%);
      transform: translateX(-120%);
      animation: sweep 1.8s ease-out .4s 1 forwards;
      mix-blend-mode: screen; opacity:.9; border-radius: 30px;
    }
    @keyframes sweep { to { transform: translateX(120%); } }

    /* SVG burst */
    .burst { animation: burst 1.1s ease-out .2s 1 both; transform-origin: center; }
    @keyframes burst { 0%{ transform: scale(.6); opacity:.6 } 80%{ opacity:.15 } 100%{ transform: scale(1.35); opacity:0 } }
  </style>
</head>
<body class="min-h-screen grid place-items-center bg-gradient-to-br from-sky1/30 to-accent/20 font-display">
  <!-- soft blobs -->
  <div class="fixed inset-0 -z-10">
    <div class="bg-orb absolute -top-24 -left-16 w-80 h-80 rounded-full bg-sky1/60"></div>
    <div class="bg-orb absolute -bottom-28 -right-24 w-96 h-96 rounded-full bg-accent/60"></div>
  </div>

  <!-- Confetti overlay -->
  <div id="confetti"></div>

    @php
        $user = DB::table('employees')->select('id','name','lastname')->where('id', auth()->user()->name)->first();
        $name = $user->name.' '.$user->lastname;
    @endphp
  <!-- Celebration Card -->
  <div class="mx-4 w-full max-w-md rounded-3xl border border-white/60 bg-white/70 backdrop-blur shadow-xl shadow-slate-200/40">
    <div class="p-8">
      <div class="text-center space-y-4">
        <!-- Tiny badge -->
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-accent/40 bg-accent/10 text-accent text-xs font-semibold">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
          Congrats  {{ $name }} ! You’re loaded
        </div>

        <!-- Starburst behind icon -->
        <div class="relative mx-auto h-16 w-16">
          <svg viewBox="0 0 120 120" class="absolute inset-0 burst" aria-hidden="true">
            <g stroke="#93c21c" stroke-width="2" stroke-linecap="round" fill="none" opacity=".7">
              <!-- radial lines -->
              <line x1="60" y1="6"  x2="60" y2="24"/>
              <line x1="60" y1="96" x2="60" y2="114"/>
              <line x1="6"  y1="60" x2="24" y2="60"/>
              <line x1="96" y1="60" x2="114" y2="60"/>
              <line x1="24" y1="24" x2="36" y2="36"/>
              <line x1="96" y1="24" x2="84" y2="36"/>
              <line x1="24" y1="96" x2="36" y2="84"/>
              <line x1="96" y1="96" x2="84" y2="84"/>
              <circle cx="60" cy="60" r="18" stroke-opacity=".4"/>
            </g>
          </svg>
          <svg viewBox="0 0 24 24" class="absolute inset-0 m-auto h-16 w-16 text-accent" fill="currentColor" aria-label="Trophy">
            <path d="M17 3h-1V2H8v1H7a2 2 0 0 0-2 2v1a5 5 0 0 0 4 4.9V13H7v2h10v-2h-2V10.9A5 5 0 0 0 19 6V5a2 2 0 0 0-2-2Zm-9 3V5h1v1a3 3 0 0 1-1 0Zm10 0a3 3 0 0 1-1 0V5h1v1Z"/>
          </svg>
        </div>

        <!-- Headline -->
        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Your AI chat token</p>
        <div class="text-5xl md:text-6xl font-extrabold tabular-nums bg-clip-text text-transparent bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 shiny">1,000,000</div>
        <p class="text-sm text-slate-600">tokens available</p>

        <div class="pt-2">
          <button id="replay" class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm">Replay celebration</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Fire confetti on load and when replay is clicked
    const colors = ["#93c21c", "#74b2d4", "#f59e0b", "#ef4444", "#10b981", "#6366f1"]; // brand + accents
    function celebrate() {
      const layer = document.getElementById('confetti');
      if (!layer) return;
      layer.innerHTML = '';
      const COUNT = 140;
      for (let i = 0; i < COUNT; i++) {
        const s = document.createElement('span');
        s.className = 'confetti';
        const size = 6 + Math.random() * 8; // 6–14px
        const left = Math.random() * 100;   // vw
        const delay = Math.random() * 0.4;  // s
        const dur = 1.6 + Math.random() * 1.3; // 1.6–2.9s
        const tilt = Math.random() * 360;
        s.style.width = size + 'px';
        s.style.height = (size * 0.45) + 'px';
        s.style.left = left + 'vw';
        s.style.top = (-10 - Math.random() * 20) + 'px';
        s.style.background = colors[(Math.random() * colors.length) | 0];
        s.style.transform = `rotate(${tilt}deg)`;
        s.style.animationDuration = dur + 's';
        s.style.animationDelay = delay + 's';
        layer.appendChild(s);
      }
    }
    celebrate();
    document.getElementById('replay')?.addEventListener('click', celebrate);
  </script>
</body>
</html>
