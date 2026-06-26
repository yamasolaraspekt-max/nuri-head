{{-- Replace the old "Filiale" filter block with this Unternehmen + branch address block --}}

<div class="col-md-6">
    <label for="branchFilter" class="form-label d-flex align-items-center">
        Unternehmen
        <span class="badge badge-secondary ml-2 d-none" id="countBranches">{{ count($branches ?? []) }}</span>
    </label>

    <select name="branch" id="branchFilter" class="form-control select2">
        <option value="">Alle Unternehmen</option>
        @foreach (($branches ?? []) as $b)
            <option value="{{ $b->id }}" data-color="{{ $b->color ?? '#93c21c' }}">
                {{ $b->branch }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-6">
    <label for="branchAddressFilter" class="form-label d-flex align-items-center">
        Unternehmensadresse
        <span class="badge badge-secondary ml-2 d-none" id="countBranchAddresses">{{ count($branchAddresses ?? []) }}</span>
    </label>

    <select name="branch_address" id="branchAddressFilter" class="form-control select2">
        <option value="">Alle Adressen</option>
        @foreach (($branchAddresses ?? []) as $address)
            @php
                $addressLabel = trim(($address->name ? $address->name . ' · ' : '') . ($address->full_address ?: trim(($address->street ?? '') . ', ' . ($address->postcode ?? '') . ' ' . ($address->city ?? ''))));
            @endphp
            <option value="{{ $address->id }}" data-branch-id="{{ $address->branch_id }}">
                {{ $addressLabel ?: ('Adresse #' . $address->id) }}
            </option>
        @endforeach
    </select>
</div>
