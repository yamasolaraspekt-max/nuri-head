@extends('admin.layouts.app')
@section('title') Lohn Vollkosten @stop
@section('content')

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
                          
            <div class="content-body">
             <!-- Table Hover Animation start -->
             <div class="row" id="table-hover-animation">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Mitarbeiter Lohn Vollkosten</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                
            

                                  <div class="col-md-3 float-right">
                                        <div class="card-body">
                                            <a type="button" class="btn btn-outline-primary block btn-lg" href="{{ url('upload_salary/'.request()->id) }}">
                                                Aktualisierung
                                            </a>
                                        </div>
                                    </div>

                                       
                                    
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th>Mitarbeiter</th>
                                                    <th scope="col">Stunde pro Woche</th>
                                                    <th scope="col">Stunde pro Tag</th>
                                                    <th scope="col">Stunden pro Jahr</th>
                                                    <th scope="col">Urlaubstage</th>
                                                    <th scope="col">Urlaubstunden</th>
                                                    <th scope="col">Krankheit</th>
                                                    <th scope="col">Krankheitstunden</th>
                                                    <th scope="col">Lohanteil Krankasse</th>
                                                    <th scope="col">Lohnanteil Arbeitgeber</th>
                                                    <th scope="col">Feiertage</th>
                                                    <th scope="col">Feiertagstunden</th>
                                                    <th scope="col">verbleibende Arbeitsstunden</th>
                                                    <th scope="col">Unproduktive Arbeitstage</th>
                                                    <th scope="col">Unproduktive Arbeitsstunden</th>
                                                    <th scope="col">Produktivzeit</th>
                                                    <th scope="col">Lohn pro Stunde</th>
                                                    <th scope="col">monatlichen lohn ohne nebenkosten</th>
                                                    <th scope="col">Lohnkosten ohne nebenkosten</th>
                                                    <th scope="col">Lohnnebenkosten pro Stunden</th>
                                                    <th scope="col">monatlichen lohnnebenkosten</th>
                                                    <th scope="col">Jährliche Lohnnebenkosten</th>
                                                    <th scope="col">zzgl. Lohnnebenkosten</th>
                                                    <th scope="col">Bruttogehalt pro Jahr inkl. Lohnnebenkosten</th>
                                                    <th scope="col">Stundenlohn nach Produktivstunde</th>
                                                    <th scope="col">Monatlicher Lohn</th>
                                                    <th scope="col">Ackion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $item)
                                                <tr>
                                                    <th scope="row">{{$item->id}}</th>
                                                    <td>
                                                        <div class="avatar mr-1 ">
                                                        <img src="{{ asset('images/employee/'.$item->image) }}" alt="avtar img holder" height="32" width="32">
                                                        </div>
                                                        <br>
                                                        {{ $item->name }} {{ $item->lastname }}
                                                        <br>
                                                        <hr>
                                                        <div class="chip chip-warning mr-1">
                                                            <div class="chip-body">
                                                                <div class="avatar">
                                                                    <i class="feather icon-calendar"></i>
                                                                </div>
                                                                <span class="chip-text">{{ $item->salary_month }}.{{ $item->salary_year }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $item->per_week }}</td>
                                                    <td>{{ $item->per_day }}</td>
                                                    <td>{{ $item->per_year }}</td>
                                                    <td>{{ $item->holiday }}</td>
                                                    <td>{{ $item->holiday_hour }}</td>
                                                    <td>{{ $item->sick_leave }}</td>
                                                    <td>{{ $item->sick_leave_hour }}</td>
                                                    <td>{{ $item->health_insurance }}</td>
                                                    <td>{{ $item->shared_wage }}</td>
                                                    <td>{{ $item->public_holiday }}</td>
                                                    <td>{{ $item->public_holiday_hour }}</td>
                                                    <td>{{ $item->remaining_working_hour }}</td>
                                                    <td>{{ $item->unproductive_working_day }}</td>
                                                    <td>{{ $item->unproductive_working_hour }}</td>
                                                    <td>{{ $item->productive_hour }}</td>
                                                    <td>{{ $item->wege_per_hour }}</td>
                                                    <td>{{ $item->monthly_salary }}</td>
                                                    <td>{{ $item->labor_cost_hour }}</td>
                                                    <td>{{ $item->additional_cost }}</td>
                                                    <td>{{ $item->additional_cost_monthly }}</td>
                                                    <td>{{ $item->additional_cost_yearly }}</td>
                                                    <td>{{ $item->plus_additional_wage_cost }}</td>
                                                    <td>{{ $item->gross_salary }}</td>
                                                    <td>{{ $item->productive_hour_wege }}</td>
                                                    <td>{{ $item->total_monthly_salary }}</td>
                                                    <td>

                                                <!-- Delete Modal -->
                                                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" data-toggle="modal" data-target="#delete-pro{{$item->id}}">
                                                <i class="feather icon-trash"></i>
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade text-left" id="delete-pro{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">                                       
                                                                <h5>Datensatz löschen</h5>
                                                                <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                <p>Die Datensatznummer lautet: {{$item->id}} </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                              <a type="button" href="{{url('/department_destroy').'/'.$item->id}}" class="btn btn-primary">Yes</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Delete Modal -->

                                           
                                           

                                                    </td>
                                                   
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
                <!-- Table head options end -->
                {{$data->links()}}
            </div>
        </div>
    </div>
    <!-- END: Content-->
@stop

@section('script')
<script>
   
    $(document).ready(function(){
        @if(Session::has('update_msg'))
        toastr.success("{{ session('updated_msg') }}");
        @endif
        @if(Session::has('save_msg'))
        toastr.success("{{ session('save_msg') }}");
        @endif

       
  
  @if(Session::has('delete_msg'))
  toastr.error("{{ session('delete_msg') }}");
  @endif
    });
    

</script>
@endsection