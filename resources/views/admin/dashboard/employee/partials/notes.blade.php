<div class="col-xl-12 col-md-12 col-sm-12  p-0">
    <div class="tools" style="  position: absolute;  bottom: 3px;  right:9px;"> 
        <select name="filter_by" id="" class="filter ">
            <option></option>
            <option value="date">Datum</option>
            <option value="sort">Meine Sortierung</option>
            <option value="calendar">In Kalender verschieben</option>
            <option value="reminder">Erinnerung</option>
            <option value="repeat">Wiederholen</option>
        </select>

        <button type="button" data-toggle="modal" data-target="#newNote"
            class="btn btn-icon btn-icon rounded-circle btn-primary   waves-effect waves-light  "
            style="position: relative;bottom: 6px; padding: 4px;left: 1px;">
            <i class="feather icon-plus" style="    font-size: 20px;font-weight: bold;"></i>
        </button>
    </div>
    <div class="cards p-0" id="todo_card"  >
        <div class="card-content p-0">
            <div class="card-body p-0"> 
                <section id="sortable-lists">
                    <!-- Basic List Group -->
                    <div class="col-sm-12 p-0">
                        <div class="card" style="width: 100%;background: transparent;box-shadow: none;">
                            <div class="card-content p-0">
                                <div class="card-body" style="padding:0; padding-right:41px">
                                    <ul class="list-group" id="personal-note-list"></ul>
                                </div>
                            </div>
                        </div>
                    </div> 
                </section>
            </div>

            <div class="card-body">
                <a href="{{ url('notes_details') }}" class="card-link">Alle anzeigen</a>
            </div>
        </div>
    </div>
</div>

