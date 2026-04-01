@extends('admin.layouts.app')

@section('title', 'GC Online – Produkt übernehmen')

{{-- Select2 CSS --}}
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
    <style>
        .select2-container--default .select2-selection--single {
            border: 1px solid #d8d6de;
            border-radius: 0.357rem;
            height: calc(1.5em + 0.75rem + 2px);
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: calc(1.5em + 0.75rem);
            padding-left: 0.75rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(1.5em + 0.75rem + 2px);
            right: 8px;
        }
    </style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title">Produkt aus IDS-Artikel übernehmen</h2>
            </div>
        </div>

        <div class="content-body">
            <div class="max-w-4xl mx-auto px-4 py-4 space-y-4">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('ids.items.promote', $item) }}">
                    @csrf

                    <div class="row">
                        {{-- IDS-Artikel Info --}}
                        <div class="col-md-5 mb-2">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">IDS-Artikel</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-1 text-sm">
                                        <strong>Artikelnummer:</strong><br>
                                        <span class="font-mono text-xs">{{ $item->article_no }}</span>
                                    </div>
                                    <div class="mb-1 text-sm">
                                        <strong>Bezeichnung:</strong><br>
                                        <span>{{ $item->short_text ?? $item->long_text }}</span>
                                    </div>
                                    <div class="mb-1 text-sm">
                                        <strong>Menge:</strong><br>
                                        <span>{{ $item->qty }} {{ $item->unit }}</span>
                                    </div>
                                    <div class="mb-1 text-sm">
                                        <strong>Netto / Angebot:</strong><br>
                                        <span>
                                            {{ number_format($item->net_price, 2, ',', '.') }} €
                                            /
                                            {{ number_format($item->offer_price, 2, ',', '.') }} €
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Produkt-Stammdaten --}}
                        <div class="col-md-7 mb-2">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Produkt-Stammdaten</h4>
                                </div>
                                <div class="card-body space-y-2">

                                    <div class="form-group">
                                        <label for="product_name">Produktname</label>
                                        <input type="text"
                                               id="product_name"
                                               name="product_name"
                                               class="form-control"
                                               value="{{ old('product_name', $item->short_text ?? $item->long_text) }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="brand_id">Hersteller / Marke</label>
                                        <select id="brand_id"
                                                name="brand_id"
                                                class="form-control select2"
                                                required>
                                            <option value="">Bitte wählen …</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}"
                                                    {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="article_group_id">Artikelgruppe</label>
                                        <select id="article_group_id"
                                                name="article_group_id"
                                                class="form-control select2"
                                                required>
                                            <option value="">Bitte wählen …</option>
                                            @foreach($articleGroups as $group)
                                                <option value="{{ $group->id }}"
                                                    {{ old('article_group_id') == $group->id ? 'selected' : '' }}>
                                                    {{ $group->article_group }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="sub_article_group_id">Untergruppe</label>
                                        <select id="sub_article_group_id"
                                                name="sub_article_group_id"
                                                class="form-control select2">
                                            <option value="">Bitte zuerst Artikelgruppe wählen …</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="measure_unit">Mengeneinheit</label>
                                        <input type="text"
                                               id="measure_unit"
                                               name="measure_unit"
                                               class="form-control"
                                               value="{{ old('measure_unit', $item->unit) }}">
                                    </div>

                                    <div class="text-right mt-2">
                                        <a href="{{ route('ids.search.form') }}" class="btn btn-secondary">
                                            Abbrechen
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            Produkt übernehmen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> {{-- row --}}
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    {{-- jQuery + Select2 JS (falls nicht global geladen) --}}
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        (function () {
            const endpointTemplate = @json(
                route('article-groups.sub-groups', ['articleGroup' => 'ARTICLE_GROUP_ID'])
            );

            const oldArticleGroupId    = @json(old('article_group_id'));
            const oldSubArticleGroupId = @json(old('sub_article_group_id'));

            $(document).ready(function () {
                // Select2 initialisieren für alle Selects
                $('.select2').select2({
                    width: '100%',
                    placeholder: 'Bitte wählen …',
                    allowClear: true
                });

                const $articleSelect = $('#article_group_id');
                const $subSelect     = $('#sub_article_group_id');

                function loadSubGroups(groupId, preselectedId = null) {
                    if (!groupId) {
                        $subSelect.html('<option value="">Bitte zuerst Artikelgruppe wählen …</option>');
                        $subSelect.val(null).trigger('change');
                        return;
                    }

                    $subSelect.html('<option value="">Lade Untergruppen …</option>');
                    $subSelect.val(null).trigger('change');

                    const url = endpointTemplate.replace('ARTICLE_GROUP_ID', groupId);

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            let options = '<option value=""></option>';
                            data.forEach(function (sg) {
                                options += `<option value="${sg.id}">${sg.sub_article}</option>`;
                            });
                            $subSelect.html(options);

                            if (preselectedId) {
                                $subSelect.val(String(preselectedId));
                            }
                            $subSelect.trigger('change');
                        })
                        .catch(() => {
                            $subSelect.html('<option value="">Fehler beim Laden der Untergruppen</option>');
                            $subSelect.val(null).trigger('change');
                        });
                }

                $articleSelect.on('change', function () {
                    const groupId = $(this).val();
                    loadSubGroups(groupId);
                });

                // Wenn Validation-Fehler -> alte Auswahl nachladen
                if (oldArticleGroupId) {
                    loadSubGroups(oldArticleGroupId, oldSubArticleGroupId);
                }
            });
        })();
    </script>
@endsection
