<div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="playlist_action" tabindex="-1"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create a new playlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="playlist_title-name" class="col-form-label">Title:</label>
                        <input type="text" class="form-control" id="playlist_title">
                    </div>
                    <div class="mb-2">
                        <span>Playlist will be:</span>
                    </div>

                    <div class="mb-1">
                        <input type="radio" name="playlist_type" id="playlist_type-public" value="public" checked>
                        <label for="playlist_type-public">Public</label>
                    </div>
                    <div class="mb-3">
                        <input type="radio" name="playlist_type" id="playlist_type-private" value="private">
                        <label for="playlist_type-private">Private</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="create_playlist();">
                    Create
                </button>
            </div>
        </div>
    </div>
</div>