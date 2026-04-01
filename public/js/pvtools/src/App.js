import * as THREE from "three";
import { OrbitControls } from "three/examples/jsm/controls/OrbitControls.js";
import Roof from "./roof/Roof";

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
const renderer = new THREE.WebGLRenderer({ antialiasing: true });
renderer.setPixelRatio(window.devicePixelRatio * 1.5);
renderer.setSize(window.innerWidth, window.innerHeight);
//renderer.setClearColor(0x99ff66);
//renderer.setClearColor(0x303030);
renderer.setClearColor(0xc0c0c0);
//renderer.setClearColor(0xffffff);
//document.body.appendChild( renderer.domElement );
//canvas-container.appendChild( renderer.domElement );
document.getElementById("canvas-container").appendChild(renderer.domElement);

// const camera = new THREE.OrthographicCamera( window.innerWidth / window.innerHeight, 1, 500 );
const orthoCamera = new THREE.OrthographicCamera(
    window.innerWidth / -2,
    window.innerWidth / 2,
    window.innerHeight / 2,
    window.innerHeight / -2,
    1,
    3000
);
orthoCamera.position.set(0, 1000, 0);
orthoCamera.lookAt(0, 0, 0);

const perspCamera = new THREE.PerspectiveCamera(
    60,
    window.innerWidth / window.innerHeight,
    0.1,
    10000
);
perspCamera.position.set(0, 800, 0);
perspCamera.lookAt(0, 0, 0);
const pspCamControls = new OrbitControls(perspCamera, renderer.domElement);

// Aktueller Zoomfaktor
var zoomFactor = 2; // Zum Beispiel: um den Zoom um das Doppelte zu ändern

// Berechne die neuen Grenzen basierend auf dem aktuellen Zoomfaktor
orthoCamera.left *= zoomFactor;
orthoCamera.right *= zoomFactor;
orthoCamera.top *= zoomFactor;
orthoCamera.bottom *= zoomFactor;

// Aktualisiere die Kamera
orthoCamera.updateProjectionMatrix();

export const scene = new THREE.Scene();
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

const ambiLight = new THREE.AmbientLight(0xffffff, 1.1);
scene.add(ambiLight);

// Grid
// const size = 1000;
// const divisions = 10;
// const gridHelper = new THREE.GridHelper( size, divisions );
// scene.add( gridHelper );

// Initialisierung der Statusleiste
const switchCameraButton = document.getElementById("switchCamera");
let isOrthoCam = true; // Anfangs ist Orthografiekamera aktiv

// Funktion zum Umschalten der Kamera
function switchCamera() {
    if (isOrthoCam) {
        // Schalte auf Perspektivkamera um
        renderer.render(scene, perspCamera);
        isOrthoCam = false;
        switchCameraButton.textContent = "Switch to Top-View (2D)";
    } else {
        // Schalte auf Orthografiekamera um
        renderer.render(scene, orthoCamera);
        isOrthoCam = true;
        switchCameraButton.textContent = "Switch to 3D-View";
    }
}

// Event Listener für Button-Klick
switchCameraButton.addEventListener("click", switchCamera);

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

const mainHouseGroup = new THREE.Group();
const roofGroup = new THREE.Group();
const rafterGroup = new THREE.Group();
const batteningGroup = new THREE.Group();
const slatsGroup = new THREE.Group();
const tilesGroup = new THREE.Group();
const modulesGroup = new THREE.Group();

mainHouseGroup.add(roofGroup);
roofGroup.add(orthoCamera);
scene.add(mainHouseGroup);
roofGroup.rotateX((45 * Math.PI) / 180);

// pvmodule
const roofBorder = 10;
const moduleDistance = 2;
let moduleWidth = 113.4;
let moduleHeight = 172.2;
// moduleWidth = 172.2;
// moduleHeight = 113.4;
const pvModuleWattPeak = 440;
const moduleMat = new THREE.MeshBasicMaterial({ color: 0x151515 });
moduleMat.transparent = true;
moduleMat.opacity = 0.75;
//moduleMat.needsUpdate = true;
const moduleRect = new THREE.BoxGeometry(moduleWidth, 3, moduleHeight);

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Funktion zum Aktualisieren der Breite des Quaders basierend auf dem Eingabefeldwert
document.getElementById("width").addEventListener("input", function (event) {
    console.clear();
    roofProps.width = parseFloat(event.target.value);
    updateRoof();
});

// Funktion zum Aktualisieren der Hoehe des Quaders basierend auf dem Eingabefeldwert
document.getElementById("height").addEventListener("input", function (event) {
    console.clear();
    roofProps.length = parseFloat(event.target.value);
    updateRoof();
});

const createSatteldach = document.getElementById("createSatteldach");
const widthInput = document.getElementById("width");
const heightInput = document.getElementById("height");
const nRafterInput = document.getElementById("nRafter");
const rafterDistLeftInput = document.getElementById("rafterDistLeft");
const rafterDistRightInput = document.getElementById("rafterDistRight");

document.addEventListener("DOMContentLoaded", function () {
    //widthInput.value = roofProps.width;
    //heightInput.value = roofProps.length;
    nRafterInput.value = 0; //rafterTotalNumber;
    rafterDistLeftInput.value = rafterDistLeft;
    rafterDistRightInput.value = rafterDistRight;
});

createSatteldach.addEventListener("click", function () {
    createRoof("Satteldach");
});

widthInput.addEventListener("change", function () {
    console.clear(); // Leere die Konsole
    updateRoof();
});

heightInput.addEventListener("change", function () {
    console.clear();
    updateRoof();
});

document.getElementById("nRafter").addEventListener("input", function (event) {
    console.clear();
    rafterTotalNumber = parseInt(event.target.value);
    updateRoof();
});

rafterDistLeftInput.addEventListener("change", function () {
    console.clear(); // Leere die Konsole
    rafterDistLeft = parseFloat(event.target.value);
    updateRoof();
});

rafterDistRightInput.addEventListener("change", function () {
    console.clear(); // Leere die Konsole
    rafterDistRight = parseFloat(event.target.value);
    updateRoof();
});

function moveAndrotateRestrictedAreas(deltaTime) {
    resAreaGroup.position.x += 200 * deltaTime;
    let vPos = new THREE.Vector3();
    resAreaGroup.getWorldPosition(vPos);
    //console.log(vPos);
    if (vPos.x > 1000) {
        resAreaGroup.position.set(-1000, 0, 0);
    }
    resAreaGroup.rotateY(-0.5 * deltaTime);
    // update BB Positions
    for (let i = 0; i <= restrictedAreasBB.length - 1; i++) {
        restrictedAreasBB[i]
            .copy(restrictedAreasMesh[i].geometry.boundingBox)
            .applyMatrix4(restrictedAreasMesh[i].matrixWorld);
    }

    updateRoof();
}

// Rendere die Szene
let clock = new THREE.Clock();
let deltaTime = 0;

function animate() {
    deltaTime = clock.getDelta();
    //moveAndrotateRestrictedAreas(deltaTime);

    renderer.render(scene, perspCamera);
    pspCamControls.enabled = true;

    requestAnimationFrame(animate);
}
animate();

let camZoomSpeed = 5;

// Hinzufügen eines Event-Listeners für das Mausrad
window.addEventListener("wheel", (event) => {
    if (isOrthoCam) {
        // Zoom-Faktor festlegen (negative Werte für reinzoomen, positive Werte für rauszoomen)
        const zoomFactor = 0.0002;
        //console.log(event.deltaY);
        // + rauszoomen
        if (event.deltaY < 0) {
            camZoomSpeed += zoomFactor;
            if (camZoomSpeed > 10) {
                camZoomSpeed = 10;
            }
        } else {
            camZoomSpeed -= zoomFactor;

            if (camZoomSpeed < 1) {
                camZoomSpeed = 1;
            }
        }
        // Berechnen der neuen Kameraposition basierend auf dem Mausrad-Delta
        orthoCamera.zoom -= event.deltaY * zoomFactor * camZoomSpeed;
        // Begrenze den Zoom auf einen sinnvollen Bereich
        orthoCamera.zoom = Math.max(0.1, orthoCamera.zoom);
        orthoCamera.updateProjectionMatrix(); // Aktualisieren der Kamera-Projektionsmatrix
    }
});

// Variable, um den letzten Mausposition zu speichern
let lastMousePosition = { x: 0, y: 0 };
let middleMouseDown = false;

// Hinzufügen eines Event-Listeners für Mausbewegungen
window.addEventListener("mousemove", (event) => {
    // // Koordinaten der Mausposition
    // const mouseX = event.clientX;
    // const mouseY = event.clientY;

    // // HTML-Element unterhalb des Mauszeigers
    // const elementUnderMouse = document.elementFromPoint(mouseX, mouseY);

    // // Loggen des HTML-Elements in der Konsole (kann entfernt werden)
    // console.log("Element under mouse:", elementUnderMouse);

    if (isOrthoCam) {
        if (middleMouseDown) {
            // Berechne die Änderung der Mausposition seit dem letzten Frame
            const mouseDeltaX = event.clientX - lastMousePosition.x;
            const mouseDeltaY = event.clientY - lastMousePosition.y;

            // Aktualisiere die Kameraposition basierend auf der Mausbewegung
            orthoCamera.position.x -= mouseDeltaX * 2.1;
            orthoCamera.position.z -= mouseDeltaY * 2.1;

            // Speichere die aktuelle Mausposition für den nächsten Frame
            lastMousePosition.x = event.clientX;
            lastMousePosition.y = event.clientY;
        }
    }
});

// Hinzufügen eines Event-Listeners für Mausdown, um die letzte Mausposition zu aktualisieren
window.addEventListener("mousedown", (event) => {
    if (event.button === 1) {
        // Überprüfen, ob der mittlere Mausknopf (Button 1) gedrückt wurde
        middleMouseDown = true;
        lastMousePosition.x = event.clientX;
        lastMousePosition.y = event.clientY;
    }
});

// Hinzufügen eines Event-Listeners für Mausup, um den Zustand des mittleren Mausknopfs zu aktualisieren
window.addEventListener("mouseup", (event) => {
    if (event.button === 1) {
        // Überprüfen, ob der mittlere Mausknopf (Button 1) losgelassen wurde
        middleMouseDown = false;
    }
});

// Funktion zum Aktualisieren der Kamera- und Renderergröße bei Änderungen der Fenstergröße
function onWindowResize() {
    // Aktualisiere die Größe des Renderers
    renderer.setSize(window.innerWidth, window.innerHeight);

    // orthoCam
    orthoCamera.left = -window.innerWidth / 2;
    orthoCamera.right = window.innerWidth / 2;
    orthoCamera.top = window.innerHeight / 2;
    orthoCamera.bottom = -window.innerHeight / 2;
    orthoCamera.updateProjectionMatrix();

    // pspCam
    perspCamera.aspect = window.innerWidth / window.innerHeight;
    perspCamera.updateProjectionMatrix();
    perspCamera.lookAt(0, 0, 0);
}

// Event-Listener für das 'resize'-Ereignis, um die Funktion zum Anpassen der Größe der Szene aufzurufen
window.addEventListener("resize", onWindowResize);

//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////

function createRoof(type) {
    console.log("Roof-Objekt wird erstellt ...");
    let myRoof = new Roof("Satteldach");
}
