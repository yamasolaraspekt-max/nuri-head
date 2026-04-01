<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Model Viewer</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css')}}">
    <style>
        body {
            margin: 0;
            background: #FFFFFF;
        }

        /* Ensure the body has no margin and a white background */
    </style>
</head>

<body>
    <script type="module">
        import * as THREE from 'https://unpkg.com/three@0.126.1/build/three.module.js';
        import { OrbitControls } from 'https://unpkg.com/three@0.126.1/examples/jsm/controls/OrbitControls.js';
        import { GLTFLoader } from 'https://unpkg.com/three@0.126.1/examples/jsm/loaders/GLTFLoader.js';
        const mo = "{{ asset('models/base.glb') }}"; // Model Origin
        
        document.addEventListener('DOMContentLoaded', () => {
            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
            const renderer = new THREE.WebGLRenderer({ alpha: true });
            renderer.setClearColor(0xffffff, 1); // Set clear color to white with full opacity
            renderer.setSize(window.innerWidth, window.innerHeight);
            document.body.appendChild(renderer.domElement);

            const loader = new GLTFLoader();
            loader.load(mo, (gltf) => {
                scene.add(gltf.scene);
            }, undefined, function(error) {
                console.error('An error occurred while loading the model:', error);
            });

            const light = new THREE.HemisphereLight(0xffffff, 0x444444);
            light.position.set(1, 3, 2);
            scene.add(light);

            camera.position.z = 5;
            const controls = new OrbitControls(camera, renderer.domElement);
            controls.update();
                
            function animate() {
                requestAnimationFrame(animate);
                controls.update(); // only required if controls.enableDamping or controls.autoRotate are set to true
                renderer.render(scene, camera);
            }
                
            animate();
        });
    </script>
</body>

</html>