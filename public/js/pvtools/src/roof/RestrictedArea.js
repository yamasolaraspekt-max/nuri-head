// Sperrflaechen
const restrictedAreas = [];
const restrictedAreasMesh = [];
const restrictedAreasBB = [];

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


// let restrictedAreaRect = new THREE.BoxGeometry(restArea.width, restArea.height, restArea.length);
let restrictedAreaMat = new THREE.MeshBasicMaterial({ color: restArea.color });
// let restrictedArea = new THREE.Mesh(restrictedAreaRect, restrictedAreaMat);
// restrictedArea.position.set(restArea.posX_left, 0, restArea.posZ_bottom);
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