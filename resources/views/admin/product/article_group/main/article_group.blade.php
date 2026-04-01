@extends('admin.layouts.app')
@section('title') Artikel-Gruppen @stop

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <!-- Header -->
        <div class="content-header row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-1">
                    <div>
                        <h2 class="content-header-title mb-0">Artikel-Gruppen</h2>
                        <small class="text-muted d-block">
                            Verwalten Sie Gruppen, Min-/Max-Werte und Sub-Artikelgruppen in einer Ansicht.
                        </small>
                    </div>

                    <button type="button"
                            class="btn btn-primary"
                            data-toggle="modal"
                            data-target="#modal-create-article-group">
                        <i class="feather icon-plus mr-50"></i> Neue Gruppe
                    </button>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="content-body">
            <div class="row" id="table-hover-animation">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            {{-- Global Filter / Search --}}
                            <form action="{{ route('article_group.index') }}" method="GET" class="mb-2">
                                <div class="form-row align-items-end">
                                    <div class="col-md-4 mb-1">
                                        <label for="search">Suche (Gruppe / Sub-Gruppe / Wert)</label>
                                        <div class="input-group input-group-merge">
                                            <input type="text"
                                                   id="search"
                                                   name="search"
                                                   class="form-control"
                                                   placeholder="z.B. PV, Speicher, Heizkreis"
                                                   value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" type="submit">
                                                    <i class="feather icon-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-1">
                                        <label for="min_value">Min-Wert (≥)</label>
                                        <input type="number"
                                               step="0.01"
                                               id="min_value"
                                               name="min_value"
                                               class="form-control"
                                               value="{{ request('min_value') }}"
                                               placeholder="z.B. 100.00">
                                    </div>

                                    <div class="col-md-3 mb-1">
                                        <label for="max_value">Max-Wert (≤)</label>
                                        <input type="number"
                                               step="0.01"
                                               id="max_value"
                                               name="max_value"
                                               class="form-control"
                                               value="{{ request('max_value') }}"
                                               placeholder="z.B. 10000.00">
                                    </div>

                                    <div class="col-md-2 mb-1 d-flex">
                                        <button type="submit" class="btn btn-primary mr-50 flex-fill">
                                            Anwenden
                                        </button>
                                        <a href="{{ route('article_group.index') }}"
                                           class="btn btn-light border flex-fill">
                                            Zurücksetzen
                                        </a>
                                    </div>
                                </div>
                            </form>

                            <hr class="my-1">

                            {{-- Main Table --}}
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                    <tr>
                                        <th style="width: 40px;"></th>
                                        <th style="width: 60px;">#</th>
                                        <th style="width: 90px;">Foto</th>
                                        <th>Initial</th>
                                        <th>Artikel-Gruppe</th>
                                        <th class="text-right">Min-Wert</th>
                                        <th class="text-right">Max-Wert</th>
                                        <th style="width: 150px;" class="text-center">Aktion</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($data as $group)
                                        {{-- Hauptzeile --}}
                                        <tr>
                                            <td class="text-center align-middle">
                                                <button class="btn btn-sm btn-icon btn-outline-secondary"
                                                        type="button"
                                                        data-toggle="collapse"
                                                        data-target="#collapse-group-{{ $group->id }}"
                                                        aria-expanded="false"
                                                        aria-controls="collapse-group-{{ $group->id }}">
                                                    <i class="feather icon-chevron-down"></i>
                                                </button>
                                            </td>
                                            <td>{{ $group->id }}</td>
                                            <td>
                                                <div class="avatar avatar-xl">
                                                    @if($group->image)
                                                        <img src="{{ asset('images/articles/'.$group->image) }}"
                                                             alt="{{ $group->article_group }}">
                                                    @else
                                                        @php
                                                            $label = $group->initial ?: $group->article_group;
                                                            $label = mb_substr($label, 0, 2);
                                                        @endphp
                                                        <span class="avatar-content bg-light-primary text-primary">
                                                            {{ mb_strtoupper($label) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $group->initial }}</td>
                                            <td>{{ $group->article_group }}</td>
                                            <td class="text-right">
                                                @if(!is_null($group->min_value))
                                                    {{ number_format($group->min_value, 2, ',', '.') }}
                                                @else
                                                    <span class="text-muted">–</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                @if(!is_null($group->max_value))
                                                    {{ number_format($group->max_value, 2, ',', '.') }}
                                                @else
                                                    <span class="text-muted">–</span>
                                                @endif
                                            </td>
                                            <td class="text-center">

                                                {{-- Edit Group --}}
                                                <button type="button"
                                                        class="btn btn-sm btn-icon btn-outline-primary mr-25"
                                                        data-toggle="modal"
                                                        data-target="#modal-edit-article-group-{{ $group->id }}">
                                                    <i class="feather icon-edit"></i>
                                                </button>

                                                {{-- Delete Group --}}
                                                <button type="button"
                                                        class="btn btn-sm btn-icon btn-outline-danger"
                                                        data-toggle="modal"
                                                        data-target="#modal-delete-article-group-{{ $group->id }}">
                                                    <i class="feather icon-trash-2"></i>
                                                </button>

                                            </td>
                                        </tr>

                                        {{-- Collapse: Sub-Artikelgruppen --}}
                                        <tr class="collapse" id="collapse-group-{{ $group->id }}">
                                            <td colspan="8" class="bg-light">
                                                <div class="p-1 p-md-2">
                                                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-1">
                                                        <div>
                                                            <h5 class="mb-0">
                                                                Sub-Artikelgruppen
                                                                <span class="badge badge-pill badge-primary">
                                                                    {{ $group->subArticleGroups->count() }}
                                                                </span>
                                                            </h5>
                                                            <small class="text-muted">
                                                                Varianten / Unterkategorien zu
                                                                <strong>{{ $group->article_group }}</strong>.
                                                            </small>
                                                        </div>

                                                        <div class="mt-50 mt-md-0">
                                                            <input type="text"
                                                                   class="form-control form-control-sm js-sub-search"
                                                                   data-target="#sub-table-{{ $group->id }}"
                                                                   placeholder="Innerhalb dieser Gruppe suchen …">
                                                        </div>
                                                    </div>

                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0"
                                                               id="sub-table-{{ $group->id }}">
                                                            <thead class="thead-light">
                                                            <tr>
                                                                <th style="width: 60px;">#</th>
                                                                <th style="width: 120px;">Initial</th>
                                                                <th>Sub-Artikel</th>
                                                                <th style="width: 150px;">Wert</th>
                                                                <th style="width: 120px;">Status</th>
                                                                <th style="width: 130px;" class="text-center">Aktion</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @forelse($group->subArticleGroups as $sub)
                                                                <tr>
                                                                    <td>{{ $sub->id }}</td>
                                                                    <td>{{ $sub->initial }}</td>
                                                                    <td>{{ $sub->sub_article }}</td>
                                                                    <td>{{ $sub->value }}</td>
                                                                    <td>{{ $sub->status }}</td>
                                                                    <td class="text-center">
                                                                        {{-- Edit sub --}}
                                                                        <button type="button"
                                                                                class="btn btn-xs btn-outline-primary"
                                                                                data-toggle="modal"
                                                                                data-target="#modal-edit-sub-{{ $sub->id }}">
                                                                            <i class="feather icon-edit"></i>
                                                                        </button>

                                                                        {{-- Delete sub --}}
                                                                        <button type="button"
                                                                                class="btn btn-xs btn-outline-danger"
                                                                                data-toggle="modal"
                                                                                data-target="#modal-delete-sub-{{ $sub->id }}">
                                                                            <i class="feather icon-trash-2"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>

                                                                {{-- Edit Sub Modal --}}
                                                                <div class="modal fade text-left"
                                                                     id="modal-edit-sub-{{ $sub->id }}"
                                                                     tabindex="-1"
                                                                     role="dialog"
                                                                     aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h4 class="modal-title">
                                                                                    Sub-Artikelgruppe bearbeiten
                                                                                </h4>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <form method="POST"
                                                                                  action="{{ route('sub_article_group.update') }}">
                                                                                @csrf
                                                                                <div class="modal-body">
                                                                                    <input type="hidden" name="id" value="{{ $sub->id }}">

                                                                                    <div class="form-row">
                                                                                        <div class="col-md-4">
                                                                                            <div class="form-group">
                                                                                                <label>Initial</label>
                                                                                                <input type="text"
                                                                                                       name="initial"
                                                                                                       class="form-control"
                                                                                                       value="{{ old('initial', $sub->initial) }}">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <div class="form-group">
                                                                                                <label>Sub-Artikel</label>
                                                                                                <input type="text"
                                                                                                       name="sub_article"
                                                                                                       class="form-control"
                                                                                                       value="{{ old('sub_article', $sub->sub_article) }}"
                                                                                                       required>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="form-row">
                                                                                        <div class="col-md-6">
                                                                                            <div class="form-group">
                                                                                                <label>Wert</label>
                                                                                                <input type="text"
                                                                                                       name="value"
                                                                                                       class="form-control"
                                                                                                       value="{{ old('value', $sub->value) }}">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-6">
                                                                                            <div class="form-group">
                                                                                                <label>Status</label>
                                                                                                <input type="text"
                                                                                                       name="status"
                                                                                                       class="form-control"
                                                                                                       value="{{ old('status', $sub->status) }}"
                                                                                                       placeholder="z.B. aktiv, inaktiv">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button"
                                                                                            class="btn btn-outline-secondary"
                                                                                            data-dismiss="modal">
                                                                                        Abbrechen
                                                                                    </button>
                                                                                    <button type="submit" class="btn btn-primary">
                                                                                        Speichern
                                                                                    </button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                {{-- Delete Sub Modal --}}
                                                                <div class="modal fade text-left"
                                                                     id="modal-delete-sub-{{ $sub->id }}"
                                                                     tabindex="-1"
                                                                     role="dialog"
                                                                     aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h4 class="modal-title">Sub-Artikelgruppe löschen</h4>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Möchten Sie diesen Eintrag wirklich löschen?</p>
                                                                                <p>
                                                                                    <strong>{{ $sub->sub_article }}</strong><br>
                                                                                    ID: {{ $sub->id }}
                                                                                </p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button"
                                                                                        class="btn btn-outline-secondary"
                                                                                        data-dismiss="modal">
                                                                                    Abbrechen
                                                                                </button>
                                                                                <a href="{{ route('sub_article_group.destroy', $sub->id) }}"
                                                                                   class="btn btn-danger">
                                                                                    Ja, löschen
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            @empty
                                                                <tr>
                                                                    <td colspan="6" class="text-center text-muted">
                                                                        Noch keine Sub-Artikelgruppen vorhanden.
                                                                    </td>
                                                                </tr>
                                                            @endforelse

                                                            {{-- Inline Create Row --}}
                                                            <tr class="bg-white">
                                                                <td colspan="6">
                                                                    <form class="form-inline flex-wrap"
                                                                          method="POST"
                                                                          action="{{ route('sub_article_group.store') }}">
                                                                        @csrf
                                                                        <input type="hidden"
                                                                               name="article_group_id"
                                                                               value="{{ $group->id }}">

                                                                        <div class="form-group mb-50 mr-50">
                                                                            <label class="sr-only">Initial</label>
                                                                            <input type="text"
                                                                                   name="initial"
                                                                                   class="form-control form-control-sm"
                                                                                   placeholder="Initial">
                                                                        </div>
                                                                        <div class="form-group mb-50 mr-50">
                                                                            <label class="sr-only">Sub-Artikel</label>
                                                                            <input type="text"
                                                                                   name="sub_article"
                                                                                   class="form-control form-control-sm"
                                                                                   placeholder="Sub-Artikel*"
                                                                                   required>
                                                                        </div>
                                                                        <div class="form-group mb-50 mr-50">
                                                                            <label class="sr-only">Wert</label>
                                                                            <input type="text"
                                                                                   name="value"
                                                                                   class="form-control form-control-sm"
                                                                                   placeholder="Wert">
                                                                        </div>
                                                                        <div class="form-group mb-50 mr-50">
                                                                            <label class="sr-only">Status</label>
                                                                            <input type="text"
                                                                                   name="status"
                                                                                   class="form-control form-control-sm"
                                                                                   placeholder="Status">
                                                                        </div>

                                                                        <button type="submit"
                                                                                class="btn btn-sm btn-primary mb-50">
                                                                            <i class="feather icon-plus mr-25"></i>
                                                                            Sub-Artikel hinzufügen
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            </tr>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        {{-- Edit Group Modal --}}
                                        <div class="modal fade text-left"
                                             id="modal-edit-article-group-{{ $group->id }}"
                                             tabindex="-1"
                                             role="dialog"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Artikel-Gruppe bearbeiten</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form method="POST"
                                                          action="{{ route('article_group.update') }}"
                                                          enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" value="{{ $group->id }}">

                                                            <div class="form-row">
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label>Initial</label>
                                                                        <input type="text"
                                                                               name="initial"
                                                                               class="form-control"
                                                                               value="{{ old('initial', $group->initial) }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <div class="form-group">
                                                                        <label>Artikel-Gruppe <span class="text-danger">*</span></label>
                                                                        <input type="text"
                                                                               name="article_group"
                                                                               class="form-control"
                                                                               value="{{ old('article_group', $group->article_group) }}"
                                                                               required>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Min-Wert</label>
                                                                        <input type="number"
                                                                               step="0.01"
                                                                               name="min_value"
                                                                               class="form-control"
                                                                               value="{{ old('min_value', $group->min_value) }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Max-Wert</label>
                                                                        <input type="number"
                                                                               step="0.01"
                                                                               name="max_value"
                                                                               class="form-control"
                                                                               value="{{ old('max_value', $group->max_value) }}">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Foto</label>
                                                                <input type="file"
                                                                       name="image"
                                                                       class="form-control">
                                                                @if($group->image)
                                                                    <small class="d-block mt-50 text-muted">
                                                                        Aktuelles Bild: {{ $group->image }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button"
                                                                    class="btn btn-outline-secondary"
                                                                    data-dismiss="modal">
                                                                Abbrechen
                                                            </button>
                                                            <button type="submit" class="btn btn-primary">
                                                                Speichern
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Delete Group Modal --}}
                                        <div class="modal fade text-left"
                                             id="modal-delete-article-group-{{ $group->id }}"
                                             tabindex="-1"
                                             role="dialog"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Artikel-Gruppe löschen</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Möchten Sie diese Artikel-Gruppe wirklich löschen?</p>
                                                        <p>
                                                            <strong>{{ $group->article_group }}</strong><br>
                                                            ID: {{ $group->id }}
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button"
                                                                class="btn btn-outline-secondary"
                                                                data-dismiss="modal">
                                                            Abbrechen
                                                        </button>
                                                        <a href="{{ route('article_group.destroy', $group->id) }}"
                                                           class="btn btn-danger">
                                                            Ja, löschen
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                Keine Artikel-Gruppen gefunden.
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-1">
                                {{ $data->appends(request()->query())->links() }}
                            </div>

                        </div>
                    </div>
                </div>
            </div> <!-- row -->
        </div> <!-- content-body -->
    </div> <!-- content-wrapper -->
</div>

{{-- Create Group Modal --}}
<div class="modal fade text-left"
     id="modal-create-article-group"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Neue Artikel-Gruppe</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST"
                  action="{{ route('article_group.store') }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Initial</label>
                                <input type="text"
                                       name="initial"
                                       class="form-control"
                                       value="{{ old('initial') }}">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Artikel-Gruppe <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="article_group"
                                       class="form-control"
                                       value="{{ old('article_group') }}"
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Min-Wert</label>
                                <input type="number"
                                       step="0.01"
                                       name="min_value"
                                       class="form-control"
                                       value="{{ old('min_value') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Max-Wert</label>
                                <input type="number"
                                       step="0.01"
                                       name="max_value"
                                       class="form-control"
                                       value="{{ old('max_value') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Foto</label>
                        <input type="file"
                               name="image"
                               class="form-control">
                        <small class="text-muted d-block mt-50">
                            Optional – z.B. Icon oder Produktbild.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-outline-secondary"
                            data-dismiss="modal">
                        Abbrechen
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(function () {
        @if(Session::has('update_msg'))
        toastr.success(@json(session('update_msg')));
        @endif

        @if(Session::has('save_msg'))
        toastr.success(@json(session('save_msg')));
        @endif

        @if(Session::has('delete_msg'))
        toastr.error(@json(session('delete_msg')));
        @endif

        // Local search for sub-article tables
        $(document).on('input', '.js-sub-search', function () {
            const term = $(this).val().toLowerCase();
            const target = $(this).data('target');
            const $rows = $(target).find('tbody tr');

            $rows.each(function () {
                const text = $(this).text().toLowerCase();
                // keep the inline-create row always visible
                if ($(this).find('form').length) {
                    $(this).show();
                } else {
                    $(this).toggle(text.indexOf(term) !== -1);
                }
            });
        });
    });
</script>
@endsection
