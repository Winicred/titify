</div>
</div>
</main>

<footer id="footer">
    {if ($page->url != 'login')}
        {include file="elements/player.tpl"}
        <script>
            window.onload = () => {
                play_random_tracks();
                init_player();
            }

            {if (is_auth())}
            setInterval(function () {
                get_notifications();
            }, 1000);

            $('.comment_field input').on('keypress', function (e) {
                if (e.which === 13) {
                    set_comment_to_track($(this).attr('track-id'), $(this).val());
                    $(this).val('');

                    if (window.location.pathname === '/track') {
                        load_track_comment($(this).attr('track-id'));
                    }
                }
            });
            {/if}

            $(document).on('click', function (e) {
                const find_track_block = $(e.target).closest('.find_tracks_results').length;
                const input_search = $(e.target).closest('#find_tracks_input').length;

                if (!find_track_block) {
                    $('.find_tracks_results').removeClass('active');
                }

                if (input_search && $('#find_tracks_input').val() !== '') {
                    $('.find_tracks_results').addClass('active');
                }
            });

            $('.left_sidebar ul li a').on('click', function () {
                $('.left_sidebar ul li a').removeClass('active');
                $(this).addClass('active');
            });

            // получить блок по которому кликнули
            $(document).ready(function () {
                $('[data-bs-dismiss="modal"]').on('click', function () {
                    setTimeout(() => {
                        const id = $(this).attr('id')
                        $(this).closest(id).remove()
                    }, 1000)
                });
            });
        </script>
    {/if}

    <script>
        // инициализация тултипа
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // скрыть тултип при клике на кнопку плеера (когда кнопка имеет атрибут focus, то тултип не скрывается)
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            tooltipTriggerEl.addEventListener('mouseleave', function () {
                tooltipList.forEach(function (tooltip) {
                    tooltip.hide()
                })
            })
            tooltipTriggerEl.addEventListener('focusout', function () {
                tooltipList.forEach(function (tooltip) {
                    tooltip.hide()
                })
            })
        });
    </script>
</footer>