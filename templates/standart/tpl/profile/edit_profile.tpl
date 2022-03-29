<div class="profile_settings">
    <div class="profile_settings_container">
        <div class="profile_settings_header">
            <span>{display_name} basic information</span>
        </div>
        <div class="profile_settings_content">
            <div class="profile_settings_content_item">
                <div class="profile_settings_content_item_field">
                    <label for="display_name">Display Name</label>
                    <input type="text" id="display_name" value="{display_name}"/>
                </div>
            </div>
            <div class="profile_settings_content_item">
                <div class="profile_settings_content_item_field">
                    <label for="name">Name</label>
                    <input type="text" id="name" value="{name}" placeholder="Your name"/>
                </div>
                <div class="profile_settings_content_item_field">
                    <label for="lastname">Lastname</label>
                    <input type="text" id="lastname" value="{lastname}" placeholder="Your lastname"/>
                </div>
            </div>
            <div class="profile_settings_content_item">
                <div class="profile_settings_content_item_field">
                    <label for="year">Date of Birth</label>

                    <div class="profile_settings_content_item_select_field">
                        <select id="year"></select>
                        <select id="month"></select>
                        <select id="day"></select>
                    </div>
                </div>
            </div>

            <div class="profile_settings_content_item">
                <div class="profile_settings_content_item_field">
                    <label for="email">Email address</label>
                    <input type="text" id="email" value="{email}" placeholder="Your email"/>
                </div>
                <div class="profile_settings_content_item_field">
                    <label for="email_notice">Email Notifications</label>

                    <label class="switch">
                        {if ('{email_notice}' == 'true')}
                            <input type="checkbox" id="email_notice" checked/>
                        {else}
                            <input type="checkbox" id="email_notice"/>
                        {/if}
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="profile_settings_content_item">
                <div class="profile_settings_content_item_field">
                    <label for="gender">Gender</label>
                    <select id="gender" style="width: auto">
                        <option value="Indicate gender" hidden>Indicate gender</option>
                        <option value="Female" {if('{gender}' == "Female")}selected{/if}>Female</option>
                        <option value="Male" {if('{gender}' == "Male")}selected{/if}>Male</option>
                        <option value="Prefer not to say" {if('{gender}' == "Prefer not to say")}selected{/if}>Prefer not to say</option>
                    </select>
                </div>
            </div>
            <div class="profile_settings_content_item">
                <div class="profile_settings_content_item_field">
                    <label for="facebook">Facebook</label>
                    <input type="text" id="facebook" value="{facebook}" placeholder="Your Facebook"/>
                </div>
                <div class="profile_settings_content_item_field">
                    <label for="twitter">Twitter</label>
                    <input type="text" id="twitter" value="{twitter}" placeholder="Your Twitter"/>
                </div>
                <div class="profile_settings_content_item_field">
                    <label for="instagram">Instagram</label>
                    <input type="text" id="instagram" value="{instagram}" placeholder="Your Instagram"/>
                </div>
                <div class="profile_settings_content_item_field">
                    <label for="youtube">Youtube Channel</label>
                    <input type="text" id="youtube" value="{youtube}" placeholder="Your Youtube"/>
                </div>
                <div class="profile_settings_content_item_field">
                    <label for="telegram">Telegram</label>
                    <input type="text" id="telegram" value="{telegram}" placeholder="Your Telegram"/>
                </div>
                <div class="profile_settings_content_item_field">
                    <label for="vk">VK</label>
                    <input type="text" id="vk" value="{vk}" placeholder="Your VK"/>
                </div>
                <div class="profile_settings_content_item_field">
                    <label for="github">GitHub</label>
                    <input type="text" id="github" value="{github}" placeholder="Your GitHub"/>
                </div>
                <div class="profile_settings_content_item_field">
                    <label for="website">Website</label>
                    <input type="text" id="website" value="{website}" placeholder="Your website"/>
                </div>
            </div>
            <div class="profile_settings_content_item">
                <div class="profile_settings_content_item_field">
                    <label for="password">Password</label>
                    <input type="password" id="password" placeholder="Change your password"/>

                    <div class="change_password">
                        <input type="password" id="password_repeat" placeholder="Confirm your password">
                    </div>
                </div>
            </div>
            <div class="profile_settings_content_item">
                <div class="profile_settings_content_item_field very_field">
                    <div class="profile_settings_content_item_field_very_field_title">
                        <label for="very">Verification Badge</label>
                        {include file='elements/verification/verification.tpl'}
                    </div>
                    {if ('{id}' == '{user_id}')}
                        {if ('{is_very}' != 'true')}
                            <button disabled>You are not verified</button>
                        {else}
                            <button disabled>You are verified</button>
                        {/if}
                    {else}
                        {if (is_worthy('f'))}
                            {if ('{is_very}' == 'true')}
                                <button onclick="unvery_user({id}, $(this))">Unverify user</button>
                            {else}
                                <button onclick="very_user({id}, $(this))">Verify user</button>
                            {/if}
                        {else}

                            {if ('{is_very}' != 'true')}
                                <button onclick="send_very_request($(this))">Send verification request</button>
                            {else}
                                {if ('{is_very_request}' == 'true')}
                                    <button onclick="cancel_very_request({id})">Request has been sent</button>
                                {else}
                                    <button disabled>You are verified</button>
                                {/if}
                            {/if}
                        {/if}
                    {/if}
                    <span class="very_result"></span>
                </div>
            </div>
            <div class="profile_settings_content_item">
                <div class="profile_settings_content_item_field account_delete_field">
                    <label>Account</label>
                    {if ('{deleted}' == 'true')}
                        <button onclick="restore_user({id}, $(this))">Restore account</button>
                    {else}
                        <button onclick="call_modal('delete_account_alert'{if(is_worthy('g'))}, {id: {id}}{/if})">
                            Account delete
                        </button>
                    {/if}
                </div>
            </div>
        </div>

        <div class="profile_settings_footer">
            <button onclick="load_template('profile', {id: {id}})">Cancel</button>
            <button class="save_settings" onclick="save_profile_setting({if(is_worthy('f'))}{id}{/if})">Save</button>
            <span style="display: block; margin-top: 0.5rem" class="save_settings_msg"></span>
        </div>
    </div>

    {if('{id}' != '{user_id}' && (is_worthy('f') && is_worthy('g')))}
        <div class="profile_data_container">
            <div class="profile_data_content">
                <div class="profile_data_header">
                    <span>User information</span>
                </div>

                <div class="profile_user_data">
                    <table class="table table-striped table-bordered table-hover m-0">
                        <tr>
                            <td>Registration date</td>
                            <td>{reg_date}</td>
                        </tr>
                        <tr>
                            <td>Email notifications</td>
                            <td>
                                {if('{email_notice}' == 'true')}
                                    <p class="text-success mb-0">Enabled</p>
                                {else}
                                    <p class="text-danger mb-0">Disabled</p>
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td>Registration IP</td>
                            <td>
                                {if('{reg_ip}' == '127.0.0.1')}

                                {else}
                                    {reg_ip}
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td>Last IP</td>
                            <td>
                                {if('{ip}'=='127.0.0.1')}
                                    Unknown
                                {else}
                                    {ip}
                                {/if}
                            </td>
                        </tr>
                        {if('{ip}'!='127.0.0.1')}
                            <td>Location</td>
                            <td id="place">
                                Unknown
                            </td>
                            <script>
                                $.getJSON('//api.sypexgeo.net/json/{ip}', function (resp) {
                                    $('#place').html(resp.country.name_en + ', ' + resp.region.name_en + ', ' + resp.city.name_en);
                                });
                            </script>
                        {/if}
                    </table>
                </div>
            </div>
        </div>
    {/if}
</div>

<script>
    $(function () {
        const date = new Date();
        const date_diff = date.getFullYear() - (date.getFullYear() - 123);

        for (let i = 0; i < date_diff; i++) {
            $('#year').append(`<option value="${date.getFullYear() - i}">${date.getFullYear() - i}</option>`);
        }

        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
        for (let i = 0; i < 12; i++) {
            $('#month').append(`<option value="${i + 1}">${months[i]}</option>`)
        }

        $('#month').on('change', function () {
            date.setMonth(+(this.value));
            date.setDate(0);
            const days = date.getDate(), opts = [];

            for (let i = 0; i < days; i++) {
                opts.push($('<option />', {
                    text: i + 1,
                    value: i + 1,
                }));
            }
            $('#day').html(opts);
        }).trigger('change');

        $('.change_password').hide();
        $('#password').on('keyup', function () {
            if ($(this).val().length > 0) {
                $('.change_password').show('fast');
            } else {
                $('.change_password').hide('fast');
            }
        });

        if ('{year}' !== '') {
            $('#year').val('{year}');
        }

        if ('{month}' !== '') {
            $('#month').val('{month}');
        }

        if ('{day}' !== '') {
            $('#day').val('{day}');
        }
    });
</script>