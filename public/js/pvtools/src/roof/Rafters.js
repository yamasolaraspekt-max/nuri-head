import * as THREE from 'three';
//import { scene } from '../App';

class Rafters {
    constructor (roofAreaWidth, roofAreaLength) {
        console.log("Creating Rafters ...");

        // Roof Area
        this.roofAreaWidth = roofAreaWidth;
        this.roofAreaLength = roofAreaLength;

        this.rafterGroup = new THREE.Group();
        
        // Dachsparren
        this.rafterMeshes = [];
        this.rafterDistanceLeft = 5;
        this.rafterDistanceRight = this.rafterDistanceLeft;
        this.rafterWidth = 8;
        this.rafterHeight = 18;
        this.rafterMinDistance = 60;
        this.rafterMaxDistance = 90;
        this.rafterTotalNumber = 10;
        this.colorRafters = 0x726F5E;
        this.colorCounterBattens = 0x4C6E29;

        // Konterlattung
        this.counterBattens = [];
        this.counterBattensWidth = 5;
        this.counterBattensHeight = 3;
        this.counterBattensGroup = new THREE.Group();

        this.createRafter();

        this.rafterGroup.add(this.counterBattensGroup);
        //this.rafterGroup.position.set(0, -(this.rafterHeight)/2 + -(this.counterBattensHeight)/2, 0);
        this.rafterGroup.position.set(0, 0, 0);
    }

    createRafter() {
        // Abschraegung der Dachsparren am First berechnen
        let p = 800;
        let alpha = 40;
        let h = p * Math.tan(alpha * Math.PI/180);
        //console.log("Bei", alpha + "° beträgt die Skalierung", h.toFixed(1));
        //////////////////////////////////////////////////

        // Mesh und Material erstellen
        const rafterRect = new THREE.BoxGeometry(this.rafterWidth, this.rafterHeight, this.roofAreaLength);
        const rafterMat = new THREE.MeshBasicMaterial({ color: this.colorRafters });

        const counterBattenRect = new THREE.BoxGeometry(this.counterBattensWidth, this.counterBattensHeight, this.roofAreaLength);
        const counterBattenMat = new THREE.MeshBasicMaterial({ color: this.colorCounterBattens });
    
        let leftPosX = -(this.roofAreaWidth/2 - this.rafterDistanceLeft);
        let rightPosX = this.roofAreaWidth/2-this.rafterDistanceRight;
        let totalDistanceBetween = Math.abs(leftPosX) + rightPosX;
        let distanceBetweenTwo = totalDistanceBetween/(this.rafterTotalNumber-1);
        while (distanceBetweenTwo >= this.rafterMaxDistance) {
            this.rafterTotalNumber++;
            distanceBetweenTwo = totalDistanceBetween/(this.rafterTotalNumber-1);
        }
        while (distanceBetweenTwo < this.rafterMinDistance) {
            this.rafterTotalNumber--;
            distanceBetweenTwo = totalDistanceBetween/(this.rafterTotalNumber-1);
        }
        console.log("__________________________________________________________________________________________________________");
        console.log("Anzahl der Dachsparren:", this.rafterTotalNumber);
        console.log("Abstand linker Dachsparren:", this.rafterDistanceLeft.toFixed(1) + "cm (Dach außen <-> Mitte Sparren)");
        console.log("Abstand rechter Dachsparren:", this.rafterDistanceRight.toFixed(1) + "cm (Dach außen <-> Mitte Sparren)");
        console.log("Berechneter Abstand der Dachsparren:", distanceBetweenTwo.toFixed(1) + "cm (Mitte <-> Mitte)");
        console.log("__________________________________________________________________________________________________________");
        
        // counterBattenRect = new THREE.BoxGeometry(counterBattenWidth, counterBattenHeight, rafter.length);
        for ( let i=0; i<this.rafterTotalNumber; i++) {
            this.rafterMeshes.push( new THREE.Mesh(rafterRect, rafterMat) );
            this.rafterMeshes[i].position.set(leftPosX, -(this.rafterHeight/2), this.roofAreaLength/2);
            //this.rafterMeshes[i].position.set(leftPosX, 0, this.roofAreaLength/2);
            this.rafterGroup.add(this.rafterMeshes[i]);
    
            this.counterBattens.push( new THREE.Mesh(counterBattenRect, counterBattenMat) );
            this.counterBattens[i].position.set(leftPosX, this.counterBattensHeight/2, this.roofAreaLength/2);
            this.counterBattensGroup.add(this.counterBattens[i]);
    
            leftPosX += distanceBetweenTwo;
        }
    }    

    getLayerHeight () {
        return this.rafterHeight;
    }
    
    getGroup () {
        return this.rafterGroup;
    }
}

export default Rafters;