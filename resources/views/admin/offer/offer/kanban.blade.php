
  
  <div class="kanban-board">
    <div class="kanban-column" ondragover="event.preventDefault()" ondrop="drop(event)"><h5>Neue</h5> </div>
    <div class="kanban-column" ondragover="event.preventDefault()" ondrop="drop(event)"><h5>In Bearbeitung</h5></div>
    <div class="kanban-column" ondragover="event.preventDefault()" ondrop="drop(event)"><h5>Geprüft</h5></div> 
     <div class="kanban-column" ondragover="event.preventDefault()" ondrop="drop(event)"><h5>Verkauf</h5></div> 
    <div class="kanban-column" ondragover="event.preventDefault()" ondrop="drop(event)"><h5>Auftrag</h5></div>
    <div class="kanban-column" ondragover="event.preventDefault()" ondrop="drop(event)"><h5>Junk</h5></div>
  </div>
 
 

<!-- Panels -->
<div class="panel" id="detailPanel">
  <div class="panel-header">
    <h5>Angebot Details</h5>
    <button class="btn btn-sm btn-outline-secondary" onclick="closePanel('detailPanel')">&times;</button>
  </div>
  <div class="p-3">
    <p><strong>Kunde:</strong> Max Müller</p>
    <p><strong>Produkt:</strong> Photovoltaik</p>
    <p><strong>Preis:</strong> 18.500€</p>
  </div>
</div>


<div class="panel" id="commentPanel">
  <div class="panel-header d-flex justify-content-between align-items-center">
    <h5>Kommentare</h5>
    <button class="btn btn-sm btn-outline-secondary" onclick="closePanel('commentPanel')">&times;</button>
  </div>

  <div class="p-3">
    <select id="commentFilter" class="form-control form-control-sm w-auto mb-2">
        <option value="all">Alle</option>
        <option value="with-comments">Mit Kommentar</option>
        <option value="no-comments">Ohne Kommentar</option>
    </select>

    <form id="newCommentForm">
        <textarea class="form-control mb-2" placeholder="Kommentar schreiben..." name="comment" required></textarea>
        <input type="hidden" name="customer_id">
        <input type="hidden" name="alternative_id">
        <input type="hidden" name="product_id">
        <button class="btn btn-primary btn-sm" type="submit">Speichern</button>
    </form>


  <hr>

  <div id="commentsList">
      <!-- AJAX-loaded comment thread -->
  </div>

  </div>
</div>


<div class="panel" id="filePanel">
  <div class="panel-header d-flex justify-content-between align-items-center">
    <h5>Dateien</h5>
    <button class="btn btn-sm btn-outline-secondary" onclick="closePanel('filePanel')">&times;</button>
  </div>

  <div class="p-3">
    <!-- Dropzone Upload Form -->
    <form action="{{ route('file.upload') }}" class="dropzone mb-4" id="file-dropzone">
      @csrf
 
    </form>

    <!-- Gallery Grid -->
    <div class="row" id="file-gallery">
      <div class="col-12 text-center text-muted" id="file-gallery-loading" style="display: none;">
        <div class="spinner-border text-secondary" role="status"></div>
        <p class="mt-2">Lade Dateien...</p>
      </div>
    </div>

   </div>
</div>

