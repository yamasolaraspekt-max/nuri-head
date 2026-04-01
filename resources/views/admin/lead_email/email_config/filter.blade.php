@extends('admin.layouts.app')
@section('title') E-Mail Domain-Filter @stop
@section('style')
<style>
    .is-invalid {
    border-color: #dc3545;
}

</style>
@endsection
@section('content')
<div class="app-content content">
    <div class="content-wrapper">
          <div class="content-wrapper">
                <div class="content-header row">
                    <div class="content-header-left col-md-9 col-12 mb-2">
                        <div class="row breadcrumbs-top">
                            <div class="col-12">
                                <h2 class="content-header-title float-left mb-0">E-Mail-Filter</h2>
                                <div class="breadcrumb-wrapper col-12">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        <div class="content-body">

            <!-- Session Messages -->
            @if(session('save_msg'))
                <div class="alert alert-success">{{ session('save_msg') }}</div>
            @endif
            @if(session('delete_msg'))
                <div class="alert alert-danger">{{ session('delete_msg') }}</div>
            @endif

            <!-- Top Bar: Search + Add Button -->
            <div class="row mb-2">
                <div class="col-md-6">
                    <input type="text" class="form-control" id="domainSearch" placeholder="Suche nach Domain...">
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addDomainModal">+ Neue Domain</button>
                </div>
            </div>

            <!-- Domain Table -->
            <table class="table table-bordered table-hover" id="domainTable">
                <thead>
                    <tr>
                        <th>Domain</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filters as $filter)
                        <tr>
                            <td class="domain-value">{{ $filter->domain }}</td>
                            <td>
                                <form action="{{ route('lead.email.domain.filters.destroy', $filter->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Diesen Filter löschen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Löschen</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2">Keine Filter vorhanden.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Domain Modal -->
<div class="modal fade" id="addDomainModal" tabindex="-1" role="dialog" aria-labelledby="addDomainModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <form action="{{ route('lead.email.domain.filters.store') }}" method="POST" id="filterForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Neue Domains hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div id="domainFields">
                        <div class="form-group row">
                            <div class="col-md-8">
                                <input type="text" name="domains[]" class="form-control domain-input" placeholder="z.B. viessmann.com" required>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-success" id="addDomain">+ Weitere Domain</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Speichern</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
 <script>
// Declare domain regex only once
const domainRegex = /^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i;

// Add new domain input field
document.getElementById('addDomain').addEventListener('click', function () {
    const fieldHTML = `
        <div class="form-group row">
            <div class="col-md-8">
                <input type="text" name="domains[]" class="form-control domain-input" placeholder="z.B. nibe.com" required>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-danger remove-domain">Entfernen</button>
            </div>
        </div>`;
    document.getElementById('domainFields').insertAdjacentHTML('beforeend', fieldHTML);
});

// Remove a domain field
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-domain')) {
        e.target.closest('.form-group').remove();
    }
});

// Validate domains on form submission
document.getElementById('filterForm').addEventListener('submit', function (e) {
    let isValid = true;
    let firstInvalidInput = null;

    document.querySelectorAll('.domain-input').forEach(input => {
        const wrapper = input.closest('.col-md-8');
        wrapper.querySelectorAll('.text-danger').forEach(el => el.remove());

        input.value = input.value.trim().toLowerCase();

        if (!domainRegex.test(input.value)) {
            isValid = false;
            input.classList.add('is-invalid');

            const error = document.createElement('small');
            error.className = 'text-danger';
            error.textContent = 'Ungültige Domain. Beispiel: viessmann.com';
            wrapper.appendChild(error);

            if (!firstInvalidInput) firstInvalidInput = input;
        } else {
            input.classList.remove('is-invalid');
        }
    });

    if (!isValid) {
        e.preventDefault();
        firstInvalidInput?.focus();
    }
});

// Live validation while typing
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('domain-input')) {
        const input = e.target;
        const wrapper = input.closest('.col-md-8');

        input.value = input.value.toLowerCase();
        wrapper.querySelectorAll('.text-danger').forEach(el => el.remove());

        if (!domainRegex.test(input.value)) {
            input.classList.add('is-invalid');

            const error = document.createElement('small');
            error.className = 'text-danger';
            error.textContent = 'Ungültige Domain. Beispiel: viessmann.com';
            wrapper.appendChild(error);
        } else {
            input.classList.remove('is-invalid');
        }
    }
});

// Search/filter table rows by domain
document.getElementById('domainSearch').addEventListener('input', function () {
    const search = this.value.toLowerCase();
    document.querySelectorAll('#domainTable tbody tr').forEach(row => {
        const domain = row.querySelector('.domain-value').textContent.toLowerCase();
        row.style.display = domain.includes(search) ? '' : 'none';
    });
});
</script>

@endsection
