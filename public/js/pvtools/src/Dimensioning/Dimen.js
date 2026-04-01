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