
@extends('admin.layouts.app')
@section('title', 'Alle Berechnungen')
@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-12">
                <h2 class="content-header-title">Berechnungsübersicht</h2>
            </div>
        </div>
        <div class="content-body">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kunde</th>
                                <th>Solar ROI</th>
                                <th>WP ROI</th>
                                <th>Gesamtersparnis</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calculations as $calc)
                            <tr>
                                <td>{{ $calc->lead->name ?? 'N/A' }}</td>
                                <td>{{ $calc->solar_roi_years }} Jahre</td>
                                <td>{{ $calc->wp_roi_years }} Jahre</td>
                                <td>{{ number_format($calc->combined_savings, 2) }} €</td>
                                <td>
                                    <a href="{{ route('economic_calculations.edit', $calc->id) }}" class="btn btn-sm btn-warning">Bearbeiten</a>
                                    <form action="{{ route('economic_calculations.destroy', $calc->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Löschen?')">Löschen</button>
                                    </form>
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
@endsection


@section('script')
<script>
    $(document).ready(function() {
        $('.datatable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/German.json'
            }
        });
    });
</script>
@endsection
