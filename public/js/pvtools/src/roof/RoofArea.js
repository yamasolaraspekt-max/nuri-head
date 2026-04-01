import * as THREE from "three";
import { scene } from "../App";
import Rafters from "./Rafters";
import SupportBattening from "./SupportBattening";

class RoofArea {
    constructor(roofWidth, roofLength) {
        console.log("Creating RoofArea ...");

        this.width = roofWidth;
        this.length = roofLength;
        this.height = 0.1;

        this.roofAreaGroup = new THREE.Group();

        this.colorArea = 0x555555;
        this.roofAreaRect = new THREE.BoxGeometry(
            this.width,
            this.height,
            this.length
        );
        this.roofAreaMat = new THREE.MeshStandardMaterial({
            color: this.colorArea,
            transparent: true,
            opacity: 0.6,
        });
        this.roofAreaMesh = new THREE.Mesh(this.roofAreaRect, this.roofAreaMat);
        this.roofAreaGroup.add(this.roofAreaMesh);
        this.roofAreaMesh.position.set(0, 0, this.length / 2);

        //this.rafterGroup = new THREE.Group();
        this.rafters = null;
        this._createRafter();

        //this.supportBatteningGroup = new THREE.Group();
        this.supportBattening = null;
        this._createSupportBattening();
    }

    _createRafter() {
        console.log("creating Rafters and Counter Battens"); // Sparren und Konterlattung

        this.rafters = new Rafters(this.width, this.length);
        this.roofAreaGroup.add(this.rafters.getGroup());
    }

    _createSupportBattening() {
        console.log("creating Support Battening"); // Traglattung

        this.supportBattening = new SupportBattening(this.width, this.length);
        this.roofAreaGroup.add(this.supportBattening.getGroup());
    }

    getGroup() {
        return this.roofAreaGroup;
    }
}

export default RoofArea;
