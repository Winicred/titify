<div class="playlist_item">
    <div class="playlist_item_img">
        <div class="cover_image" data-bs-toggle="tooltip" title="Click to play" onclick="{script}" data-src="{playlist_cover}" style="width: 200px; height: 200px; background-repeat: no-repeat; background-position: center; background-size: contain;"></div>
    </div>

    <div class="playlist_item_description">
        <a onclick="load_template(`{script_type}`, {script_data})" data-bs-toggle="tooltip" title="{title}">{playlist_name}</a>

        {if ("{author}" != "")}
            <div class="playlist_item_description_author">
                <a onclick="load_template(`profile`, {id: {playlist_author_id}})" data-bs-toggle="tooltip" title="Go to author profile page">{playlist_author_login}</a>
            </div>
        {/if}
    </div>

    <script>
        $(document).ready(function() {
            $(".playlist_item_img .cover_image").each(function() {
                $(this).css("background-image", "url(" + $(this).attr("data-src") + ")");
            });
        });
    </script>
</div>