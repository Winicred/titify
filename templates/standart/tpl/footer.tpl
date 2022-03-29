</div>
</div>
</main>

<footer>
    {if ($page->url != 'login')}
        {include file="elements/player.tpl"}
        <audio id="track" preload="none" src=""></audio>
        <script>

            // document.addEventListener('mousedown', function (ev) {
            //     const browserBack = ev.buttons & 8
            //     const browserForward = ev.buttons & 4
            //
            //     if (browserBack || browserForward) {
            //
            //         const State = History.getState();
            //         const states = History.savedStates;
            //         const prevUrlIndex = states.length - 1;
            //
            //         const url = states[prevUrlIndex].url
            //         const method = url.split('/')[3]
            //
            //         if (typeof method !== undefined) {
            //             const id = method.split('?')[1]
            //             const json_id = id.replace('=', ':')
            //
            //             let dir;
            //             let sub_dir;

                        // const json = JSON.parse('{"' + json_id.split(':')[0] + '":' + json_id.split(':')[1] + '}')

                        // if (method.includes('edit_profile')) {
                        //     dir = 'profile';
                        //     sub_dir = method.split('?')[0];
                        // } else {
                        //     dir = method.split('/')[2];
                        // }

                        // if (dir === '') {
                            // load_template(method, json);
                        // }

                        // load_template(dir, json, sub_dir === '' ? null : sub_dir)
                    // } else {
                        // load_template(method)
                    // }
            //     }
            // })

            {if (is_auth())}
            setInterval(function () {
                get_notifications();
            }, 1000);

            $('.comment_field input').on('keypress', function (e) {
                if (e.which == 13) {
                    set_comment_to_track($(this).attr('track-id'), $(this).val());
                    $(this).val('');

                    if (window.location.pathname === '/track') {
                        load_track_comment($(this).attr('track-id'));
                    }
                }
            });
            {/if}

            // удаление модального окна при закрытии
            $('[data-bs-dismiss="modal"]').click(function () {
                setInterval(() => {
                    $(this).closest('.modal').remove()
                }, 1000)
            });

            // инициализация тултипа
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            //show tooltip on hover
            $('a').on('hover', function () {
                $(this).tooltip('show')
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

            // получить блок по которому кликнули
            $(document).ready(function () {
                $('[data-bs-dismiss="modal"]').on('click', function () {
                    setTimeout(() => {
                        const id = $(this).attr('id')
                        $(this).closest(id).remove()
                    }, 1000)
                });

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
            });
        </script>
    {/if}
</footer>