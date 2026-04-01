 
@extends('admin.layouts.app')
@section('title')Förderungen @stop
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
                            <h2 class="content-header-title float-left mb-0">Förderungen</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Liste</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                          
            <div class="content-body"> 

                <div class="row">
                    <div class="col-md-8 col-12 mb-1">
                        <fieldset>
                            <div class="input-group">
                              
                                <input type="text" class="form-control" placeholder="suchen" aria-label="search" name="search">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary  waves-effect waves-light ml-2" data-toggle="modal" data-target="#createModal">
                                        Erstellen
                                    </button>  
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="modal fade text-left" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModal" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="createModal">Erstellen</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="begFundingForm">
                                    <table class="table table-bordered" id="fundingTable">
                                        <thead>
                                            <tr>
                                                <th>Heizungstyp</th>
                                                <th>Alt > 20 Jahre</th>
                                                <th>Bonus (Speed/Effizienz/Einkommen)</th>
                                                <th>Gesamt %</th>
                                                <th>Max Betrag (€)</th>
                                                <th>Aktion</th>
                                            </tr>
                                        </thead>
                                        <tbody id="fundingRows">
                                            <!-- Rows will be added here -->
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-sm btn-success" id="addRow">+ Zeile hinzufügen</button>
                                </form>

                               
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary waves-effect waves-light save_button"  >speichern</button>
                                <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">abbrechen</button>
                            </div>
                        </div>
                    </div>
                </div>
             
                <div class="row">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Typ</th>
                                    <th>Alt</th>
                                    <th>Bonus</th>
                                    <th>Gesamt %</th>
                                    <th>Max Betrag</th>
                                    <th>Aktion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fundings as $funding)
                                    <tr>
                                        <td>{{ $funding->heating_type }}</td>
                                        <td>{{ $funding->is_older_than_20_years ? 'Ja' : 'Nein' }}</td>
                                        <td>
                                            @if($funding->uses_speed_bonus) Speed @endif
                                            @if($funding->uses_efficiency_bonus) Effizienz @endif
                                            @if($funding->uses_income_bonus) Einkommen @endif
                                        </td>
                                        <td>{{ $funding->total_percentage }}%</td>
                                        <td>{{ number_format($funding->max_funding_amount, 0, ',', '.') }} EUR</td>
                                        <td>
                                            <a href="{{ route('beg-fundings.edit', $funding) }}" class="btn btn-primary btn-sm">Bearbeiten</a>
                                            <form action="{{ route('beg-fundings.destroy', $funding) }}" method="POST" style="display:inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-danger btn-sm" onclick="return confirm('Sicher?')">Löschen</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> 
                    {{ $fundings->links() }}
                </div>
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


<script>
$(document).ready(function () {
    function newRow() {
        return `
        <tr>
            <td>
                <select name="heating_type[]" class="form-control">
                    <option value="gas">Gas</option>
                    <option value="öl">Öl</option>
                    <option value="kohle">Kohle</option>
                    <option value="nachtspeicher">Nachtspeicher</option>
                    <option value="wärmepumpe">Wärmepumpe</option>
                </select>
            </td>
            <td>
                <select name="is_older_than_20_years[]" class="form-control">
                    <option value="1">Ja</option>
                    <option value="0">Nein</option>
                </select>
            </td>
            <td>
                <div class="form-check"><input type="checkbox" name="uses_speed_bonus[]" value="1"></div>
                <div class="form-check"><input type="checkbox" name="uses_efficiency_bonus[]" value="1"></div>
                <div class="form-check"><input type="checkbox" name="uses_income_bonus[]" value="1"></div>
            </td>
            <td><input type="number" step="0.1" name="total_percentage[]" class="form-control" /></td>
            <td><input type="number" name="max_funding_amount[]" class="form-control" /></td>
            <td><button type="button" class="btn btn-sm btn-danger removeRow">Löschen</button></td>
        </tr>`;
    }

    $('#addRow').on('click', function () {
        $('#fundingRows').append(newRow());
    });

    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').remove();
    });

    $('.save_button').on('click', function (e) {
    e.preventDefault();

    const form = $('#begFundingForm');
    const data = form.serialize();

    // Clear previous errors
    form.find('.is-invalid').removeClass('is-invalid');
    form.find('.invalid-feedback').remove();

    $.ajax({
        url: "{{ route('beg-fundings.store') }}",
        method: "POST",
        data: data,
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        success: function () {
            Swal.fire('Erfolg', 'Einträge wurden gespeichert!', 'success');
            $('#createModal').modal('hide');
            form[0].reset();
            $('#fundingRows').empty();
            location.reload();
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;

                $.each(errors, function (field, messages) {
                    const baseField = field.replace(/\.\d+/, '[]'); // normalize array field
                    const input = form.find(`[name="${baseField}"]`).first(); // only mark first row

                    input.addClass('is-invalid');
                    input.after(`<div class="invalid-feedback d-block">${messages[0]}</div>`);
                });

                Swal.fire('Fehler', 'Bitte korrigieren Sie die markierten Felder.', 'error');
            }
        }
    });
});

    
});
</script>

@endsection