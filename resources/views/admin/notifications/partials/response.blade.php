
 <div class="card-content">
    <div class="table-responsive mt-1">
        <table class="table   mb-0">
            <thead>
                <tr>    
                     <th>Anfrage an</th>  
                    <th>Erstellt von</th>  
                     <th>Anfrage durch</th>   
                    <th>Zweck</th>   
                    <th>Status</th>
                    <th>Bearbeitung</th>
                </tr>
            </thead>
            <tbody>
                @if(count($response) > 0)
                    @foreach($response as $item)
                        <tr style="border-bottom: 10px solid #f8f8f8; ">    
                            <td>
                                <span data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title=" {{ $item->rname }} {{ $item->rlastname }} " class="avatar pull-up">
                                    <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$item->rimage) }}"alt="Avatar" height="30" width="30">
                                </span>
                               {{ $item->rlastname }} {{ $item->rname }} 
                            </td>
                            <td>
                                <span data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title=" {{ $item->cname }} {{ $item->clastname }} " class="avatar pull-up">
                                    <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$item->cimage) }}"alt="Avatar" height="30" width="30">
                                </span>
                                {{ $item->clastname }} {{ $item->cname }} 
                            </td>

                            <td>
                                <span data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title=" {{ $item->emp_name }} {{ $item->emp_lastname }} " class="avatar pull-up">
                                    <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$item->emp_image) }}"alt="Avatar" height="30" width="30">
                                </span>
                                {{ $item->emp_lastname }} {{ $item->emp_name }} 
                            </td> 
                            <td>
                                <p class="m-0 p-0">Urlaubsanfrage </p>
                                    <p class="m-0 p-0">  <small><i class="feather icon-calendar primary"></i> <strong>{{ $item->start_date }}</strong> - <strong>{{ $item->end_date }}</strong></small> </p>
                                     @if($item->old_start)
                                    <p class="m-0 p-0"><small class="warning"><i class="fa fa-calendar-times-o warning"></i>   <strong>{{ $item->old_start }}</strong> - <strong>{{ $item->old_end }}</strong></small> </p> 
                                     @endif 
                            </td>
                      
                      
                         
                            <td>
                                  
                                    @if($item->approved == "Yes")
                                    <span class="badge badge-primary badge-pill"> 
                                        Genehmigt
                                    </span>
                                    @else
                                        <span class="badge badge-warning badge-pill"> 
                                        Ausstehend
                                        </span> 
                                    @endif

                                      @if($item->status != 'accept')
                                    <span class="badge badge-primary badge-pill"> 
                                       {{$item->status}}
                                    </span>  
                                    @endif
                            </td>
                            <td>
                                
                            
                                 <button class="btn btn-success  check-leave" 
                                 data-id="{{ $item->leave_id }}" data-start-date="{{$item->start_date}}" data-end-date="{{$item->end_date}}" data-employee-id="{{$item->emp_id}}" >
                                        Konflikt prüfen  
                                </button> 


                                <button class="btn btn-success leave-notes" data-id="{{ $item->leave_id }}">
                                    <i class="feather icon-file-text"></i> Notiz
                                </button>

                                    @if($item->approved != "Yes")
                                        <button class="btn btn-success approve-btn" 
                                                data-leave-id="{{ $item->leave_id   }}" 
                                                data-employee-id="{{ $item->emp_id }}">
                                            Genehmigen
                                        </button>                                     
                                       <button class="btn btn-danger change-btn" 
                                            data-leave-id="{{ $item->leave_id }}" 
                                            data-start="{{ $item->start_date }}" 
                                            data-end="{{ $item->end_date }}"       
                                            data-employee-id="{{ $item->emp_id }}">
                                            Ablehnen
                                        </button>                  
                                    @endif
                            </td>


                        </tr>  
                    
                    @endforeach 
                @else
                    <p class="text-muted">No notifications found.</p>
                @endif

               
            </tbody>
        </table>
    </div>

  
</div>

<div class="mt-2">
{{ $response->links('pagination::bootstrap-4') }}
</div>


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentLeaveId = null;
    let employeesList = [];

    // 📥 Load employees for @mention
    fetch('/get-employee-usernames')
        .then(res => res.json())
        .then(data => employeesList = data);

    // 📌 Open Notes Sidebar on Button Click
    document.querySelectorAll('.leave-notes').forEach(btn => {
        btn.addEventListener('click', function () {
            currentLeaveId = this.dataset.id;
            document.getElementById('leaveNotesSidebar').classList.add('active');
            loadLeaveNotes();
        });
    });

    // 🧹 Close Sidebar
    window.closeLeaveSidebar = function () {
        document.getElementById('leaveNotesSidebar').classList.remove('active');
        currentLeaveId = null;
    }

    // 📦 Load Notes from Server
    function loadLeaveNotes() {
        fetch(`/leaves/${currentLeaveId}/notes`)
            .then(res => res.json())
            .then(renderLeaveNotes)
            .catch(err => console.error('Fehler beim Laden der Notizen:', err));
    }

    // ✏️ Render Notes to Sidebar
    function renderLeaveNotes(notes) {
        const content = document.getElementById('leaveNotesContent');
        content.innerHTML = '';
        if (!Array.isArray(notes)) notes = [];

        notes.forEach((note, index) => {
            content.innerHTML += `
                <div class="note-item border p-2 mb-2 d-flex">
                    <img src="/images/employees/${note.image || 'images/gender/male.png'}" 
                        alt="${note.employee}" 
                        class="rounded-circle mr-2" 
                        style="width: 40px; height: 40px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <small><strong>${note.employee}</strong> - ${note.date}</small>
                        <p class="mb-1">${note.text}</p>
                        <button class="btn btn-sm btn-warning" onclick="editLeaveNote(${index})"><i class="feather icon-edit"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="deleteLeaveNote(${index})"><i class="feather icon-trash"></i></button>
                    </div>
                </div>`;
        });
    }

    // 💾 Save New Note
    window.saveLeaveNote = function () {
        const text = document.getElementById('newNoteText').value;
        if (!text.trim()) return;

        fetch(`/leaves/${currentLeaveId}/notes/store`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ text })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('newNoteText').value = '';
            renderLeaveNotes(data.notes);
        });
    }

    // ❌ Delete Note
    window.deleteLeaveNote = function (index) {
        Swal.fire({
            title: 'Löschen?',
            text: 'Diese Notiz wirklich entfernen?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, löschen',
            cancelButtonText: 'Abbrechen'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`/leaves/${currentLeaveId}/notes/delete/${index}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => renderLeaveNotes(data.notes));
            }
        });
    }

    // ✏️ Edit Note
    window.editLeaveNote = function (index) {
        const newText = prompt("Neue Notiz eingeben:");
        if (!newText) return;

        fetch(`/leaves/${currentLeaveId}/notes/update/${index}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ text: newText })
        })
        .then(res => res.json())
        .then(data => renderLeaveNotes(data.notes));
    }

    // 🧠 Mention Suggestion
    const noteInput = document.getElementById('newNoteText');
    noteInput.addEventListener('input', function () {
        const val = this.value;
        const caretPos = this.selectionStart;
        const match = val.substring(0, caretPos).match(/@([\w\.]*)$/);
        const suggestionBox = document.getElementById('mentionSuggestions');

        if (match) {
            const term = match[1].toLowerCase();
            const matches = employeesList.filter(name => name.toLowerCase().includes(term)).slice(0, 5);

            suggestionBox.innerHTML = '';
            matches.forEach(name => {
                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.textContent = name;
                li.onclick = () => {
                    noteInput.value = val.substring(0, caretPos - match[0].length) + `@${name} ` + val.substring(caretPos);
                    noteInput.focus();
                    suggestionBox.style.display = 'none';
                };
                suggestionBox.appendChild(li);
            });

            const rect = this.getBoundingClientRect();
            suggestionBox.style.top = `${rect.top + window.scrollY + this.offsetHeight}px`;
            suggestionBox.style.left = `${rect.left}px`;
            suggestionBox.style.display = 'block';
        } else {
            suggestionBox.style.display = 'none';
        }
    });

});
</script>

    
@endpush

