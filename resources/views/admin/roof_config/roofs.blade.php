<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Dachstuhl Wizard – Maßketten</title>
<style>
  :root{
    --navy:#091a2e; --bg:#0b1524; --card:#0f2136; --line:#1f3452; --muted:#9fb3cc;
    --text:#e6edf3; --accent:#4aa3ff;
  }
  *{box-sizing:border-box}
  html,body{height:100%;margin:0;background:var(--bg);color:var(--text);font:14px/1.5 Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif}
  .app{min-height:100%;display:grid;grid-template-rows:auto 1fr}
  .top{display:flex;align-items:center;gap:10px;padding:10px 16px;background:var(--navy);color:#fff;height:56px;box-shadow:0 2px 0 #081426}
  .brand{font-weight:800}
  .steps{display:flex;gap:8px;margin-left:12px;flex-wrap:wrap}
  .step{padding:6px 10px;border-radius:999px;border:1px solid #ffffff33;background:#ffffff12;color:#fff;font-weight:700;cursor:pointer}
  .step.active{background:#fff;color:var(--navy);border-color:#fff}
  .actions{margin-left:auto;display:flex;gap:10px}
  .btn{border:1px solid transparent;border-radius:999px;padding:8px 12px;font-weight:700;cursor:pointer}
  .btn.primary{background:#ffffff;color:var(--navy)}
  .btn.ghost{background:transparent;border:1px solid #ffffff44;color:#fff}

  .main{display:grid;grid-template-columns:360px 1fr;min-height:calc(100vh - 56px)}
  .sidebar{background:var(--card);border-right:1px solid var(--line);padding:12px;overflow:auto}
  .panel{padding-bottom:8px;margin-bottom:10px;border-bottom:1px dashed #224}
  .panel h3{margin:4px 0 10px;font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.12em}
  .grid{display:grid;grid-template-columns:repeat(2,minmax(120px,1fr));gap:10px}
  .field{display:flex;flex-direction:column;gap:6px}
  .label{font-size:11px;color:var(--muted)}
  input[type="text"]{padding:10px 12px;border:1px solid var(--line);border-radius:10px;background:#0e2238;color:#e6edf3;outline:none}
  input:focus{border-color:var(--accent);box-shadow:0 0 0 3px #4aa3ff33}
  .meta{margin-top:8px;padding-top:8px;border-top:1px solid var(--line);font:12px/1.4 ui-monospace,Menlo,Consolas,monospace;color:#c7d7ea}

  .tog{display:flex;flex-direction:column;gap:8px}
  .chk{display:flex;align-items:center;gap:8px}
  .pill{font-size:11px;padding:2px 8px;border-radius:999px;border:1px solid #345;background:#0d1c30;color:#cfe2ff}

  .stage{position:relative;overflow:hidden;background:linear-gradient(#0b1524,#0e1b2e)}
  canvas{display:block;width:100%;height:100%}
  .legend{position:absolute;left:12px;bottom:12px;background:#0b1628d0;border:1px solid var(--line);border-radius:10px;padding:6px 8px;font-size:12px;color:#cfe2ff;backdrop-filter:blur(6px)}
  .row{display:flex;align-items:center;gap:6px}
  .sw{width:12px;height:12px;border-radius:3px;border:1px solid #3a5a82}

  #toolbar {
    position: absolute;
    top: 0;
    right: 0;
    width: 180px;
    height: 100%;
    background: #1c2a38;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    padding: 10px;
    gap: 10px;
    border-left: 2px solid #2f3d4f;
    z-index: 200;
    }

    #toolbar h3 {
    text-align: center;
    margin: 10px 0;
    font-size: 16px;
    border-bottom: 1px solid #444;
    padding-bottom: 5px;
    }

    .tool-btn {
    background: #2f3d4f;
    border: none;
    color: #fff;
    padding: 10px;
    cursor: pointer;
    text-align: left;
    border-radius: 5px;
    transition: background 0.2s;
    }

    .tool-btn:hover {
    background: #3f5066;
    }


    #window-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 10px;
    }
    .window-card {
    background: #2a3b4f;
    padding: 8px;
    border-radius: 5px;
    font-size: 13px;
    }
    .window-card h4 {
    margin: 0 0 5px;
    font-size: 14px;
    color: #fff;
    }
    .window-card label {
    display: block;
    margin: 4px 0;
    color: #ddd;
    }
    .window-card input {
    width: 60px;
    margin-left: 5px;
    }
    .window-card button {
    margin-top: 4px;
    background: #444;
    color: #fff;
    border: none;
    padding: 4px 6px;
    cursor: pointer;
    border-radius: 3px;
    }
    .window-card button:hover {
    background: #666;
    }


</style>
</head>
<body>

    <!-- Right Toolbar -->
<div id="toolbar">
  <h3>Tools</h3>
  <button id="addWindowBtn">➕ Add Window</button>
  <ul id="window-list"></ul>

  <button class="tool-btn" data-tool="chimney">➕ Chimney</button>
  <button class="tool-btn" data-tool="solar">☀️ Solar Panel</button>
  <button class="tool-btn" data-tool="door">🚪 Door</button>
</div>



<div class="app">
  <div class="top">
    <div class="brand">Dachstuhl Wizard</div>
    <div class="steps" id="steps">
      <button class="step active" data-step="1">1 Mauerwerk</button>
      <button class="step" data-step="2">2 Pfetten</button>
      <button class="step" data-step="3">3 Sparren</button>
      <button class="step" data-step="4">4 Konter</button>
      <button class="step" data-step="5">5 Eindeck</button>
    </div>
    <div class="actions">
      <button id="prev" class="btn ghost">Zurück</button>
      <button id="next" class="btn primary">Weiter</button>
    </div>
  </div>

  <div class="main">
    <aside class="sidebar">
      <div class="panel">
        <h3>Eingaben</h3>
        <div class="grid">
          <div class="field"><div class="label">Länge L (m)</div><input id="L" value="10"></div>
          <div class="field"><div class="label">Spannweite S (m)</div><input id="S" value="8"></div>
          <div class="field"><div class="label">Traufhöhe HT (m)</div><input id="HT" value="3.00"></div>
          <div class="field"><div class="label">Mauerstärke tW (m)</div><input id="tW" value="0.36"></div>
          <div class="field"><div class="label">Dachneigung α (°)</div><input id="alpha" value="35"></div>
          <div class="field"><div class="label">Ortgang UL (m)</div><input id="UL" value="0.40"></div>
          <div class="field"><div class="label">Ortgang UR (m)</div><input id="UR" value="0.60"></div>
          <div class="field"><div class="label">Traufüberstand UT (m)</div><input id="UT" value="0.30"></div>

          <div class="field"><div class="label">Firstpfette Bf (m)</div><input id="Bf" value="0.12"></div>
          <div class="field"><div class="label">Firstpfette Hf (m)</div><input id="Hf" value="0.20"></div>
          <div class="field"><div class="label">Fußpfette Bfu (m)</div><input id="Bfu" value="0.12"></div>
          <div class="field"><div class="label">Fußpfette Hfu (m)</div><input id="Hfu" value="0.20"></div>

          <div class="field"><div class="label">Sparren Br (m)</div><input id="Br" value="0.08"></div>
          <div class="field"><div class="label">Sparren Hr (m)</div><input id="Hr" value="0.16"></div>
          <div class="field"><div class="label">Raster s (m)</div><input id="sdes" value="0.60"></div>
          <div class="field">
            <div class="label">Rafter positions (m, comma separated)</div>
            <input id="rafters" type="text" value="">
         </div> 
          <div class="field"><div class="label">Konterlatte Bk (m)</div><input id="Bk" value="0.04"></div>
          <div class="field"><div class="label">Konterlatte Hk (m)</div><input id="Hk" value="0.06"></div>
          <div class="field"><div class="label">Eindecklatte Be (m)</div><input id="Be" value="0.04"></div>
          <div class="field"><div class="label">Eindecklatte He (m)</div><input id="He" value="0.04"></div>
          <div class="field"><div class="label">Lattenweite eSpacing (m)</div><input id="eSpacing" value="0.36"></div>
        </div>
        <div id="debug" class="meta">–</div>
      </div>

      <div class="panel">
        <h3>Maßketten</h3>
        <div class="tog">
          <label class="chk"><input type="checkbox" id="dimAll"> <span class="pill">Alle Maße ein/aus</span></label>
          <label class="chk"><input type="checkbox" class="dimchk" data-layer="Exterior" checked> Außenmaße (S, L)</label>
          <label class="chk"><input type="checkbox" class="dimchk" data-layer="Vertical" checked> Vertikal (HT, HF, ΔY)</label>
          <label class="chk"><input type="checkbox" class="dimchk" data-layer="First" checked> Firstlänge</label>
          <label class="chk"><input type="checkbox" class="dimchk" data-layer="SparrenLen" checked> Sparrenlänge</label>
          <label class="chk"><input type="checkbox" class="dimchk" data-layer="Rafter"> Sparrenabstand (s_final)</label>
          <label class="chk"><input type="checkbox" class="dimchk" data-layer="Lattung"> Eindecklattenabstand (eSpacing)</label>
          <label class="chk"><input type="checkbox" class="dimchk" data-layer="Ortgang"> Ortgang (UL/UR)</label>
        </div>
      </div>
    </aside>

    <section class="stage" id="stage">
      <canvas id="c"></canvas>
      <div class="legend">
        <div class="row"><span class="sw" style="background:#8b5a2b"></span>Sparren</div>
        <div class="row"><span class="sw" style="background:#e8c39e"></span>Konter</div>
        <div class="row"><span class="sw" style="background:#6f6f6f"></span>Eindeck</div>
      </div>
    </section>
  </div>
</div>
 <script type="module">
(async () => {
/* ============================================================
   Load THREE + OrbitControls + TransformControls (with fallbacks)
============================================================ */
const cdns = [
  {
    three:     "https://cdn.jsdelivr.net/npm/three@0.161.0/build/three.module.js",
    orbit:     "https://cdn.jsdelivr.net/npm/three@0.161.0/examples/jsm/controls/OrbitControls.js",
    transform: "https://cdn.jsdelivr.net/npm/three@0.161.0/examples/jsm/controls/TransformControls.js",
  },
  {
    three:     "https://unpkg.com/three@0.161.0/build/three.module.js",
    orbit:     "https://unpkg.com/three@0.161.0/examples/jsm/controls/OrbitControls.js",
    transform: "https://unpkg.com/three@0.161.0/examples/jsm/controls/TransformControls.js",
  },
  {
    three:     "https://esm.sh/three@0.161.0/build/three.module.js",
    orbit:     "https://esm.sh/three@0.161.0/examples/jsm/controls/OrbitControls.js",
    transform: "https://esm.sh/three@0.161.0/examples/jsm/controls/TransformControls.js",
  },
];

let THREE, OrbitControls, TransformControls;
for (const cdn of cdns) {
  try {
    THREE = await import(cdn.three);
    ({ OrbitControls } = await import(cdn.orbit));
    ({ TransformControls } = await import(cdn.transform));
    break;
  } catch (err) { console.warn("CDN failed, trying next…", err); }
}
if (!THREE || !OrbitControls || !TransformControls) {
  alert("THREE/Controls konnten nicht geladen werden.");
  throw new Error("CDN imports failed");
}

/* ============================================================
   Helpers
============================================================ */
const $  = s => document.querySelector(s);
const $$ = s => Array.from(document.querySelectorAll(s));
const num   = (v,d)=>{ const x=parseFloat(String(v??"").replace(",",".").trim()); return Number.isFinite(x)?x:d; };
const clamp = (x,a,b)=>Math.min(b,Math.max(a,x));
const rad   = d=> d*Math.PI/180;
const fmt   = m=> (Math.round(m*1000)/1000).toLocaleString("de-DE",{minimumFractionDigits:3})+" m";
const dbg   = m=> { const el=$("#debug"); if(el) el.textContent=m; };

/* ============================================================
   THREE basics
============================================================ */
const canvas   = $("#c");
const stage    = $("#stage");
const renderer = new THREE.WebGLRenderer({ canvas, antialias:true });
renderer.setPixelRatio(Math.min(2, window.devicePixelRatio));

const scene  = new THREE.Scene(); scene.background = new THREE.Color("#0d1b2a");
const camera = new THREE.PerspectiveCamera(55, 1, 0.1, 10000);
const controls = new OrbitControls(camera, renderer.domElement);
function resetCam(){ camera.position.set(14,10,18); controls.target.set(0,3,0); controls.update(); }
resetCam();

scene.add(new THREE.AmbientLight(0xffffff,0.9));
const sun = new THREE.DirectionalLight(0xffffff,1.0); sun.position.set(40,60,30); scene.add(sun);
const grid = new THREE.GridHelper(200,200,0x3b5e8a,0x223b5a);
grid.material.transparent = true; grid.material.opacity = 0.35; scene.add(grid);

/* Resize */
const ro = new ResizeObserver(()=>{
  const w=stage.clientWidth, h=stage.clientHeight;
  if (w>0 && h>0){ renderer.setSize(w,h,false); camera.aspect=w/h; camera.updateProjectionMatrix(); }
});
ro.observe(stage);

/* ============================================================
   Scene graph
============================================================ */
const ROOT=new THREE.Group(); scene.add(ROOT);
const G={};
[
 "Mauerwerk","Pfetten","Sparren","Konter","Eindeck","Overlay",
 "Dims:Exterior","Dims:Vertical","Dims:First","Dims:SparrenLen","Dims:Rafter","Dims:Lattung","Dims:Ortgang"
].forEach(n=>{ G[n]=new THREE.Group(); G[n].name=n; ROOT.add(G[n]); });

/* ============================================================
   Materials
============================================================ */
const MAT = {
  wall:  new THREE.MeshStandardMaterial({color:"#ffffff", roughness:0.92}),
  wallHighlight: new THREE.MeshStandardMaterial({ color:"#a7c8ff", roughness:0.9 }),
  wood:  new THREE.MeshStandardMaterial({color:"#8b5a2b", roughness:0.85}),
  woodHighlight: new THREE.MeshStandardMaterial({ color:"#ffcc00", roughness:0.6 }),
  konter:new THREE.MeshStandardMaterial({color:"#e8c39e", roughness:0.75}),
  batten:new THREE.MeshStandardMaterial({color:"#6f6f6f", roughness:0.7}),
  overlay:new THREE.MeshStandardMaterial({color:"#4aa3ff", transparent:true, opacity:0.07, side:THREE.DoubleSide}),
  line:  (hex)=> new THREE.LineBasicMaterial({color:hex}),
  arrow: (hex)=> new THREE.MeshBasicMaterial({color:hex}),
};
const DCLR = { Exterior:0xffffff, Vertical:0x00e5ff, First:0x9ad1ff, SparrenLen:0x00ff85, Rafter:0xffd400, Lattung:0xff8c00, Ortgang:0xff00c8 };

/* ============================================================
   Dispose + Fit
============================================================ */
function dispose(group){
  group.traverse(o=>{
    if (o.isMesh || o.isInstancedMesh || o.isLine){
      o.geometry?.dispose?.();
      if (Array.isArray(o.material)) o.material.forEach(m=>m?.dispose?.());
      else o.material?.dispose?.();
    }
  });
  while(group.children.length) group.remove(group.children[0]);
}
function fit(stepMax){
  const order=["Mauerwerk","Pfetten","Sparren","Konter","Eindeck","Overlay"];
  const box=new THREE.Box3(); let has=false;
  for(const n of order){
    const idx={Mauerwerk:1,Pfetten:2,Sparren:3,Konter:4,Eindeck:5,Overlay:5}[n];
    if(idx>stepMax) continue;
    const b=new THREE.Box3().setFromObject(G[n]); if(!Number.isFinite(b.min.x)) continue;
    has?box.union(b):box.copy(b), has=true;
  }
  if(!has){ resetCam(); return; }
  const s=new THREE.Vector3(); box.getSize(s);
  const c=new THREE.Vector3(); box.getCenter(c);
  const d=Math.max(s.x,s.y,s.z)*1.6+6;
  camera.position.set(c.x+d*0.6, c.y+d*0.5, c.z+d*0.8);
  controls.target.copy(c); controls.update();
}

/* ============================================================
   Parameters
============================================================ */
function P(){
  const L=num($("#L")?.value,10), S=num($("#S")?.value,8), HT=num($("#HT")?.value,3.0), tW=num($("#tW")?.value,0.36);
  let a=num($("#alpha")?.value,35); a = clamp(a,5,75); const alpha=rad(a);
  const UL=num($("#UL")?.value,0.40), UR=num($("#UR")?.value,0.60), UT=num($("#UT")?.value,0.30);
  const Bf=num($("#Bf")?.value,0.12), Hf=num($("#Hf")?.value,0.20), Bfu=num($("#Bfu")?.value,0.12), Hfu=num($("#Hfu")?.value,0.20);
  const Br=num($("#Br")?.value,0.08), Hr=num($("#Hr")?.value,0.16), sdes=clamp(num($("#sdes")?.value,0.6),0.1,2.0);
  const Bk=num($("#Bk")?.value,0.04), Hk=num($("#Hk")?.value,0.06), Be=num($("#Be")?.value,0.04), He=num($("#He")?.value,0.04);
  const eSpacing=clamp(num($("#eSpacing")?.value,0.36),0.05,2);
  const HF = HT + Math.tan(alpha)*(S/2);
  const Ltr = L + UL + UR;
  const Leff = Math.max(0.01, L - 2*tW);
  const nI = Math.max(1, Math.round(Leff/sdes));
  const s_final = Leff / nI;
  const zMin=-UL, zMax=L+UR;
  return {L,S,HT,HF,aDeg:a,alpha,tW,UL,UR,UT,Bf,Hf,Bfu,Hfu,Br,Hr,Bk,Hk,Be,He,eSpacing,Ltr,Leff,s_final,zMin,zMax};
}

/* ============================================================
   Bases
============================================================ */
const Zaxis = new THREE.Vector3(0,0,1);
const YAXIS = new THREE.Vector3(0,1,0);
function basisRafter(u){
  let uN=u.clone(); if(uN.lengthSq()<1e-9) uN.set(1,0.2,0); uN.normalize();
  let n=new THREE.Vector3().crossVectors(uN,Zaxis); if(n.lengthSq()<1e-9) n.set(0,1,0); n.normalize(); if(n.y<0) n.multiplyScalar(-1);
  const z=new THREE.Vector3().crossVectors(n,uN).normalize();
  const q=new THREE.Quaternion().setFromRotationMatrix(new THREE.Matrix4().makeBasis(n,uN,z));
  return {u:uN,n,z,q};
}
function basisLatten(n){
  let nN=n.clone(); if(nN.lengthSq()<1e-9) nN.set(0,1,0); nN.normalize();
  const z=new THREE.Vector3().crossVectors(nN,Zaxis).normalize();
  const q=new THREE.Quaternion().setFromRotationMatrix(new THREE.Matrix4().makeBasis(nN,Zaxis,z));
  return {q};
}

/* ============================================================
   Geometry helpers
============================================================ */
function makeRafterGeometry(len, Hr, Br, alpha, sideSign){
  const g = new THREE.BoxGeometry(Hr, len, Br, 1, 1, 1);
  const pos = g.getAttribute("position");
  const halfLen = len/2, m = Math.tan(alpha);
  for(let i=0;i<pos.count;i++){
    const x = pos.getX(i), y = pos.getY(i);
    if (y > halfLen - 1e-6){
      const shift = sideSign * m * (x + Hr/2);
      const yNew = halfLen - shift;
      pos.setY(i, Math.min(halfLen, yNew));
    }
  }
  g.computeVertexNormals();
  return g;
}

/* ============================================================
   Dim helpers
============================================================ */
function labelSprite(text, color=0xffffff){
  const pad=12, fs=44;
  const c=document.createElement("canvas"); c.width=1024; c.height=256;
  const ctx=c.getContext("2d");
  ctx.clearRect(0,0,c.width,c.height);
  ctx.font=`${fs}px Inter, system-ui, Arial`; ctx.textBaseline="middle";
  const tw = ctx.measureText(text).width;
  ctx.globalAlpha=0.15; ctx.fillStyle="#000";
  ctx.fillRect((c.width-tw)/2 - pad, (c.height-fs)/2 - 8, tw + 2*pad, fs + 16);
  ctx.globalAlpha=1; ctx.fillStyle=`#${color.toString(16).padStart(6,"0")}`;
  ctx.fillText(text, (c.width-tw)/2, c.height/2);
  const tex=new THREE.CanvasTexture(c); tex.anisotropy=4; tex.needsUpdate=true; tex.encoding=THREE.sRGBEncoding;
  const mat=new THREE.SpriteMaterial({map:tex, transparent:true});
  const spr=new THREE.Sprite(mat);
  const worldW = Math.max(0.5, tw/200); const worldH = worldW*(c.height/c.width);
  spr.scale.set(worldW, worldH, 1);
  return spr;
}
function line(a,b, color){ return new THREE.Line(new THREE.BufferGeometry().setFromPoints([a,b]), MAT.line(color)); }
function arrowHead(pos, dir, color, size=0.08){
  const cone = new THREE.ConeGeometry(size*0.35, size, 8);
  const mesh = new THREE.Mesh(cone, MAT.arrow(color));
  const q = new THREE.Quaternion().setFromUnitVectors(YAXIS, dir.clone().normalize());
  mesh.quaternion.copy(q); mesh.position.copy(pos); return mesh;
}
function dimChain(a, b, nrm, off, color, text){
  const grp=new THREE.Group();
  const dir = b.clone().sub(a); const L = dir.length(); if(L<1e-6) return grp;
  const u = dir.clone().normalize(); const n = nrm.clone().normalize();
  const A = a.clone().add(n.clone().multiplyScalar(off));
  const B = b.clone().add(n.clone().multiplyScalar(off));
  grp.add(line(a,A,color), line(b,B,color), line(A,B,color));
  const ah=0.12;
  grp.add(arrowHead(A.clone().add(u.clone().multiplyScalar(+ah)), u, color, 0.10));
  grp.add(arrowHead(B.clone().add(u.clone().multiplyScalar(-ah)), u.clone().multiplyScalar(-1), color, 0.10));
  const lbl = labelSprite(text, color);
  const mid = A.clone().add(B).multiplyScalar(0.5).add(n.clone().multiplyScalar(0.05));
  lbl.position.copy(mid); grp.add(lbl); return grp;
}

/* ============================================================
   Walls
============================================================ */
function createWall(pointsXY, { thickness, material = MAT.wall, z = 0 } = {}){
  const pts = pointsXY.map(p => p.isVector2 ? p : new THREE.Vector2(p.x, p.y));
  const shape = new THREE.Shape(pts);
  const depth = Math.max(0.001, thickness || 0.1);
  const geo = new THREE.ExtrudeGeometry(shape, { depth, bevelEnabled: false });
  const mesh = new THREE.Mesh(geo, material);
  mesh.position.z = z;
  return mesh;
}
/* ------------------------------
   WINDOW SYSTEM
------------------------------ */

let windowCounter = 0;
let selectedWindow = null;
let placingWindow = false;
let ghostWindow = null;
const windowList = document.getElementById("window-list");

/** Create a 3D window mesh (frame + glass) */
function createWindowMesh(width = 1.2, height = 1.0) {
  const group = new THREE.Group();
  const depth = 0.15;

  const frameMat = new THREE.MeshStandardMaterial({
    color: "#555",
    roughness: 0.85,
    metalness: 0.2
  });
  const glassMat = new THREE.MeshPhysicalMaterial({
    color: "#99ccee",
    transmission: 0.8,   // makes glass effect
    opacity: 0.6,
    transparent: true,
    roughness: 0.05,
    metalness: 0.1,
    clearcoat: 0.8,
    clearcoatRoughness: 0.1
  });

  // Frame
  const frame = new THREE.Mesh(new THREE.BoxGeometry(width, height, depth), frameMat);

  // Glass slightly inset
  const glass = new THREE.Mesh(new THREE.PlaneGeometry(width * 0.9, height * 0.9), glassMat);
  glass.position.z = depth / 2 - 0.01;

  group.add(frame, glass);

  group.userData = {
    type: "window",
    id: ++windowCounter,
    width,
    height
  };

  return group;
}

/** Start placing a new window */
document.getElementById("addWindowBtn").addEventListener("click", () => {
  if (placingWindow) return; // already placing
  ghostWindow = createWindowMesh();
  ghostWindow.material?.forEach?.(m => m.transparent = true);
  scene.add(ghostWindow);
  placingWindow = true;
});

/** Update ghost window position on hover */
renderer.domElement.addEventListener("pointermove", (event) => {
  if (!placingWindow || !ghostWindow) return;

  mouse.x = (event.clientX / renderer.domElement.clientWidth) * 2 - 1;
  mouse.y = -(event.clientY / renderer.domElement.clientHeight) * 2 + 1;
  raycaster.setFromCamera(mouse, camera);

  const hits = raycaster.intersectObjects([G.Mauerwerk, G.Overlay], true);
  if (hits.length > 0) {
    const hit = hits[0];
    ghostWindow.position.copy(hit.point);
    ghostWindow.lookAt(hit.point.clone().add(hit.face.normal)); // orient flat
  }
});

/** Place window on click */
renderer.domElement.addEventListener("pointerdown", (event) => {
  if (placingWindow && ghostWindow) {
    const finalWindow = ghostWindow;
    placingWindow = false;
    ghostWindow = null;

    // make glass visible
    finalWindow.traverse(obj => {
      if (obj.material) obj.material.transparent = false;
    });

    // add to list & controls
    addWindowCard(finalWindow);
    scene.add(finalWindow);

    // attach to transform controls
    transformControls.attach(finalWindow);
    selectedWindow = finalWindow;
    return;
  }

  // Select existing window
  mouse.x = (event.clientX / renderer.domElement.clientWidth) * 2 - 1;
  mouse.y = -(event.clientY / renderer.domElement.clientHeight) * 2 + 1;
  raycaster.setFromCamera(mouse, camera);

  const intersects = raycaster.intersectObjects(scene.children, true);
  if (intersects.length > 0) {
    const obj = intersects[0].object.parent;
    if (obj.userData.type === "window") {
      selectWindow(obj);
    }
  }
});

/** Rebuild a window with new dimensions */
function resizeWindow(mesh, width, height) {
  const depth = 0.15;
  mesh.clear();

  const frameMat = new THREE.MeshStandardMaterial({
    color: "#555",
    roughness: 0.85,
    metalness: 0.2
  });
  const glassMat = new THREE.MeshPhysicalMaterial({
    color: "#99ccee",
    transmission: 0.8,
    opacity: 0.6,
    transparent: true,
    roughness: 0.05,
    metalness: 0.1,
    clearcoat: 0.8,
    clearcoatRoughness: 0.1
  });

  const frame = new THREE.Mesh(new THREE.BoxGeometry(width, height, depth), frameMat);
  const glass = new THREE.Mesh(new THREE.PlaneGeometry(width * 0.9, height * 0.9), glassMat);
  glass.position.z = depth / 2 - 0.01;

  mesh.add(frame, glass);
  mesh.userData.width = width;
  mesh.userData.height = height;
}

/* ------------------------------
   UI: Cards for each window
------------------------------ */
function addWindowCard(mesh) {
  const li = document.createElement("li");
  li.dataset.id = mesh.userData.id;
  li.innerHTML = `
    <strong>Window #${mesh.userData.id}</strong><br>
    W: <input type="number" step="0.1" value="${mesh.userData.width}" class="win-width" style="width:60px"> m
    H: <input type="number" step="0.1" value="${mesh.userData.height}" class="win-height" style="width:60px"> m
    <button class="delete-btn">🗑</button>
  `;
  windowList.appendChild(li);

  const widthInput = li.querySelector(".win-width");
  const heightInput = li.querySelector(".win-height");
  const delBtn = li.querySelector(".delete-btn");

  widthInput.addEventListener("change", () => {
    resizeWindow(mesh, parseFloat(widthInput.value), mesh.userData.height);
  });
  heightInput.addEventListener("change", () => {
    resizeWindow(mesh, mesh.userData.width, parseFloat(heightInput.value));
  });

  delBtn.addEventListener("click", () => {
    scene.remove(mesh);
    transformControls.detach();
    li.remove();
  });

  li.addEventListener("click", () => {
    selectWindow(mesh);
  });
}

/** Highlight + attach transform controls */
function selectWindow(mesh) {
  if (selectedWindow) {
    selectedWindow.traverse(obj => {
      if (obj.material && obj.material.color) obj.material.color.set("#555");
    });
  }
  selectedWindow = mesh;
  transformControls.attach(mesh);
  selectedWindow.traverse(obj => {
    if (obj.material && obj.material.color) obj.material.color.set("#ffcc00");
  });
}

/* ============================================================
   Rafter positions (editable)
============================================================ */
let rafterPositions = [];
function syncRafterInput() {
  const el=$("#rafters"); if (!el) return;
  if (!Array.isArray(rafterPositions) || rafterPositions.length===0){ el.value=""; return; }
  el.value = rafterPositions.map(z => z.toFixed(2)).join(", ");
}
$("#rafters")?.addEventListener("change", ()=>{
  const vals = $("#rafters").value.split(",").map(v => parseFloat(v.trim())).filter(v => Number.isFinite(v));
  if (vals.length>0){ rafterPositions = vals; rebuild(); }
});

/* ============================================================
   Build structure
============================================================ */
let WALLS = {}; // name -> Mesh
const WALL_OFFSETS = { left:new THREE.Vector3(), right:new THREE.Vector3(), front:new THREE.Vector3(), back:new THREE.Vector3() };

function buildStructure(stepMax = 1){
  const p = P();
  dbg(`α=${p.aDeg.toFixed(1)}°, L=${p.L.toFixed(2)}m, S=${p.S.toFixed(2)}m, HF=${p.HF.toFixed(2)}m`);
  ["Mauerwerk","Pfetten","Sparren","Konter","Eindeck","Overlay"].forEach(n => dispose(G[n]));
  WALLS = {};

  // 1) Walls
  if(stepMax >= 1){
    const HT_e = p.HT + p.Hfu;

    // left / right (extrude along Z = L)
    const leftPts = [
      new THREE.Vector2(-p.S/2, 0),
      new THREE.Vector2(-p.S/2 + p.tW, 0),
      new THREE.Vector2(-p.S/2 + p.tW, HT_e),
      new THREE.Vector2(-p.S/2, HT_e)
    ];
    const rightPts = [
      new THREE.Vector2(p.S/2 - p.tW, 0),
      new THREE.Vector2(p.S/2, 0),
      new THREE.Vector2(p.S/2, HT_e),
      new THREE.Vector2(p.S/2 - p.tW, HT_e)
    ];
    const leftWall  = createWall(leftPts,  { thickness: p.L, z: 0 });
    const rightWall = createWall(rightPts, { thickness: p.L, z: 0 });

    // front / back gables (extrude thickness = tW, placed at 0 and L - tW)
    const gablePts = [
      new THREE.Vector2(-p.S/2, 0),
      new THREE.Vector2( p.S/2, 0),
      new THREE.Vector2( p.S/2, HT_e),
      new THREE.Vector2( 0,     p.HF),
      new THREE.Vector2(-p.S/2, HT_e)
    ];
    const frontWall = createWall(gablePts, { thickness: p.tW, z: 0 });
    const backWall  = createWall(gablePts, { thickness: p.tW, z: p.L - p.tW });

    // tag + basePos + kind
    const setWallMeta = (mesh, name)=>{
      mesh.name = name;
      mesh.userData.kind = "wall";
      mesh.userData.wallName = name;
      mesh.userData.basePos = mesh.position.clone(); // before offset
    };
    setWallMeta(leftWall,"left"); setWallMeta(rightWall,"right");
    setWallMeta(frontWall,"front"); setWallMeta(backWall,"back");

    // apply persisted offsets
    leftWall.position.add(WALL_OFFSETS.left);
    rightWall.position.add(WALL_OFFSETS.right);
    frontWall.position.add(WALL_OFFSETS.front);
    backWall.position.add(WALL_OFFSETS.back);

    WALLS = { left:leftWall, right:rightWall, front:frontWall, back:backWall };
    G.Mauerwerk.add(leftWall, rightWall, frontWall, backWall);
  }

  // 2) Pfetten
  if(stepMax >= 2){
    const zMin = p.zMin, zMax = p.zMax, zC = (zMin + zMax) / 2;
    const first = new THREE.Mesh(new THREE.BoxGeometry(p.Bf, p.Hf, p.Ltr), MAT.wood);
    first.position.set(0, p.HF - p.Hf/2, zC);
    const footL = new THREE.Mesh(new THREE.BoxGeometry(p.Bfu, p.Hfu, p.Ltr), MAT.wood);
    footL.position.set(-p.S/2, p.HT + p.Hfu/2, zC);
    const footR = footL.clone(); footR.position.x = +p.S/2;
    G.Pfetten.add(first, footL, footR);
  }

  // 3) Sparren
  const uL = new THREE.Vector3( +(p.S/2), (p.HF - (p.HT + p.Hfu)), 0);
  const uR = new THREE.Vector3( -(p.S/2), (p.HF - (p.HT + p.Hfu)), 0);
  const bL = basisRafter(uL), bR = basisRafter(uR);
  const cosA = Math.abs(Math.cos(p.alpha));
  const l_over = p.UT / Math.max(1e-6, cosA);
  const l_ridge = (p.S/2) / Math.max(1e-6, cosA);
  const cutL = (p.Bf/2) / Math.max(1e-6, Math.abs(bL.u.x));
  const cutR = (p.Bf/2) / Math.max(1e-6, Math.abs(bR.u.x));
  const lenY_L = Math.max(0.01, l_over + Math.max(0.01, l_ridge - cutL));
  const lenY_R = Math.max(0.01, l_over + Math.max(0.01, l_ridge - cutR));

  let zList;
  if (Array.isArray(rafterPositions) && rafterPositions.length > 0) {
    zList = rafterPositions.slice();
  } else {
    const insideCount = Math.max(1, Math.round(p.Leff / Math.max(0.1, p.s_final)));
    const inside = Array.from({length: insideCount+1}, (_,k) => p.tW + k*(p.Leff/insideCount));
    zList = [p.zMin, 0, ...inside, p.L, p.zMax];
    rafterPositions = [...zList];
    syncRafterInput();
  }

  if (stepMax >= 3) {
    const geoL_base = makeRafterGeometry(lenY_L, p.Hr, p.Br, p.alpha, +1);
    const geoR_base = makeRafterGeometry(lenY_R, p.Hr, p.Br, p.alpha, -1);
    G.Sparren.userData.items = [];

    for (let i = 0; i < zList.length; i++) {
      const z = zList[i]; if (!Number.isFinite(z)) continue;

      const meshL = new THREE.Mesh(geoL_base.clone(), MAT.wood);
      placeRafterMesh(meshL, "L", z, lenY_L, bL, l_over, p);
      meshL.userData = { kind:"rafter", index:i, side:"L", Hr:p.Hr, Br:p.Br, lenY:lenY_L, alpha:p.alpha };
      G.Sparren.add(meshL); G.Sparren.userData.items.push(meshL);

      const meshR = new THREE.Mesh(geoR_base.clone(), MAT.wood);
      placeRafterMesh(meshR, "R", z, lenY_R, bR, l_over, p);
      meshR.userData = { kind:"rafter", index:i, side:"R", Hr:p.Hr, Br:p.Br, lenY:lenY_R, alpha:p.alpha };
      G.Sparren.add(meshR); G.Sparren.userData.items.push(meshR);
    }
  }

  // 4) Konter
  if(stepMax >= 4){
    const eps = 1e-4;
    const Bk_eff = (br,bk)=> Math.min(bk, Math.max(0.01, 0.9*br));
    const geoK_L = new THREE.BoxGeometry(p.Hk, 1.0, Bk_eff(p.Br,p.Bk));
    const geoK_R = new THREE.BoxGeometry(p.Hk, 1.0, Bk_eff(p.Br,p.Bk));
    const kL = new THREE.InstancedMesh(geoK_L, MAT.konter, zList.length);
    const kR = new THREE.InstancedMesh(geoK_R, MAT.konter, zList.length);

    function placeK(i,z,side){
      const b = side==="L"? bL : bR;
      const len = side==="L"? lenY_L : lenY_R;
      const foot = new THREE.Vector3(side==="L"? -p.S/2 : p.S/2, p.HT + p.Hfu, z);
      const start = foot.clone().add(b.u.clone().multiplyScalar(-l_over));
      const end   = foot.clone().add(b.u.clone().multiplyScalar( len - l_over ));
      const mid   = start.clone().add(end).multiplyScalar(0.5);
      const c = mid.add(b.n.clone().multiplyScalar(p.Hr + p.Hk/2 + eps));
      const mat = new THREE.Matrix4().compose(c, b.q, new THREE.Vector3(1,len,1));
      (side==="L"? kL : kR).setMatrixAt(i, mat);
    }
    for(let i=0;i<zList.length;i++){
      const z=zList[i]; if(!Number.isFinite(z)) continue;
      placeK(i,z,"L"); placeK(i,z,"R");
    }
    kL.instanceMatrix.needsUpdate = kR.instanceMatrix.needsUpdate = true;
    G.Konter.add(kL,kR);
  }

  // 5) Eindeck + Overlay
  if(stepMax >= 5){
    const qLatL = basisLatten(bL.n).q, qLatR = basisLatten(bR.n).q;
    const spanZ = p.zMax - p.zMin;
    const lenRoof = Math.max(lenY_L,lenY_R);
    const rows = Math.max(1, Math.ceil(lenRoof / Math.max(0.05,p.eSpacing)));
    const eGeo = new THREE.BoxGeometry(p.He, spanZ, p.Be);
    const eL = new THREE.InstancedMesh(eGeo, MAT.batten, rows);
    const eR = new THREE.InstancedMesh(eGeo, MAT.batten, rows);
    const l_over2 = p.UT / Math.max(1e-6, Math.abs(Math.cos(p.alpha)));
    const eps2 = 1e-4;

    for(let r=0;r<rows;r++){
      const dist = (r+0.5)*Math.max(0.05,p.eSpacing);
      const cL = new THREE.Vector3(-p.S/2, p.HT + p.Hfu, p.zMin + spanZ/2)
        .add(bL.u.clone().multiplyScalar(dist - l_over2))
        .add(bL.n.clone().multiplyScalar(p.Hr + p.Hk + p.He/2 + eps2));
      const cR = new THREE.Vector3( p.S/2, p.HT + p.Hfu, p.zMin + spanZ/2)
        .add(bR.u.clone().multiplyScalar(dist - l_over2))
        .add(bR.n.clone().multiplyScalar(p.Hr + p.Hk + p.He/2 + eps2));
      eL.setMatrixAt(r, new THREE.Matrix4().compose(cL, qLatL, new THREE.Vector3(1,1,1)));
      eR.setMatrixAt(r, new THREE.Matrix4().compose(cR, qLatR, new THREE.Vector3(1,1,1)));
    }
    eL.instanceMatrix.needsUpdate = eR.instanceMatrix.needsUpdate = true;
    G.Eindeck.add(eL,eR);

    const overlay=(x1,x2,y1,y2,zA,zB)=>{
      const ok=[x1,x2,y1,y2,zA,zB].every(Number.isFinite); if(!ok) return null;
      const g=new THREE.BufferGeometry();
      g.setAttribute("position", new THREE.Float32BufferAttribute([
        x1,y1,zA, x1,y1,zB, x2,y2,zB,
        x1,y1,zA, x2,y2,zB, x2,y2,zA
      ],3));
      g.computeVertexNormals();
      return new THREE.Mesh(g, MAT.overlay);
    };
    const o1=overlay(-p.S/2-p.UT,0,p.HT+p.Hfu,p.HF,p.zMin,p.zMax);
    const o2=overlay(0,p.S/2+p.UT,p.HF,p.HT+p.Hfu,p.zMin,p.zMax);
    if(o1) G.Overlay.add(o1);
    if(o2) G.Overlay.add(o2);
  }

  return {p, bL, bR, lenY_L, lenY_R, l_over, zList};
}
function placeRafterMesh(mesh, side, z, lenY, b, l_over, p){
  const foot = new THREE.Vector3(side==="L"? -p.S/2 : p.S/2, p.HT + p.Hfu, z);
  const start = foot.clone().add(b.u.clone().multiplyScalar(-l_over));
  const end   = foot.clone().add(b.u.clone().multiplyScalar(lenY - l_over));
  const center= start.clone().add(end).multiplyScalar(0.5).add(b.n.clone().multiplyScalar(p.Hr/2));
  mesh.position.copy(center); mesh.quaternion.copy(b.q);
}

/* ============================================================
   Resize rafter geometry after scaling
============================================================ */
function resizeRafter(mesh, newHr, newBr){
  const u = mesh.userData;
  const sideSign = (u.side === "L") ? +1 : -1;
  const newGeo = makeRafterGeometry(u.lenY, newHr, newBr, u.alpha, sideSign);
  mesh.geometry.dispose(); mesh.geometry = newGeo;
  u.Hr = newHr; u.Br = newBr;
}

/* ============================================================
   Dimensions
============================================================ */
function buildDims(ctx, stepMax){
  const {p, bL, bR, l_over} = ctx;
  ["Dims:Exterior","Dims:Vertical","Dims:First","Dims:SparrenLen","Dims:Rafter","Dims:Lattung","Dims:Ortgang"].forEach(n=>dispose(G[n]));

  if($("#chk-Exterior")?.checked){
    const a = new THREE.Vector3(-p.S/2, 0.05, -0.6);
    const b = new THREE.Vector3( +p.S/2, 0.05, -0.6);
    G["Dims:Exterior"].add(dimChain(a,b,new THREE.Vector3(0,0,1), 0.25, DCLR.Exterior, `S = ${fmt(p.S)}`));
    const a2 = new THREE.Vector3(0, 0.05, 0);
    const b2 = new THREE.Vector3(0, 0.05, p.L);
    G["Dims:Exterior"].add(dimChain(a2,b2,new THREE.Vector3(-1,0,0), 0.25, DCLR.Exterior, `L = ${fmt(p.L)}`));
  }
  if($("#chk-Vertical")?.checked){
    const zMid = p.L/2;
    const a = new THREE.Vector3(p.S/2 + 0.4, p.HT, zMid);
    const b = new THREE.Vector3(p.S/2 + 0.4, p.HF, zMid);
    G["Dims:Vertical"].add(dimChain(a,b,new THREE.Vector3(0,0,1), 0.22, DCLR.Vertical, `ΔY = ${fmt(p.HF-p.HT)}`));
    const g0 = new THREE.Vector3(p.S/2 + 0.9, 0, zMid);
    const g1 = new THREE.Vector3(p.S/2 + 0.9, p.HT, zMid);
    const g2 = new THREE.Vector3(p.S/2 + 1.4, 0, zMid);
    const g3 = new THREE.Vector3(p.S/2 + 1.4, p.HF, zMid);
    G["Dims:Vertical"].add(dimChain(g0,g1,new THREE.Vector3(0,0,1), 0.18, DCLR.Vertical, `HT = ${fmt(p.HT)}`));
    G["Dims:Vertical"].add(dimChain(g2,g3,new THREE.Vector3(0,0,1), 0.18, DCLR.Vertical, `HF = ${fmt(p.HF)}`));
  }
  if($("#chk-First")?.checked){
    const y = p.HF + 0.20;
    const a = new THREE.Vector3(0, y, p.zMin);
    const b = new THREE.Vector3(0, y, p.zMax);
    G["Dims:First"].add(dimChain(a,b,new THREE.Vector3(1,0,0), 0.28, DCLR.First, `Firstlänge = ${fmt(p.Ltr)}`));
  }
  if($("#chk-SparrenLen")?.checked){
    const cosA = Math.abs(Math.cos(p.alpha));
    const l_UT = (p.S/2 + p.UT)/Math.max(1e-6,cosA);
    const footL = new THREE.Vector3(-p.S/2, p.HT + p.Hfu, p.L/2);
    const aL = footL.clone().add(bL.u.clone().multiplyScalar(-l_over));
    const bLend = footL.clone().add(bL.u.clone().multiplyScalar(l_UT - l_over));
    G["Dims:SparrenLen"].add(dimChain(aL,bLend,bL.n, 0.25, DCLR.SparrenLen, `Sparrenlänge = ${fmt(l_UT)}`));
    const footR = new THREE.Vector3( p.S/2, p.HT + p.Hfu, p.L/2);
    const aR = footR.clone().add(bR.u.clone().multiplyScalar(-l_over));
    const bRend = footR.clone().add(bR.u.clone().multiplyScalar(l_UT - l_over));
    G["Dims:SparrenLen"].add(dimChain(aR,bRend,bR.n, 0.25, DCLR.SparrenLen, `Sparrenlänge = ${fmt(l_UT)}`));
  }
  if($("#chk-Rafter")?.checked){
    const y = p.HT + p.Hfu + 0.15;
    const x = -p.S/2 - 0.35;
    const a = new THREE.Vector3(x, y, p.tW);
    const b = new THREE.Vector3(x, y, p.L - p.tW);
    const nIntervals = Math.round(p.Leff/p.s_final);
    G["Dims:Rafter"].add(dimChain(a,b,new THREE.Vector3(-1,0,0),0.18, DCLR.Rafter, `s_final = ${fmt(p.s_final)}  (n=${nIntervals})`));
  }
  if($("#chk-Lattung")?.checked){
    const cosA = Math.abs(Math.cos(p.alpha));
    const l_UT = (p.S/2 + p.UT)/Math.max(1e-6,cosA);
    const rows = Math.max(1, Math.ceil(l_UT/Math.max(0.05,p.eSpacing)));
    const baseL = new THREE.Vector3(-p.S/2, p.HT + p.Hfu, p.L*0.35);
    G["Dims:Lattung"].add(dimChain(baseL.clone(), baseL.clone().add(bL.u.clone().multiplyScalar(l_UT)), bL.n, 0.22, DCLR.Lattung, `eSpacing = ${fmt(p.eSpacing)}  (Reihen ≈ ${rows})`));
    const baseR = new THREE.Vector3( p.S/2, p.HT + p.Hfu, p.L*0.65);
    G["Dims:Lattung"].add(dimChain(baseR.clone(), baseR.clone().add(bR.u.clone().multiplyScalar(l_UT)), bR.n, 0.22, DCLR.Lattung, `eSpacing = ${fmt(p.eSpacing)}  (Reihen ≈ ${rows})`));
  }
  if($("#chk-Ortgang")?.checked){
    const y = p.HT + p.Hfu + 0.25;
    const aL=new THREE.Vector3(-p.S/2, y, -p.UL);
    const bLZ=new THREE.Vector3(-p.S/2, y, 0);
    G["Dims:Ortgang"].add(dimChain(aL,bLZ,new THREE.Vector3(-1,0,0),0.18, DCLR.Ortgang, `UL = ${fmt(p.UL)}`));
    const aR=new THREE.Vector3( p.S/2, y, p.L);
    const bRZ=new THREE.Vector3( p.S/2, y, p.L + p.UR);
    G["Dims:Ortgang"].add(dimChain(aR,bRZ,new THREE.Vector3(1,0,0),0.18, DCLR.Ortgang, `UR = ${fmt(p.UR)}`));
  }
}

/* ============================================================
   Interaction: rafters AND walls (NEW)
============================================================ */
const raycaster = new THREE.Raycaster();
const mouse = new THREE.Vector2();
let transformControls;
let hoveredObj  = null;
let selectedObj = null;
let lastCtx = null;
let editMode = "rafter"; // "rafter" | "wall"

/* Small tooltip */
const tip = document.createElement("div");
tip.style.cssText = "position:absolute;pointer-events:none;padding:4px 8px;border-radius:8px;background:#000a;color:#fff;font:12px/1.2 ui-sans-serif;transform:translate(8px,8px);display:none;";
stage.appendChild(tip);

function ndcFromEvent(e){
  const rect = renderer.domElement.getBoundingClientRect();
  return {
    x: ((e.clientX - rect.left) / rect.width) * 2 - 1,
    y: -((e.clientY - rect.top) / rect.height) * 2 + 1,
    rect
  };
}
function getPickables(){
  if (editMode === "wall") return Object.values(WALLS);
  return G.Sparren.children;
}
function resetMaterial(obj){
  if (!obj) return;
  const k = obj.userData.kind;
  if (k==="wall") obj.material = MAT.wall;
  else if (k==="rafter") obj.material = MAT.wood;
}
function highlightMaterial(obj){
  if (!obj) return;
  const k = obj.userData.kind;
  if (k==="wall") obj.material = MAT.wallHighlight;
  else if (k==="rafter") obj.material = MAT.woodHighlight;
}
function configureTCFor(obj){
  if (!obj) return;
  const k = obj.userData.kind;
  if (k==="wall"){
    transformControls.setMode("translate");
    transformControls.setSpace("world");
    transformControls.showX = true;  // move wall in X
    transformControls.showY = false; // lock Y
    transformControls.showZ = true;  // and in Z (length)
    transformControls.setTranslationSnap(0.01);
  } else {
    transformControls.setMode("translate");
    transformControls.setSpace("world");
    transformControls.showX = false; transformControls.showY = false; transformControls.showZ = true;
    transformControls.setTranslationSnap(0.01);
  }
}
function attachSelection(obj){
  if (selectedObj && selectedObj !== obj) resetMaterial(selectedObj);
  selectedObj = obj;
  if (selectedObj){
    highlightMaterial(selectedObj);
    configureTCFor(selectedObj);
    transformControls.attach(selectedObj);
  } else {
    transformControls.detach();
  }
}
function initInteraction(scene, camera, renderer, controls){
  transformControls = new TransformControls(camera, renderer.domElement);
  scene.add(transformControls);
  transformControls.addEventListener("dragging-changed", e => { controls.enabled = !e.value; });

  transformControls.addEventListener("objectChange", () => {
    if (!selectedObj) return;
    const k = selectedObj.userData.kind;

    if (k==="rafter"){
      const { p } = lastCtx || {};
      if (p && transformControls.getMode()==="translate"){
        const zMin = p.tW, zMax = p.L - p.tW;
        selectedObj.position.z = clamp(selectedObj.position.z, zMin, zMax);
        const idx = selectedObj.userData.index;
        rafterPositions[idx] = selectedObj.position.z;
        syncRafterInput();
      }
      if (transformControls.getMode()==="scale"){
        const u = selectedObj.userData;
        const newHr = Math.max(0.02, u.Hr * selectedObj.scale.x);
        const newBr = Math.max(0.02, u.Br * selectedObj.scale.z);
        resizeRafter(selectedObj, newHr, newBr);
        selectedObj.scale.set(1,1,1);
      }
    } else if (k==="wall"){
      // persist wall offsets relative to base position so they survive rebuild()
      const name = selectedObj.userData.wallName;
      if (name && selectedObj.userData.basePos){
        WALL_OFFSETS[name] = selectedObj.position.clone().sub(selectedObj.userData.basePos);
      }
    }
  });

  transformControls.addEventListener("objectChange", () => {
  if (!selectedWindow) return;

  if (selectedWindow.userData.type === "window" && transformControls.getMode() === "scale") {
    // Convert scale back into real width/height
    const u = selectedWindow.userData;
    const newWidth  = Math.max(0.2, u.width  * selectedWindow.scale.x);
    const newHeight = Math.max(0.2, u.height * selectedWindow.scale.y);

    // Apply resize
    resizeWindow(selectedWindow, newWidth, newHeight);

    // Bake scale (reset to 1,1,1)
    selectedWindow.scale.set(1, 1, 1);

    // Update toolbar inputs
    const card = document.querySelector(`#window-list li[data-id="${u.id}"]`);
    if (card) {
      card.querySelector(".win-width").value  = newWidth.toFixed(2);
      card.querySelector(".win-height").value = newHeight.toFixed(2);
    }
  }
});


  // Hover
  renderer.domElement.addEventListener("pointermove", (e)=>{
    const {x,y,rect} = ndcFromEvent(e);
    mouse.set(x,y);
    raycaster.setFromCamera(mouse, camera);
    const hits = raycaster.intersectObjects(getPickables(), true);
    if (hits.length){
      const obj = hits[0].object;
      if(hoveredObj !== obj){
        if(hoveredObj && hoveredObj !== selectedObj) resetMaterial(hoveredObj);
        hoveredObj = obj;
        if(hoveredObj !== selectedObj) highlightMaterial(hoveredObj);
      }
      tip.style.left = (e.clientX - rect.left) + "px";
      tip.style.top  = (e.clientY - rect.top)  + "px";
      const u = obj.userData||{};
      if (u.kind==="wall"){
        tip.textContent = `Wand: ${u.wallName}  pos=(${obj.position.x.toFixed(2)}, ${obj.position.y.toFixed(2)}, ${obj.position.z.toFixed(2)})`;
      } else {
        tip.textContent = `Sparren ${u.side ?? "?"}-${String((u.index??0)+1).padStart(2,"0")}  z=${(obj.position.z).toFixed(2)} m`;
      }
      tip.style.display = "block";
    } else {
      if(hoveredObj && hoveredObj !== selectedObj) resetMaterial(hoveredObj);
      hoveredObj = null; tip.style.display = "none";
    }
  });

  // Click select
  renderer.domElement.addEventListener("pointerdown", (e)=>{
    const {x,y} = ndcFromEvent(e);
    mouse.set(x,y);
    raycaster.setFromCamera(mouse, camera);
    const hits = raycaster.intersectObjects(getPickables(), true);
    if(hits.length){
      const obj = hits[0].object;
      if (obj?.userData?.kind) attachSelection(obj);
    } else {
      if (selectedObj) resetMaterial(selectedObj);
      selectedObj = null; transformControls.detach();
    }
  });

  // Keys
  window.addEventListener("keydown", (e)=>{
    const key = e.key.toLowerCase();
    if (key === "w") { editMode = "wall"; if (selectedObj && selectedObj.userData.kind!=="wall"){ resetMaterial(selectedObj); transformControls.detach(); selectedObj=null; } }
    if (key === "r" && e.ctrlKey === false && e.metaKey === false) { editMode = "rafter"; if (selectedObj && selectedObj.userData.kind!=="rafter"){ resetMaterial(selectedObj); transformControls.detach(); selectedObj=null; } }

    if (!selectedObj) return;

    if (selectedObj.userData.kind === "rafter"){
      if (key === "t"){ transformControls.setMode("translate"); configureTCFor(selectedObj); }
      if (key === "s"){ transformControls.setMode("scale"); transformControls.setSpace("local"); transformControls.showX=true; transformControls.showY=false; transformControls.showZ=true; }
      if (key === "o"){ transformControls.setMode("rotate"); transformControls.setSpace("local"); transformControls.showX=false; transformControls.showY=true; transformControls.showZ=false; }
    } else if (selectedObj.userData.kind === "wall"){
      if (key === "t"){ configureTCFor(selectedObj); }
      if (key === "x"){ transformControls.showX=true; transformControls.showZ=false; }
      if (key === "z"){ transformControls.showX=false; transformControls.showZ=true; }
      if (key === "a"){ transformControls.showX=true; transformControls.showZ=true; }
    }

    if (key === "escape"){ if (selectedObj) resetMaterial(selectedObj); selectedObj=null; transformControls.detach(); }
    if (key === "arrowup"){ selectedObj.position.z += 0.01; transformControls.dispatchEvent({type:"objectChange"}); }
    if (key === "arrowdown"){ selectedObj.position.z -= 0.01; transformControls.dispatchEvent({type:"objectChange"}); }
    if (key === "arrowleft"){ selectedObj.position.x -= 0.01; transformControls.dispatchEvent({type:"objectChange"}); }
    if (key === "arrowright"){ selectedObj.position.x += 0.01; transformControls.dispatchEvent({type:"objectChange"}); }
  });
}

/* ============================================================
   PUBLIC API (NEW): change wall positions programmatically
   Usage in console:
     enableWallEditing(true)
     setWallPosition('front', {z: 1.0})
     setWallOffset('left', {z: 0.2, x: -0.05})
     getWallPositions()
============================================================ */
function setWallPosition(name, pos={}){
  const m = WALLS[name]; if(!m) return;
  if (typeof pos.x === "number") m.position.x = pos.x;
  if (typeof pos.y === "number") m.position.y = pos.y;
  if (typeof pos.z === "number") m.position.z = pos.z;
  if (m.userData?.basePos) WALL_OFFSETS[name] = m.position.clone().sub(m.userData.basePos);
}
function setWallOffset(name, delta={}){
  const m = WALLS[name]; if(!m) return;
  m.position.add(new THREE.Vector3(delta.x||0, delta.y||0, delta.z||0));
  if (m.userData?.basePos) WALL_OFFSETS[name] = m.position.clone().sub(m.userData.basePos);
}
function enableWallEditing(on=true){ editMode = on ? "wall" : "rafter"; }
function getWallPositions(){
  const out={};
  for (const k of Object.keys(WALLS)){
    const v=WALLS[k].position; out[k] = {x:+v.x.toFixed(3), y:+v.y.toFixed(3), z:+v.z.toFixed(3)};
  }
  return out;
}
Object.assign(window, { setWallPosition, setWallOffset, enableWallEditing, getWallPositions });

/* ============================================================
   Orchestrator + UI wiring
============================================================ */
let current=1;
function rebuild(){
  const ctx = buildStructure(current);
  lastCtx = ctx;
  buildDims(ctx, current);
  for(const key of ["Exterior","Vertical","First","SparrenLen","Rafter","Lattung","Ortgang"]){
    const chk = $("#chk-"+key);
    if(chk) G["Dims:"+key].visible = chk.checked;
  }
  fit(current);
  syncRafterInput();
}

$("#prev")?.addEventListener("click", ()=>{ current = Math.max(1, current-1); updateSteps(); rebuild(); });
$("#next")?.addEventListener("click", ()=>{ current = Math.min(5, current+1); updateSteps(); rebuild(); });
$("#steps")?.addEventListener("click", e=>{
  const b=e.target.closest(".step"); if(!b) return;
  current = Number(b.dataset.step)||1; updateSteps(); rebuild();
});
function updateSteps(){ $$("#steps .step").forEach(b=> b.classList.toggle("active", Number(b.dataset.step)===current)); }
$$('input').forEach(el=> el.addEventListener('change', rebuild));

const dimIds = {Exterior:1, Vertical:1, First:1, SparrenLen:1, Rafter:0, Lattung:0, Ortgang:0};
for(const key in dimIds){
  if ($("#chk-"+key)) continue;
  const box=document.createElement("input"); box.type="checkbox"; box.id="chk-"+key; box.className="hidden"; box.checked=!!dimIds[key];
  document.body.appendChild(box);
}
$$('.dimchk').forEach(el=>{
  const key=el.dataset.layer; const hidden=$("#chk-"+key);
  el.checked=hidden.checked;
  el.addEventListener('change', ()=>{ hidden.checked=el.checked; rebuild(); });
});
$("#dimAll")?.addEventListener('change', (e)=>{
  const on=e.target.checked;
  $$('.dimchk').forEach(ch=>{ ch.checked=on; const key=ch.dataset.layer; $("#chk-"+key).checked=on; });
  rebuild();
});

/* ============================================================
   Render + Boot
============================================================ */
function loop(){ controls.update(); renderer.render(scene,camera); requestAnimationFrame(loop); }
initInteraction(scene, camera, renderer, controls);
loop();
rebuild();

})();
</script>


</body>
</html>
