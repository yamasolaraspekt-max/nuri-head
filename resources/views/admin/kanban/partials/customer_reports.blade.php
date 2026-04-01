<div class="cr-shell">
    <div class="cr-header">
        <div>
            <div class="cr-title-row">
                <span class="badge badge-light-primary mr-50">Kundenreport</span>
            </div>
            <div class="text-muted small">
                Reports zu diesem Kunden / dieser Alternative / diesem Produkt.
            </div>
        </div>

        <button type="button" class="btn btn-sm btn-primary cr-toggle-new">
            <i class="feather icon-plus mr-25"></i> Neuer Report
        </button>
    </div>

    <div class="cr-new-wrapper" hidden>
        <form class="cr-create-form">
            <input type="hidden" name="customer_id" value="{{ $customer_id }}">
            <input type="hidden" name="alternative_id" value="{{ $alternative_id }}">
            <input type="hidden" name="product_id" value="{{ $product_id }}">

            <div class="form-group mb-50">
                <label class="small text-muted mb-25">Phase / Stage</label>
                <select name="stage" class="form-control form-control-sm">
                    <option value="">– wählen –</option>
                    <option value="lead">Lead</option>
                    <option value="offer">Angebot</option>
                    <option value="deal">Auftrag</option>
                    <option value="project">Montage</option>
                    <option value="completed">Abschluss</option>
                </select>
            </div>

            <div class="form-group mb-50">
                <label class="small text-muted mb-25">Report</label>
                <textarea name="report"
                          rows="4"
                          class="form-control form-control-sm"
                          placeholder="Report schreiben…"></textarea>
            </div>

            <div class="d-flex justify-content-end gap-1">
                <button type="button" class="btn btn-sm btn-outline-secondary cr-cancel-new">
                    Abbrechen
                </button>
                <button type="submit" class="btn btn-sm btn-primary">
                    Speichern
                </button>
            </div>
        </form>
    </div>

    <div class="cr-list mt-75">
        @forelse($reports as $report)
            @include('admin.kanban.partials.customer_report_card', ['report' => $report])
        @empty
            <div class="text-muted small p-2">
                Noch keine Kundenreports vorhanden.
            </div>
        @endforelse
    </div>
</div>
