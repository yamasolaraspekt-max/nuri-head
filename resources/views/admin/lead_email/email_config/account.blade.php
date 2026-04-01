@extends('admin.layouts.app')
@section('title') ART DES KONTAKTS @stop

@section('content')
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Lead-E-Mail-Kontokonfiguration</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="content-body">
            <!-- Add Button -->
            <div class="row mb-2">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('lead-email-accounts.index') }}">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Suche nach Label oder E-Mail..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-info" type="submit">Suchen</button>
                                <a href="{{ route('lead-email-accounts.index') }}" class="btn btn-secondary">Zurücksetzen</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#createModal">+ Neues Konto</button>
                </div>
            </div>

            <!-- Table -->
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <th>Email</th>
                                <th>Host</th>
                                <th>Status</th>
                                <th>Letzter Test</th> 
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $account)
                                <tr>
                                    <td>{{ $account->label }}</td>
                                    <td>{{ $account->email }}</td>
                                    <td>{{ $account->host }}</td>
                                   <td>
                                        <a href="javascript:void(0);" 
                                        class="toggle-status badge badge-{{ $account->status == 'Published' ? 'success' : 'secondary' }}" 
                                        data-id="{{ $account->id }}" 
                                        data-status="{{ $account->status }}">
                                            {{ $account->status == 'Published' ? 'Aktiv' : 'Inaktiv' }}
                                        </a>
                                    </td>
                                     <td id="test-result-{{ $account->id }}">
                                        {!! $account->test ?? '<span class="text-muted">Nicht getestet</span>' !!}
                                    </td>



                                    <td>
                                        <button class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#editModal{{ $account->id }}">
                                            <i class="feather icon-edit"></i>
                                        </button>

                                        <form action="{{ route('lead-email-accounts.destroy', $account->id) }}" method="POST" onsubmit="return confirm('Löschen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light delete-form "><i class="feather icon-trash"></i></button>
                                        </form>
                                        <button class="btn btn-icon btn-info btn-sm test-email-btn" data-id="{{ $account->id }}">
                                            <i class="feather icon-send"></i>
                                        </button>
                                    </td>

                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal{{ $account->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $account->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <form action="{{ route('lead-email-accounts.update', $account->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">E-Mail-Konto bearbeiten</h5>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body row">
                                                    @include('admin.lead_email.email_config._form', ['data' => $account])
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Aktualisieren</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <tr><td colspan="5">Keine E-Mail-Konten gefunden.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('lead-email-accounts.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Neues E-Mail-Konto hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body row">
                    @include('admin.lead_email.email_config._form', ['data' => null])
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Speichern</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('script')
<script>
$(document).ready(function(){
    @if(Session::has('updated_msg'))
    toastr.success("{{ session('updated_msg') }}");
    @endif
    @if(Session::has('save_msg'))
    toastr.success("{{ session('save_msg') }}");
    @endif
    @if(Session::has('delete_msg'))
    toastr.error("{{ session('delete_msg') }}");
    @endif

    $('.toggle-status').click(function() {
        const el = $(this);
        const id = el.data('id');
        const currentStatus = el.data('status');

        $.ajax({
            url: '/admin/lead-email-accounts/toggle-status/' + id,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
            },
            success: function(response) {
                el.data('status', response.new_status);
                el.removeClass('badge-success badge-secondary');
                el.addClass(response.new_status === 'Published' ? 'badge-success' : 'badge-secondary');
                el.text(response.new_status === 'Published' ? 'Aktiv' : 'Inaktiv');
                toastr.success('Status aktualisiert.');
            },
            error: function() {
                toastr.error('Fehler beim Aktualisieren des Status.');
            }
        });
    });

    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        let form = this;
        Swal.fire({
            title: 'Bist du sicher?',
            text: "Diese Aktion kann nicht rückgängig gemacht werden!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ja, löschen!',
            cancelButtonText: 'Abbrechen'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });



});
</script>


<script>

    $('.test-email-btn').on('click', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Verbindung testen?',
            text: "Eine Test-E-Mail wird gesendet.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ja, testen',
            cancelButtonText: 'Abbrechen'
        }).then((result) => {
            if (result.isConfirmed) {
                 $.post('/admin/lead-email-accounts/test/' + id, {
                    _token: '{{ csrf_token() }}'
                }, function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#test-result-' + id).html('✅ Erfolgreich');
                    } else {
                        toastr.error(response.message);
                        $('#test-result-' + id).html('❌ Fehlgeschlagen');
                    }
                }).fail(function (xhr) {
                    toastr.error('Serverfehler: ' + xhr.responseText);
                    $('#test-result-' + id).html('❌ Fehler beim Verbinden');
                });

            }
        });
    });

</script>

@endsection
