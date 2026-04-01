@extends('admin.layouts.app')
@section('title') 📩 Gespeicherte E-Mails @stop

@section('content')
<div class="app-content content">
    <div class="content-wrapper">

        <!-- Header -->
        <div class="content-header mb-2 d-flex justify-content-between align-items-center">
            <h2 class="mb-0">📬 Gespeicherte E-Mails</h2>
            <div class="btn-group">
                <a href="{{ route('lead.email.fetch') }}" class="btn btn-outline-primary btn-sm">
                    🔄 Jetzt abrufen
                </a>
                <a href="{{ route('lead.email.export.csv') }}" class="btn btn-outline-success btn-sm">
                    📄 CSV Exportieren
                </a>
                <a href="{{ route('lead.email.export.pdf') }}" class="btn btn-outline-danger btn-sm">
                    📄 PDF Exportieren
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('save_msg'))
            <div class="alert alert-success">{{ session('save_msg') }}</div>
        @endif
        @if(session('delete_msg'))
            <div class="alert alert-danger">{{ session('delete_msg') }}</div>
        @endif

        <!-- Filters -->
        <form method="GET" class="mb-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="🔍 Suche nach Absender, Betreff oder Inhalt" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="domain" class="form-control">
                        <option value="">🌐 Domain wählen</option>
                        @foreach($availableDomains as $domain)
                            <option value="{{ $domain }}" {{ request('domain') == $domain ? 'selected' : '' }}>
                                {{ $domain }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">Filtern</button>
                </div>
            </div>
        </form>

        <!-- Emails Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th>📧 Absender</th>
                        <th>📝 Betreff</th>
                        <th>🌍 Domain</th>
                        <th>📅 Datum</th>
                        <th>🔍 Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emails as $email)
                        <tr @if(!$email->is_read) style="font-weight: bold;" @endif>
                            <td>{{ $email->from }}</td>
                            <td>{{ \Str::limit($email->subject, 60, '...') }}</td>
                            <td>{{ $email->domain }}</td>
                            <td>{{ optional($email->date)->format('d.m.Y H:i') }}</td>
                            <td>
                                <button class="btn btn-sm btn-info view-email" data-id="{{ $email->id }}">
                                    📖 Anzeigen
                                </button>
                                <button class="btn btn-sm btn-success ai-verify" data-id="{{ $email->id }}">
                                    🤖 AI Verification
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Keine E-Mails gefunden.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- AI Verification Modal -->
        <div class="modal fade" id="aiModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
            <form id="aiSaveForm">
                <div class="modal-header">
                <h5 class="modal-title">🧠 AI Verifizierte Anfrage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="ai-form-content">
                        <div class="row">
                            <div class="col-md-6 mb-2"><label>Firma</label><input type="text" name="firma" class="form-control"></div>
                            <div class="col-md-6 mb-2"><label>Vorname</label><input type="text" name="name" class="form-control"></div>
                            <div class="col-md-6 mb-2"><label>Nachname</label><input type="text" name="lastname" class="form-control"></div>
                            <div class="col-md-6 mb-2"><label>Email</label><input type="email" name="email" class="form-control"></div>
                            <div class="col-md-6 mb-2"><label>Telefon</label><input type="text" name="telephone" class="form-control"></div>
                            <div class="col-md-6 mb-2"><label>Telefon (alt)</label><input type="text" name="phone" class="form-control"></div>
                            <div class="col-md-12 mb-2"><label>Straße</label><input type="text" name="street" class="form-control"></div>
                            <div class="col-md-12 mb-2"><label>Adresse</label><input type="text" name="full_address" class="form-control"></div>
                            <div class="col-md-6 mb-2"><label>Stadt</label><input type="text" name="city" class="form-control"></div>
                            <div class="col-md-6 mb-2"><label>PLZ</label><input type="text" name="postcode" class="form-control"></div>
                            <div class="col-md-4 mb-2"><label>Latitude</label><input type="text" name="latitude" class="form-control"></div>
                            <div class="col-md-4 mb-2"><label>Longitude</label><input type="text" name="longitude" class="form-control"></div>
                            <div class="col-md-4 mb-2"><label>Elevation</label><input type="text" name="elevation" class="form-control"></div>
                            <div class="col-md-6 mb-2"><label>Status</label><input type="text" name="status" class="form-control"></div>
                            <div class="col-md-6 mb-2"><label>Priorität</label><input type="text" name="periority" class="form-control"></div>
                            <div class="col-md-12 mb-2"><label>Nächster Schritt</label><input type="text" name="next_step" class="form-control"></div>
                            <div class="col-md-12 mb-2"><label>Notiz</label><textarea name="note" class="form-control"></textarea></div>
                        </div>
                    </div> 
                </div>
                <div class="modal-footer">
                <button type="submit" class="btn btn-primary">💾 Speichern</button>
                </div>
            </form>
            </div>
        </div>
        </div>


        <!-- Pagination -->
        <div class="mt-3">
            {{ $emails->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- Email Detail Modal -->
<div class="modal fade" id="emailDetailModal" tabindex="-1" aria-labelledby="emailDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📨 E-Mail Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>
            <div class="modal-body" id="email-detail-body">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).on('click', '.view-email', function () {
    const id = $(this).data('id');
    const modalBody = $('#email-detail-body');

    modalBody.html('<div class="spinner-border text-primary" role="status"></div>');

    $.get(`/admin/lead-email/show/${id}`, function (data) {
        modalBody.html(`
            <div class="mb-2"><strong>Von:</strong> ${data.from}</div>
            <div class="mb-2"><strong>Betreff:</strong> ${data.subject}</div>
            <div class="mb-2"><strong>Datum:</strong> ${data.date}</div>
            <div class="mb-2"><strong>Domain:</strong> ${data.domain}</div>
            <hr>
            <div>${data.body}</div>
        `);

        // Mark as read
        $.post(`/admin/lead-email/mark-read/${id}`, {_token: '{{ csrf_token() }}'});

    }).fail(function () {
        modalBody.html('<div class="text-danger">Fehler beim Laden der E-Mail.</div>');
    });

    $('#emailDetailModal').modal('show');
});
</script>

<script>
$(document).on('click', '.ai-verify', function () {
    const id = $(this).data('id');
    const form = $('#ai-form-content');
    
    // Initial loading state
    form.html('<div class="text-center p-4"><div class="spinner-border text-primary"></div><p class="mt-2">AI Analyse läuft...</p></div>');

    // Step 1: (optional if needed by backend) hit internal API
    $.get(`/lead/email/api/${id}`, function () {
        // Step 2: send to n8n AI parser
        $.get(`https://sadid2024.app.n8n.cloud/webhook-test/email-leads?id=${id}`, function (ai) {
            const fields = [
                'pre_type', 'source', 'title', 'type', 'type_extra', 'firma', 'lastname', 'name', 'street',
                'latitude', 'longitude', 'elevation', 'postcode', 'full_address', 'city', 'phone', 'telephone',
                'email', 'note', 'reason', 'status', 'periority', 'next_step'
            ];

            let html = '<div class="row">';
            fields.forEach(key => {
                const value = ai[key] ?? '';
                if (key === 'note') {
                    html += `<div class="col-md-12 mb-2"><label>${key}</label><textarea name="${key}" class="form-control">${value}</textarea></div>`;
                } else {
                    html += `<div class="col-md-6 mb-2"><label>${key}</label><input type="text" name="${key}" class="form-control" value="${value}"></div>`;
                }
            });
            html += '</div>';
            form.html(html);

            // ✅ Now open the modal once data is ready
            $('#aiModal').modal('show');
        }).fail(() => {
            form.html('<div class="alert alert-danger">❌ Fehler bei AI Analyse.</div>');
            $('#aiModal').modal('show'); // even show modal on error
        });
    });
});


$('#aiSaveForm').submit(function (e) {
    e.preventDefault();
    const data = $(this).serialize();

    $.post('/admin/inquiries/ai-save', data, function (res) {
        alert(res.message || '✅ Anfrage erfolgreich gespeichert!');
        $('#aiModal').modal('hide');
    }).fail(() => {
        alert('❌ Fehler beim Speichern.');
    });
});
</script>

@endpush
