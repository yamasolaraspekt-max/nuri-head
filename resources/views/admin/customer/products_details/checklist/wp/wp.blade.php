<style>
    .coll {
        background: #cfe09b;
        border: 0;
            border-bottom: 10px solid #f8f8f8;
    }


.progress {

    height: 23px !important;
    border: 1px solid gray !important;
    border-radius: 6px !important;

}

.progress-bar {
    width: 60%;
    height: 23px;
    border-radius: 0 !important;
    background-color: #e50056 !important;
}

</style>

<section>
    <div class="row mb-3">
        <div class="col-12">
            <div class="cards"> 
                <div class="card-content">
                    <div class="card-body"> 
                        <div class="row">
                        <div class="col-lg-12 col-md-12">
                                <div class="progress-container">
                                    @foreach ($phases as $progress)   
                                    <div class="progress-item ">{{ $progress->phase_name }}</div> 
                                    @endforeach
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</section> 
    <div class="row"> 
        <article class="mb-2">
            <div class="col-md-12" style="display:flex;">
                <div class="col-md-1 ">
                    <span>Intention</span>
                </div>
                <div class="col-md-7">
                    <ul class="list-unstyled mb-0">
                        <li class="d-inline-block mr-1">
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="intention" id="intention_interest"
                                        value="Interesse">
                                    <label class="custom-control-label" for="intention_interest">Interesse</label>
                                </div>
                            </fieldset>
                        </li>
                        <li class="d-inline-block mr-1">
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="intention" id="intention_available"
                                        value="vorhanden">
                                    <label class="custom-control-label" for="intention_available">vorhanden</label>
                                </div>
                            </fieldset>
                        </li>
                        <li class="d-inline-block mr-1">
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="intention" id="intention_extension"
                                        value="Erweiterung">
                                    <label class="custom-control-label" for="intention_extension">Erweiterung</label>
                                </div>
                            </fieldset>
                        </li>
                        <li class="d-inline-block mr-1">
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="intention" id="intention_spater"
                                        value="später">
                                    <label class="custom-control-label" for="intention_spater">später</label>
                                </div>
                            </fieldset>
                        </li>
                        <li class="d-inline-block mr-1">
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input danger" name="intention" id="intention_absage" value="Absage">
                                    <label class="custom-control-label" for="intention_absage">Absage</label>
                                </div>
                            </fieldset>
                        </li>
                    </ul>
                </div>  
        </article>  
    </div>
    <hr class="normal">

        
      <div class="col-12">
        <div id="accordionWrapa10" role="tablist" aria-multiselectable="true">
            <div class="cards collapse-icon accordion-icon-rotate"> 
                <div class="card-content">
                    <div class="card-body p-0"> 
                        <div class="default-collapse collapse-bordered"> 
                            <div class="cards collapse-header">
                                <div id="heading11" class="card-header" data-toggle="collapse" role="button" data-target="#accordion10" aria-expanded="true" aria-controls="accordion10">
                                    <span class="lead collapse-title">
                                        <h2 class="bold section_title ">OBJEKTDATEN</h2>
                                    </span>
                                </div>
                                <div id="accordion10" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading11" class="collapse show" style="">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">Baujahr </h4>
                                                        </div>
                                                        <div class="col-md-8 textbox-container"> 
                                                             {{ $wp_checklist->construction_year }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group row ">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">beheizte Wohnfläche</h4>
                                                        </div>
                                                        <div class="col-md-8 flex_me">
                                                            {{$wp_checklist->living_space}}
                                                            <span> m²</span> </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group row ">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">Nutzfläche</h4>
                                                        </div>
                                                        <div class="col-md-8 flex_me">
                                                             {{$wp_checklist->unusable_space}} 
                                                            <span> m²</span> </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group row ">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">Anzahl Personen</h4>
                                                        </div>
                                                        <div class="col-md-8">
                                                            {{$wp_checklist->number_people}}  
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">Anzahl WE</h4>
                                                        </div>
                                                        <div class="col-md-8 textbox-container">
                                                            {{$wp_checklist->wp_number_we}}   
                                                            <div class="indicator"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">Anzahl Geschoß</h4>
                                                        </div>
                                                        <div class="col-md-8"> 
                                                            {{$wp_checklist->wp_number_stories}}   
                                                        </div>
                                                    </div>
                                                </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <h4 class="bold ">Fensterverglasung</h4>
                                                            </div>
                                                            <div class="col-md-10">
                                                                @if($wp_checklist->glass1 == "on") 1-fach,  @endif
                                                                @if($wp_checklist->glass2 == "on") 2-fach,  @endif
                                                                @if($wp_checklist->glass3 == "on") 3-fach @endif  
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <h4 class="bold ">Fensterrahmen</h4>
                                                            </div>
                                                            <div class="col-md-10">
                                                            {{$wp_checklist->window_margin}}    
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group row ">
                                                            <div class="col-md-4">
                                                                <h4 class="bold ">Aussendämmung Stärke</h4>
                                                            </div>
                                                            <div class="col-md-8 flex_me">
                                                            {{$wp_checklist->insulation_thickness}}     
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                    </div>

                                                    <div class="col-6">
                                                        <div class="form-group row ">
                                                            <div class="col-md-4">
                                                                <h4 class="bold ">Mauerart</h4>
                                                            </div>
                                                            <div class="col-md-8 flex_me">
                                                             {{$wp_checklist->wall_type}}      
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-6">
                                                        <div class="form-group row ">
                                                            <div class="col-md-4">
                                                                <h4 class="bold ">Mauer-stärke</h4>
                                                            </div>
                                                            <div class="col-md-8 flex_me">
                                                             {{$wp_checklist->wall_thickness}}      

                                                                <span>cm</span>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <h3 class="bold">Aufdachdämmung</h3>
                                                            </div>
                                                            <div class="col-md-10">

                                                                <ul class="list-unstyled mb-0"> 
                                                                     <li class="d-inline-block mr-1"
                                                                        style="width:330px">
                                                                        <div class="form-group row"> 
                                                                            <div class="col-md-8">
                                                                             {{$wp_checklist->wp_insulation}}       
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                    <li class="d-inline-block mr-1"
                                                                        style="width:330px">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-4">
                                                                                <h4 class="bold">Stärke</h4>
                                                                            </div>
                                                                            <div class="col-md-8">
                                                                                 {{$wp_checklist->wp_insulation_strength}}     
                                                                                
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <h3 class="bold">Zwischen sparrendämmung</h3>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <ul class="list-unstyled mb-0">
                                                                     <li class="d-inline-block mr-1"
                                                                        style="width:330px">
                                                                        <div class="form-group row"> 
                                                                            <div class="col-md-8">                                                               
                                                                            {{$wp_checklist->wp_insulation_strength}} 
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                    
                                                                    
                                                                    <li class="d-inline-block mr-1"
                                                                        style="width:330px">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-4">
                                                                                <h4 class="bold">Stärke</h4>
                                                                            </div>
                                                                            <div class="col-md-8"> 
                                                                                 {{$wp_checklist->wp_rafter_strength}}     

                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div> 

                                                        <div class="col-6">
                                                            <div class="form-group row">
                                                                <div class="col-md-4">
                                                                    <h4 class="bold">Anzahl Bäder</h4>
                                                                </div>
                                                                <div class="col-md-8">
                                                                    {{$wp_checklist->wp_bathrooms}}     
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>

                                                        

                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <h4 class="bold ">Badewanne</h4>
                                                                </div>
                                                                <div class="col-md-10">
                                                                    <ul class="list-unstyled mb-0">
                                                                         <li class="d-inline-block mr-1"
                                                                                style="width:330px">
                                                                                <div class="form-group row"> 
                                                                                    <div class="col-md-8">                                                               
                                                                                    {{$wp_checklist->wp_bathtub}} 
                                                                                    </div>
                                                                                </div>
                                                                            </li> 
                                                                        
                                                                            <li class="d-inline-blocks mr-1 "
                                                                                style="width:330px">
                                                                                <div class="form-group row ">
                                                                                    <div class="col-md-4">
                                                                                        <h4 class="bold">Anzahl</h4>
                                                                                    </div>
                                                                                    <div class="col-md-8"> 
                                                                                        {{$wp_checklist->wp_bathtub_count}}  
                                                                                    </div>
                                                                                </div>
                                                                            </li>

                                                                        <li class="d-inline-blocks mr-1 "
                                                                            style="width:330px">
                                                                            <div class="form-group row ">
                                                                                <div class="col-md-4">
                                                                                    <h4 class="bold">Abmessung</h4>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                        {{$wp_checklist->wp_bathtub_measure}}   
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <h4 class="bold ">Schwimmbad</h4>
                                                                </div>
                                                                <div class="col-md-10">
                                                                    <ul class="list-unstyled mb-0">
                                                                        <li class="d-inline-block mr-1"
                                                                            style="width:330px">
                                                                            <div class="form-group row"> 
                                                                                <div class="col-md-8">                                                               
                                                                                {{$wp_checklist->wp_swimming_pool}} 
                                                                                </div>
                                                                            </div>
                                                                        </li>  

                                                                        <li class="d-inline-blocks mr-1 "
                                                                            style="width:330px">
                                                                            <div class="form-group row ">
                                                                                <div class="col-md-4">
                                                                                    <h4 class="bold">Anzahl</h4>
                                                                                </div>
                                                                                <div class="col-md-8"> 
                                                                                    {{$wp_checklist->wp_swimming_pool_count}}  
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div> 
                                                        <div class="col-12">
                                                            <hr>
                                                        </div> 
                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <h4 class="bold ">Solarthermie vorhanden</h4>
                                                                </div>
                                                                <div class="col-md-10">
                                                                    <ul class="list-unstyled mb-0">
                                                                         <li class="d-inline-block mr-1"
                                                                            style="width:330px">
                                                                            <div class="form-group row"> 
                                                                                <div class="col-md-8">                                                               
                                                                                {{$wp_checklist->solor}} 
                                                                                </div>
                                                                            </div>
                                                                        </li>  
                                                                        

                                                                        <li class="d-inline-blocks mr-1"
                                                                            style="width:330px">
                                                                            <div class="form-group row">
                                                                                <div class="col-md-4">
                                                                                    <h4 class="bold">Anzahl der
                                                                                        Kollektoren</h4>
                                                                                </div>
                                                                                <div class="col-md-8 flex_me"> 
                                                                                        {{$wp_checklist->number_collector}}  
                                                                                    &nbsp;<span>Stk</span>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <h4 class="bold ">Kamin vorhanden</h4>
                                                                </div>
                                                                <div class="col-md-10">
                                                                    <ul class="list-unstyled mb-0"> 

                                                                        <li class="d-inline-block mr-1"
                                                                            style="width:330px">
                                                                            <div class="form-group row"> 
                                                                                <div class="col-md-8">                                                               
                                                                                {{$wp_checklist->chimney}} 
                                                                                </div>
                                                                            </div>
                                                                        </li>  
                                                                      
                                                                        <li class="d-inline-blocks mr-1"
                                                                            style="width:330px">
                                                                            <div class="form-group row">
                                                                                <div class="col-md-4">
                                                                                    <h4 class="bold">Verbrauch</h4>
                                                                                </div>
                                                                                <div class="col-md-8 flex_me"> 
                                                                                        {{$wp_checklist->chimney_usage}} 
                                                                                    &nbsp;<span> Holz/ m3 pro
                                                                                        Jahr</span>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <h4 class="bold ">Heizlastberechnung vorhanden</h4>
                                                                </div>
                                                                <div class="col-md-10">
                                                                    <ul class="list-unstyled mb-0">
                                                                         <li class="d-inline-block mr-1"
                                                                            style="width:330px">
                                                                            <div class="form-group row"> 
                                                                                <div class="col-md-8">                                                               
                                                                                {{$wp_checklist->hlb_calc}} 
                                                                                </div>
                                                                            </div>
                                                                        </li>   
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="cards collapse-header">
                                <div id="heading2" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion2" aria-expanded="false" aria-controls="accordion2">
                                    <span class="lead collapse-title">
                                            <h2 class="bold section_title">Energiekosten, Verbrauch, Daten</h2>
                                    </span>
                                </div>
                                <div id="accordion2" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading2" class="collapse" style="">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr> 
                                                                <th>Erstes Jahr</th>
                                                                <th>Zweites Jahr</th>
                                                                <th>Drittes Jahr</th> 
                                                                <th>Gesamt</th>
                                                                <th>AVG</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr> 
                                                                <td> {{$wp_checklist->energy_first_year_consumption}} {{$wp_checklist->energy_consumption_type}}    </td>
                                                                <td> {{$wp_checklist->energy_second_year_consumption}}{{$wp_checklist->energy_consumption_type}}    </td>
                                                                <td> {{$wp_checklist->energy_third_year_consumption}} {{$wp_checklist->energy_consumption_type}}  </td> 
                                                                <td> {{$wp_checklist->energy_total_year_consumption}} {{$wp_checklist->energy_consumption_type}}   </td>     
                                                                <td> {{$wp_checklist->energy_avg_year_consumption}}{{$wp_checklist->energy_consumption_type}}   </td>      
                                                            </tr> 

                                                            <tr> 
                                                                <td> {{ number_format($wp_checklist->energy_first_year_cost, 2, ',', '.') }} € </td>
                                                                <td> {{ number_format($wp_checklist->energy_second_year_cost, 2, ',', '.') }} € </td>
                                                                <td> {{ number_format($wp_checklist->energy_third_year_cost, 2, ',', '.') }} € </td>
                                                                <td> {{ number_format($wp_checklist->energy_total_year_cost, 2, ',', '.') }} € </td>    
                                                                <td> {{ number_format($wp_checklist->energy_avg_year_cost, 2, ',', '.') }} € </td>    
                                                                
                                                            </tr>
                                                            
                                                            
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="cards collapse-header">
                                <div id="heading4" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion4" aria-expanded="false" aria-controls="accordion4">
                                    <span class="lead collapse-title">
                                        <h2 class="bold section_title ">AKTUELLE HEIZUNG</h2>
                                    </span>
                                </div>
                                <div id="accordion4" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading4" class="collapse" aria-expanded="false">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4 " >  
                                                    <h4 class="bold ">Heizungart</h4>  
                                                      {{$wp_checklist->heatpump}}  
                                                </div>

                                                <div class="col-md-3 "  >  
                                                    <h4 class="bold ">Aufstellort</h4>  
                                                      {{$wp_checklist->exhibition_location}}   
                                                </div>

                                                <div class="col-md-5 "  >  
                                                    <h4 class="bold ">Notiz</h4>  
                                                      {{$wp_checklist->exhibation_location_note}}    
                                        
                                                </div>

                                                <div class="col-4 mt-1">
                                                    <div class="form-group row">
                                                        <div class="col-md-12 ">
                                                            <h4 class="bold">Alter der Heizung <label id="heating_age_label"></label></h4>
                                                        </div>
                                                        <div class="col-md-12 flex_me"> 
                                                                {{$wp_checklist->heating_manufacture_year}}   
                                                            <span id="heating_lables"
                                                                style="align-content: center;">
                                                                <span> Jahr(e)</span> </span>

                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Enable only for Oil and Gas  -->
                                                <div class="col-3 mt-1">
                                                    <div class="form-group row">
                                                        <div class="col-md-12 ">
                                                            <h4 class="bold">Heiztechnik</h4>
                                                        </div>
                                                        <div class="col-md-12 flex_me"> 
                                                                {{$wp_checklist->heating_type}}    
                                                     
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-4 mt-1">
                                                    <div class="form-group row">
                                                        <div class="col-md-12 ">
                                                            <h4 class="bold">Leistung der Anlage</h4>
                                                        </div>
                                                        <div class="col-md-12 flex_me"> 
                                                                {{$wp_checklist->system_performance}}    
                                                                 &nbsp;<span>kWh</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                    <div class="col-4 mt-1">
                                                        <div class="form-group row">
                                                            <div class="col-md-12 ">
                                                                <h4 class="bold">Hersteller</h4>
                                                            </div>
                                                            <div class="col-md-12 flex_me">  
                                                                    {{$wp_checklist->heating_company}}    

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mt-1">
                                                        <div class="form-group row">
                                                            <div class="col-md-12 ">
                                                                <h4 class="bold">Typbezeichnung</h4>
                                                            </div>
                                                            <div class="col-md-12 flex_me"> 
                                                                    {{$wp_checklist->type_designation}}    

                                                            </div>
                                                        </div>
                                                    </div>

                                                <div class="col-4 mt-1">
                                                    <div class="form-group row">
                                                        <div class="col-md-12 ">
                                                            <h4 class="bold">Warmwasseraufbereitung</h4>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            {{$wp_checklist->hot_water_preparation}}    
                                                        </div>
                                                    </div>
                                                </div>

                                                    <div class="col-4 mt-1">
                                                    <div class="form-group row">
                                                        <div class="col-md-12 ">
                                                            <h4 class="bold">Warmwasserverbrauch pro Person</h4>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            {{$wp_checklist->number_hotWaterConsumptionPerPerson}}     
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-4 mt-1">
                                                    <div class="form-group row">
                                                        <div class="col-md-12 ">
                                                            <h4 class="bold">Heizsystem</h4>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            {{$wp_checklist->general_heating_system}}     
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-4 mt-1">
                                                    <div class="form-group row">
                                                        <div class="col-md-12 ">
                                                            <h4 class="bold">Rohrsystem</h4>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            {{$wp_checklist->pipe_system}}    
                                                        </div>
                                                    </div>
                                                </div>

                                                    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="cards collapse-header">
                                <div id="heading5" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion5" aria-expanded="false" aria-controls="accordion5">
                                    <span class="lead collapse-title">
                                        <h2 class="bold section_title ">Hydraulichen Eignungsprüfung Fußbodenheizung & elektrischen Anschluß: </h2>
                                    </span>
                                </div>
                                <div id="accordion5" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading5" class="collapse" aria-expanded="false">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4 " >  
                                                    <h4 class="bold ">Heizkreisverteiler</h4>  
                                                    {{$wp_checklist->heating_circuit_distributor}}     
                                                </div>

                                                <div class="col-md-4 "  >  
                                                    <h4 class="bold ">Stellantriebe</h4>  
                                                    {{$wp_checklist->actuators}}      
                                                </div>

                                                <div class="col-md-4"  >  
                                                    <h4 class="bold ">Kühlung Funktion</h4>  
                                                    {{$wp_checklist->suitable_cooling_system}}     
                                                </div>

                                                <div class="col-md-12 mt-2 "  >  
                                                    <h4 class="bold ">Notiz</h4>  
                                                    {{$wp_checklist->exhibation_location_note}}   
                                        
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 

                            <div class="cards collapse-header">
                                <div id="heading7" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion7" aria-expanded="false" aria-controls="accordion7">
                                    <span class="lead collapse-title">
                                        <h2 class="bold section_title ">Hydraulichen Eignungsprüfung Heizkörper</h2>
                                    </span>
                                </div>
                                <div id="accordion7" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading7" class="collapse" aria-expanded="false">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3 " >  
                                                    <h4 class="bold ">Heizkörper</h4>  
                                                    {{$wp_checklist->radiator}}  
                                                </div>

                                                <div class="col-md-3 "  >  
                                                    <h4 class="bold ">Thermostate</h4>  
                                                    {{$wp_checklist->thermostats}}   
                                                </div>

                                                    <div class="col-md-3 "  >  
                                                    <h4 class="bold ">Thermostatventile</h4>  
                                                    {{$wp_checklist->thermostatic_valves}}   
                                                </div>

                                                <div class="col-md-3"  >  
                                                    <h4 class="bold ">Kühlung Funktion</h4>  
                                                    {{$wp_checklist->radiator_cooling_system}}    
                                                </div>

                                                <div class="col-md-12 mt-2 "  >  
                                                    <h4 class="bold ">Notiz</h4>
                                                    {{$wp_checklist->radiator_note}}     
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cards collapse-header">
                                <div id="heading8" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion8" aria-expanded="false" aria-controls="accordion8">
                                    <span class="lead collapse-title">
                                            <h2 class="bold section_title">Welche Leitungen sind verlegt?</h2>
                                    </span>
                                </div>
                                <div id="accordion8" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading8" class="collapse" aria-expanded="false">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="row">
                                                    <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th># </th>
                                                                <th>Art</th>
                                                                <th>Dimension</th>
                                                                <th>Hersteller</th>
                                                                <th>Typbezeichnung</th>
                                                                <th>Notiz</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($heating as $heat)
                                                                <tr>
                                                                    <td>{{$heat->system}}</td>
                                                                    <td>{{$heat->type}}</td>
                                                                    <td>{{$heat->dimension}}</td>
                                                                    <td>{{$heat->company}}</td>
                                                                    <td>{{$heat->designation}}</td>
                                                                    <td>{{$heat->note}}</td>
                                                                </tr>
                                                            @endforeach 
                                                            
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="cards collapse-header">
                                <div id="heading9" class="card-header" data-toggle="collapse" role="button" data-target="#accordion9" aria-expanded="false" aria-controls="accordion9">
                                    <span class="lead collapse-title">
                                        <h2 class="bold section_title">Einbringmaße Zuwegung Heizraum</h2>
                                    </span>
                                </div>
                                <div id="accordion9" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading9" class="collapse" aria-expanded="false">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="table-responsive">
                                                    <form id="room_dimension_form">
                                                        @csrf
                                                        <table class="table" id="room_dimension_table">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Dimensionstyp</th>
                                                                    <th>Breite</th>
                                                                    <th>Höhe</th>
                                                                    <th>Deckenhöhe</th>
                                                                    <th>Treppe Form</th>
                                                                    <th>Treppe Breite</th>
                                                                    <th>Geschoss</th>
                                                                    <th>Aktion</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="room_dimension_tbody">
                                                                <tr id="room_dimension_row_0">
                                                                    <th scope="row">
                                                                        <input type="text" name="room[0][room_number]" class="form-control" value="1" readonly>
                                                                    </th>
                                                                    <td>
                                                                        <select name="room[0][dimension_type]" class="form-control dimension_type">
                                                                            <option></option>
                                                                            <option value="Tür">Tür</option>
                                                                            <option value="Wand">Wand</option>
                                                                            <option value="Deche">Deche</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control" placeholder="Breite" name="room[0][width]"> 
                                                                        <input type="hidden" class="form-control" name="customer_id" value="{{ $customer->id }}"> 
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control" placeholder="Höhe" name="room[0][height]"> 
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control ceiling_height" placeholder="Deckenhöhe" name="room[0][ceiling_height]">
                                                                    </td>
                                                                    <td>
                                                                        <select name="room[0][stair_form]" class="form-control stair_form">
                                                                            <option></option>
                                                                            <option value="L-Form">L-Form</option>
                                                                            <option value="U-Form">U-Form</option>
                                                                            <option value="Wendel">Wendel</option>
                                                                            <option value="Gradeluäfig">Gradeluäfig</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control stair_width" placeholder="Treppe Breite" name="room[0][stair_width]">
                                                                    </td>
                                                                    <td>
                                                                        <select name="room[0][room_story]" class="form-control">
                                                                            <option></option>
                                                                            <option value="KG">KG</option>
                                                                            <option value="EG">EG</option>
                                                                            <option value="OG">OG</option>
                                                                            <option value="DG">DG</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-icon btn-warning add_dimension">
                                                                            <i class="feather icon-plus"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <button type="button" class="btn btn-outline-success mr-1 mb-1 waves-effect waves-light" id="save_dimension">Speichern</button>
                                                    </form>
                                                </div>

                                                <div class="table-responsive">  
                                                    <table class="table" id="room_dimension_data">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Dimensionstyp</th>
                                                                <th>Breite</th>
                                                                <th>Höhe</th>
                                                                <th>Deckenhöhe</th>
                                                                <th>Treppe Form</th>
                                                                <th>Treppe Breite</th>
                                                                <th>Geschoss</th>
                                                                <th>Aktion</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="room_dimension_data">
                                                            <!-- Rows will be dynamically inserted here -->
                                                        </tbody>
                                                    </table>

                                                </div>

                                                <!-- Room Dimension Modal for Editing -->
                                                <div class="modal fade" id="editRoomDimensionModal" tabindex="-1" role="dialog" aria-labelledby="editRoomDimensionModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editRoomDimensionModalLabel">Raumdetails bearbeiten</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form id="editRoomDimensionForm">
                                                                    <!-- Hidden field to store room ID -->
                                                                    <input type="hidden" id="edit_room_id">
                                                                    <div class="form-group">
                                                                        <label>Türmaße#</label>
                                                                        <input type="text" id="edit_room_number" class="form-control" name="room_number" readonly>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Dimensionstyp</label>
                                                                        <select name="dimension_type" id="edit_dimension_type" class="form-control">
                                                                            <option value="Tür">Tür</option>
                                                                            <option value="Wand">Wand</option>
                                                                            <option value="Deche">Deche</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Breite</label>
                                                                        <input type="text" id="edit_width" class="form-control" name="width">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Höhe</label>
                                                                        <input type="text" id="edit_height" class="form-control" name="height">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Deckenhöhe</label>
                                                                        <input type="text" id="edit_ceiling_height" class="form-control" name="ceiling_height">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Treppe Form</label>
                                                                        <select id="edit_stair_form" class="form-control" name="stair_form">
                                                                            <option value="L-Form">L-Form</option>
                                                                            <option value="U-Form">U-Form</option>
                                                                            <option value="Wendel">Wendel</option>
                                                                            <option value="Gradeluäfig">Gradeluäfig</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Treppe Breite</label>
                                                                        <input type="text" id="edit_stair_width" class="form-control" name="stair_width">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Geschoss</label>
                                                                        <select id="edit_room_story" class="form-control" name="room_story">
                                                                            <option value="KG">KG</option>
                                                                            <option value="EG">EG</option>
                                                                            <option value="OG">OG</option>
                                                                            <option value="DG">DG</option>
                                                                        </select>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                                                                <button type="button" id="updateRoomDimension" class="btn btn-primary">Speichern</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="cards collapse-header">
                                <div id="heading13" class="card-header" data-toggle="collapse" role="button" data-target="#accordion13" aria-expanded="false" aria-controls="accordion13">
                                    <span class="lead collapse-title">
                                        <h2 class="bold section_title">Zustand Zählerschrank</h2>
                                    </span>
                                </div>
                                <div id="accordion13" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading13" class="collapse" aria-expanded="false">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="row"> 
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Zählerschrank</th>
                                                                <th>Größe</th>
                                                                <th>Hersteller</th>
                                                                <th>Einzubauende Komponenten</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody> 
                                                             
                                                                    <tr>
                                                                        <td>
                                                                            @if($meter_cabinet->meter_cabinet == "ok") OK @elseif($meter_cabinet->meter_cabinet=="upgrade") ertüchtigen @else neu @endif 
                                                                        </td>
                                                                        <td> {{$meter_cabinet->cabinet_size}} </td>
                                                                        <td>  
                                                                            @foreach ($electro as $elec)
                                                                                @if($elec->id == $meter_cabinet->meter_cabinet_company)
                                                                                {{ $elec->name }}
                                                                                @endif
                                                                            @endforeach  
                                                                        </td>
                                                                        <td>
                                                                            <strong>Zähleradapterplatte</strong>: {{ $meter_cabinet->wp_meter_adapter_plate }} <br>
                                                                            <strong>AC Überspannungsschutz</strong>: {{ $meter_cabinet->wp_ac_surge_protection }} <br>
                                                                            <strong>SLS Schalter</strong>: {{ $meter_cabinet->wp_ac_switch }} <br>
                                                                            <strong>APZ Feld</strong>: {{ $meter_cabinet->wp_apz_field }} <br>
                                                                            <strong>Trenn-Relais</strong>: {{ $meter_cabinet->wp_disconnect_relay }} <br>
                                                                            <strong>Potentialausgleichsschiene</strong>: {{ $meter_cabinet->wp_equipotential_bonding }} <br>
                                                                        </td>
                                                                    </tr> 


                                                        </tbody>
                                                        
                                                    </table>
                                                </div>                       
                                            </div>
                                        </div>
                                    </div>
                            </div> 

                            <div class="cards collapse-header">
                                <div id="heading40" class="card-header" data-toggle="collapse" role="button" data-target="#accordion40" aria-expanded="false" aria-controls="accordion40">
                                    <span class="lead collapse-title">
                                        <h2 class="bold section_title">Lüftung</h2>
                                    </span>
                                </div>
                                <div id="accordion40" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading40" class="collapse" aria-expanded="false">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Lüftung</th>
                                                                <th>Lüftungsystem</th>
                                                                <th>Wärmerückgewinnung</th>
                                                                <th>Hersteller</th>
                                                                <th>Typ</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row">
                                                                    {{$wp_checklist->ventilation}}     
                                                                </th>
                                                                <td> {{$wp_checklist->ventilation_system}} </td>
                                                                <td> {{$wp_checklist->ventilation_company}} </td>
                                                                <td> {{$wp_checklist->ventilation_type}} </td>   
                                                            </tr>
                                                    
                                                            
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 


    @push('scripts')

<script>
  $(document).ready(function() {
    let roomIndex = 1; // Start with room 1

    // Add new row
    $(document).on('click', '.add_dimension', function() {
        roomIndex++;
        $('#room_dimension_table tbody').append(`
            <tr id="room_dimension_row_${roomIndex}">
                <th scope="row">
                    <input type="text" name="room[${roomIndex}][room_number]" class="form-control" value="${roomIndex}" readonly>
                </th>
                <td>
                    <select name="room[${roomIndex}][dimension_type]" class="form-control dimension_type">
                        <option value="Tür">Tür</option>
                        <option value="Wand">Wand</option>
                        <option value="Decke">Decke</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control" placeholder="Breite" name="room[${roomIndex}][width]">
                </td>
                <td>
                    <input type="text" class="form-control" placeholder="Höhe" name="room[${roomIndex}][height]">
                </td>
                <td>
                    <input type="text" class="form-control ceiling_height" placeholder="Deckenhöhe" name="room[${roomIndex}][ceiling_height]">
                </td>
                <td>
                    <select name="room[${roomIndex}][stair_form]" class="form-control stair_form">
                        <option value=""></option>
                        <option value="L-Form">L-Form</option>
                        <option value="U-Form">U-Form</option>
                        <option value="Wendel">Wendel</option>
                        <option value="Gradeluäfig">Gradeluäfig</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control stair_width" placeholder="Treppe Breite" name="room[${roomIndex}][stair_width]">
                </td>
                <td>
                    <select name="room[${roomIndex}][room_story]" class="form-control">
                        <option value="KG">KG</option>
                        <option value="EG">EG</option>
                        <option value="OG">OG</option>
                        <option value="DG">DG</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-icon btn-danger remove-dimension-row">
                        <i class="feather icon-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });

    // Remove row
    $(document).on('click', '.remove-dimension-row', function() {
        $(this).closest('tr').remove();
        updateRoomNumbers(); // Update room numbers after removal
    });

    // Update room numbers after deletion
    function updateRoomNumbers() {
        $('#room_dimension_table tbody tr').each(function(index, row) {
            $(row).find('input[name*="[room_number]"]').val(index + 1); // Adjust room numbers
        });
    }

    // Disable fields based on dimension_type selection
    $(document).on('change', '.dimension_type', function() {
        const row = $(this).closest('tr');
        const dimensionType = $(this).val();

        if (dimensionType === 'Wand') {
            row.find('.stair_form, .stair_width, .ceiling_height').prop('disabled', true);
        } else {
            row.find('.stair_form, .stair_width, .ceiling_height').prop('disabled', false);
        }
    });

    // Save room dimensions
    $('#save_dimension').click(function() {
        var roomData = $('#room_dimension_form').serialize();

            $.ajax({
                url: '/room_dimensions/store',
                method: 'POST',
                data: roomData,
                success: function(response) {
                    if (response.success) {
                        toastr.success('Raumdimensionen erfolgreich gespeichert');
                        loadRoomDimensions();
                    } else {
                        toastr.error('Fehler beim Speichern der Raumdimensionen');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Display validation errors
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0]); // Display each error
                            console.error('Validation Error: ' + value[0]);
                        });
                    } else {
                        toastr.error('Fehler beim Speichern der Raumdimensionen');
                    }
                }
            });
    });

    function loadRoomDimensions() {
        const customerId = {{ $customer->id }}; 

        $.ajax({
            url: `/room_dimensions/get/${customerId}`,  // Correct route with customer_id and room_id
            method: 'GET',
            success: function(response) {
                console.log('Server Response:', response); // Add this line to debug the response
                if (response.success) {
                    // Clear the existing table
                    $('#room_dimension_data tbody').empty();
                    
                    // Populate the table with the fetched data
                    $.each(response.data, function(index, room) {
                        $('#room_dimension_data tbody').append(`
                            <tr>
                                <td>${room.room_number}</td>
                                <td>${room.dimension_type}</td>
                                <td>${room.width}</td>
                                <td>${room.height}</td>
                                <td>${room.ceiling_height ? room.ceiling_height : ''}</td>
                                <td>${room.stair_form ? room.stair_form : ''}</td>
                                <td>${room.stair_width ? room.stair_width : ''}</td>
                                <td>${room.room_story}</td>
                                <td>
                                    <button type="button" class="btn btn-icon btn-primary edit-room" data-id="${room.id}">
                                        <i class="feather icon-edit"></i> Bearbeiten
                                    </button>
                                    <button type="button" class="btn btn-icon btn-danger delete-room" data-id="${room.id}">
                                        <i class="feather icon-trash"></i> Löschen
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    toastr.error('Fehler beim Laden der Raumdimensionen');
                }
            },
            error: function(xhr) {
                console.error('Error Loading Room Dimensions:', xhr.responseText);  // Add this line for more detailed error information
                toastr.error('Fehler beim Laden der Raumdimensionen');
            }
        });
    }


    // Load room dimensions initially when the page loads
    loadRoomDimensions();

     // Edit room dimension
     // Function to enable/disable fields based on dimension_type selection
    function toggleFields(dimensionType) {
        if (dimensionType === 'Tür') {
            $('#edit_ceiling_height, #edit_stair_form, #edit_stair_width').prop('disabled', false);  // Enable ceiling height, stair form, and stair width
        } else if (dimensionType === 'Wand') {
            $('#edit_ceiling_height, #edit_stair_form, #edit_stair_width').prop('disabled', true);   // Disable ceiling height, stair form, and stair width
        }
    }

    // When the modal opens, we check the current dimension_type value and toggle fields
    $('#editRoomDimensionModal').on('shown.bs.modal', function() {
        const dimensionType = $('#edit_dimension_type').val();
        toggleFields(dimensionType);
    });

    // When dimension_type is changed, toggle fields dynamically
    $('#edit_dimension_type').on('change', function() {
        const dimensionType = $(this).val();
        toggleFields(dimensionType);
    });

    // Edit room dimension (fetch and show in the modal)
    $(document).on('click', '.edit-room', function() {
        const roomId = $(this).data('id');
        $.ajax({
            url: `/room_dimensions/edit/${roomId}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const room = response.data;

                    // Populate modal form with fetched data
                    $('#edit_room_id').val(room.id);
                    $('#edit_room_number').val(room.room_number);
                    $('#edit_dimension_type').val(room.dimension_type).trigger('change');  // Trigger change to enable/disable fields
                    $('#edit_width').val(room.width);
                    $('#edit_height').val(room.height);
                    $('#edit_ceiling_height').val(room.ceiling_height);
                    $('#edit_stair_form').val(room.stair_form);
                    $('#edit_stair_width').val(room.stair_width);
                    $('#edit_room_story').val(room.room_story);

                    // Open the modal
                    $('#editRoomDimensionModal').modal('show');
                } else {
                    toastr.error('Fehler beim Laden der Raumdetails');
                }
            },
            error: function(xhr) {
                toastr.error('Fehler beim Laden der Raumdetails');
            }
        });
    });

    // Update room dimension (on save button click)
    $('#updateRoomDimension').click(function() {
        const roomId = $('#edit_room_id').val();
        const roomData = {
            room_number: $('#edit_room_number').val(),
            dimension_type: $('#edit_dimension_type').val(),
            width: $('#edit_width').val(),
            height: $('#edit_height').val(),
            ceiling_height: $('#edit_ceiling_height').val(),
            stair_form: $('#edit_stair_form').val(),
            stair_width: $('#edit_stair_width').val(),
            room_story: $('#edit_room_story').val(),
            _token: "{{ csrf_token() }}"
        };

        $.ajax({
            url: `/room_dimensions/update/${roomId}`,
            method: 'PUT',
            data: roomData,
            success: function(response) {
                if (response.success) {
                    toastr.success('Raumdetails erfolgreich aktualisiert');
                    $('#editRoomDimensionModal').modal('hide');  // Close the modal
                    loadRoomDimensions();  // Reload room dimensions
                } else {
                    toastr.error('Fehler beim Aktualisieren der Raumdetails');
                }
            },
            error: function(xhr) {
                toastr.error('Fehler beim Aktualisieren der Raumdetails');
            }
        });
    });

    // Delete room dimension
    $(document).on('click', '.delete-room', function() {
        const roomId = $(this).data('id');
        if (confirm('Sind Sie sicher, dass Sie diesen Raum löschen möchten?')) {
            $.ajax({
                url: `/room_dimensions/delete/${roomId}`,
                method: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Raum erfolgreich gelöscht');
                        loadRoomDimensions();  // Reload room dimensions
                    } else {
                        toastr.error('Fehler beim Löschen des Raums');
                    }
                },
                error: function(xhr) {
                    toastr.error('Fehler beim Löschen des Raums');
                }
            });
        }
    });

});
</script>
    @endpush