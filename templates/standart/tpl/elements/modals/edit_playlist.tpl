<?
$playlist = get_playlist_by_id('{id}');
$playlist_tracks = get_playlist_tracks('{id}');
?>

<div class="modal fade" data-bs-backdrop="static" id="edit_playlist" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit playlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-3 justify-content-center">
                        <label for="cover" class="fw-bold">Cover:</label>
                        <div class="cover_preview mt-3" id="cover" data-src="{{$playlist->cover}}"
                             style="width: 250px; height: 250px; background-image: url('{{$playlist->cover}}'); background-position: center; background-size: cover"></div>

                        <div class="mt-3">
                            <button class="btn btn-outline-secondary" id="change_cover_btn">Change cover</button>
                            <input type="file" style="display: none" accept="image/png, image/jpg">
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title:</label>
                            <input type="text" class="form-control" id="title" placeholder="Playlist title"
                                   value="{{$playlist->name}}">
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="playlist_type" id="private"
                                       {if ($playlist->private == 1)}checked{/if} value="private">
                                <label class="form-check-label mt-1" for="private">
                                    Private
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="playlist_type" id="public"
                                       {if ($playlist->private == 0)}checked{/if} value="public">
                                <label class="form-check-label mt-1" for="public">
                                    Public
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tracks_in_playlist" class="form-label mb-3 pb-3 w-100"
                                   style="border-bottom: 1px solid var(--extra-light-gray-color)">Tracks in
                                playlist:</label>
                            <div id="tracks_in_playlist_container" style="max-height: 400px; overflow-y: auto">
                                <div id="tracks_in_playlist">
                                    {if (isset($playlist_tracks))}
                                        {for($i=0; $i < count($playlist_tracks); $i++)}
                                            <div class="track_in_playlist"
                                                 style="display: flex; width: 100%; height: 50px; padding: 5px; align-items: center; border-bottom: 1px solid var(--extra-light-gray-color)">
                                                <div class="track_in_playlist_cover">
                                                    <img src="{{$playlist_tracks[$i]['cover']}}" alt=""
                                                         style="width: 40px; height: 40px">
                                                </div>
                                                <div class="track_in_playlist_title" style="margin-left: 5px">
                                                    {{$playlist_tracks[$i]['title']}}
                                                </div>
                                                <div class="track_in_playlist_remove btn btn-outline-danger"
                                                     onclick="delete_track_from_playlist({{$playlist->id}}, {{$playlist_tracks[$i]['id']}}, $(this))"
                                                     data-track-id="{{$playlist_tracks[$i]['id']}}"
                                                     style="margin-left: auto">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </div>
                                            </div>
                                        {/for}
                                    {else}
                                        <div class="track_in_playlist">
                                            <div class="track_in_playlist_title mt-5"
                                                 style="text-align: center; font-size: 1.5rem; color: var(--light-gray-color)">
                                                No tracks in playlist
                                            </div>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-danger" data-bs-target="#accept_delete_playlist" data-bs-toggle="modal" style="margin-right: auto;">Delete playlist</button>
                <span class="modal_result"></span>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary"
                        onclick="edit_playlist({id})">Edit
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="accept_delete_playlist" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel2">Delete playlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="delete_playlist_password" class="col-form-label fw-bold">Enter your password to delete playlist:</label>
                <input type="password" class="form-control" id="delete_playlist_password" placeholder="Password">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-target="#edit_playlist" data-bs-toggle="modal">Back</button>
                <button class="btn btn-danger" onclick="delete_playlist({id}, $('#delete_playlist_password').val())">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#change_cover_btn').click(function () {
        $('input[type=file]').click();
    });

    $('input[type=file]').change(function () {
        let file = this.files[0];
        let reader = new FileReader();

        if (file.size > 100000000) {
            alert("Image must not exceed 1 mb.");
            return;
        }

        const data = new FormData();
        data.append("update_playlist_cover", 1);
        data.append("playlist_id", {id});
        data.append("cover", file);

        send_image_query('ajax/actions_auth.php', data, (result) => {
            if (result.status === "success") {
                get_library_playlists();

                $('.modal_result').addClass('text-success');
                $('.modal_result').text(result.message);

                reader.onload = function (e) {
                    $('#cover').attr('data-src', file.name)
                    $('#cover').css('background-image', 'url(' + e.target.result + ')');
                };
                reader.readAsDataURL(file);
            } else {
                $('.modal_result').addClass('text-danger');
                $('.modal_result').text(result.message);
            }
        });
    });
</script>