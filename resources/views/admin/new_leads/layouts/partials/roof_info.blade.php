<form class="partial-form" data-section="roof_info" data-id="{{ $alternative->id }}">
    @csrf
    <div id="roof-wrapper">
        @foreach ($roofs as $index => $roof)
            @include('admin.new_leads.layouts.partials.roof-fields', ['index' => $index, 'roof' => $roof])
        @endforeach
    </div>

    <button type="button" class="btn btn-sm btn-primary mt-2" onclick="addNewRoofEditProfile()">+ Neue Dachfläche hinzufügen</button>


<div class="mt-3 text-end">
        <button type="submit" class="btn btn-success">Speichern</button>
    </div>
</form>
