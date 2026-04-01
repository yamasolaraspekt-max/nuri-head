
 
<style>
    .wizard-nav {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        gap: 10px;
        flex-wrap: wrap;
    }

    .wizard-step {
        flex: 1;
        text-align: center;
        padding: 10px 5px;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #666;
        font-weight: normal;
    }

    .wizard-step img {
        width: 90px;
        margin-bottom: 5px;
        transition: transform 0.3s ease;
    }

    .wizard-step.active {
        color: #8fc73e;
        font-weight: bold;
    }

    .wizard-step.active .wizard-progress-count {
        color: #8fc73e;
        font-weight: bold;
    }

    .wizard-step:hover img {
        transform: scale(1.05);
    }

    .wizard-progress-count {
        display: block;
        font-size: 0.8rem;
        color: #aaa;
        font-weight: normal;
    }

    .tab-pane .row {
        padding: 0 20px;
    }
</style>
<div class="section-content"> 
    <div class="col-12 p-0"> 
        <div class="cards"> 
            <div class="card-header  d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0"><i class="feather icon-settings"></i> Energieverbrauch & Objektdaten</h5>  
            </div> 
            <div class="card-body p-0">
                <div class="wizard-nav">
                    <div class="wizard-step active" onclick="showTab(1)">
                        <img src="{{ asset('images/icons/dokumente.svg') }}" alt="" style="width: 72px;"> <br> 
                                Objektdaten 
                            <span class="wizard-progress-count" id="step1-count">(0/17)</span>
                        </div>
                        <div class="wizard-step" onclick="showTab(2)">
                            <img src="{{ asset('images/icons/haus_schraeg.svg') }}" alt="" style="width: 72px;"> <br> 
                                Dachinformation 
                            <span class="wizard-progress-count" id="step2-count">(0/26)</span>
                        </div>
                        <div class="wizard-step" onclick="showTab(3)">
                            <img src="{{ asset('images/articles/warmpumpe.png') }}" alt="" style="width: 100px;"> <br> 
                            Heizungsinformation
                            <span class="wizard-progress-count" id="step3-count">(0/21)</span>
                        </div>
                        <div class="wizard-step" onclick="showTab(4)">
                        <img src="{{ asset('images/articles/battery.png') }}" alt="" style="width: 100px;"> <br>
                            E-Mobilität 
                            <span class="wizard-progress-count" id="step4-count">(0/10)</span>
                        </div>

                        <div class="wizard-step" onclick="showTab(5)">
                        <img src="{{ asset('images/icons/zaehler.svg') }}" alt="" style="width: 72px;"> <br>
                            Energieverbrauch 
                            <span class="wizard-progress-count" id="step5-count">(0/12)</span>
                        </div> 
                </div>

                <div class="tab-content pt-2">
                    <div class="tab-pane active" id="step1" role="tabpanel">
                    @include('admin.new_leads.layouts.partials.object_data', ['alternative_id' => $alternative->id])
                    </div>
                    <div class="tab-pane" id="step2" role="tabpanel">
                       @include('admin.new_leads.layouts.partials.roof_info', ['alternative_id' => $alternative->id])
                    </div>
                    <div class="tab-pane" id="step3" role="tabpanel">
                         @include('admin.new_leads.layouts.partials.heating_info', ['alternative_id' => $alternative->id])
                    </div>
                    <div class="tab-pane" id="step4" role="tabpanel">
                        @include('admin.new_leads.layouts.partials.e_mobility', ['alternative_id' => $alternative->id])
                    </div>
                    <div class="tab-pane" id="step5" role="tabpanel">
                    @include('admin.new_leads.layouts.partials.energy_usage', ['alternative_id' => $alternative->id])
                     
                    </div>
                </div> 
            </div> 
        </div> 
    </div> 
</div>
