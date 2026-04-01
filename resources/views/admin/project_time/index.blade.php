@extends('admin.layouts.app')

@section('title', 'Projektzeit-Anfragen')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Projektzeit-Anfragen</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kunde</th>
                        <th>Objekt</th>
                        <th>Produkt</th>
                        <th>Extra</th>
                        <th>Status</th>
                        <th>Beantragt von</th>
                        <th>Antwort</th>
                        <th>Aktion</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($requests as $req)
                    @php
                        $hm = sprintf('%02d:%02d', intdiv($req->extra_minutes, 60), $req->extra_minutes % 60);
                    @endphp
                    <tr>
                        <td>{{ $req->id }}</td>
                        <td>
                            @if($req->customer)
                                #{{ $req->customer->id }} {{ $req->customer->name }} {{ $req->customer->lastname }}
                            @endif
                        </td>
                        <td>{{ $req->alternative->object_name ?? '' }}</td>
                        <td>{{ $req->product->article_group ?? '' }}</td>
                        <td>{{ $hm }} h</td>
                        <td>
                            @if($req->status === 'pending')
                                <span class="badge badge-warning">Offen</span>
                            @elseif($req->status === 'approved')
                                <span class="badge badge-success">Genehmigt</span>
                            @else
                                <span class="badge badge-danger">Abgelehnt</span>
                            @endif
                        </td>
                        <td>{{ $req->requester->name ?? '' }} {{ $req->requester->lastname ?? '' }}</td>
                        <td style="max-width:260px; white-space:pre-wrap;">{{ $req->reason }}</td>
                        <td>
                            @if($req->status === 'pending')
                                <form action="{{ route('admin.project-time.approve', $req) }}" method="post" class="mb-25">
                                    @csrf
                                    <input type="text" name="answer" class="form-control form-control-sm mb-25"
                                           placeholder="Kommentar...">
                                    <button class="btn btn-sm btn-success btn-block">Genehmigen</button>
                                </form>
                                <form action="{{ route('admin.project-time.reject', $req) }}" method="post">
                                    @csrf
                                    <input type="text" name="answer" class="form-control form-control-sm mb-25"
                                           placeholder="Kommentar...">
                                    <button class="btn btn-sm btn-danger btn-block">Ablehnen</button>
                                </form>
                            @else
                                <div style="max-width:260px; white-space:pre-wrap;">
                                    {{ $req->answer }}
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted">Keine Anfragen gefunden.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-1">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection

