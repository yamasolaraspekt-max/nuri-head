@extends('admin.layouts.app')

@section('title') Branch @stop

@section('content')
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            {{-- ===== Header + Breadcrumb ===== --}}
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <h2 class="content-header-title float-left mb-0">Filialen</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ url('/employee_dashboard') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                Filialen
                            </li>
                        </ol>
                    </div>
                </div>

                <div class="content-header-right col-md-3 col-12">
                    <div class="float-md-right">
                        @if(DB::table('user_rolls')
                            ->where('user_rolls.user_id', '=', auth()->user()->name)
                            ->where('user_rolls.item_id', '=', 'Employee')
                            ->where('user_rolls.is_add', '=', 'on')
                            ->first())
                            {{-- Open Create Modal instead of separate page --}}
                            <button
                                type="button"
                                class="btn btn-primary btn-sm"
                                data-toggle="modal"
                                data-target="#create-branch-modal"
                            >
                                <i class="feather icon-plus mr-25"></i> Neue Filiale
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="content-body">
                <!-- Table Hover Animation start -->
                <section id="branch-table">
                    <div class="row" id="table-hover-animation">
                        <div class="col-12">
                            <div class="card">
                                {{-- Card header with title + search --}}
                                <div class="card-header border-bottom">
                                    <div class="w-100">
                                        <div class="row align-items-center">
                                            <div class="col-md-4 col-12 mb-1 mb-md-0">
                                                <h4 class="card-title mb-0">Branch-Übersicht</h4>
                                            </div>
                                            <div class="col-md-8 col-12">
                                                <form action="{{ route('branch.info') }}" method="GET">
                                                    <fieldset>
                                                        <div class="input-group">
                                                            <input
                                                                type="text"
                                                                name="search"
                                                                class="form-control"
                                                                placeholder="Nach Filiale oder Kürzel suchen…"
                                                                value="{{ request('search') }}"
                                                            >
                                                            @if(request('search'))
                                                                <div class="input-group-append">
                                                                    <a href="{{ route('branch.info') }}" class="btn btn-outline-secondary">
                                                                        Reset
                                                                    </a>
                                                                </div>
                                                            @endif
                                                            <div class="input-group-append" id="button-addon2">
                                                                <button class="btn btn-primary" type="submit">
                                                                    <i class="feather icon-search"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card content --}}
                                <div class="card-content">
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 70px;">#</th>
                                                        <th>Branch</th>
                                                        <th style="width: 140px;">Status</th>
                                                        <th style="width: 220px;" class="text-right">Aktion</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($data as $index => $branch)
                                                        <tr>
                                                            {{-- Correct index based on pagination --}}
                                                            <td>{{ $data->firstItem() + $index }}</td>

                                                            <td>
                                                                <a href="{{ route('branch.profile', $branch->id) }}" class="d-inline-flex align-items-center">
                                                                    <span
                                                                        class="mr-50"
                                                                        style="display:inline-block;width:10px;height:10px;border-radius:999px;background:{{ $branch->color ?? '#93c21c' }};"
                                                                        title="{{ $branch->color ?? '#93c21c' }}"
                                                                    ></span>
                                                                    {{ $branch->initial }} - {{ $branch->branch }}
                                                                </a>
                                                                @if($branch->street || $branch->postcode || $branch->city)
                                                                    <div class="text-muted small">
                                                                        {{ $branch->street }}{{ $branch->street ? ',' : '' }}
                                                                        {{ $branch->postcode }} {{ $branch->city }}
                                                                    </div>
                                                                @endif
                                                            </td>

                                                            <td>
                                                                @if($branch->status === 'published')
                                                                    <span class="badge badge-success">Aktiv</span>
                                                                @else
                                                                    <span class="badge badge-secondary">Inaktiv</span>
                                                                @endif
                                                            </td>

                                                            <td class="text-right">

                                                                {{-- Delete button (with permission) --}}
                                                                @if(DB::table('user_rolls')
                                                                    ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                                    ->where('user_rolls.item_id', '=', 'Employee')
                                                                    ->where('user_rolls.is_delete', '=', 'on')
                                                                    ->first())
                                                                    <button
                                                                        type="button"
                                                                        class="btn btn-icon rounded-circle btn-danger mr-25"
                                                                        data-toggle="modal"
                                                                        data-target="#delete-branch-{{ $branch->id }}"
                                                                    >
                                                                        <i class="feather icon-trash"></i>
                                                                    </button>
                                                                @endif

                                                                {{-- Edit button (with permission) --}}
                                                                @if(DB::table('user_rolls')
                                                                    ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                                    ->where('user_rolls.item_id', '=', 'Employee')
                                                                    ->where('user_rolls.is_update', '=', 'on')
                                                                    ->first())
                                                                    <button
                                                                        type="button"
                                                                        class="btn btn-icon rounded-circle btn-primary mr-25"
                                                                        data-toggle="modal"
                                                                        data-target="#edit-branch-{{ $branch->id }}"
                                                                    >
                                                                        <i class="feather icon-edit"></i>
                                                                    </button>
                                                                @endif

                                                                {{-- Publish / Unpublish --}}
                                                                <a
                                                                    href="{{ route('branch.profile.active', $branch->id) }}"
                                                                    class="btn btn-sm {{ $branch->status === 'published' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                                >
                                                                    {{ $branch->status === 'published' ? 'Unpublish' : 'Publish' }}
                                                                </a>

                                                                {{-- Delete Modal --}}
                                                                <div
                                                                    class="modal fade text-left"
                                                                    id="delete-branch-{{ $branch->id }}"
                                                                    tabindex="-1"
                                                                    role="dialog"
                                                                    aria-labelledby="deleteBranchLabel-{{ $branch->id }}"
                                                                    aria-hidden="true"
                                                                >
                                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h4 class="modal-title" id="deleteBranchLabel-{{ $branch->id }}">
                                                                                    Aufzeichnung löschen
                                                                                </h4>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                                <p>Die Datensatznummer lautet: <strong>{{ $branch->id }}</strong></p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                                                                    Abbrechen
                                                                                </button>
                                                                                <a
                                                                                    href="{{ route('branch.destroy', $branch->id) }}"
                                                                                    class="btn btn-primary"
                                                                                >
                                                                                    Ja, löschen
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                {{-- End Delete Modal --}}

                                                                {{-- Edit Modal (full fields) --}}
                                                                <div
                                                                    class="modal fade text-left"
                                                                    id="edit-branch-{{ $branch->id }}"
                                                                    tabindex="-1"
                                                                    role="dialog"
                                                                    aria-labelledby="editBranchLabel-{{ $branch->id }}"
                                                                    aria-hidden="true"
                                                                >
                                                                    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h4 class="modal-title" id="editBranchLabel-{{ $branch->id }}">Filiale bearbeiten</h4>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <form
                                                                                class="form-horizontal"
                                                                                novalidate
                                                                                method="POST"
                                                                                action="{{ route('branch.update') }}"
                                                                            >
                                                                                @csrf
                                                                                <input type="hidden" name="id" value="{{ $branch->id }}">
                                                                                <div class="modal-body">
                                                                                    <fieldset>
                                                                                        <div class="row">
                                                                                            {{-- Branch name --}}
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label for="edit-branch-name-{{ $branch->id }}">
                                                                                                        Filialname
                                                                                                    </label>
                                                                                                    <input
                                                                                                        type="text"
                                                                                                        id="edit-branch-name-{{ $branch->id }}"
                                                                                                        class="form-control"
                                                                                                        name="branch"
                                                                                                        value="{{ old('branch', $branch->branch) }}"
                                                                                                        required
                                                                                                    >
                                                                                                </div>
                                                                                            </div>

                                                                                            {{-- Initial --}}
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label for="edit-initial-{{ $branch->id }}">
                                                                                                        Anfänglich
                                                                                                    </label>
                                                                                                    <input
                                                                                                        type="text"
                                                                                                        id="edit-initial-{{ $branch->id }}"
                                                                                                        class="form-control"
                                                                                                        name="initial"
                                                                                                        value="{{ old('initial', $branch->initial) }}"
                                                                                                        required
                                                                                                    >
                                                                                                </div>
                                                                                            </div>

                                                                                            {{-- Color --}}
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label for="create-color">Farbe</label>
                                                                                                    <div class="d-flex align-items-center">
                                                                                                        <input
                                                                                                            type="color"
                                                                                                            id="create-color"
                                                                                                            name="color"
                                                                                                            value="{{ old('color', '#93c21c') }}"
                                                                                                            class="mr-1"
                                                                                                            style="width: 48px; height: 38px; padding: 0; border: 0; background: transparent;"
                                                                                                        >
                                                                                                        <input
                                                                                                            type="text"
                                                                                                            id="create-color-text"
                                                                                                            value="{{ old('color', '#93c21c') }}"
                                                                                                            class="form-control"
                                                                                                            style="max-width: 160px;"
                                                                                                            placeholder="#93c21c"
                                                                                                            oninput="document.getElementById('create-color').value = this.value"
                                                                                                        >
                                                                                                    </div>
                                                                                                    <small class="text-muted">Hex, z.B. #93c21c</small>
                                                                                                </div>
                                                                                            </div>


                                                                                            {{-- Street --}}
                                                                                            <div class="col-md-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="edit-street-{{ $branch->id }}">
                                                                                                        Straße / Nr.
                                                                                                    </label>
                                                                                                    <input
                                                                                                        type="text"
                                                                                                        id="edit-street-{{ $branch->id }}"
                                                                                                        class="form-control"
                                                                                                        name="street"
                                                                                                        value="{{ old('street', $branch->street) }}"
                                                                                                    >
                                                                                                </div>
                                                                                            </div>

                                                                                            {{-- Postcode + City --}}
                                                                                            <div class="col-md-4">
                                                                                                <div class="form-group">
                                                                                                    <label for="edit-postcode-{{ $branch->id }}">
                                                                                                        PLZ
                                                                                                    </label>
                                                                                                    <input
                                                                                                        type="text"
                                                                                                        id="edit-postcode-{{ $branch->id }}"
                                                                                                        class="form-control"
                                                                                                        name="postcode"
                                                                                                        value="{{ old('postcode', $branch->postcode) }}"
                                                                                                    >
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-8">
                                                                                                <div class="form-group">
                                                                                                    <label for="edit-city-{{ $branch->id }}">
                                                                                                        Ort
                                                                                                    </label>
                                                                                                    <input
                                                                                                        type="text"
                                                                                                        id="edit-city-{{ $branch->id }}"
                                                                                                        class="form-control"
                                                                                                        name="city"
                                                                                                        value="{{ old('city', $branch->city) }}"
                                                                                                    >
                                                                                                </div>
                                                                                            </div>

                                                                                            {{-- Country --}}
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label for="edit-country-{{ $branch->id }}">
                                                                                                        Land
                                                                                                    </label>
                                                                                                    <input
                                                                                                        type="text"
                                                                                                        id="edit-country-{{ $branch->id }}"
                                                                                                        class="form-control"
                                                                                                        name="country"
                                                                                                        value="{{ old('country', $branch->country) }}"
                                                                                                    >
                                                                                                </div>
                                                                                            </div>

                                                                                            {{-- Phone --}}
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label for="edit-phone-{{ $branch->id }}">
                                                                                                        Telefon
                                                                                                    </label>
                                                                                                    <input
                                                                                                        type="text"
                                                                                                        id="edit-phone-{{ $branch->id }}"
                                                                                                        class="form-control"
                                                                                                        name="phone"
                                                                                                        value="{{ old('phone', $branch->phone) }}"
                                                                                                    >
                                                                                                </div>
                                                                                            </div>

                                                                                            {{-- Email --}}
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label for="edit-email-{{ $branch->id }}">
                                                                                                        E-Mail
                                                                                                    </label>
                                                                                                    <input
                                                                                                        type="email"
                                                                                                        id="edit-email-{{ $branch->id }}"
                                                                                                        class="form-control"
                                                                                                        name="email"
                                                                                                        value="{{ old('email', $branch->email) }}"
                                                                                                    >
                                                                                                </div>
                                                                                            </div>

                                                                                            {{-- Chairman --}}
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label for="edit-chairman-{{ $branch->id }}">
                                                                                                        Kontaktperson
                                                                                                    </label>
                                                                                                    <select
                                                                                                        id="edit-chairman-{{ $branch->id }}"
                                                                                                        class="form-control"
                                                                                                        name="chairman"
                                                                                                    >
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        @foreach($employees as $emp)
                                                                                                            <option
                                                                                                                value="{{ $emp->id }}"
                                                                                                                @if(old('chairman', $branch->chairman) == $emp->id) selected @endif
                                                                                                            >
                                                                                                                {{ $emp->name }} {{ $emp->lastname }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
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
                                                                {{-- End Edit Modal --}}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted">
                                                                Keine Einträge gefunden.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Pagination --}}
                                    <div class="card-footer py-1">
                                        {{ $data->appends(request()->only('search'))->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ===== CREATE BRANCH MODAL ===== --}}
                <div
                    class="modal fade text-left"
                    id="create-branch-modal"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="createBranchLabel"
                    aria-hidden="true"
                >
                    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="createBranchLabel">Neue Filiale anlegen</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <form
                                class="form-horizontal"
                                method="POST"
                                action="{{ route('branch.store') }}"
                                enctype="multipart/form-data"
                            >
                                @csrf
                                <div class="modal-body">
                                    @if ($errors->any())
                                        {{-- Global error box (mainly for create validation) --}}
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <fieldset>
                                        <div class="row">
                                            {{-- Branch name --}}
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="create-branch-name">
                                                        Zweig name
                                                    </label>
                                                    <input
                                                        type="text"
                                                        id="create-branch-name"
                                                        class="form-control"
                                                        name="branch"
                                                        value="{{ old('branch') }}"
                                                    >
                                                </div>
                                            </div>

                                            {{-- Initial --}}
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="create-initial">
                                                        Anfänglich
                                                    </label>
                                                    <input
                                                        type="text"
                                                        id="create-initial"
                                                        class="form-control"
                                                        name="initial"
                                                        value="{{ old('initial') }}"
                                                    >
                                                </div>
                                            </div>


                                            {{-- Color --}}
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="edit-color-{{ $branch->id }}">Farbe</label>
                                                    <div class="d-flex align-items-center">
                                                        <input
                                                            type="color"
                                                            id="edit-color-{{ $branch->id }}"
                                                            name="color"
                                                            value="{{ old('color', $branch->color ?? '#93c21c') }}"
                                                            class="mr-1"
                                                            style="width: 48px; height: 38px; padding: 0; border: 0; background: transparent;"
                                                        >
                                                        <input
                                                            type="text"
                                                            id="edit-color-text-{{ $branch->id }}"
                                                            value="{{ old('color', $branch->color ?? '#93c21c') }}"
                                                            class="form-control"
                                                            style="max-width: 160px;"
                                                            placeholder="#93c21c"
                                                            oninput="document.getElementById('edit-color-{{ $branch->id }}').value = this.value"
                                                        >
                                                    </div>
                                                    <small class="text-muted">Hex, z.B. #93c21c</small>
                                                </div>
                                            </div>


                                            {{-- Street --}}
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="create-street">
                                                        Straße / Nr.
                                                    </label>
                                                    <input
                                                        type="text"
                                                        id="create-street"
                                                        class="form-control"
                                                        name="street"
                                                        value="{{ old('street') }}"
                                                    >
                                                </div>
                                            </div>

                                            {{-- Postcode + City --}}
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="create-postcode">
                                                        PLZ
                                                    </label>
                                                    <input
                                                        type="text"
                                                        id="create-postcode"
                                                        class="form-control"
                                                        name="postcode"
                                                        value="{{ old('postcode') }}"
                                                    >
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="create-city">
                                                        Ort
                                                    </label>
                                                    <input
                                                        type="text"
                                                        id="create-city"
                                                        class="form-control"
                                                        name="city"
                                                        value="{{ old('city') }}"
                                                    >
                                                </div>
                                            </div>

                                            {{-- Country --}}
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="create-country">
                                                        Land
                                                    </label>
                                                    <input
                                                        type="text"
                                                        id="create-country"
                                                        class="form-control"
                                                        name="country"
                                                        value="{{ old('country') }}"
                                                    >
                                                </div>
                                            </div>

                                            {{-- Phone --}}
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="create-phone">
                                                        Tel
                                                    </label>
                                                    <input
                                                        type="text"
                                                        id="create-phone"
                                                        class="form-control"
                                                        name="phone"
                                                        value="{{ old('phone') }}"
                                                    >
                                                </div>
                                            </div>

                                            {{-- Email --}}
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="create-email">
                                                        E-Mail
                                                    </label>
                                                    <input
                                                        type="email"
                                                        id="create-email"
                                                        class="form-control"
                                                        name="email"
                                                        value="{{ old('email') }}"
                                                    >
                                                </div>
                                            </div>

                                            {{-- Chairman --}}
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="create-chairman">
                                                        Kontaktperson
                                                    </label>
                                                    <select
                                                        id="create-chairman"
                                                        class="form-control"
                                                        name="chairman"
                                                    >
                                                        <option value="">Bitte wählen</option>
                                                        @foreach($employees as $emp)
                                                            <option
                                                                value="{{ $emp->id }}"
                                                                @if(old('chairman') == $emp->id) selected @endif
                                                            >
                                                                {{ $emp->name }} {{ $emp->lastname }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
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
                {{-- ===== END CREATE BRANCH MODAL ===== --}}
            </div>
        </div>
    </div>
    <!-- END: Content-->
@stop

@section('script')
<script>
    $(document).ready(function(){
        @if(session('update_msg'))
            toastr.success("{{ session('update_msg') }}");
        @endif

        @if(session('save_msg'))
            toastr.success("{{ session('save_msg') }}");
        @endif

        @if(session('delete_msg'))
            toastr.error("{{ session('delete_msg') }}");
        @endif
    });
</script>
@endsection
