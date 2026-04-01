import * as THREE from 'three';
// import { scene } from '../App';

class Pfette {
    constructor (type, roofAreaWidth, roofAreaLength) {
        this.type = type;
        this.firstPfette = null;
        this.mittelPfette = [];
        this.fussPfette = null;
        this.color = 0x726F5E;

        this.width = 15,
        this.height = 30,
        this.length = roofAreaWidth;

        this.pfetteRect = new THREE.BoxGeometry(this.width, this.height, this.length);
        this.pfetteMat = new THREE.MeshStandardMaterial({ color: this.color });

        this.pfetteMesh = new THREE.Mesh(this.pfetteRect, this.pfetteMat);
        this.pfetteMesh.position.set(0, -35, 0);
        this.pfetteMesh.rotateY(90 * Math.PI/180);

        this.rafterDiv = new THREE.BoxGeometry(1, 20, this.length+1);
        this.rafterDivMat = new THREE.MeshBasicMaterial({ color: 0x303030 });

        this.rafterDivMesh = new THREE.Mesh(this.rafterDiv, this.rafterDivMat);
        this.rafterDivMesh.position.set(0, -10, 0);
        this.rafterDivMesh.rotateY(90 * Math.PI/180);

        this.group = new THREE.Group();

        this.group.add(this.pfetteMesh);
        this.group.add(this.rafterDivMesh);

    }

    getGroup() {
        return this.group;
    }
}

export default Pfette