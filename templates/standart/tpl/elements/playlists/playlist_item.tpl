<div class="playlist_item">
    <div class="playlist_item_img" onclick="{script}">
        <img src="{playlist_cover}" alt="{playlist_name}">
    </div>

    <div class="playlist_item_description">
        <span>{playlist_name}</span>

        {if ("{author}" != "")}
            <div class="playlist_item_description_author">
                <a onclick="load_template(\'profile\', {id: {playlist_author_id}})">{playlist_author_login}</a>
            </div>
        {/if}
    </div>
</div>