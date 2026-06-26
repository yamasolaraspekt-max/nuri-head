@extends('admin.layouts.app')

@section('title', 'Produktbilder CSV Import')

@section('content')
<div class="container-fluid" style="padding:120px 24px 40px;">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <div class="card border-0 shadow-sm" style="border-radius:22px; overflow:hidden;">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h3 class="mb-1" style="font-weight:900;">
                                Produktbilder per CSV importieren
                            </h3>
                            <p class="text-muted mb-0">
                                CSV hochladen, Produkt finden, Bild-URL herunterladen und in <strong>product_images</strong> speichern.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Fehler:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.products.images.csv-import.store') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="mb-4">
                        @csrf

                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-lg-7">
                                <label class="form-label fw-bold">
                                    CSV Datei
                                </label>
                                <input type="file"
                                       name="csv_file"
                                       accept=".csv,.txt"
                                       class="form-control"
                                       required>
                                <small class="text-muted d-block mt-1">
                                    Unterstützte Spalten: Artikelnummer, Produktname, Direkte_Bild_URL, Bild_URL_Klickbar
                                </small>
                            </div>

                            <div class="col-12 col-lg-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="replace_existing"
                                           value="1"
                                           id="replaceExisting">
                                    <label class="form-check-label" for="replaceExisting">
                                        Vorhandene Bilder ersetzen
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 col-lg-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    Import starten
                                </button>
                            </div>
                        </div>
                    </form>

                    @if (session('import_stats'))
                        @php
                            $stats = session('import_stats');
                            $results = session('import_results', []);
                        @endphp

                        <div class="row mb-4">
                            <div class="col-6 col-lg-2 mb-3">
                                <div class="p-3 rounded bg-light border">
                                    <div class="text-muted small">Gesamt</div>
                                    <div class="h4 mb-0">{{ $stats['total'] ?? 0 }}</div>
                                </div>
                            </div>

                            <div class="col-6 col-lg-2 mb-3">
                                <div class="p-3 rounded bg-success text-white">
                                    <div class="small">Neu</div>
                                    <div class="h4 mb-0">{{ $stats['created'] ?? 0 }}</div>
                                </div>
                            </div>

                            <div class="col-6 col-lg-2 mb-3">
                                <div class="p-3 rounded bg-info text-white">
                                    <div class="small">Ersetzt</div>
                                    <div class="h4 mb-0">{{ $stats['updated'] ?? 0 }}</div>
                                </div>
                            </div>

                            <div class="col-6 col-lg-2 mb-3">
                                <div class="p-3 rounded bg-warning text-dark">
                                    <div class="small">Übersprungen</div>
                                    <div class="h4 mb-0">{{ $stats['skipped'] ?? 0 }}</div>
                                </div>
                            </div>

                            <div class="col-6 col-lg-2 mb-3">
                                <div class="p-3 rounded bg-danger text-white">
                                    <div class="small">Fehlgeschlagen</div>
                                    <div class="h4 mb-0">{{ $stats['failed'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Zeile</th>
                                        <th>Status</th>
                                        <th>Artikelnummer</th>
                                        <th>Produkt</th>
                                        <th>Meldung</th>
                                        <th>Bild</th>
                                        <th>URL</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($results as $result)
                                        @php
                                            $status = $result['status'] ?? '';
                                            $badgeClass = match ($status) {
                                                'created' => 'bg-success',
                                                'updated' => 'bg-info',
                                                'skipped' => 'bg-warning text-dark',
                                                'failed' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };

                                            $image = $result['image'] ?? null;
                                        @endphp

                                        <tr>
                                            <td>{{ $result['line'] ?? '-' }}</td>

                                            <td>
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ strtoupper($status) }}
                                                </span>
                                            </td>

                                            <td>{{ $result['article_no'] ?? '-' }}</td>

                                            <td style="min-width:240px;">
                                                {{ $result['product'] ?? '-' }}
                                            </td>

                                            <td>{{ $result['message'] ?? '-' }}</td>

                                            <td>
                                                @if ($image)
                                                    <a href="{{ asset('images/products/' . $image) }}" target="_blank">
                                                        <img src="{{ asset('images/products/' . $image) }}"
                                                             alt=""
                                                             style="width:56px;height:56px;object-fit:contain;border-radius:10px;border:1px solid #e5e7eb;background:#fff;">
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td style="max-width:260px;">
                                                @if (!empty($result['url']))
                                                    <a href="{{ $result['url'] }}" target="_blank" class="text-break">
                                                        öffnen
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>
@endsection