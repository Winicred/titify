<div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="email_notice_alert" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Wait a second...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to turn off email notifications? After turning off notifications, you will not receive newsletters and you will not be notified of important messages
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="$('span.slider.round').click();">No!</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="change_user_settings('email_notice', '0')">Yes...</button>
            </div>
        </div>
    </div>
</div>