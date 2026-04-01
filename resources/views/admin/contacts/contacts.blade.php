@extends('admin.layouts.app')
@section('title') ALL KONTAKTS @stop

@section('style')

    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}"> 
<link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" /> 
    <meta name="csrf-token" content="{{ csrf_token() }}">  
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12  "> 
                <h2 class="content-header-title float-left mb-0">Alle Kontakte</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="{{ url('/new_lead_view') }}">Kontaktlisten</a></li> 
                            
                    </ol>
                </div>  
            </div>
        </div>

        <div class="content-body mt-2">
            <div class="row float-right"> 

                    <form method="GET" action="{{ route('all.contacts') }}" class="form-inline mr-2">
                        <input type="text" name="search" class="form-control mr-1" placeholder="Suchen Sie nach Kontakten..." value="{{ $search }}">
                        
                        <select name="type" class="form-control mr-1">
                            <option value="">-- Typ wählen --</option>
                            <option value="Kunde" {{ request('type') == 'Kunde' ? 'selected' : '' }}>Kunde</option>
                            <option value="Hersteller" {{ request('type') == 'Hersteller' ? 'selected' : '' }}>Hersteller</option>
                            <option value="Lieferant" {{ request('type') == 'Lieferant' ? 'selected' : '' }}>Lieferant</option>
                            <option value="Anfrage" {{ request('type') == 'Anfrage' ? 'selected' : '' }}>Anfrage</option>
                            <option value="Mitarbeiter" {{ request('type') == 'Mitarbeiter' ? 'selected' : '' }}>Mitarbeiter</option>
                        </select>

                        <button class="btn btn-primary" type="submit">Suchen</button>
                    </form> 
                     <a href="{{ route('all.contacts.export', ['search' => $search]) }}" class="btn btn-success">Als CSV exportieren</a> 
 
            </div>
            <div class="table-responsive">

                <table class="table table-bordered table-striped">
                    @php
                        $sortField = request('sort', 'name');
                        $sortDir = request('direction', 'asc');
                        $reverseDir = $sortDir === 'asc' ? 'desc' : 'asc';
                    @endphp

                    <thead>
                        <tr>
                            <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'lastname', 'direction' => $reverseDir]) }}">Nachname</a></th> 
                            <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => $reverseDir]) }}">Name</a></th>  
                            <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'type', 'direction' => $reverseDir]) }}">Typ</a></th>
                            <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'phone', 'direction' => $reverseDir]) }}">Telefon</a></th>
                            <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'direction' => $reverseDir]) }}">E-Mail</a></th>
                            <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'address', 'direction' => $reverseDir]) }}">Adresse</a></th>
                        </tr>
                    </thead>

                   <tbody>
                        @forelse($contacts as $contact)

                          
                            <tr>
                                <td>{{ $contact->lastname ?? '-' }}</td> 
                                <td>{{ $contact->name }}</td>
                               <td>
                                    <span class="badge badge-primary">{{ $contact->type }}</span>

                                    @if(in_array(strtolower($contact->type), ['kunde','customer']))
                                        <span class="badge ml-1 {{ $contact->purchase_status_badge }}">
                                            {{ $contact->purchase_status_label }}
                                        </span>

                                        @php
                                            $tier = $contact->tier ?? 'none';
                                            $iconAbs = public_path("icons/{$tier}.png");   // e.g. public/icons/gold.png
                                            $iconUrl = asset("icons/{$tier}.png");
                                        @endphp

                                        @if($tier !== 'none' && file_exists($iconAbs))
                                            <img
                                                src="{{ $iconUrl }}"
                                                alt="{{ $contact->tier_label }}"
                                                title="{{ $contact->tier_label }}"
                                                style="height:50px;width:50px;margin-left:6px"
                                                loading="lazy" decoding="async"
                                            />
                                            <small class="text-muted ml-1">{{ $contact->tier_label }}</small>
                                        @endif
                                    @endif
                                </td>



                               
                                <td>{{ $contact->phone }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->address ?? '-' }}</td>
                                <td>
                                    @php
                                        $link = '#';
                                        switch ($contact->type) {
                                            case 'Hersteller':
                                                $link = url('brand_department/' . $contact->id);
                                                break;
                                            case 'Lieferant':
                                                $link = url('distributor_department/' . $contact->id);
                                                break;
                                            case 'Mitarbeiter':
                                                $link = url('employee_profile/' . $contact->id);
                                                break;
                                            case 'Anfrage':
                                                $link = url('inquiry_show/' . $contact->id);
                                                break;
                                            case 'Kunde':
                                                $link = url('new_lead_profile/' . $contact->id);
                                                break;
                                        }
                                    @endphp
                                    <a href="{{ $link }}" class="btn btn-primary mr-1 mb-1 waves-effect waves-light" target="_blank">Profil ansehen</a>
                                </td>

                                 
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No contacts found.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>

                <div class="mt-2">
                    {{ $contacts->links() }}
                </div>

            </div>
        </div>
    </div>
</div>
@stop

@section('script') 
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

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