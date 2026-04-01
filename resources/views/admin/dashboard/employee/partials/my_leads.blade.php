@php
    $leadLists = DB::table('new_leads')
                    ->join('lead_alternative_adds as alt', 'alt.lead_id', '=', 'new_leads.id')
                    ->select('alt.stage', 'alt.id as alt_id', 'alt.status', 'alt.deleted_at as alt_delete', 'new_leads.status', 'new_leads.contact_person')
                     ->whereNull('new_leads.deleted_at')->get();

    $my_leads = $leadLists->where('contact_person', auth()->user()->name)->count();
    $all_lead = $leadLists->whereNull('stage')->where('status', '!=', 'Junk')->count(); 
    $qualified_lead = $leadLists->where('stage', 'plan')->count();
@endphp
 
<div class="cards">
    <h3 class="active-title">MEINE LEADS</h3>
    <a href="{{ url('my_leads') }}">
        <img src="{{ asset('images/dashboard/icon_roket.svg') }}" alt="Gauge Icon" class="dashboard-image link-image-active">
    </a>
    <hr> 
    <table class="table table-borderless" style="text-align:left;" id="dashboard_table">
        <tr >
            <td>
               <a href="{{ url('my_leads')}}" class="black">Neue</a>
            </td> 
             <td>
               <span class="dashboard-title">{{ $my_leads ?? 0 }}</span> 
            </td>  
        </tr>
        <tr class="border-zero">
            <td>
              <a href="{{ url('new_lead_view')}}" class="black"> Fällige</a>
            </td>  
            <td>
                <span class="dashboard-title">{{ $all_lead ?? 0 }}</span>
            </td>
        </tr>

        <tr class="border-zero">
            <td>
              <a href="{{ url('new_lead_view')}}" class="black"> Wichtige</a>
            </td>  
            <td>
                <span class="dashboard-title">{{ $all_lead ?? 0 }}</span>
            </td>
        </tr>
        <tr class="border-zero">
              <td>
               <a href="{{ url('my_leads')}}" class="black">Qualifiziert</a>
            </td>
            <td>
                <span class="dashboard-title">{{ $qualified_lead ?? 0 }}</span>
            </td>
        </tr>
    </table>  
</div>
