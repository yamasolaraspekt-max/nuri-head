            <div id="commentSidebar" class="report-sidebar shadow" style="display:none;">
                <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                    <h5 class="mb-0">Kommentare</h5>
                    <div>
                        <button class="btn btn-sm btn-success mr-2 open-comment-form"><i class="fa fa-plus"></i></button>
                        <button class="btn btn-sm btn-danger close-comment-sidebar">×</button>
                    </div>
                </div>
                <div id="commentContent" class="p-3 overflow-auto" style="height: calc(100% - 50px);"></div>

                <!-- 🔻 Modal Form -->
                <div id="commentFormModal" class="report-form-modal" style="display:none;">
                    <div class="modal-content bg-white p-3 shadow" style="width: 90%; max-width: 500px; position:relative;">
                        <button type="button" class="btn btn-sm btn-danger close-comment-form"
                            style="position:absolute; top:8px; right:8px; line-height:1;">×</button>

                        <form id="newCommentForm" class="mt-3">
                            <input type="hidden" name="report_id" id="report_id">
                            <input type="hidden" name="parent_id" id="parent_id">
                            <div id="commentMeta"></div>
                            <div id="quotedComment" class="alert alert-light py-2 px-3" style="display:none;"></div>

                            <textarea name="comment" class="form-control" rows="3" placeholder="Kommentieren..."
                                required></textarea>

                            <div class="d-flex justify-content-end mt-2">
                                <button type="button" class="btn btn-light mr-2 close-comment-form">Abbrechen</button>
                                <button type="submit" class="btn btn-primary">Senden</button>
                            </div>
                        </form>
                    </div>
                </div>


            </div>
