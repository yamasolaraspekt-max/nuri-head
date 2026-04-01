

@if($product_pv)
<div class="card">
    <div class="card-header" style="padding-bottom: 1.5rem;">
       <span> <h6 >Elektrische Daten</h6></span>
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
                                    <th>Zelltyp</th>
                                    <td>{{ $product_pv->cell_type }}</td>
                                </tr>
                               
                    
                                <tr>
                                    <th>Halbzelle</th>
                                    <td>{{ $product_pv->half_cell_module }}</td>
                                </tr>
                                <tr>
                                    <th>Anzahl Zellen</th>
                                    <td>{{ $product_pv->num_cells }}</td>
                                </tr>
                                <tr>
                                    <th>Anzahl Bypassdioden</th>
                                    <td>{{ $product_pv->num_bypass_diodes }}</td>
                                </tr>
                                
                                <tr>
                                    <th>Verlustspannung Bypassdioden in V</th>
                                    <td>{{ $product_pv->voltage_loss_per_bypass_diode }}</td>
                                </tr>
                           
                                <tr>
                                    <th>Integrieter Leistungsoptimierer</th>
                                    <td>{{ $product_pv->integrated_power_optimizer }}</td>
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
        <h6 ><span>UI Kennwerte bei STC</span></h6>
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
                                    <th>Spannung im MPP in V</th>
                                    <td>{{ $product_pv->mpp_voltage }}</td>
                                </tr>
                               
                    
                                <tr>
                                    <th>Strom im MPP in A</th>
                                    <td>{{ $product_pv->mpp_current }}</td>
                                </tr>
                                <tr>
                                    <th>Leerlaufspannung in V</th>
                                    <td>{{ $product_pv->open_circuit_voltage }}</td>
                                </tr>
                                <tr>
                                    <th>Kurzschlussstrom in A </th>
                                    <td>{{ $product_pv->short_circuit_current }}</td>
                                </tr>
                              
                                <tr>
                                    <th>Erhöhung Leerlaufspannung vor Stabilisierung in %</th>
                                    <td>{{ $product_pv->voltage_increase_before_stabilization }}</td>
                                </tr>
                           
                                <tr>
                                    <th>Nennleistung in W  </th>
                                    <td>{{ $product_pv->nominal_power }}</td>
                                </tr> 
                                <tr>
                                    <th>Füllfaktor in %  </th>
                                    <td>{{ $product_pv->fill_factor }}</td>
                                </tr> 
                                <tr>
                                    <th>Wirkungsgrad in % </th>
                                    <td>{{ $product_pv->efficiency }}</td>
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
        <h6 ><span>UI Kennwerte bei Schwachlicht</span></h6>
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
                                    <th>Modell</th>
                                    <td>{{ $product_pv->low_light_model }}</td>
                                </tr>
                               
                    
                                <tr>
                                    <th>Einstrahlung in W/m² (Schwachlicht)</th>
                                    <td>{{ $product_pv->irradiance }}</td>
                                </tr>
                                <tr>
                                    <th>MPP-Spnnung in V (Schwachlicht)</th>
                                    <td>{{ $product_pv->mpp_voltage_low_light }}</td>
                                </tr>
                                <tr>
                                    <th>MPP-Strom in A (Schwachlicht)</th>
                                    <td>{{ $product_pv->mpp_current_low_light }}</td>
                                </tr>
                                
                                <tr>
                                    <th>Leerlaufspanunng in V (Schwachlicht)</th>
                                    <td>{{ $product_pv->open_circuit_voltage_low_light }}</td>
                                </tr>
                           
                                <tr>
                                    <th>Kurzschlussstrom in A (Schwachlicht) </th>
                                    <td>{{ $product_pv->short_circuit_current_low_light }}</td>
                                </tr> 

                                   
                                <tr>
                                    <th>Füllfaktor in % (Schwachlicht) </th>
                                    <td>{{ $product_pv->fill_factor_low_light }}</td>
                                </tr> 

                                <tr>
                                    <th>Füllfaktor in % (Schwachlicht) </th>
                                    <td>{{ $product_pv->efficiency_low_light }}</td>
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
        <h6 ><span>Weitere Parameter</span></h6>
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
                                    <th>Temperaturkoeffizient Uoc in mV/K</th>
                                    <td>{{ $product_pv->temperature_coefficient_voc }}</td>
                                </tr>
                               
                    
                                <tr>
                                    <th>Temperaturkoeffizient Uoc in %/K</th>
                                    <td>{{ $product_pv->temperature_coefficient_voc_pct }}</td>
                                </tr>
                                <tr>
                                    <th>Temperaturkoeffizient Isc in mA/K</th>
                                    <td>{{ $product_pv->temperature_coefficient_isc }}</td>
                                </tr>
                                <tr>
                                    <th>Temperaturkoeffizient Isc in %/K</th>
                                    <td>{{ $product_pv->temperature_coefficient_pmax }}</td>
                                </tr>
                                
                                <tr>
                                    <th>Temperaturkoeffizient Pmpp in %/K</th>
                                    <td>{{ $product_pv->temperature_coefficient_pmax }}</td>
                                </tr>
                           
                                <tr>
                                    <th>Winkelkorrektur (IAM) in % </th>
                                    <td>{{ $product_pv->angle_correction_factor }}</td>
                                </tr> 
                                <tr>
                                    <th>Maximale Systemspanunng in V </th>
                                    <td>{{ $product_pv->max_system_voltage }}</td>
                                </tr>
                                <tr>
                                    <th>Bifazialitätsfaktor in %</th>
                                    <td>{{ $product_pv->bifaciality_factor }}</td>
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
        <h6 ><span>Abmessungen</span></h6>
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
                                    <th>Breite in mm </th>
                                    <td>{{ $product_pv->width }}</td>
                                </tr>
                                <tr>
                                    <th>Höhe in mm</th>
                                    <td>{{ $product_pv->height }}</td>
                                </tr>
                                <tr>
                                    <th>Fläche in mm </th>
                                    <td>{{ $product_pv->area }}</td>
                                </tr>
                                
                                <tr>
                                    <th>Tiefe in mm</th>
                                    <td>{{ $product_pv->depth }}</td>
                                </tr>
                           
                                <tr>
                                    <th>Rahmenbreite in mm</th>
                                    <td>{{ $product_pv->frame_width }}</td>
                                </tr> 

                                <tr>
                                    <th>Gewicht in kg</th>
                                    <td>{{ $product_pv->weight }}</td>
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