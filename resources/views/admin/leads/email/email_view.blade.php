@extends('admin.layouts.app')

@section('title') KUNDEN UND OBJEKTDATEN @endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
@endsection

@section('content')
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-12 mb-2">
                <h2 class="content-header-title">LEADS INFORMATION</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">DETAILS</a></li>
                </ol>
            </div>
        </div>

        <div class="content-body">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <button class="btn btn-primary btn-block mb-1" data-toggle="modal" data-target="#composeForm">
                                <i class="feather icon-edit"></i> Neue Nachricht
                            </button>
                            <a href="{{ Route::currentRouteName()=="send.email.view" ? url('send_email_refresh') : url('email_refresh') }}" class="btn btn-secondary btn-block mb-2">
                                <i class="feather icon-refresh-ccw"></i> Refresh
                            </a>
                            <input type="text" class="form-control mb-1" placeholder="Suche Name/Email..." onkeyup="filterLeads(this.value)">

                            <div class="list-group">
                                @foreach ($leads as $lead)
                                    <a href="{{ url('email_view?search='.$lead->recipient_name) }}" class="list-group-item">
                                        {{ $lead->recipient_name }}
                                        <span class="badge badge-warning float-right">
                                            {{ DB::table('leads')->where('recipient_name', '=', $lead->recipient_name)->count() }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>

                            <hr>
                            <h6>E-Mail-Ordner</h6>
                            <div class="list-group">
                                <a href="{{ url('email_view') }}" class="list-group-item {{ Route::currentRouteName() == 'email_view' ? 'active' : '' }}">
                                    <i class="feather icon-mail"></i> Inbox
                                </a>
                                <a href="{{ url('send_email_view') }}" class="list-group-item {{ Route::currentRouteName() == 'send.email.view' ? 'active' : '' }}">
                                    <i class="fa fa-paper-plane-o"></i> Sent
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-md-9">
                    <form action="{{action('App\Http\Controllers\LeadsController@index')}}" method="GET" class="mb-2">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Suche...">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">Go</button>
                            </div>
                        </div>
                    </form>

                    @foreach (['delete_msg' => 'danger', 'save_msg' => 'success', 'update_msg' => 'success', 'not_save' => 'danger'] as $msg => $type)
                        @if(Session($msg))
                            <div class="alert alert-{{ $type }}">
                                {{ Session($msg) }}
                            </div>
                        @endif
                    @endforeach

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Aufzeichnungen zur Kundenhistorie</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Datum</th>
                                            <th>Aktionen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $email)
                                            <tr>
                                                <td>
                                                    {{ $email->sender_name }}<br>
                                                    <small class="text-muted">{{ $email->recipient_name }}</small>
                                                </td>
                                                <td>
                                                    <strong>{{ $email->subject }}</strong><br>
                                                    <small>{{ $email->sender_email }}</small>
                                                    @php $customer = DB::table('new_leads')->where('email', $email->sender_email)->first(); @endphp
                                                    <div class="chip {{ $customer ? 'chip-success' : 'chip-danger' }} mt-1">
                                                        <div class="chip-body">
                                                            <span class="chip-text">
                                                                {{ $customer ? 'Gespeichert als ' . $customer->name : 'Nicht gespeichert' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($email->created_at)->diffForHumans() }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary mb-1" data-toggle="modal" data-target="#viewEmail{{ $email->id }}">
                                                        <i class="feather icon-eye"></i>
                                                    </button>
                                                    <a href="{{ url('/customer_email_add/'.$email->id.'/'.$email->sender_name.'/'.$email->sender_email) }}" class="btn btn-sm btn-success">
                                                        <i class="feather icon-save"></i>
                                                    </a>
                                                </td>
                                            </tr>

                                            <!-- View Email Modal -->
                                            <div class="modal fade" id="viewEmail{{ $email->id }}" tabindex="-1" role="dialog">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Email von {{ $email->sender_name }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><strong>Von:</strong> {{ $email->sender_email }}</p>
                                                            <hr>
                                                            <div class="modal-body" style="padding:0;">
                                                                    <iframe
                                                                        style="width: 100%; height: 600px; border: none;"
                                                                        sandbox="allow-same-origin allow-popups allow-forms allow-scripts"
                                                                        srcdoc="{!! e(str_replace('"', '&quot;', $email->body)) !!}">
                                                                    </iframe>

                                                                     
                                                            </div>


                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
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
<!-- END: Content-->

<!-- Compose Modal -->
<div class="modal fade" id="composeForm" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <form action="{{ action('App\Http\Controllers\EmailConfigurationController@send') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Neue Nachricht verfassen</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Von</label>
                        <select class="form-control select2" name="from">
                            @foreach ($emails as $em)
                                <option value="{{ $em->username }}">{{ $em->username }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>An</label>
                        <select class="form-control select2" name="to">
                            @foreach ($data as $e)
                                <option value="{{ $e->sender_email }}">{{ $e->sender_email }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Betreff</label>
                        <input type="text" name="subject" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Nachricht</label>
                        <textarea name="message" class="form-control" rows="6"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Senden</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2();
    });

    function filterLeads(keyword) {
        $('.list-group a').each(function () {
            let text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(keyword.toLowerCase()));
        });
    }
</script>
@endsection