@extends('admin.layouts.app')

@section('title', 'Lieferanten-Schnittstelle bearbeiten')

@section('content')
    <div class="sc-page">
        @include('admin.supplier-connectors._edit_styles')

        @if(session('success'))
            <div class="sc-toast sc-toast-success" data-sc-toast>{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="sc-toast sc-toast-error" data-sc-toast>{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="sc-toast sc-toast-error" data-sc-toast>
                Bitte prüfe die Eingaben. Einige Felder sind nicht korrekt ausgefüllt.
            </div>
        @endif

        <div class="sc-topbar">
            <div>
                <h1 class="sc-title">{{ $connection->name }}</h1>
                <div class="sc-subtitle">
                    {{ strtoupper($connection->connector_type) }} · {{ $connection->supplier_key }}
                    @if($connection->distributor)
                        · Lieferant: {{ $connection->distributor->name ?? $connection->distributor->short_name }}
                    @endif
                </div>
            </div>

            <div class="sc-top-actions">
                <a href="{{ route('admin.supplier-connectors.index') }}" class="sc-btn sc-btn-soft">
                    <i data-lucide="arrow-left"></i>
                    Zurück
                </a>

                <form method="POST" action="{{ route('admin.supplier-connectors.test', $connection) }}">
                    @csrf
                    <button type="submit" class="sc-btn sc-btn-primary">
                        <i data-lucide="activity"></i>
                        Verbindung testen
                    </button>
                </form>

                @if(in_array($connection->connector_type, ['ids', 'oci']))
                    <a href="{{ route('admin.supplier-connectors.open', $connection) }}" class="sc-btn sc-btn-green">
                        <i data-lucide="search"></i>
                        Suche öffnen
                    </a>
                @endif
            </div>
        </div>

        <div class="sc-status-grid">
            <div class="sc-status-card">
                <div class="sc-status-label">Status</div>
                <div class="sc-status-value">
                    @if($connection->is_active)
                        <span class="sc-badge sc-badge-green">Aktiv</span>
                    @else
                        <span class="sc-badge sc-badge-gray">Inaktiv</span>
                    @endif
                </div>
                <div class="sc-status-help">Nur aktive Verbindungen können geöffnet werden.</div>
            </div>

            <div class="sc-status-card">
                <div class="sc-status-label">Letzter Test</div>
                <div class="sc-status-value">
                    @if($connection->last_test_status === 'success')
                        <span class="sc-badge sc-badge-green">Erfolgreich</span>
                    @elseif($connection->last_test_status === 'failed')
                        <span class="sc-badge sc-badge-red">Fehlgeschlagen</span>
                    @else
                        <span class="sc-badge sc-badge-gray">Nicht getestet</span>
                    @endif
                </div>
                <div class="sc-status-help">
                    {{ $connection->last_tested_at ? $connection->last_tested_at->format('d.m.Y H:i') : 'Noch kein Test durchgeführt.' }}
                </div>
            </div>

            <div class="sc-status-card">
                <div class="sc-status-label">Mappings</div>
                <div class="sc-status-value">{{ $connection->mappings->count() }}</div>
                <div class="sc-status-help">Feld-Zuordnung vom Shop zu Laravel.</div>
            </div>

            <div class="sc-status-card">
                <div class="sc-status-label">Import Logs</div>
                <div class="sc-status-value">{{ $connection->logs->count() }}</div>
                <div class="sc-status-help">Letzte Rückgaben und Import-Versuche.</div>
            </div>
        </div>

        @if($connection->last_test_message)
            <div class="{{ $connection->last_test_status === 'success' ? 'map-info' : '' }}"
                style="{{ $connection->last_test_status === 'success'
                ? ''
                : 'background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;border-radius:16px;padding:12px 14px;font-size:13px;font-weight:800;line-height:1.5;margin-bottom:16px;' }}">
                Letzter Test: {{ $connection->last_test_message }}
            </div>
        @endif

        <div class="map-card">
            <div class="map-card-header">
                <div>
                    <h2 class="map-card-title">Vorlagen</h2>
                    <div class="map-card-desc">
                        Wenn ein Lieferant andere Parameter verlangt, kannst du hier eine Vorlage anwenden.
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <form method="POST" action="{{ route('admin.supplier-connectors.apply-preset', $connection) }}">
                    @csrf
                    <input type="hidden" name="preset" value="gc_online">
                    <button type="submit" class="map-btn map-btn-green">GC Online Vorlage</button>
                </form>

                <form method="POST" action="{{ route('admin.supplier-connectors.apply-preset', $connection) }}">
                    @csrf
                    <input type="hidden" name="preset" value="standard_ids">
                    <button type="submit" class="map-btn map-btn-primary">Standard IDS</button>
                </form>

                <form method="POST" action="{{ route('admin.supplier-connectors.apply-preset', $connection) }}">
                    @csrf
                    <input type="hidden" name="preset" value="standard_oci">
                    <button type="submit" class="map-btn map-btn-primary">Standard OCI</button>
                </form>

                <form method="POST" action="{{ route('admin.supplier-connectors.apply-preset', $connection) }}">
                    @csrf
                    <input type="hidden" name="preset" value="empty_custom">
                    <button type="submit" class="map-btn map-btn-soft">Leere Vorlage</button>
                </form>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.supplier-connectors.update', $connection) }}">
            @csrf
            @method('PUT')
            @include('admin.supplier-connectors._form')
        </form>

        @include('admin.supplier-connectors._mappings')
        @include('admin.supplier-connectors._logs')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-sc-toast]').forEach(function (toast) {
                setTimeout(function () {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-8px) scale(.98)';
                    toast.style.transition = 'all .25s ease';

                    setTimeout(function () {
                        toast.remove();
                    }, 300);
                }, 4500);
            });

            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
@endsection