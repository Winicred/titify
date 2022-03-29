<div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="delete_account_alert" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Wait a second...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete your account?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No!</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="delete_account({if(is_worthy('g'))}{id}{/if});">Yes...</button>
            </div>
        </div>
    </div>
</div>