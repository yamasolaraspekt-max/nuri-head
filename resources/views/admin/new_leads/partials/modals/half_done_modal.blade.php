            <div class="modal fade" id="halfDoneModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <form id="halfDoneForm" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Teilweise erledigt</h5>
                            <button type="button" class="close" data-bs-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-3">
                            <input type="hidden" name="activity_id">
                            <input type="hidden" name="phase_id">
                            <input type="hidden" name="is_done" value="half">

                            <div class="form-group">
                                <label>Fertigstellungsgrad</label>
                                <select name="percent" class="form-control" required>
                                    <option value="">Wählen...</option>
                                    <option value="0">0%</option>
                                    <option value="25">25%</option>
                                    <option value="50">50%</option>
                                    <option value="75">75%</option>
                                    <option value="100">100%</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Begründung</label>
                                <textarea name="reason" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Speichern</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                        </div>
                    </form>
                </div>
            </div>
