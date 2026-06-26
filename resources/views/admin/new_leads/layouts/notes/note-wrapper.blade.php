<div id="note-container" style="display: flex; flex-direction: column; height: 100vh;"> 
  <div class="ma-note-type-switcher" id="maNoteTypeSwitcher">
    <button type="button" class="ma-note-type-current" data-note-feed-current>
      <span class="ma-note-type-icon bg-blue">
        <i data-feather="message-square"></i>
      </span>
      <span class="ma-note-type-text">
        <strong>Aktuelle Notizen</strong>
        <small>Kunden-, Objekt- und Produktnotizen</small>
      </span>
      <i data-feather="chevron-down" class="ma-note-type-chevron"></i>
    </button>
  
    <div class="ma-note-type-menu" data-note-feed-menu>
      <button type="button" class="ma-note-type-item active" data-feed-type="notes" data-label="Aktuelle Notizen"
        data-icon="message-square" data-color="blue">
        <span class="ma-note-type-icon bg-blue"><i data-feather="message-square"></i></span>
        <span><strong>Aktuelle Notizen</strong><small>Standard-Notizen</small></span>
      </button>
  
      <button type="button" class="ma-note-type-item" data-feed-type="tickets" data-label="Tickets"
        data-icon="alert-triangle" data-color="pink">
        <span class="ma-note-type-icon bg-pink"><i data-feather="alert-triangle"></i></span>
        <span><strong>Tickets</strong><small>Probleme, Kommentare, Aufgaben</small></span>
      </button>
  
      <button type="button" class="ma-note-type-item" data-feed-type="appointments" data-label="Termine"
        data-icon="calendar" data-color="green">
        <span class="ma-note-type-icon bg-green"><i data-feather="calendar"></i></span>
        <span><strong>Termine</strong><small>Kalender, Berichte, Kommentare</small></span>
      </button>
  
      <button type="button" class="ma-note-type-item" data-feed-type="tasks" data-label="Aufgaben"
        data-icon="check-square" data-color="orange">
        <span class="ma-note-type-icon bg-orange"><i data-feather="check-square"></i></span>
        <span><strong>Aufgaben</strong><small>Tasks, Schritte, Kommentare</small></span>
      </button>
  
      <button type="button" class="ma-note-type-item" data-feed-type="deals" data-label="Auftrag" data-icon="package"
        data-color="blue">
        <span class="ma-note-type-icon bg-blue"><i data-feather="package"></i></span>
        <span><strong>Auftrag</strong><small>Aufträge und Auftragsnotizen</small></span>
      </button>
  
      <button type="button" class="ma-note-type-item" data-feed-type="customer_reports" data-label="Kundenberichte"
        data-icon="file-text" data-color="green">
        <span class="ma-note-type-icon bg-green"><i data-feather="file-text"></i></span>
        <span><strong>Kundenberichte</strong><small>Reports und Kommentare</small></span>
      </button>
    </div>
  </div>
    <div id="note-scroll-wrapper" style="
                height: 100%;
                overflow-y: auto;
                display: flex;
                flex-direction: column-reverse;  
            ">        
          <div id="note-list" class="space-y-4 p-0"
            data-customer-id="{{ $customer_id }}"
            data-alternative-id="{{ $alternative_id }}"
            data-product-id="{{ $product_id }}"
            data-note-type="{{ $product_id ? 'product' : 'general' }}">
            @if ($notes->count())
                @foreach ($notes as $note)
                    @include('admin.new_leads.layouts.notes.single-note', ['note' => $note])
                @endforeach
            @else
                <div class="text-muted fst-italic">Keine Notizen vorhanden.</div>
            @endif
        </div> 
    </div>

</div>


<div class="modal fade" id="deletedNotesModal" tabindex="-1" role="dialog" aria-labelledby="deletedNotesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white" style="border-radius:0">
        <h5 class="modal-title" id="deletedNotesModalLabel">Gelöschte Unter-Notizen</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Schließen">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="deletedNotesModalBody">
        <div class="text-muted">Wird geladen...</div>
      </div>
    </div>
  </div>
</div>


