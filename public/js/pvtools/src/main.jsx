import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.jsx'

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>,
)







/*

import * as THREE from 'three';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import Roof from './roof/Roof';

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
const renderer = new THREE.WebGLRenderer({antialiasing:true});
renderer.setPixelRatio( window.devicePixelRatio * 1.5 );
renderer.setSize( window.innerWidth, window.innerHeight );
//renderer.setClearColor(0x99ff66);
renderer.setClearColor(0x303030);
//renderer.setClearColor(0xffffff);
//document.body.appendChild( renderer.domElement );
//canvas-container.appendChild( renderer.domElement );
document.getElementById('canvas-container').appendChild( renderer.domElement );

// const camera = new THREE.OrthographicCamera( window.innerWidth / window.innerHeight, 1, 500 );
const orthoCamera = new THREE.OrthographicCamera( window.innerWidth / - 2, window.innerWidth / 2, window.innerHeight / 2, window.innerHeight / - 2, 1, 3000 );
orthoCamera.position.set( 0, 1000, 0 );
orthoCamera.lookAt( 0, 0, 0 );

const perspCamera = new THREE.PerspectiveCamera( 60, window.innerWidth / window.innerHeight, 0.1, 10000 );
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



// Initialisierung der Statusleiste
const switchCameraButton = document.getElementById('switchCamera');
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
switchCameraButton.addEventListener('click', switchCamera);

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Dach
const roofProps = {
    "width": 830,
    "length": 600,
    "height": 1,
    "posY": -(0.5 + 18/2),
    "color": 0x555555
};

let roofRect = new THREE.BoxGeometry(roofProps.width, roofProps.height, roofProps.length);
let roofMat = new THREE.MeshBasicMaterial({ color: roofProps.color });

let roofArea1 = new THREE.Mesh(roofRect, roofMat);
roofArea1.position.set(0, roofProps.posY, 0);
//roofGroup.add(roofArea1);

let roofArea2 = new THREE.Mesh(roofRect, roofMat);
roofArea2.position.set(0, roofProps.posY, 0);
roofArea2.rotateX(90 * Math.PI/180);
//roofGroup.add(roofArea2);

const restrictedAreas = [];
const restrictedAreasMesh = [];
const restrictedAreasBB = [];

// Sperrflaechen
const restArea = {
    "width": 180,
    "length": 140,
    "height": 50,
    "posX_left": 168,
    "posX_right": 0,
    "posZ_bottom": -185,
    "posZ_top": 600,
    "color": 0x000000,//0x4D191A
};

restrictedAreas.push( restArea );
restrictedAreas.push( JSON.parse(JSON.stringify(restArea)) );
restrictedAreas[1].length = 60;
restrictedAreas[1].width = 80;
restrictedAreas[1].posX_left = -130;
restrictedAreas[1].posZ_bottom = 120;
restrictedAreas.push( JSON.parse(JSON.stringify(restArea)) );
restrictedAreas[2].length = 80;
restrictedAreas[2].width = 80;
restrictedAreas[2].posX_left = -240;
restrictedAreas[2].posZ_bottom = -160;

console.log(restrictedAreas);

const resAreaGroup = new THREE.Group();

resAreaGroup.position.set(0, 0, 0);

const mainHouseGroup = new THREE.Group();
const roofGroup = new THREE.Group();
const rafterGroup = new THREE.Group();
const batteningGroup = new THREE.Group();
const slatsGroup = new THREE.Group();
const tilesGroup = new THREE.Group();
const modulesGroup = new THREE.Group();

mainHouseGroup.add(roofGroup);
roofGroup.add(roofArea1);
roofGroup.add(roofArea2);
roofGroup.add(rafterGroup);
roofGroup.add(batteningGroup);
roofGroup.add(slatsGroup);
roofGroup.add(tilesGroup);
// roofGroup.add(resAreaGroup);
//roofGroup.add(modulesGroup);
roofGroup.add(orthoCamera);
//mainHouseGroup.rotateY(99.3427 * Math.PI/180);


scene.add(mainHouseGroup);
roofGroup.rotateX(45 * Math.PI/180);

// let restrictedAreaRect = new THREE.BoxGeometry(restArea.width, restArea.height, restArea.length);
let restrictedAreaMat = new THREE.MeshBasicMaterial({ color: restArea.color });
// let restrictedArea = new THREE.Mesh(restrictedAreaRect, restrictedAreaMat);
// restrictedArea.position.set(restArea.posX_left, 0, restArea.posZ_bottom);
//console.log(roofArea1.name);
//const restrictedAreaBB = new THREE.Box3(new THREE.Vector3(), new THREE.Vector3());
//restrictedAreaBB.setFromObject(restrictedArea);

function addRestrictedAreas () {
    for ( let i=0; i<=restrictedAreas.length-1; i++) {
        let restrictedAreaRect = new THREE.BoxGeometry(restrictedAreas[i].width, restrictedAreas[i].height, restrictedAreas[i].length);
        //let restrictedAreaMat = new THREE.MeshBasicMaterial({ color: restrictedAreas[i].color });
        restrictedAreasMesh.push( new THREE.Mesh(restrictedAreaRect, restrictedAreaMat) );
        restrictedAreasMesh[i].position.set(restrictedAreas[i].posX_left, 0, restrictedAreas[i].posZ_bottom);
        
        restrictedAreasBB.push(new THREE.Box3(new THREE.Vector3(), new THREE.Vector3()));
        restrictedAreasBB[i].setFromObject(restrictedAreasMesh[i]);
    
        //console.log("ADDING RA", i);
        resAreaGroup.add(restrictedAreasMesh[i]);
    }
}


// Dachsparren
const rafter = {
    "width": 8,
    "length": 0,
    "height": 18,
}

let p = rafter.height;
let alpha = 38.54;
let h = p * Math.tan(alpha * Math.PI/180);
console.log("Bei", alpha + "° beträgt die Skalierung", h.toFixed(1));

//let rafter.length = roofProps.length;
let rafterDistLeft = 5;
let rafterDistRight = rafterDistLeft;
let rafterTotalNumber = 12;
let rafterRect = new THREE.BoxGeometry(rafter.width, rafter.height, rafter.length);
const rafterMat = new THREE.MeshBasicMaterial({ color: 0x726F5E });//4d4019
//const rafterMat = new THREE.MeshBasicMaterial({ color: 0x305080 });//4d4019

// Konterlattung
let counterBattenWidth = 5;
let counterBattenHeight = 3;
let counterBattenLength = roofProps.length;
let counterBattenDistLeft = rafterDistLeft;
let counterBattenDistRight = rafterDistRight;
let counterBattenTotalNumber = rafterTotalNumber;
let counterBattensPosY = rafter.height/2 + counterBattenHeight/2;
let counterBattenRect = new THREE.BoxGeometry(counterBattenWidth, counterBattenHeight, counterBattenLength);
const counterBattenMat = new THREE.MeshBasicMaterial({ color: 0x4C6E29 });

const counterBattens = [];

// Traglattung (oder Dachlattung, support battening) - Lattenquerschnitte sind 24/48 mm, 30/50 mm und 40/60 mm
let slatWidth = 4.8;
let slatHeight = 2.4;
let slatLength = roofProps.width;
let slatStartPosZ = 0;
let slatStopPosZ = 0;
let slatPosY = rafter.height/2 + counterBattenHeight + slatHeight/2;
let slatTotalNumber = 2;
let slatRect = new THREE.BoxGeometry(slatWidth, slatHeight, slatLength);
const slatMat = new THREE.MeshBasicMaterial({ color: 0x903030 });//4d4019
const slats = [];

function calcSupportBattening() {
    if (slats.length) {
        removeSlats();
    }

    slatLength = roofProps.width;
    let slatStartZAt = -(roofProps.length/2 - 6); // Abstand vom First
    slatStopPosZ = roofProps.length/2 - 6;
    let totalDistanceBetween = Math.abs(slatStartZAt) + slatStopPosZ;
    let distanceBetweenTwo = totalDistanceBetween/(slatTotalNumber-1);
    while (distanceBetweenTwo > 30) {
        slatTotalNumber++;
        distanceBetweenTwo = totalDistanceBetween/(slatTotalNumber-1);
    }
    while (distanceBetweenTwo <= 30) {
        slatTotalNumber--;
        distanceBetweenTwo = totalDistanceBetween/(slatTotalNumber-1);
    }

    let slatPosZ = slatStartPosZ;
        
    slatRect = new THREE.BoxGeometry(slatWidth, slatHeight, slatLength);
    slatRect.rotateY(90 * Math.PI/180);
    for ( let i=0; i<slatTotalNumber; i++) {
        slats.push( new THREE.Mesh(slatRect, slatMat) );
        slats[i].position.set(0, slatPosY, slatStartZAt);
        slatStartZAt += distanceBetweenTwo;
        batteningGroup.add(slats[i]);
    }
}

function removeSlats () {
    //console.log("Removing slats ...");
    for (let i=slats.length-1; i>=0; i--) {
        //console.log("Removing slat:", i);
        batteningGroup.remove(slats[i]);
        slats.pop();
    }

    //console.log(rafters);
}

calcSupportBattening();
addRestrictedAreas ();


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

function updateRoof () {
    scene.remove(roofArea1);
    roofRect = new THREE.BoxGeometry(roofProps.width, 5, roofProps.length);
    roofArea1 = new THREE.Mesh(roofRect, roofMat);
    roofArea1.position.set(0, roofProps.posY, 0);
    //roofGroup.add(roofArea1);

    scene.remove(roofArea2);
    roofArea2 = new THREE.Mesh(roofRect, roofMat);
    roofArea2.position.set(0, roofProps.posY, 0);
    //roofGroup.add(roofArea2);

    calcRafters();
    calcSupportBattening();
    calcModules();
}

const rafters = [];
calcRafters();

function calcRafters() {
    if (rafters.length) {
        removeRafters();      
    }

    rafter.length = roofProps.length;
    let leftPosX = -(roofProps.width/2-rafterDistLeft);
    let rightPosX = roofProps.width/2-rafterDistRight;
    let totalDistanceBetween = Math.abs(leftPosX) + rightPosX;
    let distanceBetweenTwo = totalDistanceBetween/(rafterTotalNumber-1);
    while (distanceBetweenTwo >= 70) {
        rafterTotalNumber++;
        distanceBetweenTwo = totalDistanceBetween/(rafterTotalNumber-1);
    }
    while (distanceBetweenTwo < 70) {
        rafterTotalNumber--;
        distanceBetweenTwo = totalDistanceBetween/(rafterTotalNumber-1);
    }
    console.log("__________________________________________________________________________________________________________");
    console.log("Anzahl der Dachsparren:", rafterTotalNumber);
    console.log("Abstand linker Dachsparren:", rafterDistLeft.toFixed(1) + "cm (Dach außen <-> Mitte Sparren)");
    console.log("Abstand rechter Dachsparren:", rafterDistRight.toFixed(1) + "cm (Dach außen <-> Mitte Sparren)");
    console.log("Berechneter Abstand der Dachsparren:", distanceBetweenTwo.toFixed(1) + "cm (Mitte <-> Mitte)");
    console.log("__________________________________________________________________________________________________________");
    
    rafterRect = new THREE.BoxGeometry(rafter.width, rafter.height, rafter.length);
    counterBattenRect = new THREE.BoxGeometry(counterBattenWidth, counterBattenHeight, rafter.length);
    for ( let i=0; i<rafterTotalNumber; i++) {
        rafters.push( new THREE.Mesh(rafterRect, rafterMat) );
        rafters[i].position.set(leftPosX, 0, 0);
        rafterGroup.add(rafters[i]);

        counterBattens.push( new THREE.Mesh(counterBattenRect, counterBattenMat) );
        counterBattens[i].position.set(leftPosX, counterBattensPosY, 0);
        batteningGroup.add(counterBattens[i]);

        leftPosX += distanceBetweenTwo;
    }
}

function removeRafters () {
    //console.log("Removing Rafters ...");
    for (let i=rafters.length-1; i>=0; i--) {
        //console.log("Removing Rafter:", i);
        rafterGroup.remove(rafters[i]);
        rafters.pop();

        batteningGroup.remove(counterBattens[i]);
        counterBattens.pop();
    }

    //console.log(rafters);
}

// Erstelle eine Linie für die Bemaßung H
// const lineDistance = 100;
// const lineMaterial = new THREE.LineBasicMaterial({ color: 0x606060 });

// const lineGeometryH = new THREE.BufferGeometry();
// const positionsH = [
//     -(roofWidth/2), 2, -(roofHeight/2+lineDistance),  // Startpunkt
//     roofWidth/2, 2, -(roofHeight/2+lineDistance)    // Endpunkt
// ];
// lineGeometryH.setAttribute('position', new THREE.Float32BufferAttribute(positionsH, 3));
// const lineH = new THREE.LineSegments(lineGeometryH, lineMaterial);
// scene.add(lineH);

// // Erstelle eine Linie für die Bemaßung H
// const lineGeometryV = new THREE.BufferGeometry();
// const positionsV = [
//     roofWidth/2+lineDistance, 2, -(roofHeight/2),  // Startpunkt
//     roofWidth/2+lineDistance, 2, roofHeight/2    // Endpunkt
// ];
// lineGeometryV.setAttribute('position', new THREE.Float32BufferAttribute(positionsV, 3));
// const lineV = new THREE.LineSegments(lineGeometryV, lineMaterial);
// scene.add(lineV);

const modules = [];
const modulesBB = [];

calcModules();
function calcModules () {
    
    if (modules.length) {
        //console.log("ArrayIsNOTEmpty");
        removeModules();  
    }

    // Modulereihe horizontal berechnen
    let maxNumberOfmodulesH = 0;
    const maxmodulespaceH = roofProps.width - roofBorder*2;

    let modulespaceLeftOver = maxmodulespaceH;
    while (modulespaceLeftOver >= moduleWidth + moduleDistance) {
        maxNumberOfmodulesH++;
        modulespaceLeftOver -= moduleWidth + moduleDistance;
    }

    if (modulespaceLeftOver >= moduleWidth) {
        maxNumberOfmodulesH++;
    }

    const totalWidthOfAllmodules = (moduleWidth + moduleDistance) * maxNumberOfmodulesH - moduleDistance;
    console.log("Horizontal passen max.", maxNumberOfmodulesH, "PV-Module auf die Dachfläche.");
    console.log("Die Gesamtbreite der Modulereihe beträgt", (totalWidthOfAllmodules/100).toFixed(3), "m (inkl. Abständen zwischen den Modulen)");
    console.log("(Verfügbare Dachbreite:", ((roofProps.width-roofBorder-roofBorder)/100).toFixed(2), "m)");

    // Modulereihe horizontal mittig ausrichten
    const freeSpaceH = roofProps.width - (totalWidthOfAllmodules);
    const freeSpaceLeftRight = freeSpaceH/2;
    console.log("Horizontal Mittig ausrichten (Modulabstand links/rechts, gemessen jew. vom äußeren Dachrand):", (freeSpaceLeftRight/100).toFixed(3), "m)");

    // Modulereihe vertikal berechnen
    let maxNumberOfModulesV = 0;
    const maxmodulespaceV = roofProps.length - roofBorder*2;

    modulespaceLeftOver = maxmodulespaceV;
    while (modulespaceLeftOver >= moduleHeight + moduleDistance) {
        maxNumberOfModulesV++;
        modulespaceLeftOver -= moduleHeight + moduleDistance;
    }

    if (modulespaceLeftOver >= moduleHeight) {
        maxNumberOfModulesV++;
    }

    const totalHeightOfAllModules = (moduleHeight + moduleDistance) * maxNumberOfModulesV - moduleDistance;

    // console.log("__________________________________________________________________________________________________________");
    // console.log("Vertikal passen max.", maxNumberOfModulesV, "PV-Module auf die Dachfläche.");
    // console.log("Die Gesamtlänge der vertikalen Modulereihe beträgt", (totalHeightOfAllModules/100).toFixed(3), "m (inkl. Abständen zwischen den Modulen)");
    // console.log("(Verfügbare Dachlänge:", ((roofHeight-roofBorder-roofBorder)/100).toFixed(2), "m)");

    // Modulereihe vertikal mittig ausrichten
    const freeSpaceV = roofProps.length - (totalHeightOfAllModules);
    const freeSpaceBottomTop = freeSpaceV/2;
    
    //let modulePosX = -(roofWidth/2-moduleWidth/2-freeSpaceLeftRight);
    let modulePosX = -(roofProps.width/2-moduleWidth/2-roofBorder);
    let modulePosY = rafter.width/2 + slatHeight + 10;
    let modulePosZ = roofProps.length/2-moduleHeight/2-freeSpaceBottomTop;

    let startPosX = -(roofProps.width/2-moduleWidth/2-freeSpaceLeftRight);
    //const startPosX = -(roofWidth/2-moduleWidth/2-roofBorder);;

    let nCollisions = 0;
    
    // create Modules
    for (let i=0; i<maxNumberOfModulesV; i++) {
        modules.push(new Array());
        modulesBB.push(new Array());
        for (let j=0; j<maxNumberOfmodulesH; j++) {
            //modules[i].push( new THREE.Mesh(moduleRect, moduleMat) );
            modules[i].push( new THREE.Mesh(moduleRect, new THREE.MeshBasicMaterial().copy(moduleMat)) );

            modules[i][j].position.set(startPosX, modulePosY, modulePosZ);
            modulesGroup.add(modules[i][j]);

            // checkCollision
            modulesBB[i].push(new THREE.Box3(new THREE.Vector3(), new THREE.Vector3()));
            modulesBB[i][j].setFromObject(modules[i][j]);
            //console.log(modulesBB[i][j]);

            // check Collisions
            for (let k=0; k<=restrictedAreasBB.length-1; k++) {
                if (modulesBB[i][j].intersectsBox(restrictedAreasBB[k])) {
                    nCollisions++;
                    modules[i][j].material.transparent = true;
                    modules[i][j].material.opacity = 0;
                }
            }
            
            startPosX += moduleWidth + moduleDistance;
        }

        startPosX = -(roofProps.width/2-moduleWidth/2-freeSpaceLeftRight);
        modulePosZ -= moduleHeight + moduleDistance;
    }

    console.log("__________________________________________________________________________________________________________");
    console.log("Vertikal passen max.", maxNumberOfModulesV, "PV-Module auf die Dachfläche.");
    console.log("Die Gesamtlänge der vertikalen Modulereihe beträgt", (totalHeightOfAllModules/100).toFixed(3), "m (inkl. Abständen zwischen den Modulen)");
    console.log("(Verfügbare Dachlänge:", ((roofProps.length-roofBorder-roofBorder)/100).toFixed(2), "m)");
    console.log("Vertikal Mittig ausrichten (Modulabstand Unten/Oben):", (freeSpaceBottomTop/100).toFixed(3), "m)");
    
    console.log("__________________________________________________________________________________________________________");
    console.log("PV-Module:", modules);
    if (nCollisions > 1) {
        console.log(nCollisions, "COLLISIONS DETECTED");
    }
    else {
        console.log(nCollisions, "COLLISION DETECTED");
    }
    

    // Auswertung
    const maxTotalNumberOfModules = maxNumberOfmodulesH * maxNumberOfModulesV;
    const maxRealNumberOfModules = maxNumberOfmodulesH * maxNumberOfModulesV - nCollisions;

    console.log("__________________________________________________________________________________________________________");
    console.log("Zusammenfassung:");
    console.log("Insgesamt passen", maxRealNumberOfModules, "PV-Module auf die Dachfläche.");
    console.log("Anzahl Module, die aufgrund von Sperrflächen nicht montiert werden können:", nCollisions);
    console.log("Leistung:", maxRealNumberOfModules, "Module x", pvModuleWattPeak, "Wp =", maxRealNumberOfModules * pvModuleWattPeak, "Wp (" + maxRealNumberOfModules * pvModuleWattPeak / 1000, "kWp)");
    console.log("__________________________________________________________________________________________________________");
}

function removeModules() {
    // remove Modules and BoundingBoxes
    //console.log("Removing Mudules ...");
    for (let i=modules.length-1; i>=0; i--) {
        for (let j=modules[i].length-1; j>=0; j--) {
            //console.log("Removing Mudule:", i + "." + j);
            modulesGroup.remove(modules[i][j]);
            modules[i].pop();
            scene.remove(modulesBB[i][j]);
            modulesBB[i].pop();
        }

        modules.pop();
        modulesBB.pop();
    }
}

// Funktion zum Aktualisieren der Breite des Quaders basierend auf dem Eingabefeldwert
document.getElementById('width').addEventListener('input', function(event) {
    console.clear();
    roofProps.width = parseFloat(event.target.value);
    updateRoof();
});

// Funktion zum Aktualisieren der Hoehe des Quaders basierend auf dem Eingabefeldwert
document.getElementById('height').addEventListener('input', function(event) {
    console.clear();
    roofProps.length = parseFloat(event.target.value);
    updateRoof();
});

const createSatteldach = document.getElementById('createSatteldach');
const widthInput = document.getElementById('width');
const heightInput = document.getElementById('height');
const nRafterInput = document.getElementById('nRafter');
const rafterDistLeftInput = document.getElementById('rafterDistLeft');
const rafterDistRightInput = document.getElementById('rafterDistRight');

document.addEventListener('DOMContentLoaded', function() {
    widthInput.value = roofProps.width;
    heightInput.value = roofProps.length;
    nRafterInput.value = rafterTotalNumber;
    rafterDistLeftInput.value = rafterDistLeft;
    rafterDistRightInput.value = rafterDistRight;
});

createSatteldach.addEventListener('click', function() {
    createRoof("Satteldach");
});

widthInput.addEventListener('change', function() {
    console.clear(); // Leere die Konsole
    updateRoof();
});

heightInput.addEventListener('change', function() {
    console.clear();
    updateRoof();
    createRoof();
});

document.getElementById('nRafter').addEventListener('input', function(event) {
    console.clear();
    rafterTotalNumber = parseInt(event.target.value);
    updateRoof();
});

rafterDistLeftInput.addEventListener('change', function() {
    console.clear(); // Leere die Konsole
    rafterDistLeft = parseFloat(event.target.value);
    updateRoof();
});

rafterDistRightInput.addEventListener('change', function() {
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
    resAreaGroup.rotateY( -(.5) * deltaTime );
    // update BB Positions
    for ( let i=0; i<=restrictedAreasBB.length-1; i++ ) {
        restrictedAreasBB[i].copy( restrictedAreasMesh[i].geometry.boundingBox ).applyMatrix4( restrictedAreasMesh[i].matrixWorld );
    }
    
    updateRoof();
}

// Rendere die Szene
let clock = new THREE.Clock();
let deltaTime = 0;

function animate() {
    
    deltaTime = clock.getDelta();
    //moveAndrotateRestrictedAreas(deltaTime);

    if (isOrthoCam) {
        renderer.render(scene, orthoCamera);
        pspCamControls.enabled = false;
    } else {
        renderer.render(scene, perspCamera);
        pspCamControls.enabled = true;
    }

    requestAnimationFrame(animate);
}
animate();



//renderer.render(scene, camera);

let camZoomSpeed = 5;

// Hinzufügen eines Event-Listeners für das Mausrad
window.addEventListener('wheel', (event) => {
    if ( isOrthoCam ) {
        // Zoom-Faktor festlegen (negative Werte für reinzoomen, positive Werte für rauszoomen)
        const zoomFactor = 0.0002;
        //console.log(event.deltaY);
        // + rauszoomen
        if (event.deltaY < 0) {
            camZoomSpeed += zoomFactor;
            if ( camZoomSpeed > 10 ) {
                camZoomSpeed = 10;
            }
        }
        else {
            camZoomSpeed -= zoomFactor;

            if ( camZoomSpeed < 1 ) {
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
window.addEventListener('mousemove', (event) => {
    // // Koordinaten der Mausposition
    // const mouseX = event.clientX;
    // const mouseY = event.clientY;
    
    // // HTML-Element unterhalb des Mauszeigers
    // const elementUnderMouse = document.elementFromPoint(mouseX, mouseY);

    // // Loggen des HTML-Elements in der Konsole (kann entfernt werden)
    // console.log("Element under mouse:", elementUnderMouse);
    
    if ( isOrthoCam ) {
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
window.addEventListener('mousedown', (event) => {
    if (event.button === 1) { // Überprüfen, ob der mittlere Mausknopf (Button 1) gedrückt wurde
        middleMouseDown = true;
        lastMousePosition.x = event.clientX;
        lastMousePosition.y = event.clientY;
    }
});

// Hinzufügen eines Event-Listeners für Mausup, um den Zustand des mittleren Mausknopfs zu aktualisieren
window.addEventListener('mouseup', (event) => {
    if (event.button === 1) { // Überprüfen, ob der mittlere Mausknopf (Button 1) losgelassen wurde
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
window.addEventListener('resize', onWindowResize);

const arrRroof = [];
function createRoof (type) {
    arrRroof.push( new Roof(type) );
    //console.log(myRoof);
    console.log("!!!!!!!!!!!! Roof-Objekt wird erstellt !!!!!!!!!!!!!!!!");
    console.log(arrRroof);
}
*/
