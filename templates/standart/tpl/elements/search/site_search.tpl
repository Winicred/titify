<div class="site_search_result_item" title="{block_title}" {script}>
    <div class="site_search_result_item_image">
        <img src="{cover}" alt="{cover}" style="width: 50px">
        {if ('{button_show}' != "false")}
            <button class="play_button" id="btn_play" onclick="player_play('{path}')" data-path="{path}" data-bs-toggle="tooltip" title="Play this track">
                <i class="fa fa-circle-play"></i>
            </button>
        {/if}
    </div>
    <div class="site_search_result_item_description">
        <span class="title">{title}</span>
        <span class="description">{description}</span>
    </div>
</div>