<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login — QR → Face</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="color-scheme" content="dark light" />
  <style>
    .bg-grid {
      background-image:
        radial-gradient(rgba(255,255,255,0.06) 1px, transparent 1px),
        linear-gradient(180deg, rgba(255,255,255,0.04), rgba(0,0,0,0));
      background-size: 16px 16px, 100% 100%;
      background-position: -1px -1px, 0 0;
    }
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
    .scanline { position: absolute; left: 0; right: 0; height: 2px; opacity: .8;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.35), transparent);
      animation: scan 2s linear infinite; }
    @keyframes scan { 0% { top: 12%; } 100% { top: 88%; } }
    .countdown-pop { animation: pop .35s ease both; }
    @keyframes pop { 0% { transform: scale(.8); opacity: .4 } 100% { transform: scale(1); opacity: 1 } }
    .draw { stroke-dasharray: 1000; stroke-dashoffset: 1000; animation: dash 1.2s ease forwards; }
    @keyframes dash { to { stroke-dashoffset: 0; } }
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
          <p class="text-slate-400 text-sm">Scan QR → Face verify → Done</p>
        </div>
      </div>
      <p class="text-xs text-slate-400 hidden md:block">Requires HTTPS or http://localhost for camera.</p>
    </header>

    <!-- Stepper -->
    <nav class="mt-6 mb-4 grid grid-cols-3 gap-2">
      <div class="step-item flex items-center gap-3 p-3 rounded-xl bg-slate-800/60 border border-slate-700" data-step="qr">
        <div class="h-8 w-8 rounded-lg bg-slate-700/60 grid place-items-center"><span class="text-sm">1</span></div>
        <div><div class="text-sm font-medium">Scan QR</div><div class="text-xs text-slate-400">Camera permission</div></div>
      </div>
      <div class="step-item flex items-center gap-3 p-3 rounded-xl bg-slate-900/40 border border-slate-800" data-step="face">
        <div class="h-8 w-8 rounded-lg bg-slate-800/60 grid place-items-center"><span class="text-sm">2</span></div>
        <div><div class="text-sm font-medium">Face Verify</div><div class="text-xs text-slate-400">Auto-capture on align</div></div>
      </div>
      <div class="step-item flex items-center gap-3 p-3 rounded-xl bg-slate-900/40 border border-slate-800" data-step="success">
        <div class="h-8 w-8 rounded-lg bg-slate-800/60 grid place-items-center"><span class="text-sm">3</span></div>
        <div><div class="text-sm font-medium">Done</div><div class="text-xs text-slate-400">Logged in</div></div>
      </div>
    </nav>

    <!-- Card -->
    <section class="rounded-2xl border border-slate-800 bg-slate-900/60 overflow-hidden shadow-xl">
      <!-- Step: QR -->
      <div id="step-qr" class="p-6 grid md:grid-cols-2 gap-6">
        <div class="relative aspect-video rounded-xl bg-slate-950/60 border border-slate-800 overflow-hidden">
          <div id="qrContainer" class="absolute inset-0"></div>
        </div>
        <div class="space-y-4">
          <h2 class="text-lg font-semibold">Scan your QR code</h2>
          <p class="text-slate-400 text-sm">Click start to open the camera. When a QR is detected, you’ll switch to face verification automatically.</p>
          <div class="flex items-center gap-3">
            <button id="btnStartQr" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-sm font-medium">Start QR Scan</button>
            <button id="btnStopQr" class="px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 hover:bg-slate-700 text-sm font-medium hidden">Stop</button>
          </div>
          <div id="qrStatus" class="text-xs text-slate-400">Status: Waiting to start…</div>
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
          <p class="text-slate-400 text-sm">Keep your face inside the guide. It turns <span class="text-emerald-400">green</span> when centered and correctly sized. We capture automatically after 2 seconds.</p>

          <div class="flex items-center gap-3 flex-wrap">
            <button id="btnFlip" class="px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 hover:bg-slate-700 text-sm font-medium">Flip Camera</button>
            <button id="btnRetry" class="hidden px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 hover:bg-slate-700 text-sm font-medium">Retry</button>
          </div>

          <div id="faceStatus" class="text-xs text-slate-400">Status: Initializing…</div>
          <canvas id="snapshot" class="hidden mt-2 w-full max-w-xs rounded-lg border border-slate-800"></canvas>
        </div>
      </div>

      <!-- Step: Success -->
      <div id="step-success" class="hidden p-10">
        <div class="grid md:grid-cols-2 gap-8 items-center">
          <div class="order-2 md:order-1 space-y-4">
            <h2 class="text-2xl font-semibold">You’re logged in</h2>
            <p class="text-slate-400">Authentication complete. Replace this with a redirect in your app.</p>
            <div class="flex items-center gap-3">
              <a href="#" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-sm font-medium">Continue</a>
              <a href="#" class="px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 hover:bg-slate-700 text-sm font-medium">Go to Dashboard</a>
            </div>
          </div>
          <div class="order-1 md:order-2">
            <svg viewBox="0 0 160 160" class="w-full max-w-sm mx-auto">
              <defs>
                <linearGradient id="g" x1="0" x2="1">
                  <stop offset="0%" stop-color="#34d399"/><stop offset="100%" stop-color="#10b981"/>
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
      All frontend. No build tools. Uses CDN libs for QR and face detection.
    </footer>
  </div>

  <!-- Libraries (loaded dynamically with fallbacks) -->
  <script>
    // --- small helpers ---
    const el = (id) => document.getElementById(id);
    const steps = ["qr","face","success"];
    function go(step) {
      steps.forEach(s => {
        el(`step-${s}`).classList.toggle("hidden", s !== step);
        const item = document.querySelector(`.step-item[data-step="${s}"]`);
        item?.classList.toggle("bg-slate-800/60", s === step);
        item?.classList.toggle("border-slate-700", s === step);
        item?.classList.toggle("bg-slate-900/40", s !== step);
        item?.classList.toggle("border-slate-800", s !== step);
      });
    }
    function toast(msg) {
      const t = document.createElement("div");
      t.textContent = msg;
      t.className = "fixed bottom-5 left-1/2 -translate-x-1/2 px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 text-sm shadow-lg";
      document.body.appendChild(t);
      setTimeout(() => t.remove(), 2400);
    }
    function loadScript(src) {
      return new Promise((resolve, reject) => {
        const s = document.createElement("script");
        s.src = src; s.async = true; s.crossOrigin = "anonymous";
        s.onload = resolve;
        s.onerror = () => reject(new Error("Failed to load: " + src));
        document.head.appendChild(s);
      });
    }
    async function loadOneOf(urls) {
      let last;
      for (const u of urls) { try { await loadScript(u); return u; } catch(e){ last=e; } }
      throw last || new Error("All script sources failed.");
    }
  </script>

  <script>
    // ---------- QR step ----------
    let html5Qr = null;
    let qrFound = false;

    el("btnStartQr").addEventListener("click", startQrScanner);
    el("btnStopQr").addEventListener("click", stopQrScanner);

    async function startQrScanner() {
      if (html5Qr) return;
      try {
        await loadOneOf([
          "https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js",
          "https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/minified/html5-qrcode.min.js",
          "https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"
        ]);
      } catch (e) {
        console.error(e);
        el("qrStatus").textContent = "Status: Failed to load QR library.";
        toast("Failed to load QR scanner library. Check network/CSP.");
        return;
      }

      const host = el("qrContainer");
      host.innerHTML = "<div id='html5qr' class='w-full h-full'></div>";
      // eslint-disable-next-line no-undef
      html5Qr = new Html5Qrcode("html5qr");
      const config = {
        fps: 12,
        qrbox: v => { const m = Math.min(v.width, v.height); return { width: m * 0.55, height: m * 0.55 }; }
      };

      try {
        el("qrStatus").textContent = "Status: Starting camera…";
        el("btnStartQr").classList.add("hidden");
        el("btnStopQr").classList.remove("hidden");

        await html5Qr.start({ facingMode: "environment" }, config, decodedText => {
          onQrScanned(decodedText);
        });

        el("qrStatus").textContent = "Status: Scanner running. Point a QR at the camera.";
      } catch (e) {
        console.error("QR start error:", e);
        el("qrStatus").textContent = "Status: Failed to start camera (HTTPS/permissions).";
        el("btnStartQr").classList.remove("hidden");
        el("btnStopQr").classList.add("hidden");
        toast("Could not start QR scanner.");
      }
    }

    async function stopQrScanner() {
      if (!html5Qr) return;
      try { await html5Qr.stop(); } catch {}
      try { await html5Qr.clear(); } catch {}
      html5Qr = null;
      el("qrContainer").innerHTML = "";
      el("btnStartQr").classList.remove("hidden");
      el("btnStopQr").classList.add("hidden");
      el("qrStatus").textContent = "Status: Stopped.";
    }

    async function onQrScanned(text) {
      if (qrFound) return;
      qrFound = true;
      el("qrStatus").textContent = "Scanned: " + String(text).slice(0, 80);
      await stopQrScanner();
      go("face");
      startFaceStep(); // auto-start face camera
    }
  </script>

  <script>
    // ---------- Face step (MediaPipe Face Detection) ----------
    let faceDetectionInstance = null;
    let faceModelsReady = false;
    let faceStream = null;
    let facingUser = true;
    let countdownTimer = null;
    let stableStart = null;
    const stableMsRequired = 2000;

    const video = el("faceVideo");
    const canvas = el("faceCanvas");
    const ctx = canvas.getContext("2d", { willReadFrequently: true });
    const overlayBox = el("overlayBox");
    const safeBox = el("safeBox");
    const snapshot = el("snapshot");
    const countdownWrap = el("countdown");
    const countdownNum = el("countdownNum");

    el("btnFlip").addEventListener("click", async () => {
      facingUser = !facingUser;
      await startUserMedia();
    });
    el("btnRetry").addEventListener("click", () => {
      go("face");
      startFaceStep();
    });

    function setBoxColor(ok) {
      overlayBox.style.setProperty("--box-color", ok ? "#10b981" : "#ef4444");
    }

    async function startFaceStep() {
      try {
        await ensureFaceModels();
        await startUserMedia();
        el("faceStatus").textContent = "Status: Detecting face…";
        startFaceLoop();
      } catch (e) {
        console.error(e);
        el("faceStatus").textContent = "Status: Could not start face detection (permissions or network).";
        toast("Face detection failed.");
      }
    }

    async function ensureFaceModels() {
      if (faceModelsReady) return;

      // Load MediaPipe Face Detection (no models to host, it fetches from the CDN via locateFile)
      await loadOneOf([
        "https://cdn.jsdelivr.net/npm/@mediapipe/face_detection/face_detection.js",
        "https://unpkg.com/@mediapipe/face_detection/face_detection.js"
      ]);

      // Create instance
      // global is FaceDetection.FaceDetection
      // eslint-disable-next-line no-undef
      faceDetectionInstance = new FaceDetection.FaceDetection({
        locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/face_detection/${file}`
      });
      faceDetectionInstance.setOptions({
        model: "short",                  // lightweight model
        minDetectionConfidence: 0.6
      });
      faceDetectionInstance.onResults(onFaceResults);

      faceModelsReady = true;
    }

    async function startUserMedia() {
      stopFace(); // stop any prior tracks/loop
      const stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: facingUser ? "user" : "environment" },
        audio: false
      });
      faceStream = stream;
      video.srcObject = stream;
      await video.play();

      // sync canvas size to element CSS box
      canvas.width = canvas.clientWidth;
      canvas.height = canvas.clientHeight;
    }

    let faceLoopRunning = false;
    async function startFaceLoop() {
      if (!faceDetectionInstance) return;
      faceLoopRunning = true;

      const loop = async () => {
        if (!faceLoopRunning) return;
        try {
          await faceDetectionInstance.send({ image: video });
        } catch {}
        requestAnimationFrame(loop);
      };
      loop();
    }

    function onFaceResults(results) {
      // Draw a subtle video background to canvas (optional)
      try {
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
      } catch {}

      const dets = results && results.detections ? results.detections : [];
      const det = dets[0];
      let ok = false;

      if (det) {
        const face = extractFaceBox(det);
        if (face) {
          ok = evaluateAlignment(face);
        }
      }

      setBoxColor(ok);

      if (ok) {
        if (!stableStart) stableStart = performance.now();
        if (performance.now() - stableStart > stableMsRequired) {
          startCountdownAndCapture();
        }
      } else {
        stableStart = null;
      }
    }

    function extractFaceBox(det) {
      // Try different shapes MediaPipe may provide
      // Prefer absolute (pixels), else convert relative (0..1) to pixels
      const videoRect = video.getBoundingClientRect();
      const abs = det.boundingBox || det.rect || null;
      const rel = det.locationData && det.locationData.relativeBoundingBox;

      if (abs && typeof abs.xCenter === "number") {
        return {
          x: abs.xCenter - abs.width / 2,
          y: abs.yCenter - abs.height / 2,
          w: abs.width,
          h: abs.height,
          absolute: true
        };
      }
      if (rel && typeof rel.xMin === "number") {
        const x = videoRect.left + rel.xMin * videoRect.width;
        const y = videoRect.top + rel.yMin * videoRect.height;
        const w = rel.width * videoRect.width;
        const h = rel.height * videoRect.height;
        return { x, y, w, h, absolute: true };
      }
      return null;
    }

    function evaluateAlignment(face) {
      // face.x/y/w/h are in page CSS pixels
      const safe = safeBox.getBoundingClientRect();
      const faceCx = face.x + face.w / 2;
      const faceCy = face.y + face.h / 2;
      const safeCx = safe.left + safe.width / 2;
      const safeCy = safe.top + safe.height / 2;

      const centerDist = Math.hypot(faceCx - safeCx, faceCy - safeCy);
      const maxCenterDist = Math.min(safe.width, safe.height) * 0.18;
      const sizeOk = face.w > safe.width * 0.45 && face.w < safe.width * 0.9;
      return centerDist < maxCenterDist && sizeOk;
    }

    async function startCountdownAndCapture() {
      // prevent running multiple times
      if (countdownTimer) return;
      el("faceStatus").textContent = "Status: Face aligned. Capturing…";
      countdownWrap.classList.remove("hidden");

      let n = 2;
      const tick = () => {
        countdownNum.textContent = String(n);
        countdownNum.classList.remove("countdown-pop");
        void countdownNum.offsetWidth;
        countdownNum.classList.add("countdown-pop");

        if (n === 0) {
          countdownWrap.classList.add("hidden");
          captureFrame();
          return;
        }
        n--;
        countdownTimer = setTimeout(tick, 1000);
      };
      tick();
    }

    function captureFrame() {
      clearTimeout(countdownTimer);
      countdownTimer = null;

      const w = 320, h = Math.floor(w * 3 / 4);
      const tmp = document.createElement("canvas");
      tmp.width = w; tmp.height = h;
      const tctx = tmp.getContext("2d");
      const vw = video.videoWidth || canvas.width, vh = video.videoHeight || canvas.height;
      tctx.drawImage(video, (vw - w) / 2, (vh - h) / 2, w, h, 0, 0, w, h);

      const sctx = snapshot.getContext("2d");
      snapshot.width = w; snapshot.height = h;
      snapshot.classList.remove("hidden");
      sctx.clearRect(0, 0, w, h);
      sctx.drawImage(tmp, 0, 0);

      el("faceStatus").textContent = "Status: Captured.";
      stopFace();
      el("btnRetry").classList.remove("hidden");
      go("success");
    }

    function stopFace() {
      faceLoopRunning = false;
      if (faceStream) {
        faceStream.getTracks().forEach(t => t.stop());
        faceStream = null;
      }
      stableStart = null;
      setBoxColor(false);
      countdownWrap.classList.add("hidden");
    }

    // Keep face canvas sized to element
    const ro = new ResizeObserver(() => {
      canvas.width = canvas.clientWidth;
      canvas.height = canvas.clientHeight;
    });
    ro.observe(canvas);

    // Start at QR step
    go("qr");
  </script>
</body>
</html>
