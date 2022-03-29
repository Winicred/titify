<div class="check_playlist inactive">
    <div class="check_playlist_panel_close" onclick="close_playlist_window()">
        <i class="fa-solid fa-angles-right"></i>
    </div>

    <div class="check_playlist_container">
        <div class="check_playlist_header">
            <span>{playlist_title}</span>
        </div>

        <div class="check_playlist_items_container">
            {playlist_items}
        </div>
    </div>
</div>

<style>
    .playlist_item_empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%;
        width: 100%;
        justify-content: center;
        font-size: 1.5em;
    }

    .playlist_item_empty i {
        font-size: 8rem;
    }

    .playlist_item_empty span {
        font-size: 1.5rem;
        font-weight: bold;
    }
</style>