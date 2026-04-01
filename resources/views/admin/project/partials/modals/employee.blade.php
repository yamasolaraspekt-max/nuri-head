           <!-- Modal for Adding Employee -->
        <div class="modal fade text-left" id="employee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h5 class="modal-title" id="myModalLabel160">Mitarbeiter hinzufügen</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ route('add.employee.to.project')}}" method="post" id="add_employe_form">
                        @csrf
                        <input type="hidden" name="project_id" id="modal_project_id" value="">
                        <input type="hidden" name="old_employee" id="modal_old_employee" value="">
                        <div class="modal-body">
                            <label for="employee_id">Mitarbeiter auswählen</label>
                            <select name="employee_id[]" id="employee_id_select" class="form-control employee" style="width: 100%;" multiple>
 
                                @foreach ($employees as $emp)
                                    <option value="{{$emp->id}}" 
                                            data-image="{{asset('images/employee/'.$emp->image)}}">
                                        {{$emp->name}} {{$emp->lastname}}
                                    </option>
                                @endforeach
                            </select>

                            <label for="employee_roll">Mitarbeiterfunktion</label>
                            <select name="employee_roll" id="employee_roll" class="form-control" style="width: 100%;">
                                <option value="member">Mitglied</option>
                                <option value="guest">Gast</option>
                                <option value="comentator">Kommentator(in)</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary waves-effect waves-light" id="save-add-employee">Hinzufügen</button>
                            <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
            