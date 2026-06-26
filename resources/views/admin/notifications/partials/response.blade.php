
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
                    <div class="oc-list">
                        @foreach($response as $item)
                            @php
                                $empImage = $item->emp_image ? asset('images/employee/' . $item->emp_image) : asset('images/gender/male.png');
                                $createdImage = $item->cimage ? asset('images/employee/' . $item->cimage) : asset('images/gender/male.png');
                                $requestImage = $item->rimage ? asset('images/employee/' . $item->rimage) : asset('images/gender/male.png');

                                $approvedLabel = $item->approved === 'Yes' ? 'Genehmigt' : 'Ausstehend';
                                $approvedClass = $item->approved === 'Yes' ? 'green' : 'orange';

                                $statusLabel = $item->status ?: 'Offen';
                                $statusClass = $item->status === 'accept' ? 'green' : 'gray';
                            @endphp

                            <div class="oc-item">
                                <div class="oc-item-row">
                                    <div class="oc-cell">
                                        <div class="oc-cell-title">ID / Zeitraum</div>
                                        <span class="oc-id-badge">#{{ $item->leave_id }}</span>

                                        <div class="oc-subt mt-1">
                                            {{ $item->start_date }} – {{ $item->end_date }}
                                        </div>

                                        @if($item->old_start)
                                            <div class="oc-subt mt-1" style="color:#d97706;">
                                                Alt: {{ $item->old_start }} – {{ $item->old_end }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Antrag</div>
                                        <div class="oc-main">
                                            <div class="oc-ttl">Urlaubsanfrage</div>
                                            <div class="oc-subt">
                                                {{ $item->duration ?? 0 }} Tag(e) · {{ $item->reason ?? 'Urlaub' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Status</div>
                                        <span class="oc-status-pill {{ $approvedClass }}">{{ $approvedLabel }}</span>

                                        @if($item->status != 'accept')
                                            <span class="oc-status-pill {{ $statusClass }} mt-1">{{ $statusLabel }}</span>
                                        @endif
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Grund</div>
                                        <div class="oc-main">
                                            <div class="oc-ttl" style="font-size:14px;">{{ $item->reason ?? '—' }}</div>
                                            <div class="oc-subt">{{ $item->description ?? 'Keine Beschreibung' }}</div>
                                        </div>
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Mitarbeiter</div>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $empImage }}" class="rounded-circle mr-2" width="34" height="34"
                                                style="object-fit:cover;" alt="">
                                            <div>
                                                <div class="oc-ttl" style="font-size:13px;">
                                                    {{ $item->emp_lastname }} {{ $item->emp_name }}
                                                </div>
                                                <div class="oc-subt">Antragsteller</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Notizen</div>
                                        <button type="button" class="oc-btn-ic primary leave-notes" data-id="{{ $item->leave_id }}"
                                            title="Notizen">
                                            <i class="feather icon-file-text"></i>
                                        </button>
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Aktionen</div>

                                        <div class="oc-actions">
                                            <button type="button" class="oc-btn-ic warning check-leave" data-id="{{ $item->leave_id }}"
                                                data-start-date="{{ $item->start_date }}" data-end-date="{{ $item->end_date }}"
                                                data-employee-id="{{ $item->emp_id }}" title="Konflikt prüfen">
                                                <i class="feather icon-calendar"></i>
                                            </button>

                                            @if($item->approved != 'Yes')
                                                <button type="button" class="oc-btn-ic success approve-btn" data-leave-id="{{ $item->leave_id }}"
                                                    data-employee-id="{{ $item->emp_id }}" title="Genehmigen">
                                                    <i class="feather icon-check-circle"></i>
                                                </button>

                                                <button type="button" class="oc-btn-ic danger change-btn" data-leave-id="{{ $item->leave_id }}"
                                                    data-start="{{ $item->start_date }}" data-end="{{ $item->end_date }}"
                                                    data-employee-id="{{ $item->emp_id }}" title="Ablehnen">
                                                    <i class="feather icon-x-circle"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="oc-pagination">
                        {{ $response->links('pagination::bootstrap-4') }}
                    </div>
                @else
                    <div class="oc-empty">Keine Anfragen gefunden.</div>
                @endif
                
                <script>
                    if (window.feather) window.feather.replace();
                </script>
               
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

