@extends('admin.layouts.app')

@section('title', 'Angebot Ordner')
@section('style')
<style>
    .status-menu {
        background: white;
        padding: 0.25rem 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .status-menu .dropdown-item {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 0.35rem 0.75rem;
        font-size: 0.875rem;
        color: #374151;
        background: none;
        border: none;
        text-align: left;
    }

    .status-menu .dropdown-item:hover {
        background-color: #f1f5f9;
    }

    .status-menu .text-danger {
        color: #e3342f !important;
    }
</style>

@endsection
@section('content')
@php use Illuminate\Support\Str; @endphp
 

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row"></div>

        <div class="content-body">
            <div class="container py-4">

                {{-- Flash --}}
                @if(session('ok'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('ok') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Schließen">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Header --}}
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h1 class="h4 mb-1">Angebot-Ordner</h1>
                        <div class="text-muted small">
                            Angebot #{{ $offer->id }} · Produkt: {{ $offer->product_id }} · Kunde: {{ $offer->customer_id }} · Alternative: {{ $offer->alternative_id }}
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <form method="GET" action="{{ route('admin.offers.folders.index', $offer) }}" class="mr-2">
                            <div class="input-group input-group-sm">
                                <input name="q" value="{{ $q }}" class="form-control" placeholder="Suchen…">
                                <div class="input-group-append">
                                    <button class="btn btn-dark" type="submit">
                                        <i data-feather="search"></i>
                                        <span class="ml-1">Suchen</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#createFolderModal">
                            <i data-feather="plus"></i>
                            <span class="ml-1">Neuer Ordner</span>
                        </button>
                    </div>
                </div>

                {{-- Folder grid --}}
                <div class="row">
                    @forelse($folders as $folder)
                        @php
                            $statusRaw   = strtolower($folder->status ?? 'draft');
                            $statusLabel = $statusRaw === 'final' ? 'Final'
                                           : (in_array($statusRaw, ['cancel','canceled','cancelled']) ? 'Storniert' : 'Draft');
                            $badgeClass  = $statusRaw === 'final' ? 'success'
                                           : (in_array($statusRaw, ['cancel','canceled','cancelled']) ? 'danger' : 'secondary');
                        @endphp
                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                            <div class="card shadow-sm h-100">
                                {{-- Color Stripe --}}
                                <div style="height:4px; width:100%; background: {{ $folder->color }};"></div>

                                <div class="card-body pb-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="text-truncate">
                                            <div class="font-weight-semibold text-truncate">{{ $folder->name ?? 'Unbenannter Ordner' }}</div>
                                            <div class="badge badge-{{ $folder->status_badge_class ?? 'secondary' }}">
                                                {{ $folder->status_label ?? 'Entwurf' }}
                                            </div>
                                        </div>

                                        {{-- Status menu (3-dots) --}} 
                                        <div class="position-relative d-inline-block">
                                            <button class="btn btn-link btn-sm p-0 text-muted toggle-status-menu" title="Status ändern" data-folder="{{ $folder->id }}">
                                                <i data-feather="more-vertical"></i>
                                            </button>

                                            {{-- Menu container (initially hidden) --}}
                                            <div id="status-menu-{{ $folder->id }}" class="status-menu border rounded bg-white shadow-sm" style="display:none; position:absolute; right:0; z-index:10; min-width:160px;">
                                                <form method="POST" action="{{ route('admin.offers.folders.status', [$offer, $folder]) }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="draft">
                                                    <button type="submit" class="dropdown-item d-flex align-items-center">
                                                        <i data-feather="file-text" class="mr-2"></i> Entwurf
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.offers.folders.status', [$offer, $folder]) }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="final">
                                                    <button type="submit" class="dropdown-item d-flex align-items-center">
                                                        <i data-feather="check-circle" class="mr-2"></i> Final
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.offers.folders.status', [$offer, $folder]) }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="cancel">
                                                    <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                                                        <i data-feather="x-circle" class="mr-2"></i> Storniert
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- Meta --}}
                                    <div class="small text-muted mt-2">
                                        <div><span class="text-secondary">Kunde:</span> <span class="font-weight-medium text-body">{{ optional($folder->customer)->display_name ?? '—' }}</span></div>
                                        <div><span class="text-secondary">Produktgruppe:</span> <span class="font-weight-medium text-body">{{ optional($folder->product)->display_name ?? '—' }}</span></div>
                                        <div><span class="text-secondary">Alternative:</span> <span class="font-weight-medium text-body">{{ optional($folder->alternative)->display_name ?? '—' }}</span></div>
                                    </div>

                                    @if($folder->history)
                                        <div class="small text-muted mt-2">
                                            {{ Str::limit(strip_tags($folder->history), 140) }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Footer --}}
                                <div class="card-footer bg-white d-flex justify-content-between align-items-center px-3 py-2">
                                    <div class="small text-muted">
                                        By: <span class="font-weight-medium text-body">{{ optional($folder->creator)->display_name ?? '—' }}</span>
                                    </div>

                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- OPEN --}}
                                        <a href="{{url('offer/wp/config/'.$folder->offer_id.'/'.$folder->id)}}"
                                        class="btn btn-outline-primary btn-sm" title="Öffnen">
                                            <i data-feather="folder"></i>
                                        </a>

                                        {{-- EDIT --}}
                                        <button class="btn btn-outline-secondary btn-sm"
                                                data-toggle="modal"
                                                data-target="#editFolderModal-{{ $folder->id }}"
                                                title="Bearbeiten">
                                            <i data-feather="edit-2"></i>
                                        </button>

                                        {{-- DELETE --}}
                                        <form method="POST" action="{{ route('admin.offers.folders.destroy', [$offer, $folder]) }}"
                                            onsubmit="return confirm('Diesen Ordner wirklich löschen?')" class="mb-0 d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm" title="Löschen">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
 
                        {{-- Edit Modal (per item) --}}
                        <div class="modal fade" id="editFolderModal-{{ $folder->id }}" tabindex="-1" role="dialog" aria-labelledby="editFolderLabel-{{ $folder->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editFolderLabel-{{ $folder->id }}">Ordner bearbeiten</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.offers.folders.update', [$offer, $folder]) }}">
                                        @csrf @method('PATCH')
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="name-{{ $folder->id }}">Name</label>
                                                <input id="name-{{ $folder->id }}" name="name" value="{{ old('name', $folder->name) }}" class="form-control">
                                            </div>

                                            {{-- STATUS --}}
                                            <div class="form-group">
                                                <label for="status-{{ $folder->id }}">Status</label>
                                                <select id="status-{{ $folder->id }}" name="status" class="form-control">
                                                    @php $s = strtolower(old('status', $folder->status ?? 'draft')); @endphp
                                                    <option value="draft"   {{ $s === 'draft' ? 'selected' : '' }}>Draft</option>
                                                    <option value="final"   {{ $s === 'final' ? 'selected' : '' }}>Final</option>
                                                    <option value="cancel"  {{ in_array($s, ['cancel','canceled','cancelled']) ? 'selected' : '' }}>Canceled</option>
                                                </select>
                                            </div>

                                            {{-- COLOR PICKER + PALETTE --}}
                                            <div class="form-group">
                                                <label for="color-{{ $folder->id }}">Farbe</label>
                                                <div class="input-group">
                                                    <input
                                                        id="color-{{ $folder->id }}"
                                                        name="color"
                                                        type="color"
                                                        value="{{ old('color', $folder->color ?? '#8fc73e') }}"
                                                        class="form-control p-1"
                                                        style="height: 38px;"
                                                    >
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Palette
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right px-2 py-2" style="min-width: 220px;">
                                                            @php $palette = ['#8fc73e','#2b6cb0','#3182ce','#805ad5','#d53f8c','#dd6b20','#e53e3e','#319795','#1a202c','#718096']; @endphp
                                                            <div class="d-flex flex-wrap">
                                                                @foreach($palette as $hex)
                                                                    <button type="button" class="btn border m-1 color-swatch" style="width:24px;height:24px;background:{{ $hex }};" data-target="#color-{{ $folder->id }}" data-color="{{ $hex }}"></button>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                            <label for="status-{{ $folder->id }}">Status</label>
                                            @php $s = strtolower(old('status', $folder->status ?? 'draft')); @endphp
                                            <select id="status-{{ $folder->id }}" name="status" class="form-control">
                                                <option value="draft"  {{ $s==='draft' ? 'selected' : '' }}>Entwurf</option>
                                                <option value="final"  {{ $s==='final' ? 'selected' : '' }}>Final</option>
                                                <option value="cancel" {{ in_array($s,['cancel','canceled','cancelled']) ? 'selected' : '' }}>Storniert</option>
                                            </select>
                                            </div>


                                            <div class="form-group">
                                                <label for="history-{{ $folder->id }}">Historie (optional)</label>
                                                <textarea id="history-{{ $folder->id }}" name="history" rows="4" class="form-control">{{ old('history', $folder->history) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Abbrechen</button>
                                            <button class="btn btn-primary">Speichern</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card border text-center p-5">
                                <div class="text-muted">Keine Ordner gefunden. Erstellen Sie den ersten Ordner.</div>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $folders->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createFolderModal" tabindex="-1" role="dialog" aria-labelledby="createFolderLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createFolderLabel">Neuen Ordner erstellen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.offers.folders.store', $offer) }}">
                @csrf
                <input type="hidden" name="customer_id" value="{{ $offer->customer_id }}">
                <input type="hidden" name="alternative_id" value="{{ $offer->alternative_id }}">
                <input type="hidden" name="product_id" value="{{ $offer->product_id }}">

                <div class="modal-body">
                    <div class="form-group">
                        <label for="create-name">Name</label>
                        <input id="create-name" name="name" class="form-control" placeholder="z. B. WP-Angebot Nordseite">
                    </div>

                    {{-- STATUS --}}
                    <div class="form-group">
                        <label for="create-status">Status</label>
                        <select id="create-status" name="status" class="form-control">
                            <option value="draft" selected>Draft</option>
                            <option value="final">Final</option>
                            <option value="cancel">Canceled</option>
                        </select>
                    </div>

                    {{-- COLOR PICKER + PALETTE --}}
                    <div class="form-group">
                        <label for="create-color">Farbe</label>
                        <div class="input-group">
                            <input
                                id="create-color"
                                name="color"
                                type="color"
                                value="#8fc73e"
                                class="form-control p-1"
                                style="height: 38px;"
                            >
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Palette
                                </button>
                                <div class="dropdown-menu dropdown-menu-right px-2 py-2" style="min-width: 220px;">
                                    @php $palette = ['#8fc73e','#2b6cb0','#3182ce','#805ad5','#d53f8c','#dd6b20','#e53e3e','#319795','#1a202c','#718096']; @endphp
                                    <div class="d-flex flex-wrap">
                                        @foreach($palette as $hex)
                                            <button type="button" class="btn border m-1 color-swatch" style="width:24px;height:24px;background:{{ $hex }};" data-target="#create-color" data-color="{{ $hex }}"></button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                    <label for="create-status">Status</label>
                    <select id="create-status" name="status" class="form-control">
                        <option value="draft" selected>Entwurf</option>
                        <option value="final">Final</option>
                        <option value="cancel">Storniert</option>
                    </select>
                    </div>


                    <div class="form-group">
                        <label for="create-history">Historie (optional)</label>
                        <textarea id="create-history" name="history" rows="4" class="form-control" placeholder="Initial erstellt …"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Abbrechen</button>
                    <button class="btn btn-success">Erstellen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- jQuery + Popper + Bootstrap 4 JS --}}
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" crossorigin="anonymous"></script>
 
    {{-- Feather Icons --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
        <script>
        $(function () {
            feather.replace();

            $(document).on('click', '.color-swatch', function () {
            var hex = $(this).data('color');
            var target = $(this).data('target');
            $(target).val(hex).trigger('change');
            });
        });
        </script>



<script>
$(function () {
    feather.replace();

    // Toggle custom status menus
    $('.toggle-status-menu').on('click', function (e) {
        e.preventDefault();
        const id = $(this).data('folder');
        $('.status-menu').not('#status-menu-' + id).hide(); // hide others
        $('#status-menu-' + id).toggle();
    });

    // Close on outside click
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.status-menu, .toggle-status-menu').length) {
            $('.status-menu').hide();
        }
    });
});
</script>

@endpush
