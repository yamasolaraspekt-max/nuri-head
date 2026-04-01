<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            Arbeitsprozess – <span class="text-muted">Version:</span>
            <span id="selectedVersion">Version {{ $usedVersion }}</span>
        </h5>

        <form id="stageVersionForm" class="form-inline">
            @csrf
            <select name="version" id="versionSelect" class="form-control mr-2">
                @foreach(($groupedStages?->keys() ?? collect()) as $version)
                    <option value="{{ $version }}" {{ (string)$usedVersion === (string)$version ? 'selected' : '' }}>
                        Version {{ $version }}
                    </option>
                @endforeach
            </select>

            <input type="hidden" name="customer_id" value="{{ $customer_id }}">
            <input type="hidden" name="alternative_id" value="{{ $alternative_id }}">
            <input type="hidden" name="product_id" value="{{ $product_id }}">
            <input type="hidden" name="section_id" value="{{ $section_id }}">

            <button type="submit" id="saveVersionBtn" class="btn btn-success">
                Speichern
            </button>
        </form>
    </div>

    <div class="card-body" id="stageList">
        @forelse(($groupedStages[$usedVersion] ?? []) as $stage)
            <div class="border p-2 mb-2 {{ $stage->version === $usedVersion ? 'bg-light' : '' }}">
                <strong>{{ $stage->stage }}</strong>
                <span class="badge badge-secondary">Sort: {{ $stage->sort_order }}</span>
            </div>
        @empty
            <div class="text-muted">Keine Stufen für diese Version gefunden.</div>
        @endforelse
    </div>
</div>
