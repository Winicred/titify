{if ('{message}' == '')}
    <div class="mini_playlists_tracks_item">
        <img src="{cover}" alt="{name}">
        <span class="index">{index}.</span>
        <span class="name">{name}</span>
        <div class="track_info">
            <span class="auditions">
                    <i class="fa-solid fa-play"></i>
                    {auditions}
            </span>
        </div>
    </div>
{else}
    <span class="empty_message">{message}</span>
{/if}