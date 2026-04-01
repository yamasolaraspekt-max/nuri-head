import * as THREE from "three";
import { scene } from "../App";
import RoofArea from "./RoofArea";
import Pfette from "./Pfette";

class Roof {
    constructor(type) {
        this.roofType = type;

        this.roofAreas = [];
        this.roofGroup = new THREE.Group();
        this.roofGroup.position.set(0, 500, 0);

        this.roofType === "Satteldach" && this._createSatteldach();

        scene.add(this.roofGroup);
    }

    _createSatteldach() {
        console.log(this.roofType, "wird erstellt");

        let defaultWidth = 1000; // cm
        let defaultLength = 1000; // cm

        this.roofAreas.push(new RoofArea(defaultWidth, defaultLength));
        this.roofAreas.push(new RoofArea(defaultWidth, defaultLength));

        let tilt = 45; // Degree

        // Firstpfette
        this.roofGroup.add(
            new Pfette("Firstpfette", defaultWidth, defaultLength).getGroup()
        );

        this.roofGroup.add(this.roofAreas[0].getGroup());
        this.roofAreas[0].getGroup().rotateX((tilt * Math.PI) / 180);

        this.roofGroup.add(this.roofAreas[1].getGroup());
        this.roofAreas[1].getGroup().rotateY((180 * Math.PI) / 180);
        this.roofAreas[1].getGroup().rotateX((tilt * Math.PI) / 180);
    }
}

export default Roof;
