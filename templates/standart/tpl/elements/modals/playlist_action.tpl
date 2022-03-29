<div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="playlist_action" tabindex="-1"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="list-group" id="list-tab" role="tablist" style="flex-direction: row">
                    <a class="list-group-item list-group-item-action active" id="add_to_playlist" data-bs-toggle="list"
                       href="#add_to_playlist_panel" role="tab" aria-controls="add_to_playlist_panel">Add To
                        Playlist</a>

                    <a class="list-group-item list-group-item-action" id="create_new_playlist" data-bs-toggle="list"
                       href="#create_new_playlist_panel" role="tab" aria-controls="create_new_playlist_panel">Create
                        new</a>
                </div>
            </div>
            <div class="modal-body">
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="add_to_playlist_panel" role="tabpanel"
                         aria-labelledby="add_to_playlist">
                        <form>
                            <div class="mb-3">
                                <label for="recipient-name" class="col-form-label">Search:</label>
                                <input type="text" class="form-control" id="playlists">
                            </div>
                            <div class="mb-3 search_playlists_result" track-id="{track_id}">

                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade w-100" id="create_new_playlist_panel" role="tabpanel"
                         aria-labelledby="create_new_playlist">
                        <div class="mb-3">
                            <label for="playlist_title-name" class="col-form-label">Title:</label>
                            <input type="text" class="form-control" id="playlist_title">
                        </div>
                        <div class="mb-2">
                            <span>Playlist will be:</span>
                        </div>

                        <div class="mb-1">
                            <input type="radio" name="playlist_type" id="playlist_type-public" value="public"
                                   checked>
                            <label for="playlist_type-public">Public</label>
                        </div>
                        <div class="mb-3">
                            <input type="radio" name="playlist_type" id="playlist_type-private" value="private">
                            <label for="playlist_type-private">Private</label>
                        </div>
                    </div>
                </div>
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

<style>
    .list-group-item {
        border: none;
        width: auto !important;
    }

    .list-group-item.active {
        background: transparent;
        color: var(--main-color);
        font-weight: bold;
    }

    .search_playlists_result {
        min-height: 200px;
        max-height: 200px;
        overflow-y: auto;
    }
</style>

<script>
    keyboard_event('#playlists', 'find_user_playlists();');
</script>