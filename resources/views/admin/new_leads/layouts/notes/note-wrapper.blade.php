<div id="note-container" style="display: flex; flex-direction: column; height: 100vh;"> 
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


