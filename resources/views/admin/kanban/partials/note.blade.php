<div class="modal fade" id="customerNotesModal" tabindex="-1" role="dialog" aria-labelledby="customerNotesTitle" aria-modal="true" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header bg-light align-items-center">
        <h5 class="modal-title d-flex align-items-center" id="customerNotesTitle">
          <i class="feather icon-message-square mr-2"></i>
          <span>Kunden-Notizen</span>
          <small id="notesContextBadge" class="text-muted ml-2"></small>
          <span id="notesCountBadge" class="badge badge-primary bg-primary ml-2 d-none">0</span>
        </h5>
        <div class="d-flex align-items-center">
          <button type="button" id="notesRefreshBtn" class="btn btn-sm btn-outline-secondary mr-2">
            <i class="feather icon-rotate-cw"></i>
          </button>
          <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Schließen">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      </div>

      <!-- Body -->
      <div class="modal-body p-0">
        <div class="row no-gutters">
          <!-- LEFT: list + search -->
          <div class="col-lg-8 border-right">
            <div class="p-2 d-flex align-items-center">
              <label for="notesSearchInput" class="sr-only">Notizen durchsuchen</label>
              <input id="notesSearchInput" class="form-control" placeholder="Notizen durchsuchen…">
              <button id="notesSearchBtn" class="btn btn-outline-secondary ml-2" type="button" aria-label="Suchen">
                <i class="feather icon-search"></i>
              </button>
              <button id="notesSearchClearBtn" class="btn btn-outline-light ml-1" type="button" aria-label="Suche löschen" title="Suche löschen">
                <i class="feather icon-x-circle"></i>
              </button>
            </div>

            <!-- Loading & Empty states -->
            <div id="notesLoading" class="px-3 py-2 d-none">
              <div class="d-flex align-items-center text-muted">
                <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                <span>Lade Notizen…</span>
              </div>
            </div>
            <div id="notesEmpty" class="px-3 py-3 text-center text-muted d-none">
              <i class="feather icon-inbox"></i> Keine Notizen gefunden.
            </div>

            <!-- Notes list -->
            <div id="notesList" class="p-2" style="max-height:60vh; overflow:auto;"></div>
          </div>

          <!-- RIGHT: composer + latest -->
          <div class="col-lg-4">
            <div class="p-2 border-bottom d-flex align-items-center justify-content-between">
              <strong>Neue Notiz</strong>
              <div class="btn-group btn-group-sm" role="group" aria-label="Note actions">
                <button id="noteClearBtn" type="button" class="btn btn-outline-secondary" title="Felder leeren">
                  <i class="feather icon-eraser"></i>
                </button>
              </div>
            </div>

            <div class="p-2">
              <div id="notesEditor" style="height:180px;"></div>

              <div class="form-row mt-2">
                <div class="form-group col-6">
                  <label for="noteDueDate">Fällig am</label>
                  <input type="date" id="noteDueDate" class="form-control">
                </div>
                <div class="form-group col-6">
                  <label for="noteColor">Farbe</label>
                  <input type="color" id="noteColor" class="form-control" value="#cfe09b">
                </div>
              </div>

              <button id="noteSaveBtn" class="btn btn-primary btn-block" type="button">
                <i class="feather icon-send"></i> Speichern
              </button>
            </div>

            <div class="p-2 border-top">
              <small class="text-muted d-block mb-1">Letzte Notiz</small>
              <div id="latestNoteBox" class="border rounded p-2 bg-light">
                <em class="text-muted">Keine</em>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" data-bs-dismiss="modal">Schließen</button>
      </div>

    </div>
  </div>
</div>
