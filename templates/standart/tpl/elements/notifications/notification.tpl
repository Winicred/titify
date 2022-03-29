<div class="notification_item {if ('{status}' == 'unread')}unread{/if}">
    <span class="message">{message}</span>

    <div class="notification_item_panel">
        <span class="date">{date}</span>
        <button class="delete_button" onclick="delete_notification({id}, $(this))">
            <i class="fa-solid fa-trash-can"></i>
        </button>
    </div>
</div>

<style>
    .notification_item {
        display: flex;
        align-items: center;
        padding: 10px;
        position: relative;
    }

    .notification_item.unread {
        background-color: var(--extra-light-gray-color);
    }

    .notification_item:not(:last-child) {
        border-bottom: 1px solid var(--gray-color);
        margin: 5px 0;
    }

    .notification_item .message {
        font-size: 0.9rem;
    }

    .notification_item .notification_item_panel {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: space-between;
    }

    .notification_item .date {
        font-size: 0.8rem;
        user-select: none;
        color: var(--gray-color);
    }


    .notification_item .delete_button i {
        font-size: 0.9rem;
        color: var(--gray-color);
    }
</style>