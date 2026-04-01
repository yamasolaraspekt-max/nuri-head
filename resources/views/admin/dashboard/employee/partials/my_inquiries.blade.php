@php
    $inquires = DB::table('inquiries')->whereNull('deleted_at')->get();
    $my_inquiry = $inquires->where('contact_person', auth()->user()->name)->count();
    $inquiry_list = $inquires->whereNotIn('status', ['Junk', 'Published'])->count(); 
    $published_inquiries = $inquires->where('status', 'Published')->count();
@endphp
<div class="cards">
    <h3 class="active-title">MEINE ANFRAGEN</h3>
    <a href="{{ url('my_inquiries') }}">
        <img src="{{ asset('images/dashboard/icon_speedometer.svg') }}" alt="Gauge Icon" class="dashboard-image link-image-active">
    </a>
    <hr>
       <table class="table table-borderless" style="text-align:left;" id="dashboard_table"> 
        <tr >
            <td>
               <a href="{{ url('my_inquiries')}}" class="black"> <i class="feather icon-star"></i> Neue</a>
            </td> 
             <td>
               <span class="dashboard-title">{{ $my_inquiry ?? 0 }}</span> 
            </td>  
            <td>
                
            </td>
        </tr>
        <tr class="border-zero">
        <td>
              <a href="{{ url('inquiry_view')}}" class="black"> <i class="feather icon-bell"></i>  Fällige</a>
            </td>  
            <td>
                <span class="dashboard-title">{{ $inquiry_list ?? 0 }}</span>
            </td>
        </tr>
        <tr class="border-zero">
              <td>
               <a href="{{ url('my_leads')}}" class="black"> <i class="feather icon-info warning"></i> Wichtige</a>
            </td>
            <td>
                <span class="dashboard-title">{{ $published_inquiries ?? 0 }}</span>
            </td>
        </tr>

           <tr class="border-zero">
              <td>
               <a href="{{ url('my_leads')}}" class="black"><i class="feather icon-thumbs-up primary"></i>  Qualifizierte</a>
            </td>
            <td>
                <span class="dashboard-title">{{ $published_inquiries ?? 0 }}</span>
            </td>
        </tr>
    </table>   
</div>
