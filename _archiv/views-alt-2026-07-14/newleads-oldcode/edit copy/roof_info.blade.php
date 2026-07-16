<div class="row">
    <div class="col-12 d-flex">
        <div class="form-group row form-element">
            <div class="col-md-12">
                <h3 class="bold">Dachform</h3>
            </div>
            <div class="col-md-12 ">
                <select class="form-control text form-element" name="roof_type" id="roof"> 
                    <option value="Satteldach"   @if( $alternative->roof_type)=="Satteldach" select @endif >Satteldach</option>
                    <option value="Flachdach"  @if( $alternative->roof_type)=="Flachdach" select @endif >Flachdach</option>
                    <option value="Carport"  @if( $alternative->roof_type)=="Carport" select @endif >Carport</option>
                    <option value="Garage"  @if( $alternative->roof_type)=="Garage" select @endif >Garage</option>
                </select>
            </div>
        </div>

        <div class="form-group row form-element">
            <div class="col-md-12">
                <h3 class="bold">Alter</h3>
            </div>
            <div class="col-md-12  ">
                <input type="text" class="form-control text form-element" name="roof_age" id="roof_age" value="{{ old('roof_age', $alternative->roof_age) }}" />
                <span style="position: absolute; right: 20px; top:10px;">Jahr</span>
            
            </div>
            <div class="col-md-12">
                <span id="roof_age_error" class="text-danger"></span>
            </div>
            
        </div>

        <div class="form-group row form-element">
            <div class="col-md-12">
                <h3 class="bold">Eindeckung </h3>
            </div>
            <div class="col-md-12">
                <input type="text" class="form-control text text textbox" name="roof_covering" value="{{ old('roof_covering', $alternative->roof_covering) }}"> 
            </div>
        </div>

        <div class="form-group row form-element">
            <div class="col-md-12">
                <h3 class="bold">Neigung</h3>
                    
            </div>
            <div class="col-md-12 flex_me"> 
                <div class="form-group">
                    <label for="roof_pitch">Dachneigung</label>
                    <select name="roof_pitch" id="roof_pitch" class="form-control text text">
                        <option value="">Auswählen</option>
                        @for($i = 0; $i <= 50; $i += 5)
                            <option value="{{ $i }}" @if($alternative->roof_pitch == $i) selected @endif>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group row form-element">
            <div class="col-md-12">
                <h3 class="bold">Ausrichtung</h3>
            </div>
            <div class="col-md-12 ">
                <select name="roof_direction" id="" class="form-control text"> 
                    <option value="south" @if($alternative->roof_direction=='south') selected @endif >Süden </option>
                    <option value="south-west" @if($alternative->roof_direction=='south-west') selected @endif>Süd-west </option>
                    <option value="west"@if($alternative->roof_direction=='west') selected @endif>Westen </option>
                    <option value="north-west"@if($alternative->roof_direction=='north-west') selected @endif>Nord-west </option>
                    <option value="north"@if($alternative->roof_direction=='north') selected @endif>Norden </option>
                    <option value="north-east"@if($alternative->roof_direction=='north-east') selected @endif>Nord-ost </option>
                    <option value="east"@if($alternative->roof_direction=='east') selected @endif>Osten </option>
                    <option value="south-east"@if($alternative->roof_direction=='south-east') selected @endif>Süd-ost </option>  
                    <option value="east-west"@if($alternative->roof_direction=='east-west') selected @endif>Ost-West</option>  
                    <option value="north-south"@if($alternative->roof_direction=='north-south') selected @endif>Nord-Süd</option> 
                    
                </select> 
            </div>
        </div>
        </div>  

        <div class="form-group row form-element">
            <div class="col-md-12">
                <span>Bemerkung</span>
            </div>
            <!-- //Add this to database -->
            <div class="col-md-12"> 
                <textarea name="roof_remark" style="text-align: left;width: 100%;height: 50px;border-radius: 7px;border: 1px solid #c6c6c6;"> 
                    {{ $alternative->roof_remark }} 
                </textarea> 
            </div>
        </div> 
</div>