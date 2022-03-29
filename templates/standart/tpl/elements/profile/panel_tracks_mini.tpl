<div class="panel_tracks_mini_item">
    <img src="{cover}" alt="{name}">

    <div class="panel_playlists_mini_item_title">
        <div class="author">
            <a onclick="load_template('profile', {id: {author_id}})">{author}</a>
        </div>

        <div class="title">
            <span>{name}</span>
        </div>

        <div class="tools">
            <div class="auditions">
                <i class="fa fa-play"></i>
                <span>{auditions}</span>
            </div>

            <div class="likes">
                <i class="fa fa-heart"></i>
                <span>{likes}</span>
            </div>

            <div class="comments">
                <i class="fa fa-comment"></i>
                <span>{comments}</span>
            </div>

            <div class="repost">
                <i class="fa fa-retweet"></i>
                <span>{reposts}</span>
            </div>
        </div>
    </div>
</div>