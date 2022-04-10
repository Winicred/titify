<div class="playlists_container">
    <div class="playlist_preview">
        <div class="playlist_title">Recently played</div>
        <div class="recent_playlists item_container">
            {if ('{recent_playlists}' == '')}
                <div class="empty_message">
                    <span>You haven't listened to any playlist yet</span>
                </div>
            {else}
                {recent_playlists}
            {/if}
        </div>
    </div>
    <div class="playlist_preview">
        <div class="playlist_title">Likes</div>
        <div class="likes_playlists item_container">
            {if ('{likes_playlists}' == '')}
                <div class="empty_message">
                    <span>You haven't liked tracks or playlists</span>
                </div>
            {else}
                {likes_playlists}
            {/if}
        </div>
    </div>
    <div class="playlist_preview">
        <div class="playlist_title">My playlists</div>
        <div class="playlist_pretitle">(click to edit)</div>
        <div class="my_playlists item_container">
            {if ('{my_playlists}' == '')}
                <div class="empty_message">
                    <span>You haven't created any playlist yet</span>
                </div>
            {else}
                {likes_playlists}
            {/if}
        </div>
    </div>
</div>

<script>
    get_library_playlists();
</script>

<style>
    body main > .container .playlists_container {
        width: 100%;
        display: flex;
        flex-direction: column;
    }

    body main > .container .playlists_container .playlist_preview {
        width: 100%;
        display: flex;
        flex-direction: column;
        padding: 10px;
    }

    body main > .container .playlists_container .playlist_preview .playlist_title {
        font-size: 1.5rem;
        font-weight: bold;
    }

    body main > .container .playlists_container .playlist_preview .playlist_pretitle {
        font-size: 0.8rem;
        color: var(--light-gray-color);
        margin-top: 5px;
        user-select: none;
    }

    body main > .container .playlists_container .playlist_preview .item_container {
        width: 100%;
        display: flex;
        min-height: 250px;
        align-items: stretch;
        border-top: 1px solid var(--extra-light-gray-color);
        margin-top: 10px;
    }

    body main > .container .playlists_container .playlist_preview .item_container .empty_message {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin: 0 auto;
        font-size: 1.5rem;
        color: var(--light-gray-color);
        user-select: none;
    }

    body main > .container .playlists_container .playlist_preview .item_container .playlist_item {
        display: flex;
        flex-direction: column;
        width: 240px;
        cursor: pointer;
        padding: 20px;
        margin: 0 35px;
    }

    body main > .container .playlists_container .playlist_preview .item_container .playlist_item .playlist_item_img {
        display: flex;
        width: 200px;
        height: 200px;
        justify-content: center;
        align-items: center;
    }

    body main > .container .playlists_container .playlist_preview .item_container .playlist_item .playlist_item_img img {
        width: 100%;
    }

    body main > .container .playlists_container .playlist_preview .item_container .playlist_item .playlist_item_description {
        display: flex;
        flex-direction: column;
        margin-top: 10px;
    }

    body main > .container .playlists_container .playlist_preview .item_container .playlist_item .playlist_item_description > a {
        font-weight: bold;
        -ms-text-overflow: ellipsis;
        -o-text-overflow: ellipsis;
        text-overflow: ellipsis;
        overflow: hidden;
        -ms-line-clamp: 2;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        display: -webkit-box;
        display: box;
        word-wrap: break-word;
        -webkit-box-orient: vertical;
        box-orient: vertical;
        transition: var(--transition);
    }

    body main > .container .playlists_container .playlist_preview .item_container .playlist_item .playlist_item_description > a:hover {
        color: var(--main-color);
    }

    body main > .container .playlists_container .playlist_preview .item_container .playlist_item .playlist_item_description .playlist_item_description_author {
        display: flex;
    }

    body main > .container .playlists_container .playlist_preview .item_container .playlist_item .playlist_item_description .playlist_item_description_author a {
        text-decoration: none;
        color: var(--gray-color);
        font-size: 0.8rem;
        position: relative;
        transform: var(--transition);
        -ms-text-overflow: ellipsis;
        -o-text-overflow: ellipsis;
        text-overflow: ellipsis;
        overflow: hidden;
        -ms-line-clamp: 1;
        -webkit-line-clamp: 1;
        line-clamp: 1;
        display: -webkit-box;
        display: box;
        word-wrap: break-word;
        -webkit-box-orient: vertical;
        box-orient: vertical;
        margin-top: 5px;
        transition: var(--transition);
    }

    body main > .container .playlists_container .playlist_preview .item_container .playlist_item .playlist_item_description .playlist_item_description_author a:after {
        content: '';
        position: absolute;
        width: 100%;
        transform: scaleX(0);
        height: 2px;
        bottom: -3px;
        left: 0;
        background-color: var(--main-color);
        transform-origin: bottom right;
        transition: transform 0.3s ease-in-out;
    }

    body main > .container .playlists_container .playlist_preview .item_container .playlist_item .playlist_item_description .playlist_item_description_author a:hover {
        color: var(--main-color);
    }

    body main > .container .playlists_container .playlist_preview .item_container .playlist_item .playlist_item_description .playlist_item_description_author a:hover:after {
        transform: scaleX(1);
        transform-origin: bottom left;
    }

</style>