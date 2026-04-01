 
 
<div class="default-collapse collapse-bordered">
    <div class="cards collapse-header">
        <div id="headingCollapse1" class="card-header collapsed coll" data-toggle="collapse" role="button" data-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
            <span class="lead collapse-title" style="font-weight: bolder;">
                ENERGIEVERBRAUCHSDATEN
            </span>
            <span class=" col-xl-5 col-xl-5 flex_me float-right">
                <div class="col-md-5"> 
                    <span style="color:#e50056" class="d-flex">
                            <span style="color: #626262; font-weight: bolder; font-size: 1.2rem;">Bewertung Projekt: </span>
                        <input type="text" id="answered_number" name="answered_number" readonly style="background: transparent;border: 0;     width: 20px;"/> / 
                        <input type="number" id="total_number" name="total_number" readonly style="background: transparent;border: 0;     width: 20px;"/>
                    </span>
                </div>
                <div class="col-md-7">
                    <div class="progress progress-bar-primary progress-lg">
                        <div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%;">
                            <span id="percent">0%</span>
                        </div>
                    </div>
                </div>
            </span>
        </div>
        <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse" style="">
            <div class="card-content">
                <div class="card-body">
                   <div class="row">
                        <div class="col-xl-6 col-xl-6 col-12">
                            <div class="card ">
                                <div class="card-body"> 
                                    <article>
                                        <div class="row mb-1">
                                            <div class="col-md-6"><strong>Gesamtstromverbrauch</strong></div>
                                            <div class="col-md-6">{{ $pv_checklist->electricity_consumption }} kWh</div>
                                        </div>   
                                        <div class="row mb-1">
                                            <div class="col-md-6"><strong>Anzahl WE</strong></div>
                                            <div class="col-md-6">
                                                <span class="mr-1">{{ $pv_checklist->number_of_units}}</span> 
                                            </div>
                                        </div> 
                                        <div class="row mb-1">
                                            <div class="col-md-6"><strong>Anzahl Zähler</strong></div>
                                            <div class="col-md-6">
                                                <span class="mr-1">{{ $pv_checklist->number_of_meters }}</span> 
                                            </div>
                                        </div> 
                                        @if($pv_checklist->electric_car=="ja")
                                        <div class="row mb-1">
                                            <div class="col-md-6"><strong>E-Auto</strong></div>
                                            <div class="col-md-6">
                                                <span class="mr-1">vorhanden</span> 
                                                <span class="mr-1">{{$pv_checklist->number_of_electric_cars}}x</span> 
                                            </div>
                                        </div>
                                        @endif
                                        <hr class="normal"> 
                                        <div class="row mb-1">
                                        <div class="col-md-6"><strong>Mieterstrommodell</strong></div>
                                            <div class="col-md-6">
                                                <span class="mr-1">gewünscht</span> 
                                            </div>
                                        </div>
                                        <div class="row mb-1">
                                            <div class="col-md-6"><strong>Objektart</strong></div>
                                            <div class="col-md-6">
                                                <span class="mr-1">{{ $pv_checklist->property_type }}</span> 
                                            </div>
                                        </div> 
                                    </article> 
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-xl-6 col-12">
                            <div class="card ">
                                <div class="card-body"> 
                                    <article>
                                        <div class="row mb-1">
                                            <div class="col-md-6"><strong>Aufschlüsselung Verbraucher</strong></div>
                                            <div class="col-md-6"> </div>
                                        </div>   
                                        <div class="row mb-1">
                                            <div class="col-md-6"><strong>Haushaltsstrom</strong></div>
                                            <div class="col-md-6">
                                                <span class="mr-1">3.500 kWh</span> 
                                            </div>
                                        </div> 
                                        <div class="row mb-1">
                                            <div class="col-md-6"><strong>Wärmepumenstrom</strong></div>
                                            <div class="col-md-6">
                                                <span class="mr-1">8.000 kWh</span> 
                                            </div>
                                        </div> 
                                        <div class="row mb-1">
                                            <div class="col-md-6"><strong>Schwimmbad</strong></div>
                                            <div class="col-md-6">
                                                <span class="mr-1">10.000 kWh</span>  
                                            </div>
                                        </div>
                                        <hr class="normal"> 
                                        <div class="row mb-1">
                                        <div class="col-md-6"><strong>E-Auto</strong></div>
                                            <div class="col-md-6">
                                                <span class="mr-1">3.000 kWh</span> 
                                            </div>
                                        </div>
                                        <div class="row mb-1">
                                            <div class="col-md-6"><strong>E-Auto Hybrid</strong></div>
                                            <div class="col-md-6">
                                                <span class="mr-1">1.500 kWh</span> 
                                            </div>
                                        </div> 
                                    </article> 
                                </div>
                            </div>
                        </div>
                   </div>
                </div>
            </div>
        </div>  
        <div id="headingCollapse2" class="card-header collapsed coll" data-toggle="collapse" role="button" data-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
            <span class="lead collapse-title" style="font-weight: bolder;">
                DACH DATEN
            </span>
            <span class=" col-xl-5 col-xl-5 flex_me float-right">
                <div class="col-md-5"> 
                    <span style="color:#e50056" class="d-flex">
                            <span style="color: #626262; font-weight: bolder; font-size: 1.2rem;">Bewertung Projekt: </span>
                        <input type="text" id="answered_number" name="answered_number" readonly style="background: transparent;border: 0;     width: 20px;"/> / 
                        <input type="number" id="total_number" name="total_number" readonly style="background: transparent;border: 0;     width: 20px;"/>
                    </span>
                </div>
                <div class="col-md-7">
                    <div class="progress progress-bar-primary progress-lg">
                        <div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%;">
                            <span id="percent">0%</span>
                        </div>
                    </div>
                </div>
            </span>
        </div>
        <div id="collapse2" role="tabpanel" aria-labelledby="headingCollapse2" class="collapse" style="">
            <div class="card-content">
                <div class="card-body">
                   <div class="row">
                        @foreach ($pv_roof as $roof) 
                        <div class="col-xl-6 col-xl-6 col-12">
                            <div class="card ">
                                <div class="card-body"> 
                                    <article>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Dach {{ 0 + 1}}</strong></div>
                                                <div class="col-md-6">{{ $roof->designation }}</div>
                                            </div>   
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Dacheindeckung</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">{{ $roof->roof_covering }}</span>
                                                    <span>{{ $roof->construction_fluid }}</span>
                                                </div>
                                            </div> 
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Aufdachdämmung</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">{{ $roof->thickness_roof_insulation ?? 'Nein' }}</span> 
                                                </div>
                                            </div> 
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Zwischensparrendämmung</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">{{ $roof->between_rafter_insulation ?? 'Nein' }}</span> 
                                                </div>
                                            </div>
                                            <hr class="normal"> 
                                            <div class="row mb-1">
                                            <div class="col-md-6"><strong>Solarhalteziegel gewünscht</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">ja</span> 
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>geliefert durch</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">Dachdecker</span> 
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Maße Dachfläche</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">15x10</span> 
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Dachüberstand Sparren links</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">45</span> 
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Dachüberstand Sparren rechts</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">45</span> 
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Sparrenstärke</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">45</span> 
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Sparrenverstärkung notwendig</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">45</span> 
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Eindeckmaß in cm</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">30x34</span> 
                                                </div>
                                            </div> 
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Statik vorhanden</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">nein</span> 
                                                </div>
                                            </div>
                                            <hr class="normal">
                                                    <div class="row mb-1">
                                                <div class="col-md-6"><strong>Dachsanierung notwendig</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">45</span> 
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Dachdecker</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">Feustel</span> 
                                                </div>
                                            </div> 
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Ort</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">Usingen</span> 
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Ansprechpartner</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">Herr Mustermann</span> 
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>geplanter Termin</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">Mai 2024</span> 
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Dauer</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">3 Wochen</span> 
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Gerüstnutzung</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">ja</span> 
                                                </div>
                                            </div>

                                            <hr class="normal">
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Dachaufbauten</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">SAT Schüssel</span> 
                                                    <span class="mr-1">auf die Nordseite versetzen</span> 
                                                </div>
                                            </div>

                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Dachaufbauten</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">Dachluke</span> 
                                                    <span class="mr-1">überbauen</span> 
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Dachaufbauten</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">Dachluke</span> 
                                                    <span class="mr-1">überbauen</span> 
                                                </div>
                                            </div>

                                            <hr class="normal">  
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Notiz</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">
                                                        <p>
                                                            Us nulluptis nonet volorercia cum consequam, quae doluptiur, enihit et ea ernatem fugiasp ediaepe quo- dis nosto blam et ipsapiet ut eritestrum ut enienim- porro explibus re sit pelita pliquo vitia vent, soluptur,
                                                        </p>
                                                    </span>  
                                                </div>
                                            </div>
                                    </article> 
                                </div>
                            </div>
                        </div>
                        @endforeach
                   </div>
                </div>

            </div>
        </div>

         <div id="headingCollapse3" class="card-header collapsed coll" data-toggle="collapse" role="button" data-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
            <span class="lead collapse-title" style="font-weight: bolder;">
                ELEKTRISCHER ANSCHLUSS            
            </span>
            <span class=" col-xl-5 col-xl-5 flex_me float-right">
                <div class="col-md-5"> 
                    <span style="color:#e50056" class="d-flex">
                            <span style="color: #626262; font-weight: bolder; font-size: 1.2rem;">Bewertung Projekt: </span>
                        <input type="text" id="answered_number" name="answered_number" readonly style="background: transparent;border: 0;     width: 20px;"/> / 
                        <input type="number" id="total_number" name="total_number" readonly style="background: transparent;border: 0;     width: 20px;"/>
                    </span>
                </div>
                <div class="col-md-7">
                    <div class="progress progress-bar-primary progress-lg">
                        <div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%;">
                            <span id="percent">0%</span>
                        </div>
                    </div>
                </div>
            </span>
        </div>
        <div id="collapse3" role="tabpanel" aria-labelledby="headingCollapse3" class="collapse" style="">
            <div class="card-content">
                <div class="card-body">
                   <div class="row">
                        <div class="col-xl-6 col-xl-6 col-12">
                            <div class="card ">
                                <div class="card-body"> 
                                    <article>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>gewünschte Größe</strong></div>
                                                <div class="col-md-6">ca. 10 kWp</div>
                                            </div>   
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Einspeisezusage EVU Netzverträglichkeit</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">nein</span> 
                                                </div>
                                            </div> 
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Leerohr vorhanden</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">nein</span> 
                                                </div>
                                            </div> 
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Kabelführung durch</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">Fassada</span> 
                                                </div>
                                            </div>
                                            <hr class="normal"> 
                                            <div class="row mb-1">
                                            <div class="col-md-6"><strong>Blitzschutz auf dem Dach vorhanden</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">nein</span> 
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Notiz</strong></div>
                                                <div class="col-md-6">
                                                    <span class="mr-1">
                                                        <p>
                                                            Kunde möchte Exceariores nis sequati tem veles sita est fuga. Non rero coritat fuga.
                                                        </p>
                                                    </span> 
                                                </div>
                                            </div> 
                                    </article> 
                                </div>
                            </div>
                        </div>
                   </div>
                </div>
            </div>
        </div> 
    </div>
     
</div>