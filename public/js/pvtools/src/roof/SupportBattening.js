import * as THREE from 'three';


class SupportBattening {
    constructor(roofAreaWidth, roofAreaLength) {
        this.roofAreaWidth = roofAreaWidth;
        this.roofAreaLength = roofAreaLength;

        this.slatWidth = 4.8;
        this.slatHeight = 2.4;
        this.slatLength = this.roofAreaWidth;
        
        this.slats = [];
        this.supportBatteningGroup = new THREE.Group();

        this._createSupportBattening();
    }

    _createSupportBattening() {
        // if (slats.length) {
        //     removeSlats();
        // }
    
        let slatTotalNumber = 2;
        let slatStartZAt = 6; // Abstand vom First
        //let slatStartZAt = -(this.roofAreaLength/2 - 6); // Abstand vom First
        let slatStopPosZ = this.roofAreaLength - 6;
        let totalDistanceBetween = Math.abs(slatStartZAt) + slatStopPosZ;
        let distanceBetweenTwo = totalDistanceBetween/(slatTotalNumber-1);
        while (distanceBetweenTwo > 34) {
            slatTotalNumber++;
            distanceBetweenTwo = totalDistanceBetween/(slatTotalNumber-1);
        }
        while (distanceBetweenTwo <= 30) {
            slatTotalNumber--;
            distanceBetweenTwo = totalDistanceBetween/(slatTotalNumber-1);
        }

        // create Geometry and Material
        const slatRect = new THREE.BoxGeometry(this.slatWidth, this.slatHeight, this.slatLength);
        const slatMat = new THREE.MeshBasicMaterial({ color: 0x903030 });//4d4019
    
        slatRect.rotateY(90 * Math.PI/180);
        for ( let i=0; i<slatTotalNumber; i++) {
            this.slats.push( new THREE.Mesh(slatRect, slatMat) );
            this.slats[i].position.set(0, this.slatHeight/2+3, slatStartZAt);
            slatStartZAt += distanceBetweenTwo;
            this.supportBatteningGroup.add(this.slats[i]);
        }
    }

    removeSlats() {
        //console.log("Removing slats ...");
        for (let i=slats.length-1; i>=0; i--) {
            //console.log("Removing slat:", i);
            this.supportBatteningGroup.remove(slats[i]);
            this.slats.pop();
        }
    
        //console.log(rafters);
    }

    getLayerHeight () {
        return this.slatHeight;
    }
    
    getGroup() {
        return this.supportBatteningGroup;
    }
}

export default SupportBattening;
