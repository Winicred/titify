<div class="error_block">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <h4>Something went wrong...</h4>
    <p>{message}</p>
</div>

<style>
    body main > .container .error_block {
        min-height: inherit;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    body main > .container .error_block i {
        font-size: 10rem;
        color: red;
        margin-bottom: 2rem;
    }
</style>
