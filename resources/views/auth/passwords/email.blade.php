{{-- resources/views/auth/passwords/email.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Forgot your password?</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {{-- CSRF for Laravel --}}
  <meta name="csrf-token" content="{{ csrf_token() }}"/>

  <style>
    :root{
      /* Brand-ish palette */
      --bg: #0b1020;
      --bg-2: #0f1630;
      --card: rgba(255,255,255,0.06);
      --border: rgba(255,255,255,0.12);
      --text: #e6eef7;
      --muted: #9fb0c6;
      --accent: #93c21c;     /* green from your earlier spec */
      --accent-2: #c0d8ea;   /* button tint you mentioned */
      --error: #ff6b6b;
      --success: #2dd4bf;
      --shadow: 0 10px 30px rgba(0,0,0,0.35);
      --radius: 16px;
    }
    /* Minimal reset */
    *,*::before,*::after{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; background: radial-gradient(1200px 800px at 10% -10%, #1b2750 0%, var(--bg) 55%) fixed;
      color:var(--text); font: 16px/1.5 system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
      overflow-x:hidden;
    }

    /* 3D background canvas */
    #bg3d{
      position:fixed; inset:0; width:100%; height:100%; display:block; z-index:-1; pointer-events:none;
    }

    /* Layout */
    .wrap{
      position:relative; min-height:100dvh; display:flex; align-items:center; justify-content:center; padding:32px;
    }
    .grid{
      width:100%; max-width:1100px; display:grid; gap:32px;
      grid-template-columns: 1fr;
    }
    @media (min-width: 900px){
      .grid{ grid-template-columns: 1.05fr 0.95fr; }
    }

    /* Card */
    .card{
      background: linear-gradient(180deg, rgba(255,255,255,0.08), rgba(255,255,255,0.04));
      backdrop-filter: blur(10px);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 28px;
      transform-style: preserve-3d;
      transition: transform .2s ease, box-shadow .2s ease;
    }
    .card:hover{ box-shadow: 0 20px 50px rgba(0,0,0,0.45) }

    .title{ font-size: clamp(22px, 2.2vw, 32px); margin: 0 0 6px }
    .subtitle{ color:var(--muted); margin:0 0 18px; font-size: 0.95rem }

    /* Alerts */
    .alert{ padding:12px 14px; border-radius:12px; font-size:0.92rem; margin-bottom:14px; border:1px solid transparent }
    .alert-success{ color:#bff7ef; background: rgba(45,212,191,0.12); border-color: rgba(45,212,191,0.25)}
    .alert-error{ color:#ffd7d7; background: rgba(255,107,107,0.12); border-color: rgba(255,107,107,0.25)}
    .alert ul{ margin:6px 0 0 20px; padding:0 }

    /* Form */
    .field{ margin-bottom:16px }
    label{ display:block; font-weight:600; margin-bottom:8px }
    .input-wrap{ position:relative }
    .icon{
      position:absolute; left:12px; top:50%; transform: translateY(-50%);
      width:20px; height:20px; color:#b9c7db; opacity:.9
    }
    input[type="email"]{
      width:100%; padding:12px 12px 12px 40px; color:var(--text);
      background: rgba(9,14,30,0.6);
      border:1px solid var(--border); border-radius:12px;
      outline:none; transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }
    input[type="email"]::placeholder{ color:#9eb0c9 }
    input[type="email"]:focus{
      border-color: color-mix(in oklab, var(--accent), white 20%);
      box-shadow: 0 0 0 4px color-mix(in oklab, var(--accent) 30%, transparent);
      background: rgba(12,18,36,0.8);
    }

    .btn{
      width:100%; display:inline-flex; align-items:center; justify-content:center; gap:10px;
      padding:12px 16px; border:1px solid transparent; border-radius:12px; cursor:pointer;
      color:#0b1324; background: linear-gradient(180deg, var(--accent-2), #a8c9dc);
      font-weight:700; letter-spacing:.2px; box-shadow: 0 6px 16px rgba(60,110,150,.35);
      transition: transform .08s ease, box-shadow .2s ease, filter .2s ease;
    }
    .btn:hover{ box-shadow: 0 10px 24px rgba(60,110,150,.45); filter: saturate(1.05) }
    .btn:active{ transform: translateY(1px) }
    .btn[disabled]{ opacity:.6; cursor:not-allowed; box-shadow:none }

    .spinner{
      width:18px; height:18px; border-radius:999px;
      border:2px solid rgba(255,255,255,0.6); border-top-color:#0b1324; animation: spin .8s linear infinite; display:none;
    }
    @keyframes spin{ to{ transform: rotate(360deg)} }

    /* Footer links */
    .links{ display:flex; justify-content:space-between; gap:12px; margin-top:10px; font-size:.92rem }
    .links a{ color:#c5d6ea; text-decoration: none; border-bottom:1px dashed transparent }
    .links a:hover{ color:white; border-bottom-color: currentColor }

    /* Side panel */
    .panel{
      background: linear-gradient(180deg, rgba(255,255,255,0.06), rgba(255,255,255,0.03));
      backdrop-filter: blur(8px);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 28px;
      box-shadow: var(--shadow);
    }
    .bullets{ list-style:none; padding:0; margin:18px 0 0; color:var(--muted) }
    .bullets li{ display:flex; gap:10px; align-items:flex-start; margin:10px 0 }
    .dot{ width:10px; height:10px; border-radius:999px; background: var(--accent) }

    /* Subtle tilt on hover (desktop) */
    @media (pointer:fine){
      .tilt{ transform: perspective(900px) rotateX(var(--rx,0deg)) rotateY(var(--ry,0deg)); }
    }
  </style>
</head>
<body>
  <canvas id="bg3d" aria-hidden="true"></canvas>

  <main class="wrap">
    <div class="grid">
      <!-- Form card -->
      <section class="card tilt" id="formCard">
        <h1 class="title">Forgot your password?</h1>
        <p class="subtitle">Enter your email and we’ll send you a secure reset link.</p>

        {{-- Success flash --}}
        @if (session('status'))
          <div class="alert alert-success" role="alert" aria-live="polite">
            {{ session('status') }}
          </div>
        @endif

        {{-- Validation errors --}}
        @if ($errors->any())
          <div class="alert alert-error" role="alert" aria-live="assertive">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" id="forgotForm" novalidate>
          @csrf

          <div class="field">
            <label for="email">Email address</label>
            <div class="input-wrap">
              <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                      d="M3 7.5A2.5 2.5 0 0 1 5.5 5h13A2.5 2.5 0 0 1 21 7.5v9A2.5 2.5 0 0 1 18.5 19h-13A2.5 2.5 0 0 1 3 16.5v-9Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                      d="m3.5 7.5 7.9 5.2a2 2 0 0 0 2.2 0l6.9-4.6"/>
              </svg>
              <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autocomplete="email">
            </div>
          </div>

          <button class="btn" id="submitBtn" type="submit">
            <span>Send reset link</span>
            <span class="spinner" id="btnSpinner" aria-hidden="true"></span>
          </button>

          <div class="links">
            <a href="{{ route('login') }}">Back to login</a>
            <a href="{{ url('/') }}">Go home</a>
          </div>
        </form>
      </section>

  
    </div>
  </main>

  <!-- Three.js (background only, no user interaction required) -->
  <script src="https://unpkg.com/three@0.155.0/build/three.min.js"></script>
  <script>
    // --- Submit UX: disable & show spinner ---
    (function(){
      const form = document.getElementById('forgotForm');
      const btn  = document.getElementById('submitBtn');
      const spn  = document.getElementById('btnSpinner');
      if(!form || !btn || !spn) return;
      form.addEventListener('submit', function(){
        btn.setAttribute('disabled','true'); spn.style.display='inline-block';
      }, { once:true });
    })();

    // --- Subtle tilt effect on the card (desktop only) ---
    (function(){
      const el = document.getElementById('formCard');
      if(!window.matchMedia('(pointer:fine)').matches || !el) return;
      const damp = 40;
      el.addEventListener('mousemove', (e)=>{
        const r = el.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width;   // 0..1
        const y = (e.clientY - r.top)  / r.height;  // 0..1
        el.style.setProperty('--ry', ((x - .5) * 4) + 'deg');
        el.style.setProperty('--rx', (-(y - .5) * 4) + 'deg');
      });
      el.addEventListener('mouseleave', ()=>{
        el.style.setProperty('--ry', '0deg');
        el.style.setProperty('--rx', '0deg');
      });
    })();

    // --- THREE.js animated starfield + glow orbs ---
    (function(){
      const canvas = document.getElementById('bg3d');
      if(!canvas || !window.THREE) return;

      const renderer = new THREE.WebGLRenderer({ canvas, antialias:true, alpha:true });
      const scene = new THREE.Scene();
      const camera = new THREE.PerspectiveCamera(60, 2, 0.1, 1000);
      camera.position.set(0,0,60);

      // Resize handler
      function resize(){
        const w = window.innerWidth, h = window.innerHeight;
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.setSize(w, h, false);
        camera.aspect = w / h; camera.updateProjectionMatrix();
      }
      window.addEventListener('resize', resize, { passive:true }); resize();

      // Stars
      const stars = new THREE.Group(); scene.add(stars);
      const starCount = 1400;
      const positions = new Float32Array(starCount * 3);
      for(let i=0;i<starCount;i++){
        const r = 180 * Math.cbrt(Math.random()); // denser near center
        const theta = Math.random() * Math.PI * 2;
        const phi = Math.acos(2 * Math.random() - 1);
        positions[i*3+0] = r * Math.sin(phi) * Math.cos(theta);
        positions[i*3+1] = r * Math.sin(phi) * Math.sin(theta);
        positions[i*3+2] = r * Math.cos(phi);
      }
      const geo = new THREE.BufferGeometry();
      geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
      const mat = new THREE.PointsMaterial({
        size: 1.2, sizeAttenuation:true, color: 0xffffff, transparent:true, opacity:0.85
      });
      const points = new THREE.Points(geo, mat);
      stars.add(points);

      // Glow orbs (brand colors)
      const orbGeo = new THREE.SphereGeometry(16, 32, 32);
      const orbA = new THREE.Mesh(orbGeo, new THREE.MeshBasicMaterial({ color: 0x93c21c, transparent:true, opacity:0.12 }));
      const orbB = new THREE.Mesh(orbGeo, new THREE.MeshBasicMaterial({ color: 0xc0d8ea, transparent:true, opacity:0.12 }));
      orbA.position.set(-48, 18, -60);
      orbB.position.set( 52,-22, -70);
      scene.add(orbA, orbB);

      // Gentle motion & parallax
      let mx = 0, my = 0;
      window.addEventListener('mousemove', (e)=>{
        const cx = window.innerWidth / 2, cy = window.innerHeight / 2;
        mx = (e.clientX - cx) / cx; my = (e.clientY - cy) / cy;
      }, { passive:true });

      let t = 0;
      function tick(){
        t += 0.0025;
        stars.rotation.y += 0.0009;
        stars.rotation.x = Math.sin(t * 0.7) * 0.12;

        // Parallax camera drift
        camera.position.x += (mx * 5 - camera.position.x) * 0.02;
        camera.position.y += (-my * 3 - camera.position.y) * 0.02;
        camera.lookAt(0,0,0);

        // Orb breathing
        const s = 1 + Math.sin(t*2)*0.02;
        orbA.scale.setScalar(s);
        orbB.scale.setScalar(2 - s);

        renderer.render(scene, camera);
        requestAnimationFrame(tick);
      }
      tick();
    })();
  </script>
</body>
</html>
