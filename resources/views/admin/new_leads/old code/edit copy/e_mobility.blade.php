<div class="row">
    <div class="col-md-6 mt-1">
        <span>Elektroauto</span>
        <select class="form-control text" name="electric_car" id="electric_car">
            <option selected disabled></option>
            <option value="Ja">Ja</option>
            <option value="Nein">Nein</option>
            <option value="Geplant">Geplant</option>
        </select>
    </div>
    <div class="col-md-6 mt-1" id="electric_car_plan" style="display: none;">
        <span>Anzahl</span>
        <input type="text" class="form-control text" name="electric_car_plan" value="{{ old('electric_car_plan') }}">
    </div>
    <div class="col-md-6 mt-1">
        <span>Fahrleistung</span>
        <div class="position-relative">
            <input type="text" class="form-control text" name="car_kilo" value="{{ old('car_kilo') }}">
            <span style="position: absolute;right: 20px;top: 8px;">km</span>
        </div>
    </div>
    <div class="col-md-12">
        <span>Bemerkung</span>
        <textarea name="car_remark" class="form-control" style="height: 50px;">{{ old('car_remark') }}</textarea>
    </div>
</div> 

