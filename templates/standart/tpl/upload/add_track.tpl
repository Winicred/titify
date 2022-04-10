<div class="track-wrapper">
    <div class="track-info-container">
        <div class="track_header_container">
            <div class="file-upload">
                <button class="file-upload-btn" type="button" onclick="$('.file-upload-input').trigger( 'click' )"
                        data-bs-toggle="tooltip" title="Click to add image for track">Add
                    Image
                </button>

                <div class="image-upload-wrap" data-bs-toggle="tooltip"
                     title="Drag and drop a file or select add Image">
                    <input class="file-upload-input" type='file' id="image_input" onchange="readURL(this);"
                           accept="image/*"/>
                    <div class="drag-text">
                        <h3>Drag and drop a file or select add Image</h3>
                    </div>
                </div>
                <div class="file-upload-content">
                    <img class="file-upload-image" id="track_image" src="#" data-src="" alt="your image"/>
                    <div class="loader2">
                        <div class="loader-wheel"></div>
                        <div class="loader-text"></div>
                    </div>
                    <div class="image-title-wrap">
                        <button type="button" onclick="removeUpload()" class="remove-image">Remove <span
                                    class="image-title">Uploaded Image</span></button>
                    </div>
                </div>
            </div>
            <div class="inputs">
                <div class="track-name-container">
                    <i class="fa-solid fa-file-signature" style="margin-left: -40px; margin-right: 20px;"></i>
                    <div class="form__group field">
                        <input type="input" class="form__field" placeholder="Track name" name="track-name"
                               id='track-name'
                               data-bs-toggle="tooltip" title="Your track name" required/>
                        <label for="track-name" class="form__label">Track name</label>
                    </div>
                </div>
                <div class="track-description-container">
                    <i class="fa-solid fa-file-signature" style="margin-left: -40px; margin-right: 20px;"></i>
                    <div class="form__group field">
                        <input type="input" class="form__field" placeholder="Description" name="description"
                               data-bs-toggle="tooltip" title="Your track description"
                               id='description'
                               required/>
                        <label for="track-name" class="form__label">Description</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="track-info-footer">
            <div class="track_genre_container">
                <div class="wrapper">
                    <div class="title">
                        Select track genre
                    </div>
                    <div class="select_wrap">
                        <ul class="default_option" id="default_option">
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>None</p>
                                </div>
                            </li>
                        </ul>
                        <ul class="select_ul">
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>None</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Custom</p>
                                </div>
                            </li>
                            <hr>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Alternative rock</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Ambient</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Classical</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Country</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Dance & EDM</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Dancehall</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Deep House</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Disco</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Drum & Bass</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Dubstep</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Electronic</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Folk & Singer-Songwriter</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Hip-hop & Rap</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>House</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Indie</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Jazz & Blues</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Latin</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Metal</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Piano</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Pop</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>R&B & Soul</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Reggae</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Reggaeton</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Rock</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Soundtrack</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Techno</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Trance</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Trap</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>Triphop</p>
                                </div>
                            </li>
                            <li>
                                <div class="option headphones">
                                    <div class="icon"></div>
                                    <p>World</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
            <div class="track_private_container" id="track_private_container">
                <div class="form-group">
                    <i class="fa-solid fa-unlock" style="margin-right: 21%; margin-left: -21%;"></i>
                    <div class="container_switch">
                        <div class="toggle">
                            <input type="radio" id="choice1" name="choice" value="Private" data-bs-toggle="tooltip"
                                   title="Private track (no one can see it except you)">
                            <label for="choice1">Private</label>
                            <input type="radio" id="choice2" name="choice" value="Public" data-bs-toggle="tooltip"
                                   title="Public track (everyone can see it)">
                            <label for="choice2">Public</label>
                            <div id="flap"><span class="content">Public</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button onclick="upload_track()" class="btn btn-primary pull-right" data-bs-toggle="tooltip"
                            title="Click to upload track">Upload
                    </button>
                </div>
            </div>
        </div>
    </div>
    <section>
        <div class="music-upload">
            <div class="music-upload-wrap" data-bs-toggle="tooltip"
                 title="Drag and drop a file or select add Music file">
                <input class="music-upload-input" type='file' data-src="" id="track_input"
                       onchange="readURL_music(this);"
                       accept=".mp3,audio/*"/>
                <div class="drag-text">
                    <h3>Drag and drop a file or select add Music file</h3>
                    <button class="music-upload-btn" type="button"
                            onclick="$('.music-upload-input').trigger( 'click' )">Add
                        Music file
                    </button>
                </div>
            </div>
            <div class="music-upload-content">
                <img class="file-upload-music" src="#" alt="your music" data-bs-toggle="tooltip"
                     title="Your track cover preview"/>
                <div class="loader">
                    <div class="loader-wheel"></div>
                    <div class="loader-text"></div>
                </div>
                <div class="music-title-wrap">
                    <button type="button" onclick="removeUpload_music()" class="remove-music">Remove <span
                                class="music-title">Uploaded Music</span></button>
                </div>
            </div>
        </div>
    </section>
</div>

<style>

    .btn-primary {
        color: #fff;
        background-color: #A74FFF;
        border-color: #A74FFF;
    }

    .btn-primary:hover, .btn-primary:active, .btn-primary:focus {
        color: #fff;
        background-color: #A74FFF;
        border-color: #A74FFF;
        box-shadow: 0 0 0 0.3rem rgba(167, 79, 255, 0.25) !important
    }

    .file-upload {
        width: 400px;
        margin: 0 auto;
        padding: 20px;
    }

    .file-upload-btn {
        width: 100%;
        margin: 0;
        color: #fff;
        background: #A74FFF;
        border: none;
        padding: 10px;
        border-radius: 4px;
        border-bottom: 4px solid #5701AE;
        transition: all .2s ease;
        outline: none;
        text-transform: uppercase;
        font-weight: 700;
    }

    .file-upload-btn:hover {
        background: #A74FFF;
        color: #ffffff;
        transition: all .2s ease;
        cursor: pointer;
    }

    .file-upload-btn:active {
        border: 0;
        transition: all .2s ease;
    }

    .file-upload-content {
        display: none;
        text-align: center;
    }

    .file-upload-input {
        position: absolute;
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        outline: none;
        opacity: 0;
        cursor: pointer;
    }

    .image-upload-wrap {
        margin-top: 20px;
        border: 4px dashed #A74FFF;
        position: relative;
    }

    .image-dropping,
    .image-upload-wrap:hover {
        background-color: #A74FFF;
        border: 4px dashed #ffffff;
    }

    .image-title-wrap {
        padding: 0 15px 15px 15px;
        color: #222;
    }

    .drag-text {
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
    }

    .drag-text h3 {
        font-weight: 100;
        text-transform: uppercase;
        color: #5701AE;
        padding: 60px 0;
    }

    .file-upload-image {
        display: none;
        height: 250px;
        width: 250px;
        margin: auto;
        padding: 20px;
    }

    .remove-image {
        width: 200px;
        margin: 0;
        color: #fff;
        opacity: 0.25;
        background: #cd4535;
        border: none;
        padding: 10px;
        border-radius: 4px;
        border-bottom: 4px solid #b02818;
        transition: all .2s ease;
        outline: none;
        text-transform: uppercase;
        font-weight: 700;
    }

    .remove-image:hover {
        background: #c13b2a;
        color: #ffffff;
        transition: all .2s ease;
        cursor: pointer;
    }

    .remove-image:active {
        border: 0;
        transition: all .2s ease;
    }

    section {
        margin: auto;
    }

    .music-upload {
        background-color: #ffffff;
        width: 800px;
        margin: 0 auto;
    }

    .music-upload-btn {
        width: 50%;
        margin: 0;
        color: #fff;
        background: #A74FFF;
        border: none;
        padding: 10px;
        border-radius: 4px;
        outline: none;
        text-transform: uppercase;
        font-weight: 700;
    }

    .music-upload-btn:hover {
        color: #ffffff;
        transition: all .2s ease;
        cursor: pointer;
    }

    .music-upload-content {
        display: none;
        text-align: center;
    }

    .music-upload-input {
        position: absolute;
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        outline: none;
        opacity: 0;
        cursor: pointer;
    }

    .music-upload-wrap {
        margin-top: 20px;
        height: 400px;
        border: 4px dashed #A74FFF;
        position: relative;
    }

    .music-dropping,
    .music-upload-wrap:hover {
        background-color: #A74FFF;
        border: 4px dashed #ffffff;
    }

    .music-dropping,
    .music-upload-wrap:hover .music-upload-btn {
        background-color: #FFFFFF;
        color: #A74FFF;
    }

    .music-title-wrap {
        padding: 0 15px 15px 15px;
        color: #222;
    }

    .file-upload-music {
        display: none;
        max-height: 100px;
        max-width: 100px;
        margin: auto;
        padding: 20px;
    }

    .remove-music {
        width: 200px;
        margin: 0;
        color: #fff;
        opacity: 0.25;
        background: #cd4535;
        border: none;
        padding: 10px;
        border-radius: 4px;
        border-bottom: 4px solid #b02818;
        transition: all .2s ease;
        outline: none;
        text-transform: uppercase;
        font-weight: 700;
    }

    .remove-music:hover {
        background: #c13b2a;
        color: #ffffff;
        transition: all .2s ease;
        cursor: pointer;
    }

    .remove-music:active {
        border: 0;
        transition: all .2s ease;
    }

    .loader {
        width: 60px;
        margin: auto;
        padding-top: 20px;
        padding-bottom: 20px;
    }

    .loader2 {
        width: 60px;
        margin: auto;
        padding-top: 20px;
        padding-bottom: 20px;
    }

    .loader-wheel {
        animation: spin 1s infinite linear;
        border: 2px solid rgba(30, 30, 30, 0.5);
        border-left: 4px solid #fff;
        border-radius: 50%;
        height: 50px;
        margin-bottom: 10px;
        width: 50px;
    }

    .loader-text {
        color: #282828;
        font-family: arial, sans-serif;
    }

    .loader-text:after {
        content: 'Loading';
        animation: load 2s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes load {
        0% {
            content: 'Loading';
        }
        33% {
            content: 'Loading.';
        }
        67% {
            content: 'Loading..';
        }
        100% {
            content: 'Loading...';
        }
    }

    /*-----------------------------------*/

    .track-wrapper {
        display: flex;
        width: 75%;
        flex-direction: row;
        justify-content: space-evenly;
        margin: auto;
    }

    .track_header_container {
        display: flex;
        flex-direction: row;

        --x: 50%;
        --y: 50%;
        justify-content: space-around;
        position: relative;
        appearance: none;
        padding: 1em 2em;
        outline: none;
        border-radius: 10px;

        border: 3px solid transparent;
        background: linear-gradient(#ffffff, #ffffff) padding-box, radial-gradient(farthest-corner at var(--x) var(--y), #c083ff, #42008a) border-box;
    }

    .track-name-container {
        display: flex;
        flex-direction: row;
        align-items: baseline;
    }

    .track-description-container {
        display: flex;
        flex-direction: row;
        align-items: baseline;
    }

    .track-info-footer {
        display: flex;
        flex-direction: row;
        justify-content: space-evenly;
        align-items: baseline;
    }

    .track-info-container {
        display: none;
        flex-direction: column;
        justify-content: start;
        margin: 0;
        font-family: sans-serif;
    }

    .track_genre_container {
        margin-top: 2rem;
    }

    .track_private_container {
        display: flex;
        justify-content: center;
        padding-top: 2rem;
    }

    .inputs {
        padding: 0 0 0 60px;
        width: 600px;
        display: flex;
        flex-direction: column;
        justify-content: space-evenly;
    }

    .container_switch {
        perspective: 800px;
    }

    .form-group {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }

    .toggle {
        position: relative;
        border: solid 3px #A74FFF;
        border-radius: 55px;
        transition: transform cubic-bezier(0, 0, 0.30, 2) .4s;
        transform-style: preserve-3d;
        perspective: 800px;
    }

    .toggle > input[type="radio"] {
        display: none;
    }

    .toggle > #choice1:checked ~ #flap {
        transform: rotateY(-180deg);
    }

    .toggle > #choice1:checked ~ #flap > .content {
        transform: rotateY(-180deg);
    }

    .toggle > #choice2:checked ~ #flap {
        transform: rotateY(0deg);
    }

    .toggle > label {
        display: inline-block;
        /* min-width: 170px; */
        padding: 10px;
        font-size: 20px;
        text-align: center;
        color: #A74FFF;
        cursor: pointer;
    }

    .toggle > label,
    .toggle > #flap {
        font-weight: bold;
        text-transform: capitalize;
    }

    .toggle > #flap {
        position: absolute;
        top: calc(0px - 3px);
        left: 50%;
        height: calc(100% + 3px * 2);
        width: 50.5%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 20px;
        background-color: #A74FFF;
        border-top-right-radius: 55px;
        border-bottom-right-radius: 55px;
        transform-style: preserve-3d;
        transform-origin: left;
        transition: transform cubic-bezier(0.4, 0, 0.2, 1) .5s;
    }

    .toggle > #flap > .content {
        color: #333;
        transition: transform 0s linear .25s;
        transform-style: preserve-3d;
    }

    /* ---------------------------------------------------- */

    .form__group {
        position: relative;
        padding: 15px 0 0;
        width: 100%;
    }

    .form__field {
        font-family: inherit;
        width: 100%;
        border: 0;
        border-bottom: 2px solid #9b9b9b;
        outline: 0;
        font-size: 1rem;
        color: #2f2f2f;
        padding: 7px 0;
        background: transparent;
        transition: border-color 0.2s;
    }

    .form__field::placeholder {
        color: transparent;
    }

    .form__field:placeholder-shown ~ .form__label {
        font-size: 1.3rem;
        cursor: text;
        top: 20px;
    }

    .form__label {
        position: absolute;
        top: 0;
        display: block;
        transition: 0.2s;
        font-size: 1rem;
        color: #9b9b9b;
    }

    .form__field:focus {
        padding-bottom: 6px;
        font-weight: 700;
        border-width: 3px;
        border-image: linear-gradient(to right, #5701AE, #A74FFF);
        border-image-slice: 1;
    }

    .form__field:focus ~ .form__label {
        position: absolute;
        top: 0;
        display: block;
        transition: 0.2s;
        font-size: 1rem;
        color: #5701AE;
        font-weight: 700;
    }

    /* reset input */
    .form__field:required, .form__field:invalid {
        box-shadow: none;
    }

    /*-------------------------------------------------------------------------*/

    * {
        box-sizing: border-box;
        list-style: none;
    }

    .wrapper .title {
        font-weight: 700;
        font-size: 24px;
        color: #232323;
    }

    .select_wrap {
        width: 225px;
        margin-top: 15px;
        position: relative;
        user-select: none;
    }

    .select_wrap .default_option {
        background: rgba(167, 79, 255, 0.15);
        border-radius: 5px;
        position: relative;
        cursor: pointer;
    }

    .select_wrap .default_option li {
        padding: 10px 20px;
    }

    ol, ul {
        padding-left: 0rem;
    }

    dl, ol, ul {
        margin-top: 0.5rem;
    }


    .col-md-12 {
        flex: 0 0 auto;
        width: 10%;
    }

    .row {
        justify-content: flex-end;
        margin-top: 5%;
    }

    .row > * {
        flex-shrink: 0;
        /* width: 100%; */
        max-width: 100%;
    }

    .select_wrap .default_option:before {
        content: "";
        position: absolute;
        top: 18px;
        right: 18px;
        width: 6px;
        height: 6px;
        border: 2px solid;
        border-color: transparent transparent #555555 #555555;
        transform: rotate(-45deg);
    }

    .select_wrap .select_ul {
        list-style: none;
        position: absolute;
        top: 55px;
        left: 0;
        width: 100%;
        background: rgba(167, 79, 255, 0.05);
        border-radius: 5px;
        display: none;
        max-height: 240px;
        overflow: hidden auto;
    }

    .select_wrap .select_ul li {
        padding: 10px 20px;
        cursor: pointer;
        display: block;
        box-sizing: border-box;
    }

    .select_wrap .select_ul li:first-child:hover {
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }

    .select_wrap .select_ul li:last-child:hover {
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
    }

    .select_wrap .select_ul li:hover {
        background: rgba(167, 79, 255, 0.25);
    }

    .select_wrap .option {
        display: flex;
        align-items: center;
    }

    .select_wrap .option .icon {
        background: url('https://i.imgur.com/zwwNAHG.png') no-repeat 0 0;
        width: 32px;
        height: 32px;
        padding-right: 32px;
        margin-right: 15px;
    }

    .select_wrap .option.headphones .icon {
        background-position: 0 0;
    }

    .select_wrap.active .select_ul {
        display: block;
    }

    .select_wrap.active .default_option:before {
        top: 25px;
        transform: rotate(-225deg);
    }

</style>

<script>
    NProgress.start();

    setTimeout(function () {
        NProgress.done();
    }, 2000);

    function readURL(input) {
        const image = input.files[0];

        if (image) {
            const reader = new FileReader();

            reader.onload = function (event) {
                $('.image-upload-wrap').hide();

                $('.file-upload-image').attr('src', event.target.result);
                $('.file-upload-content').show();

                $('.image-title').html(image.name);
            };

            let data = new FormData();
            data.append('image', image);
            data.append('token', $('#token').val());
            data.append('phpaction', "1");
            data.append('upload_image_file', "1");

            $.ajax({
                url: 'ajax/actions_auth.php',
                type: 'POST',
                data: data,
                cache: false,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function (result) {
                    console.log(result)
                    if (result.status === 'success') {
                        $('.file-upload-image').css('display', 'block');
                        $('.remove-image').css('opacity', '1');
                        $('.loader2').hide();
                        $('.file-upload-image').attr('data-src', result.data);
                    } else {
                        alert('Error with upload image file');
                        // Если ошибка поставить фотографию default.png в каталоге files/track_covers/
                        $('.file-upload-image').attr('src', 'files/track_covers/default.png');
                    }
                }
            });

            reader.readAsDataURL(image);

        } else {
            removeUpload();
        }
    }

    function removeUpload() {
        $('.file-upload-input').replaceWith($('.file-upload-input').clone());
        $('.file-upload-content').hide();
        $('.image-upload-wrap').show();
    }

    $('.image-upload-wrap').bind('dragover', function () {
        $('.image-upload-wrap').addClass('image-dropping');
    });
    $('.image-upload-wrap').bind('dragleave', function () {
        $('.image-upload-wrap').removeClass('image-dropping');
    });

    // Next script is only for music upload

    var fileTypes = [
        'audio/mp3',
        'audio/mpeg',
        'audio/ogg',
        'audio/wav',
        'audio/x-wav',
        'audio/wave',
        'audio/x-pn-wav',
        'audio/x-mpeg',
        'audio/x-mpeg-3',
        'audio/mpeg3',
    ]

    function validFileType(file) {
        for (var i = 0; i < fileTypes.length; i++) {
            if (file.type === fileTypes[i]) {
                return true;
            }
        }

        return false;
    }

    function readURL_music(input) {
        const file = input.files[0];

        const max_ize = 500 * 1024 * 1024;

        if (validFileType(file) && file.size < max_ize) {
            const str_file_type = (file.type).replace('audio/', '');
            const reader_music = new FileReader();

            reader_music.onload = function (e) {
                $('.music-upload-wrap').hide();
                $('.music-upload-content').show();

                if (str_file_type === 'mpeg') {
                    $('#track-name').val((file.name).replace('.mp3', ''));
                } else {
                    $('#track-name').val((file.name).replace('.' + str_file_type, ''));
                }

                $('.music-title').html(file.name);

                let data = new FormData();
                data.append('file', file);
                data.append('token', $('#token').val());
                data.append('phpaction', "1");
                data.append('upload_track_file', "1");

                $.ajax({
                    url: 'ajax/actions_auth.php',
                    type: 'POST',
                    data: data,
                    cache: false,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function (result) {
                        console.log(result)
                        if (result.status === 'success') {
                            $('.file-upload-music').css('display', 'block');
                            $('.remove-music').css('opacity', '1');
                            $('.file-upload-music').attr('src', 'files/covers_for_track_types/' + str_file_type + '_icon.png');
                            $('.loader').hide();
                            $('.music-upload').hide();
                            $('.track-info-container').css('display', 'flex');
                            $('.music-upload-input').attr('data-src', result.data);
                        } else {
                            alert('Error with upload music file');
                        }
                    }
                });
            }

            reader_music.readAsDataURL(file);
        } else {
            alert('File size is too large or not a music file, please ensure you are uploading a music file of less than ' + max_ize + 'MB');
            removeUpload_music();
        }
    }

    function removeUpload_music() {
        $('.music-upload-input').replaceWith($('.music-upload-input').clone());
        $('.music-upload-content').hide();
        $('.music-upload-wrap').show();
    }

    $('.music-upload-wrap').bind('dragover', function () {
        $('.music-upload-wrap').addClass('music-dropping');
    });
    $('.music-upload-wrap').bind('dragleave', function () {
        $('.music-upload-wrap').removeClass('music-dropping');
    });
</script>

<script>
    const st = {};

    st.flap = document.querySelector('#flap');
    st.toggle = document.querySelector('.toggle');

    st.choice1 = document.querySelector('#choice1');
    st.choice2 = document.querySelector('#choice2');

    st.flap.addEventListener('transitionend', () => {

        if (st.choice1.checked) {
            st.toggle.style.transform = 'rotateY(-15deg)';
            setTimeout(() => st.toggle.style.transform = '', 400);
        } else {
            st.toggle.style.transform = 'rotateY(15deg)';
            setTimeout(() => st.toggle.style.transform = '', 400);
        }

    })

    st.clickHandler = (e) => {

        if (e.target.tagName === 'LABEL') {
            setTimeout(() => {
                st.flap.children[0].textContent = e.target.textContent;
            }, 250);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        st.flap.children[0].textContent = st.choice2.nextElementSibling.textContent;
    });

    document.addEventListener('click', (e) => st.clickHandler(e));
</script>

<script>
    $(".default_option").click(function () {
        $(this).parent().toggleClass("active");
        document.getElementById('default_option').addEventListener("click", function () {
            $(".custom_input").remove();
        });
    })

    document.querySelector('.track_header_container').onmousemove = (e) => {

        const x = e.pageX - e.target.offsetLeft
        const y = e.pageY - e.target.offsetTop

        e.target.style.setProperty('--x', `${ x }px`)
        e.target.style.setProperty('--y', `${ y }px`)

    }

    $(".select_ul li").on('click', function () {
        var currentele = $(this).html();
        $(".default_option li").html(currentele);
        $(this).parents(".select_wrap").removeClass("active");
        if ($(currentele).find('p').text() == "Custom") {
            $(".select_wrap").append('<div class="form__group custom_input field"><input type="text" class="form__field" placeholder="Write your genre here..." name="custom_input" id="custom_input" required/><label for="custom_input" class="form__label">Custom genre</label></div>');
        } else {
            $(".custom_input").remove();
        }
    })
</script>