<div id="roof-wrapper">
    @foreach ($roofs as $index => $roof)
        @include('admin.new_leads.partials.edit.roof-fields', ['index' => $index, 'roof' => $roof])
    @endforeach
</div>

<button type="button" class="btn btn-sm btn-primary mt-2" onclick="addNewRoofEdit()">+ Neue Dachfläche hinzufügen</button>
