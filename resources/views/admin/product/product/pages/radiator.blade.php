


@if($product_radiator)
<div class="card">
    <div class="card-header" style="padding-bottom: 1.5rem;">
       <span> <h6 >Profile</h6></span>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="collapse" class="rotate"><i class="feather icon-chevron-down"></i></a></li>
                <li><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                <li><a data-action="close"><i class="feather icon-x"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-content collapse" style="">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table">
                         
                            <tbody>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $product_radiator->name }}</td>
                                </tr>
                               
                    
                                <tr>
                                    <th>Beschreibung</th>
                                    <td>{{ $product_radiator->description }}</td>
                                </tr>
                                <tr>
                                    <th>Erstellt am  </th>
                                    <td>{{ $product_radiator->created_on }}</td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





<div class="card">
    <div class="card-header" style="padding-bottom: 1.5rem;">
        <h6 ><span>Elektrische Daten - DC</span></h6>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="collapse" class="rotate"><i class="feather icon-chevron-down"></i></a></li>
                <li><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                <li><a data-action="close"><i class="feather icon-x"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-content collapse" style="">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table">
                         
                            <tbody>
                                <tr>
                                    <th>DC-Nennleistung  in kW</th>
                                    <td>{{ $product_radiator->dc_nennleistung_kw }}</td>
                                </tr>
                               
                    
                                <tr>
                                    <th>Max. DC-Leistung in kW</th>
                                    <td>{{ $product_radiator->max_dc_leistung_kw }}</td>
                                </tr>
                                <tr>
                                    <th>DC-Nennspannung in V</th>
                                    <td>{{ $product_radiator->dc_nennspanuung_v }}</td>
                                </tr>
                                <tr>
                                    <th>Max. Eingangsspannung in V </th>
                                    <td>{{ $product_radiator->max_eingangsspannung_v }}</td>
                                </tr>
                              
                                <tr>
                                    <th>Max. Eingangsstrom in A</th>
                                    <td>{{ $product_radiator->max_eingangsstrom_a }}</td>
                                </tr>
                           
                                <tr>
                                    <th>Max- . Kurzschlussstrom DC in A </th>
                                    <td>{{ $product_radiator->max_kurzschlussstrom_dc_a }}</td>
                                </tr> 
                                <tr>
                                    <th>Anzahl DC-Eingänge </th>
                                    <td>{{ $product_radiator->anzahl_dc_eingange }}</td>
                                </tr> 
                                
                                
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





<div class="card">
    <div class="card-header" style="padding-bottom: 1.5rem;">
        <h6 ><span>Elektrische Daten - AC</span></h6>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="collapse" class="rotate"><i class="feather icon-chevron-down"></i></a></li>
                <li><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                <li><a data-action="close"><i class="feather icon-x"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-content collapse" style="">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table">
                         
                            <tbody>
                                <tr>
                                    <th>AC-Nennleistung in kW</th>
                                    <td>{{ $product_radiator->ac_nennleistung_kw }}</td>
                                </tr>
                               
                    
                                <tr>
                                    <th>Max. AC-Leistung in kVA</th>
                                    <td>{{ $product_radiator->max_ac_leistung_kva }}</td>
                                </tr>
                                <tr>
                                    <th>AC-Nennspannung in V</th>
                                    <td>{{ $product_radiator->ac_nennspannung_v }}</td>
                                </tr>
                                <tr>
                                    <th>Anzahl Phasen </th>
                                    <td>{{ $product_radiator->anzahl_phasen }}</td>
                                </tr>
                                
                                <tr>
                                    <th>Trafo</th>
                                    <td>{{ $product_radiator->trafo }}</td>
                                </tr>
                           
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="card">
    <div class="card-header" style="padding-bottom: 1.5rem;">
        <h6 ><span>Elektrische Daten - Sonstige</span></h6>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="collapse" class="rotate"><i class="feather icon-chevron-down"></i></a></li>
                <li><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                <li><a data-action="close"><i class="feather icon-x"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-content collapse" style="">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table">
                         
                            <tbody>
                                <tr>
                                    <th>Änderung des Wirkungsgrades bei Abweichung der Eingangsspannung von der Nennspannung in %/100V</th>
                                    <td>{{ $product_radiator->anderung }}</td>
                                </tr>
                               
                    
                                <tr>
                                    <th>Min. Einspeiseleistung in W</th>
                                    <td>{{ $product_radiator->min_einspeiseleistung_w }}</td>
                                </tr>
                                <tr>
                                    <th>Standby-Verbruahc in W</th>
                                    <td>{{ $product_radiator->standby_verbruahe_w }}</td>
                                </tr>
                                <tr>
                                    <th>Nachtverbrauch in W</th>
                                    <td>{{ $product_radiator->nachtverbrauch_w }}</td>
                                </tr>
                                
                                
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





<div class="card">
    <div class="card-header" style="padding-bottom: 1.5rem;">
        <h6 ><span>MPP-Tracker</span></h6>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="collapse" class="rotate"><i class="feather icon-chevron-down"></i></a></li>
                <li><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                <li><a data-action="close"><i class="feather icon-x"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-content collapse" style="">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table">
                         
                            <tbody>
                    
                                <tr>
                                    <th>Leistungsbereich < 20% der Nennleistung in %</th>
                                    <td>{{ $product_radiator->leistungs_lower_20 }}</td>
                                </tr>
                                <tr>
                                    <th>Leistungsbereich > 20% der Nennleistung in %</th>
                                    <td>{{ $product_radiator->leistungs_greater_20 }}</td>
                                </tr>
                                <tr>
                                    <th>Parallelbetrieb </th>
                                    <td>{{ $product_radiator->parallelbetrieb }}</td>
                                </tr>
                                
                                <tr>
                                    <th>Anzahl MPP-Tracker </th>
                                    <td>{{ $product_radiator->anzahl_mpp_tracker }}</td>
                                </tr>
                           
                                <tr>
                                    <th>Identische elektrische Eigenschaften</th>
                                    <td>{{ $product_radiator->identisch_electisch }}</td>
                                </tr> 

                                <tr>
                                    <th>Max. Eingangsstrom pro MPP-Tracker in A</th>
                                    <td>{{ $product_radiator->max_eingangsstrom_mpp_a }}</td>
                                </tr> 
                                <tr>
                                    <th>Max. Kurzschlussstrom pro MPP-Tracker in A</th>
                                    <td>{{ $product_radiator->max_kurzschlussstrom_mpp_a }}</td>
                                </tr> 
                                <tr>
                                    <th>Max. Eingangsleistung pro MPP-Tracker in kW</th>
                                    <td>{{ $product_radiator->max_eingangsleistung_mpp_kw }}</td>
                                </tr> 
                                <tr>
                                    <th>Min. MPP-Spannung in V</th>
                                    <td>{{ $product_radiator->min_mpp_spannung_v }}</td>
                                </tr> 
                                <tr>
                                    <th>Max. MPP-Spannung in V</th>
                                    <td>{{ $product_radiator->max_mpp_spannung_v }}</td>
                                </tr> 
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif