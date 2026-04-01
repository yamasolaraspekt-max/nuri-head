<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login — QR + Face Verify</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="color-scheme" content="dark light" />
  <style>
    /* Subtle grid background */
    .bg-grid {
      background-image:
        radial-gradient(rgba(255,255,255,0.06) 1px, transparent 1px),
        linear-gradient(180deg, rgba(255,255,255,0.04), rgba(0,0,0,0));
      background-size: 16px 16px, 100% 100%;
      background-position: -1px -1px, 0 0;
    }
    /* Face overlay guidelines */
    .safe-box {
      box-shadow:
        0 0 0 2px var(--box-color) inset,
        0 0 0 200vmax rgba(0,0,0,0.35);
      transition: box-shadow .2s ease;
      border-radius: 1rem;
    }
    .corner::before, .corner::after {
      content: ""; position: absolute; width: 26px; height: 26px;
      border-color: var(--box-color); border-style: solid;
    }
    .corner.tl::before { left: -2px; top: -2px; border-width: 3px 0 0 3px; border-radius: 1rem 0 0 0; }
    .corner.tr::after  { right: -2px; top: -2px; border-width: 3px 3px 0 0; border-radius: 0 1rem 0 0; }
    .corner.bl::before { left: -2px; bottom: -2px; border-width: 0 0 3px 3px; border-radius: 0 0 0 1rem; }
    .corner.br::after  { right: -2px; bottom: -2px; border-width: 0 3px 3px 0; border-radius: 0 0 1rem 0; }
    .scanline {
      position: absolute; left: 0; right: 0; height: 2px; opacity: .8;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.35), transparent);
      animation: scan 2s linear infinite;
    }
    @keyframes scan {
      0% { top: 12%; } 100% { top: 88%; }
    }
    .countdown-pop {
      animation: pop .35s ease both;
    }
    @keyframes pop { 0% { transform: scale(.8); opacity: .4 } 100% { transform: scale(1); opacity: 1 } }

    /* Success check animation */
    .draw { stroke-dasharray: 1000; stroke-dashoffset: 1000; animation: dash 1.2s ease forwards; }
    @keyframes dash { to { stroke-dashoffset: 0; } }

    /* Hide controls from iOS fullscreen video */
    video::-webkit-media-controls { display:none !important; }
  </style>
</head>
<body class="min-h-screen bg-slate-950 bg-grid text-slate-100">
  <div class="max-w-5xl mx-auto px-4 py-8">
    <header class="flex items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-emerald-500/15 flex items-center justify-center">
          <svg class="h-6 w-6 text-emerald-400" viewBox="0 0 24 24" fill="none">
            <path d="M12 3l3 3-3 3-3-3 3-3Z" stroke="currentColor" stroke-width="1.5"/>
            <circle cx="12" cy="14" r="7" stroke="currentColor" stroke-width="1.5"/>
          </svg>
        </div>
        <div>
          <h1 class="text-xl font-semibold">Secure Login</h1>
          <p class="text-slate-400 text-sm">QR → Face Verify → Logged in</p>
        </div>
      </div>

      <!-- Demo Toggle -->
      <div class="flex items-center gap-3">
        <label class="text-sm text-slate-400">Demo Mode</label>
        <button id="demoToggle"
                class="relative inline-flex h-7 w-12 items-center rounded-full bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-400/60">
          <span class="sr-only">Toggle demo mode</span>
          <span class="dot translate-x-6 inline-block h-6 w-6 transform rounded-full bg-white transition"></span>
        </button>
      </div>
    </header>

    <!-- Stepper -->
    <nav class="mt-6 mb-4 grid grid-cols-3 gap-2">
      <div class="step-item flex items-center gap-3 p-3 rounded-xl bg-slate-800/60 border border-slate-700" data-step="qr">
        <div class="h-8 w-8 rounded-lg bg-slate-700/60 flex items-center justify-center">
          <span class="text-sm">1</span>
        </div>
        <div>
          <div class="text-sm font-medium">Scan QR</div>
          <div class="text-xs text-slate-400">From authenticator/badge</div>
        </div>
      </div>
      <div class="step-item flex items-center gap-3 p-3 rounded-xl bg-slate-900/40 border border-slate-800" data-step="face">
        <div class="h-8 w-8 rounded-lg bg-slate-800/60 flex items-center justify-center">
          <span class="text-sm">2</span>
        </div>
        <div>
          <div class="text-sm font-medium">Face Verify</div>
          <div class="text-xs text-slate-400">Align, then auto-capture</div>
        </div>
      </div>
      <div class="step-item flex items-center gap-3 p-3 rounded-xl bg-slate-900/40 border border-slate-800" data-step="success">
        <div class="h-8 w-8 rounded-lg bg-slate-800/60 flex items-center justify-center">
          <span class="text-sm">3</span>
        </div>
        <div>
          <div class="text-sm font-medium">Done</div>
          <div class="text-xs text-slate-400">Logged in securely</div>
        </div>
      </div>
    </nav>

    <!-- Card -->
    <section class="rounded-2xl border border-slate-800 bg-slate-900/60 overflow-hidden shadow-xl">
      <!-- Step: QR -->
      <div id="step-qr" class="p-6 grid md:grid-cols-2 gap-6">
        <div class="relative aspect-video rounded-xl bg-slate-950/60 border border-slate-800 overflow-hidden flex items-center justify-center">
          <!-- Placeholder / html5-qrcode container -->
          <div id="qrContainer" class="absolute inset-0 flex items-center justify-center">
            <!-- Demo placeholder visual -->
            <div class="text-center select-none">
              <div class="mx-auto w-40 h-40 rounded-2xl border-2 border-dashed border-slate-700/80 grid place-items-center">
                <svg viewBox="0 0 24 24" class="w-14 h-14 text-slate-600"><path fill="currentColor" d="M3 3h8v8H3V3m2 2v4h4V5H5m8-2h8v8h-8V3m2 2v4h4V5h-4M3 13h8v8H3v-8m2 2v4h4v-4H5m10 0h2v2h-2v-2m4 0h2v6h-6v-2h4v-4Z"/></svg>
              </div>
              <p class="mt-4 text-sm text-slate-400">Camera preview will appear here</p>
            </div>
          </div>
        </div>

        <div class="space-y-4">
          <h2 class="text-lg font-semibold">Scan your QR code</h2>
          <p class="text-slate-400 text-sm">In demo mode, click the simulate button below to “scan” a code. With the real scanner enabled, your device camera will read a QR and continue automatically.</p>

          <div class="flex items-center gap-3">
            <button id="btnSimulateScan"
                    class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-sm font-medium">
              Simulate Scan
            </button>
            <button id="btnStartQr"
                    class="px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 hover:bg-slate-700 text-sm font-medium">
              Use Camera Scanner
            </button>
          </div>

          <div id="qrStatus" class="text-xs text-slate-400">Status: Waiting for scan…</div>
          <div class="text-xs text-slate-500">
            Tip: You can theme this card with Tailwind to match your app branding.
          </div>
        </div>
      </div>

      <!-- Step: Face -->
      <div id="step-face" class="hidden p-6 grid md:grid-cols-2 gap-6">
        <div class="relative aspect-video rounded-xl bg-slate-950/60 border border-slate-800 overflow-hidden">
          <video id="faceVideo" autoplay playsinline muted class="absolute inset-0 w-full h-full object-cover"></video>
          <canvas id="faceCanvas" class="absolute inset-0"></canvas>

          <!-- Alignment overlay -->
          <div id="overlayBox" class="absolute inset-0 flex items-center justify-center pointer-events-none" style="--box-color:#ef4444;">
            <div class="relative w-[70%] max-w-[420px] aspect-[3/4] safe-box" id="safeBox">
              <div class="corner tl"></div><div class="corner tr"></div><div class="corner bl"></div><div class="corner br"></div>
              <div class="scanline"></div>
            </div>
          </div>

          <!-- Countdown -->
          <div id="countdown" class="hidden absolute inset-0 grid place-items-center">
            <div class="text-7xl font-bold text-white/95 drop-shadow-xl countdown-pop" id="countdownNum">2</div>
          </div>
        </div>

        <div class="space-y-4">
          <h2 class="text-lg font-semibold">Align your face</h2>
          <p class="text-slate-400 text-sm">Keep your face inside the guide. It turns <span class="text-emerald-400">green</span> when centered. Then we auto-capture after 2 seconds.</p>

          <div class="flex items-center gap-3 flex-wrap">
            <button id="btnStartFace"
                    class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-sm font-medium">
              Start Face Check
            </button>
            <button id="btnFlip"
                    class="px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 hover:bg-slate-700 text-sm font-medium">
              Flip Camera
            </button>
            <button id="btnRetry"
                    class="hidden px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 hover:bg-slate-700 text-sm font-medium">
              Retry
            </button>
          </div>

          <div id="faceStatus" class="text-xs text-slate-400">Status: Idle</div>
          <canvas id="snapshot" class="hidden mt-2 w-full max-w-xs rounded-lg border border-slate-800"></canvas>
        </div>
      </div>

      <!-- Step: Success -->
      <div id="step-success" class="hidden p-10">
        <div class="grid md:grid-cols-2 gap-8 items-center">
          <div class="order-2 md:order-1 space-y-4">
            <h2 class="text-2xl font-semibold">You’re logged in</h2>
            <p class="text-slate-400">This is a demo success screen with an animated SVG check. Swap it for your app’s post-login redirect.</p>
            <div class="flex items-center gap-3">
              <a href="#" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-sm font-medium">Continue</a>
              <a href="#" class="px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 hover:bg-slate-700 text-sm font-medium">Go to Dashboard</a>
            </div>
          </div>

          <div class="order-1 md:order-2">
            <svg viewBox="0 0 160 160" class="w-full max-w-sm mx-auto">
              <defs>
                <linearGradient id="g" x1="0" x2="1">
                  <stop offset="0%" stop-color="#34d399"/>
                  <stop offset="100%" stop-color="#10b981"/>
                </linearGradient>
              </defs>
              <circle cx="80" cy="80" r="68" fill="none" stroke="url(#g)" stroke-width="4" class="draw"/>
              <circle cx="80" cy="80" r="52" fill="none" stroke="url(#g)" stroke-width="4" class="draw" style="animation-delay:.15s"/>
              <path d="M55 82l17 17 34-42" fill="none" stroke="url(#g)" stroke-width="8" stroke-linecap="round" stroke-linejoin="round" class="draw" style="animation-delay:.3s"/>
            </svg>
          </div>
        </div>
      </div>
    </section>

    <footer class="mt-6 text-center text-xs text-slate-500">
      Frontend demo. Fake data by default. Toggle Demo Mode to wire up real devices.
    </footer>
  </div>

  <script>
    // ---------- Simple state machine ----------
    const steps = ["qr", "face", "success"];
    let currentStep = "qr";
    let demoMode = true;

    const el = (id) => document.getElementById(id);
    const stepEl = (key) => el(`step-${key}`);

    function go(step) {
      steps.forEach(s => {
        stepEl(s).classList.toggle("hidden", s !== step);
        document.querySelector(`.step-item[data-step="${s}"]`)?.classList.toggle("bg-slate-800/60", s === step);
        document.querySelector(`.step-item[data-step="${s}"]`)?.classList.toggle("border-slate-700", s === step);
        document.querySelector(`.step-item[data-step="${s}"]`)?.classList.toggle("bg-slate-900/40", s !== step);
        document.querySelector(`.step-item[data-step="${s}"]`)?.classList.toggle("border-slate-800", s !== step);
      });
      currentStep = step;
    }

    // ---------- Demo toggle ----------
    const demoToggle = el("demoToggle");
    const dot = demoToggle.querySelector(".dot");
    demoToggle.addEventListener("click", () => {
      demoMode = !demoMode;
      demoToggle.classList.toggle("bg-emerald-600", demoMode);
      demoToggle.classList.toggle("bg-slate-600", !demoMode);
      dot.style.transform = demoMode ? "translateX(1.5rem)" : "translateX(0)";
      // Stop any real scanners if toggled back to demo
      stopQrScanner();
      stopFace();
      go("qr");
      el("qrStatus").textContent = `Status: ${demoMode ? "Demo mode — click Simulate Scan" : "Ready to start camera scanner"}`;
    });

    // Initialize toggle style
    (function initDemoSwitch(){
      demoToggle.classList.add("bg-emerald-600");
      dot.style.transform = "translateX(1.5rem)";
    })();

    // ---------- QR step ----------
    let html5Qr; // will hold the scanner instance if used

    el("btnSimulateScan").addEventListener("click", () => {
      if (!demoMode) {
        toast("Demo mode is OFF. Turn it on or use the camera scanner.");
        return;
      }
      el("qrStatus").textContent = "Status: Simulating scan…";
      setTimeout(() => onQrScanned({payload:"DEMO-USER-123"}), 700);
    });

    el("btnStartQr").addEventListener("click", async () => {
      if (demoMode) {
        toast("Turn OFF Demo Mode to use the real QR scanner.");
        return;
      }
      await startQrScanner();
    });

    async function startQrScanner() {
      // Lazy-load html5-qrcode library
      await loadScript("https://unpkg.com/html5-qrcode@2.3.10/minified/html5-qrcode.min.js");
      const container = document.createElement("div");
      container.id = "html5qr";
      const host = document.getElementById("qrContainer");
      host.innerHTML = "";
      host.appendChild(container);

      // eslint-disable-next-line no-undef
      html5Qr = new Html5Qrcode("html5qr");
      const config = { fps: 12, qrbox: { width: 240, height: 240 } };
      try {
        el("qrStatus").textContent = "Status: Starting camera…";
        await html5Qr.start({ facingMode: "environment" }, config, decodedText => {
          onQrScanned({ payload: decodedText });
        });
        el("qrStatus").textContent = "Status: Scanner running. Point your QR code at the camera.";
      } catch (e) {
        console.error(e);
        el("qrStatus").textContent = "Status: Failed to start camera. Check permissions.";
        toast("Could not start QR scanner.");
      }
    }

    async function stopQrScanner() {
      if (html5Qr) {
        try { await html5Qr.stop(); } catch {}
        try { await html5Qr.clear(); } catch {}
        html5Qr = null;
        document.getElementById("qrContainer").innerHTML = `
          <div class="text-center select-none">
            <div class="mx-auto w-40 h-40 rounded-2xl border-2 border-dashed border-slate-700/80 grid place-items-center">
              <svg viewBox="0 0 24 24" class="w-14 h-14 text-slate-600"><path fill="currentColor" d="M3 3h8v8H3V3m2 2v4h4V5H5m8-2h8v8h-8V3m2 2v4h4V5h-4M3 13h8v8H3v-8m2 2v4h4v-4H5m10 0h2v2h-2v-2m4 0h2v6h-6v-2h4v-4Z"/></svg>
            </div>
            <p class="mt-4 text-sm text-slate-400">Camera preview will appear here</p>
          </div>`;
      }
    }

    function onQrScanned(result) {
      el("qrStatus").textContent = `Scanned: ${String(result.payload).slice(0,64)}`;
      // Fake validation delay
      setTimeout(() => {
        go("face");
        el("faceStatus").textContent = "Status: Ready to start face check.";
      }, 500);
    }

    // ---------- Face step ----------
    let faceStream = null;
    let facingUser = true;
    let faceLoop = null;
    let modelLoaded = false;
    let stableStart = null;
    const stableMsRequired = 2000;

    const overlayBox = el("overlayBox");
    const safeBox = el("safeBox");
    const video = el("faceVideo");
    const canvas = el("faceCanvas");
    const ctx = canvas.getContext("2d", { willReadFrequently: true });
    const snapshot = el("snapshot");
    const countdownWrap = el("countdown");
    const countdownNum = el("countdownNum");

    el("btnStartFace").addEventListener("click", () => {
      demoMode ? startFaceDemo() : startFaceReal();
    });
    el("btnFlip").addEventListener("click", async () => {
      facingUser = !facingUser;
      if (!demoMode) {
        await startFaceReal(); // restart with the other camera
      } else {
        toast(`Demo: flipping to ${facingUser ? "front" : "back"} simulated camera`);
      }
    });
    el("btnRetry").addEventListener("click", () => {
      go("face");
      el("btnRetry").classList.add("hidden");
      el("faceStatus").textContent = "Status: Ready to start face check.";
    });

    function setBoxColor(ok) {
      const color = ok ? "#10b981" : "#ef4444"; // emerald / red
      overlayBox.style.setProperty("--box-color", color);
    }

    // ---- DEMO PIPELINE ----
    async function startFaceDemo() {
      el("faceStatus").textContent = "Status: Demo aligning…";
      // Show a subtle animated background by drawing noise to canvas
      await startFakeVideoBackground();
      setBoxColor(false);
      stableStart = null;

      let t0 = performance.now();
      cancelAnimationFrame(faceLoop);
      const loop = (t) => {
        // Fake “alignment” goes green after ~1200ms
        if (t - t0 > 1200) {
          setBoxColor(true);
          if (!stableStart) stableStart = t;
          if (t - stableStart > stableMsRequired) {
            cancelAnimationFrame(faceLoop);
            startCountdownAndCapture(true);
            return;
          }
        } else {
          setBoxColor(false);
          stableStart = null;
        }
        faceLoop = requestAnimationFrame(loop);
      };
      faceLoop = requestAnimationFrame(loop);
    }

    async function startFakeVideoBackground() {
      // Fill video/canvas area with a gradient shimmer
      canvas.width = canvas.clientWidth;
      canvas.height = canvas.clientHeight;
      let x = 0;
      cancelAnimationFrame(faceLoop);
      const draw = () => {
        const grd = ctx.createLinearGradient(0,0,canvas.width,canvas.height);
        grd.addColorStop(0, "#0f172a"); grd.addColorStop(1, "#111827");
        ctx.fillStyle = grd; ctx.fillRect(0,0,canvas.width,canvas.height);
        ctx.globalAlpha = 0.12;
        ctx.fillStyle = "#fff";
        for (let i=0;i<80;i++){
          const w=4, h=4;
          ctx.fillRect((i*37 + x)%canvas.width, (i*53 + x)%canvas.height, w, h);
        }
        ctx.globalAlpha = 1;
        x += 2;
        requestAnimationFrame(draw);
      };
      requestAnimationFrame(draw);
    }

    // ---- REAL PIPELINE (optional) ----
    async function startFaceReal() {
      try {
        await ensureFaceModels();
        await startUserMedia();
        el("faceStatus").textContent = "Status: Detecting face…";
        canvas.width = canvas.clientWidth;
        canvas.height = canvas.clientHeight;

        // eslint-disable-next-line no-undef
        const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 });

        cancelAnimationFrame(faceLoop);
        const loop = async () => {
          try {
            // eslint-disable-next-line no-undef
            const det = await faceapi.detectSingleFace(video, options);
            const ok = evaluateAlignment(det);
            setBoxColor(ok);
            if (ok) {
              if (!stableStart) stableStart = performance.now();
              if (performance.now() - stableStart > stableMsRequired) {
                startCountdownAndCapture(false);
                return;
              }
            } else stableStart = null;
          } catch {}
          faceLoop = requestAnimationFrame(loop);
        };
        loop();
      } catch (e) {
        console.error(e);
        el("faceStatus").textContent = "Status: Could not start face detection.";
        toast("Face detection failed. Check camera permissions and model path.");
      }
    }

    function evaluateAlignment(det) {
      if (!det) return false;
      const vidW = video.videoWidth || 640, vidH = video.videoHeight || 480;
      const box = det.box || det; // { x,y,width,height }
      // Project a safe area roughly centered and sized for a face
      const safe = safeBox.getBoundingClientRect();
      const videoRect = video.getBoundingClientRect();
      // Convert face box from video pixels to DOM CSS pixels -> then compare to safe rect
      const scaleX = videoRect.width / vidW;
      const scaleY = videoRect.height / vidH;
      const face = {
        x: videoRect.left + box.x * scaleX,
        y: videoRect.top + box.y * scaleY,
        w: box.width * scaleX,
        h: box.height * scaleY
      };
      // Compute center
      const faceCx = face.x + face.w/2, faceCy = face.y + face.h/2;
      const safeCx = safe.left + safe.width/2, safeCy = safe.top + safe.height/2;

      const centerDist = Math.hypot(faceCx - safeCx, faceCy - safeCy);
      const maxCenterDist = Math.min(safe.width, safe.height) * 0.18;

      const sizeOk = face.w > safe.width*0.45 && face.w < safe.width*0.9;
      return centerDist < maxCenterDist && sizeOk;
    }

    async function ensureFaceModels() {
      if (modelLoaded) return;
      // Lazy-load face-api.js
      await loadScript("https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js");
      // Load models from /face-models (put model files there: tiny_face_detector_model-weights_manifest.json, etc.)
      const MODEL_URL = "/face-models"; // <<< Place your models here in Laravel public/
      // eslint-disable-next-line no-undef
      await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
      modelLoaded = true;
    }

    async function startUserMedia() {
      stopFace();
      const stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: facingUser ? "user" : "environment" },
        audio: false
      });
      faceStream = stream;
      video.srcObject = stream;
      await video.play();
    }

    function stopFace() {
      if (faceLoop) cancelAnimationFrame(faceLoop);
      if (faceStream) {
        faceStream.getTracks().forEach(t => t.stop());
        faceStream = null;
      }
      stableStart = null;
      setBoxColor(false);
      countdownWrap.classList.add("hidden");
    }

    // ---------- Countdown + capture ----------
    async function startCountdownAndCapture(isDemo) {
      el("faceStatus").textContent = "Status: Face aligned. Capturing soon…";
      countdownWrap.classList.remove("hidden");
      let n = 2;
      const tick = () => {
        countdownNum.textContent = String(n);
        countdownNum.classList.remove("countdown-pop");
        void countdownNum.offsetWidth; // reflow to restart animation
        countdownNum.classList.add("countdown-pop");
        if (n === 0) {
          countdownWrap.classList.add("hidden");
          captureFrame(isDemo);
        } else {
          n--;
          setTimeout(tick, 1000);
        }
      };
      tick();
    }

    function captureFrame(isDemo) {
      const target = snapshot;
      target.classList.remove("hidden");
      const w = 320, h = Math.floor(w * 3/4);
      target.width = w; target.height = h;

      const tmp = document.createElement("canvas");
      tmp.width = w; tmp.height = h;
      const tctx = tmp.getContext("2d");
      if (isDemo) {
        const grd = tctx.createLinearGradient(0,0,w,h);
        grd.addColorStop(0,"#111827"); grd.addColorStop(1,"#0f172a");
        tctx.fillStyle = grd; tctx.fillRect(0,0,w,h);
        tctx.fillStyle = "#34d399";
        tctx.font = "bold 16px ui-sans-serif,system-ui";
        tctx.fillText("DEMO SNAPSHOT", 16, 28);
        tctx.strokeStyle = "#34d399";
        tctx.strokeRect(60, 40, 200, 200);
        tctx.beginPath(); tctx.arc(160, 140, 60, 0, Math.PI*2); tctx.stroke();
      } else {
        const vw = video.videoWidth || 640, vh = video.videoHeight || 480;
        tctx.drawImage(video, (vw-w)/2, (vh-h)/2, w, h, 0, 0, w, h);
      }
      const sctx = target.getContext("2d");
      sctx.clearRect(0,0,w,h);
      sctx.drawImage(tmp, 0, 0);

      el("faceStatus").textContent = "Status: Captured. Verifying…";
      // Fake verification delay then success
      setTimeout(() => {
        stopFace();
        el("btnRetry").classList.remove("hidden");
        go("success");
      }, 700);
    }

    // ---------- Utilities ----------
    function loadScript(src) {
      return new Promise((resolve, reject) => {
        const s = document.createElement("script");
        s.src = src; s.async = true; s.onload = resolve; s.onerror = reject;
        document.head.appendChild(s);
      });
    }
    function toast(msg) {
      const t = document.createElement("div");
      t.textContent = msg;
      t.className = "fixed bottom-5 left-1/2 -translate-x-1/2 px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 text-sm shadow-lg";
      document.body.appendChild(t);
      setTimeout(() => t.remove(), 2200);
    }

    // Resize canvas to element size on layout changes
    const ro = new ResizeObserver(() => {
      canvas.width = canvas.clientWidth;
      canvas.height = canvas.clientHeight;
    });
    ro.observe(canvas);

    // Start on QR step
    go("qr");
  </script>
</body>
</html>
