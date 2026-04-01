
                  
    <div class="row"> 
        <div class="kanban-container" id="kanban">
            @php
                $columns = [
                    'lead' => 'Lead', 
                    'Lead' => 'Lead', 
                    'offer' => 'Angebot',
                    'deal' => 'Auftrag',
                    'project' => 'Montage',
                    'completed' => 'Abschluss',  
                    'archive' => 'Archiv',  
                ];
            @endphp

            @foreach($columns as $id => $title)
                <div class="column" id="{{ $id }}" ondrop="drop(event)" ondragover="allowDrop(event)">
                    <h3>{{ $title }}</h3>
                    <div class="column-content"></div>
                </div>
            @endforeach
        </div> 
    </div> 