<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>3D Roof Angle Control</title>
  <style>
    body { margin: 0; overflow: hidden; }
    canvas { display: block; }
    #angleControl {
      position: absolute;
      top: 10px;
      left: 10px;
      padding: 5px;
      background: rgba(255, 255, 255, 0.8);
      z-index: 1;
      font-family: sans-serif;
    }
  </style>
</head>
<body>
  <div id="angleControl">
    <label>Roof Angle: <span id="angleValue">30</span>°</label>
    <input type="range" id="angleSlider" min="10" max="80" value="30">
  </div>

  <!-- Dependencies -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/geometries/RoundedBoxGeometry.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tween.js/18.6.4/tween.umd.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0xf0f0f0);

    const camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 1000);
    camera.position.set(8, 6, 10);
    camera.lookAt(0, 2, 0);

    const renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    document.body.appendChild(renderer.domElement);

    const controls = new THREE.OrbitControls(camera, renderer.domElement);

    const gridHelper = new THREE.GridHelper(20, 20);
    scene.add(gridHelper);

    const wallTexture = new THREE.TextureLoader().load('https://threejsfundamentals.org/threejs/resources/images/wall.jpg');
    const baseMaterial = new THREE.MeshStandardMaterial({ map: wallTexture });
    const base = new THREE.Mesh(new THREE.BoxGeometry(6, 2, 4), baseMaterial);
    base.position.y = 1;
    scene.add(base);

    const doorMaterial = new THREE.MeshStandardMaterial({ color: 0x5a3a22 });
    const door = new THREE.Mesh(new THREE.BoxGeometry(0.8, 1.4, 0.1), doorMaterial);
    door.position.set(0, 0.7, 2.01);
    scene.add(door);

    const windowMaterial = new THREE.MeshStandardMaterial({ color: 0x87ceeb, transparent: true, opacity: 0.5 });
    const window1 = new THREE.Mesh(new THREE.BoxGeometry(0.6, 0.6, 0.1), windowMaterial);
    window1.position.set(-1.5, 1.2, 2.01);
    scene.add(window1);

    const window2 = new THREE.Mesh(new THREE.BoxGeometry(0.6, 0.6, 0.1), windowMaterial);
    window2.position.set(1.5, 1.2, 2.01);
    scene.add(window2);

    let roof;
    function createRoof(angleDeg) {
      if (roof) scene.remove(roof);

      const angle = THREE.Math.degToRad(angleDeg);
      const width = 6.8;
      const depth = 4.8;
      const height = Math.tan(angle) * (width / 2);

      const positions = new Float32Array([
        -width/2, 0, -depth/2,  width/2, 0, -depth/2,  0, height, -depth/2, // back triangle
        -width/2, 0, depth/2,   width/2, 0, depth/2,   0, height, depth/2,  // front triangle
        -width/2, 0, -depth/2,  width/2, 0, -depth/2,  width/2, 0, depth/2,
        -width/2, 0, -depth/2,  width/2, 0, depth/2,   -width/2, 0, depth/2,
        width/2, 0, -depth/2,   0, height, -depth/2,   0, height, depth/2,
        width/2, 0, -depth/2,   0, height, depth/2,    width/2, 0, depth/2,
        -width/2, 0, -depth/2,  -width/2, 0, depth/2,  0, height, depth/2,
        -width/2, 0, -depth/2,  0, height, depth/2,    0, height, -depth/2
      ]);

      const geometry = new THREE.BufferGeometry();
      geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
      geometry.computeVertexNormals();

      const roofTexture = new THREE.TextureLoader().load('https://threejsfundamentals.org/threejs/resources/images/hardwood2_diffuse.jpg');
      roofTexture.wrapS = roofTexture.wrapT = THREE.RepeatWrapping;
      roofTexture.repeat.set(2, 2);
      const roofMaterial = new THREE.MeshStandardMaterial({ map: roofTexture });

      roof = new THREE.Mesh(geometry, roofMaterial);
      roof.position.y = 2;
      scene.add(roof);
    }

    const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
    scene.add(ambientLight);

    const directionalLight = new THREE.DirectionalLight(0xffffff, 0.6);
    directionalLight.position.set(5, 10, 7.5);
    scene.add(directionalLight);

    const angleSlider = document.getElementById('angleSlider');
    const angleValue = document.getElementById('angleValue');
    angleSlider.addEventListener('input', () => {
      const angle = parseInt(angleSlider.value);
      angleValue.textContent = angle;
      createRoof(angle);
    });

    createRoof(parseInt(angleSlider.value));

    function animate() {
      requestAnimationFrame(animate);
      controls.update();
      renderer.render(scene, camera);
    }
    animate();

    window.addEventListener('resize', () => {
      camera.aspect = window.innerWidth / window.innerHeight;
      camera.updateProjectionMatrix();
      renderer.setSize(window.innerWidth, window.innerHeight);
    });
  </script>
</body>
</html>
