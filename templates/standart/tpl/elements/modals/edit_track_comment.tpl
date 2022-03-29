<div class="modal fade" data-bs-backdrop="static" id="edit_track_comment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">New message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="old_track_comment" class="col-form-label">Old comment:</label>
                        <textarea class="form-control" id="old_track_comment" disabled style="resize: none">{text}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="new_track_comment" class="col-form-label">New comment:</label>
                        <textarea class="form-control" id="new_track_comment" placeholder="Write new comment here..." style="resize: none"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="edit_track_comment({id}, $('#new_track_comment').val())">Edit</button>
            </div>
        </div>
    </div>
</div>