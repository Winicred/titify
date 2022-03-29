<div class="comment_item" data-id="{id}">
    <div class="comment_avatar">
        <img src="{avatar}" alt="{author}"/>
    </div>

    <div class="comment_body">
        <a class="title" onclick="load_template('profile', {id: {author_id}})">{author}</a>

        <p class="comment_text">{text}</p>
    </div>

    <div class="comment_footer">
        <span>{date}</span>
    </div>

    {if(is_auth() && is_worthy('q'))}
        <div class="admin_tools">
            <button onclick="call_modal('edit_track_comment', {id: {id}, text: '{text}'})">
                <i class="fa fa-pencil"></i>
                <span>Edit</span>
            </button>

            <button onclick="call_modal('delete_track_comment', {id: {id}, display_name: '{author}'})">
                <i class="fa fa-trash"></i>
                <span>Delete</span>
            </button>
        </div>
    {/if}
</div>