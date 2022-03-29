<div class="site_search_container">
    <div class="site_search">
        <input type="text" placeholder="Site Search" id="site_search_input" value="{search_value}">
        <script>keyboard_event('#site_search_input', 'site_search();');</script>
    </div>

    <div class="site_search_result"></div>
</div>

<script>
    site_search();
</script>
