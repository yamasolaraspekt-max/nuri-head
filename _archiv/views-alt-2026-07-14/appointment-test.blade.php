<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>We’re Under Maintenance 🚧</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body,html {
      margin:0; padding:0; height:100%; overflow:hidden;
      font-family: sans-serif; background:#0b0b0b;
    }
    canvas { display:block; }
    #overlay {
      position: absolute; top:0; left:0; width:100%; height:100%;
      display:flex; flex-direction:column; justify-content:center; align-items:center;
      color:white; pointer-events:none;
    }
    #contactBtn {
      margin-top:20px;
      padding:12px 24px;
      background:#2563eb; border-radius:9999px;
      font-weight:bold; color:white;
      pointer-events:auto;
      transition: background 0.2s;
    }
    #contactBtn:hover { background:#1d4ed8; }
  </style>
</head>
<body>
  <div id="overlay">
    <h1 class="text-4xl font-bold">🚧 We Are Under Maintenance 🚧</h1>
    <p class="mt-2">Meanwhile, drive around and smash some cubes!</p>
    <button id="contactBtn">Contact Us</button>
  </div>

  <!-- Three.js (older stable build with extras support) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/TransformControls.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/FontLoader.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/geometries/TextGeometry.js"></script>
  <!-- Cannon.js physics -->
  <script src="https://cdn.jsdelivr.net/npm/cannon@0.6.2/build/cannon.min.js"></script>
<script>
// Scene, Camera, Renderer
const scene = new THREE.Scene();
scene.background = new THREE.Color(0x111111);

const camera = new THREE.PerspectiveCamera(75, window.innerWidth/window.innerHeight, 0.1, 1000);
camera.position.set(0,5,12);

const renderer = new THREE.WebGLRenderer({antialias:true});
renderer.setSize(window.innerWidth, window.innerHeight);
renderer.physicallyCorrectLights = true;
renderer.outputEncoding = THREE.sRGBEncoding;
document.body.appendChild(renderer.domElement);

// Physics world
const world = new CANNON.World();
world.gravity.set(0,-9.82,0);

// Lights
const hemiLight = new THREE.HemisphereLight(0xffffff,0x444444,1.2);
hemiLight.position.set(0,20,0);
scene.add(hemiLight);

const dirLight = new THREE.DirectionalLight(0xffffff,1);
dirLight.position.set(5,10,7);
dirLight.castShadow = true;
scene.add(dirLight);

// Simple environment cube texture (gives reflections)
const urls = [
  "https://threejs.org/examples/textures/cube/Bridge2/posx.jpg",
  "https://threejs.org/examples/textures/cube/Bridge2/negx.jpg",
  "https://threejs.org/examples/textures/cube/Bridge2/posy.jpg",
  "https://threejs.org/examples/textures/cube/Bridge2/negy.jpg",
  "https://threejs.org/examples/textures/cube/Bridge2/posz.jpg",
  "https://threejs.org/examples/textures/cube/Bridge2/negz.jpg"
];
const envMap = new THREE.CubeTextureLoader().load(urls);
scene.environment = envMap;

// Road
const roadGeo = new THREE.TorusGeometry(30, 1, 16, 100);
const roadMat = new THREE.MeshStandardMaterial({color:0x333333, roughness:0.8});
const road = new THREE.Mesh(roadGeo, roadMat);
road.rotation.x = Math.PI/2;
scene.add(road);

// Car (box placeholder, later replace with GLB loader)
const carGeo = new THREE.BoxGeometry(2,1,4);
const carMat = new THREE.MeshStandardMaterial({color:0xff4444, metalness:0.3, roughness:0.4, envMap:envMap});
let carMesh = new THREE.Mesh(carGeo, carMat);
scene.add(carMesh);

const carBody = new CANNON.Body({
  mass: 5,
  shape: new CANNON.Box(new CANNON.Vec3(1,0.5,2)),
  position: new CANNON.Vec3(0,2,0)
});
world.addBody(carBody);

// Floor
const groundBody = new CANNON.Body({ mass: 0 });
groundBody.addShape(new CANNON.Plane());
groundBody.quaternion.setFromEuler(-Math.PI/2,0,0);
world.addBody(groundBody);

// Cubes with letters
const text = "RAMIN SOLAR ASPEKT";
const cubes = [];
const cubeBodies = [];

const fontLoader = new THREE.FontLoader();
fontLoader.load("https://threejs.org/examples/fonts/helvetiker_regular.typeface.json", font=>{
  [...text].forEach((letter, i)=>{
    if(letter === " ") return;
    const geo = new THREE.BoxGeometry(1,1,1);

    // Glassy material
    const mat = new THREE.MeshPhysicalMaterial({
      color: 0x66ccff,
      transmission: 0.9,   // glass transparency
      thickness: 0.5,
      roughness: 0.05,
      metalness: 0.0,
      envMap: envMap,
      clearcoat: 1,
      clearcoatRoughness: 0.05
    });

    const cube = new THREE.Mesh(geo,mat);

    // Letter overlay
    const textGeo = new THREE.TextGeometry(letter, {
      font: font,
      size:0.5, height:0.05
    });
    const textMat = new THREE.MeshStandardMaterial({color:0x000000, metalness:0.2, roughness:0.8});
    const textMesh = new THREE.Mesh(textGeo,textMat);
    textMesh.position.set(-0.25,-0.25,0.52);
    cube.add(textMesh);

    cube.position.set((i%10)*2-10, 2, Math.floor(i/10)*-3);
    scene.add(cube);
    cubes.push(cube);

    const body = new CANNON.Body({
      mass:1,
      shape:new CANNON.Box(new CANNON.Vec3(0.5,0.5,0.5)),
      position:new CANNON.Vec3(cube.position.x, cube.position.y, cube.position.z)
    });
    world.addBody(body);
    cubeBodies.push(body);
  });
});

// Controls
let keys = {};
window.addEventListener("keydown", e=>keys[e.code]=true);
window.addEventListener("keyup", e=>keys[e.code]=false);

function handleCarControls(){
  if(keys["ArrowUp"]) carBody.velocity.z -= 0.2;
  if(keys["ArrowDown"]) carBody.velocity.z += 0.2;
  if(keys["ArrowLeft"]) carBody.velocity.x -= 0.2;
  if(keys["ArrowRight"]) carBody.velocity.x += 0.2;
}

// Animation loop
const clock = new THREE.Clock();
function animate(){
  requestAnimationFrame(animate);

  handleCarControls();

  world.step(1/60, clock.getDelta());

  if(carMesh){
    carMesh.position.copy(carBody.position);
    carMesh.quaternion.copy(carBody.quaternion);
  }

  cubes.forEach((cube,i)=>{
    cube.position.copy(cubeBodies[i].position);
    cube.quaternion.copy(cubeBodies[i].quaternion);
  });

  camera.lookAt(carBody.position);
  renderer.render(scene,camera);
}
animate();

// Resize
window.addEventListener("resize", ()=>{
  camera.aspect = window.innerWidth/window.innerHeight;
  camera.updateProjectionMatrix();
  renderer.setSize(window.innerWidth,window.innerHeight);
});
</script>

</body>
</html>
