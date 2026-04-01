// const modules = [];
// const modulesBB = [];

// calcModules();
// function calcModules () {
    
//     if (modules.length) {
//         //console.log("ArrayIsNOTEmpty");
//         removeModules();  
//     }

//     // Modulereihe horizontal berechnen
//     let maxNumberOfmodulesH = 0;
//     const maxmodulespaceH = roofProps.width - roofBorder*2;

//     let modulespaceLeftOver = maxmodulespaceH;
//     while (modulespaceLeftOver >= moduleWidth + moduleDistance) {
//         maxNumberOfmodulesH++;
//         modulespaceLeftOver -= moduleWidth + moduleDistance;
//     }

//     if (modulespaceLeftOver >= moduleWidth) {
//         maxNumberOfmodulesH++;
//     }

//     const totalWidthOfAllmodules = (moduleWidth + moduleDistance) * maxNumberOfmodulesH - moduleDistance;
//     console.log("Horizontal passen max.", maxNumberOfmodulesH, "PV-Module auf die Dachfläche.");
//     console.log("Die Gesamtbreite der Modulereihe beträgt", (totalWidthOfAllmodules/100).toFixed(3), "m (inkl. Abständen zwischen den Modulen)");
//     console.log("(Verfügbare Dachbreite:", ((roofProps.width-roofBorder-roofBorder)/100).toFixed(2), "m)");

//     // Modulereihe horizontal mittig ausrichten
//     const freeSpaceH = roofProps.width - (totalWidthOfAllmodules);
//     const freeSpaceLeftRight = freeSpaceH/2;
//     console.log("Horizontal Mittig ausrichten (Modulabstand links/rechts, gemessen jew. vom äußeren Dachrand):", (freeSpaceLeftRight/100).toFixed(3), "m)");

//     // Modulereihe vertikal berechnen
//     let maxNumberOfModulesV = 0;
//     const maxmodulespaceV = roofProps.length - roofBorder*2;

//     modulespaceLeftOver = maxmodulespaceV;
//     while (modulespaceLeftOver >= moduleHeight + moduleDistance) {
//         maxNumberOfModulesV++;
//         modulespaceLeftOver -= moduleHeight + moduleDistance;
//     }

//     if (modulespaceLeftOver >= moduleHeight) {
//         maxNumberOfModulesV++;
//     }

//     const totalHeightOfAllModules = (moduleHeight + moduleDistance) * maxNumberOfModulesV - moduleDistance;

//     // console.log("__________________________________________________________________________________________________________");
//     // console.log("Vertikal passen max.", maxNumberOfModulesV, "PV-Module auf die Dachfläche.");
//     // console.log("Die Gesamtlänge der vertikalen Modulereihe beträgt", (totalHeightOfAllModules/100).toFixed(3), "m (inkl. Abständen zwischen den Modulen)");
//     // console.log("(Verfügbare Dachlänge:", ((roofHeight-roofBorder-roofBorder)/100).toFixed(2), "m)");

//     // Modulereihe vertikal mittig ausrichten
//     const freeSpaceV = roofProps.length - (totalHeightOfAllModules);
//     const freeSpaceBottomTop = freeSpaceV/2;
    
//     //let modulePosX = -(roofWidth/2-moduleWidth/2-freeSpaceLeftRight);
//     let modulePosX = -(roofProps.width/2-moduleWidth/2-roofBorder);
//     let modulePosY = rafter.width/2 + slatHeight + 10;
//     let modulePosZ = roofProps.length/2-moduleHeight/2-freeSpaceBottomTop;

//     let startPosX = -(roofProps.width/2-moduleWidth/2-freeSpaceLeftRight);
//     //const startPosX = -(roofWidth/2-moduleWidth/2-roofBorder);;

//     let nCollisions = 0;
    
//     // create Modules
//     for (let i=0; i<maxNumberOfModulesV; i++) {
//         modules.push(new Array());
//         modulesBB.push(new Array());
//         for (let j=0; j<maxNumberOfmodulesH; j++) {
//             //modules[i].push( new THREE.Mesh(moduleRect, moduleMat) );
//             modules[i].push( new THREE.Mesh(moduleRect, new THREE.MeshBasicMaterial().copy(moduleMat)) );

//             modules[i][j].position.set(startPosX, modulePosY, modulePosZ);
//             modulesGroup.add(modules[i][j]);

//             // checkCollision
//             modulesBB[i].push(new THREE.Box3(new THREE.Vector3(), new THREE.Vector3()));
//             modulesBB[i][j].setFromObject(modules[i][j]);
//             //console.log(modulesBB[i][j]);

//             // check Collisions
//             for (let k=0; k<=restrictedAreasBB.length-1; k++) {
//                 if (modulesBB[i][j].intersectsBox(restrictedAreasBB[k])) {
//                     nCollisions++;
//                     modules[i][j].material.transparent = true;
//                     modules[i][j].material.opacity = 0;
//                 }
//             }
            
//             startPosX += moduleWidth + moduleDistance;
//         }

//         startPosX = -(roofProps.width/2-moduleWidth/2-freeSpaceLeftRight);
//         modulePosZ -= moduleHeight + moduleDistance;
//     }

//     console.log("__________________________________________________________________________________________________________");
//     console.log("Vertikal passen max.", maxNumberOfModulesV, "PV-Module auf die Dachfläche.");
//     console.log("Die Gesamtlänge der vertikalen Modulereihe beträgt", (totalHeightOfAllModules/100).toFixed(3), "m (inkl. Abständen zwischen den Modulen)");
//     console.log("(Verfügbare Dachlänge:", ((roofProps.length-roofBorder-roofBorder)/100).toFixed(2), "m)");
//     console.log("Vertikal Mittig ausrichten (Modulabstand Unten/Oben):", (freeSpaceBottomTop/100).toFixed(3), "m)");
    
//     console.log("__________________________________________________________________________________________________________");
//     console.log("PV-Module:", modules);
//     if (nCollisions > 1) {
//         console.log(nCollisions, "COLLISIONS DETECTED");
//     }
//     else {
//         console.log(nCollisions, "COLLISION DETECTED");
//     }
    

//     // Auswertung
//     const maxTotalNumberOfModules = maxNumberOfmodulesH * maxNumberOfModulesV;
//     const maxRealNumberOfModules = maxNumberOfmodulesH * maxNumberOfModulesV - nCollisions;

//     console.log("__________________________________________________________________________________________________________");
//     console.log("Zusammenfassung:");
//     console.log("Insgesamt passen", maxRealNumberOfModules, "PV-Module auf die Dachfläche.");
//     console.log("Anzahl Module, die aufgrund von Sperrflächen nicht montiert werden können:", nCollisions);
//     console.log("Leistung:", maxRealNumberOfModules, "Module x", pvModuleWattPeak, "Wp =", maxRealNumberOfModules * pvModuleWattPeak, "Wp (" + maxRealNumberOfModules * pvModuleWattPeak / 1000, "kWp)");
//     console.log("__________________________________________________________________________________________________________");
// }

// function removeModules() {
//     // remove Modules and BoundingBoxes
//     //console.log("Removing Mudules ...");
//     for (let i=modules.length-1; i>=0; i--) {
//         for (let j=modules[i].length-1; j>=0; j--) {
//             //console.log("Removing Mudule:", i + "." + j);
//             modulesGroup.remove(modules[i][j]);
//             modules[i].pop();
//             scene.remove(modulesBB[i][j]);
//             modulesBB[i].pop();
//         }

//         modules.pop();
//         modulesBB.pop();
//     }
// }