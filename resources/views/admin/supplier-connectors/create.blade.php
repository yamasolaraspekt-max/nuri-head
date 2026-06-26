@extends('admin.layouts.app')

@section('title', 'Neue Lieferanten-Schnittstelle')

@section('content')
    <div class="sc-page">
        @if(session('success'))
            <div class="sc-toast sc-toast-success" data-sc-toast>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="sc-toast sc-toast-error" data-sc-toast>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="sc-toast sc-toast-error" data-sc-toast>
                Bitte prüfe die Eingaben. Einige Felder sind nicht korrekt ausgefüllt.
            </div>
        @endif

        <div class="sc-create-header">
            <div>
                <h1 class="sc-create-title">Neue Lieferanten-Schnittstelle</h1>
                <div class="sc-create-subtitle">
                    Erstelle eine Verbindung zu einem Lieferanten-Shop wie GC Online, Sonepar, FEGA & Schmitt oder Buderus.
                    Die Zugangsdaten werden in der Datenbank gespeichert und können später getestet werden.
                </div>
            </div>

            <a href="{{ route('admin.supplier-connectors.index') }}" class="sc-btn sc-btn-soft">
                Zurück zur Liste
            </a>
        </div>

        <div class="sc-helper-card">
            <div class="sc-helper-main">
                <div class="sc-helper-icon">
                    <i data-lucide="info"></i>
                </div>

                <div>
                    <strong>Beispiel: GC Online Plus</strong>
                    <p>
                        Für GC Online kannst du die Daten so eintragen:
                        Shop-Adresse <code>https://gconlineplus.de/ids.aspx</code>,
                        Kundennummer = <code>IDS_KNDNR</code>,
                        Benutzername = <code>IDS_USERNAME</code>,
                        Passwort = <code>IDS_PASSWORD</code>.
                    </p>
                </div>
            </div>

            <button type="button" class="sc-btn sc-btn-green" onclick="fillGcOnlineExample()">
                GC Online Beispiel einfügen
            </button>
        </div>

        <form method="POST" action="{{ route('admin.supplier-connectors.store') }}">
            @csrf
            @include('admin.supplier-connectors._form')
        </form>
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

        function fillGcOnlineExample() {
            const values = {
                name: 'GC Online Plus',
                supplier_key: 'gc_online',
                connector_type: 'ids',
                auth_type: 'basic',
                endpoint_url: 'https://gconlineplus.de/ids.aspx',
                test_url: 'https://gconlineplus.de/ids.aspx',
                customer_number: '017896',
                username: '160160017896',
                request_method: 'GET',
                request_content_type: '',
                timeout: '20',
                match_by: 'ean',
                price_mode: 'purchase_price',
            };

            Object.keys(values).forEach(function (name) {
                const field = document.querySelector('[name="' + name + '"]');

                if (!field) {
                    return;
                }

                field.value = values[name];
            });

            const isActive = document.querySelector('[name="is_active"]');
            if (isActive) {
                isActive.checked = true;
            }

            const updateExisting = document.querySelector('[name="update_existing"]');
            if (updateExisting) {
                updateExisting.checked = true;
            }

            const createMissing = document.querySelector('[name="create_missing"]');
            if (createMissing) {
                createMissing.checked = true;
            }

            alert('GC Online Beispiel wurde eingefügt. Bitte Passwort manuell eintragen.');
        }
    </script>
@endsection