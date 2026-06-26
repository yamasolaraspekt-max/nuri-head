@extends('admin.layouts.app')

@section('title', 'IDS Preisanfrage')

@section('content')
<div class="container-fluid" style="padding:120px 24px 40px;">
    <div class="card shadow-sm border-0" style="border-radius:18px; overflow:hidden;">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h3 class="mb-1" style="font-weight:900;">
                        IDS Preisanfrage aus Materialliste
                    </h3>

                    <div class="text-muted">
                        Angebot #{{ $offer?->id ?? '-' }}
                        · Ordner: {{ $folder?->name ?? '-' }}
                        · Kunde:
                        {{
                            trim(
                                ($offer?->customer?->firma ?? '') . ' ' .
                                ($offer?->customer?->name ?? '') . ' ' .
                                ($offer?->customer?->lastname ?? '')
                            ) ?: '-'
                        }}
                    </div>
                </div>

                <a href="{{ url()->previous() }}" class="btn btn-light border">
                    Zurück
                </a>
            </div>
        </div>

        <div class="card-body">
            @if($items->isEmpty())
                <div class="alert alert-warning mb-0">
                    Keine Materialpositionen für die IDS Preisanfrage gefunden.
                </div>
            @else
                <form method="POST" action="{{ route('ids.search.forward') }}" target="_blank">
                    @csrf

                    <input type="hidden" name="offer_id" value="{{ $offer?->id }}">
                    <input type="hidden" name="offer_folder_id" value="{{ $folder?->id }}">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width:48px;">
                                        <input type="checkbox" id="select-all-ids-items" checked>
                                    </th>
                                    <th>Material</th>
                                    <th>Hersteller-Nr.</th>
                                    <th>Lieferant-Nr.</th>
                                    <th class="text-end">Menge</th>
                                    <th>Einheit</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($items as $index => $item)
                                    <tr>
                                        <td>
                                            <input
                                                type="checkbox"
                                                class="ids-item-check"
                                                name="items[{{ $index }}][selected]"
                                                value="1"
                                                checked
                                            >
                                        </td>

                                        <td>
                                            <strong>{{ $item['name'] ?? '-' }}</strong>

                                            <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item['product_id'] ?? '' }}">
                                            <input type="hidden" name="items[{{ $index }}][component_id]" value="{{ $item['component_id'] ?? '' }}">
                                            <input type="hidden" name="items[{{ $index }}][name]" value="{{ $item['name'] ?? '' }}">
                                        </td>

                                        <td>
                                            {{ $item['article_no'] ?? '-' }}
                                            <input type="hidden" name="items[{{ $index }}][article_no]" value="{{ $item['article_no'] ?? '' }}">
                                        </td>

                                        <td>
                                            {{ $item['distributor_article_no'] ?? '-' }}
                                            <input type="hidden" name="items[{{ $index }}][distributor_article_no]" value="{{ $item['distributor_article_no'] ?? '' }}">
                                        </td>

                                        <td class="text-end">
                                            {{ number_format((float) ($item['qty'] ?? 0), 2, ',', '.') }}
                                            <input type="hidden" name="items[{{ $index }}][qty]" value="{{ $item['qty'] ?? 0 }}">
                                        </td>

                                        <td>
                                            {{ $item['unit'] ?? '-' }}
                                            <input type="hidden" name="items[{{ $index }}][unit]" value="{{ $item['unit'] ?? '' }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ url()->previous() }}" class="btn btn-light border">
                            Abbrechen
                        </a>

                        <button type="submit" class="btn btn-primary">
                            An IDS weiterleiten
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all-ids-items');

    if (!selectAll) return;

    selectAll.addEventListener('change', function () {
        document.querySelectorAll('.ids-item-check').forEach(function (checkbox) {
            checkbox.checked = selectAll.checked;
        });
    });
});
</script>
@endsection