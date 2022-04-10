function create_material(data, dataType = 0) {
    let material = '';
    if (dataType === 0) {
        $.each(data, function (key, value) {
            material = material + key + '=' + encodeURIComponent(value) + '&';
        });
        material.substring(0, material.length - 1);
    } else {
        material = new FormData();
        $.each(data, function (key, value) {
            material.append(key, value);
        });
    }
    return material;
}


// перезагрузка страницы
function reset_page() {
    location.reload();
}

// переход на страницу по url
function go_to(link) {
    location.href = link;
}

// получение домашнего url
function get_url() {
    return "https://" + location.host + "/messenger/";
}

// отправка ajax-запроса на сервер и получение ответа
function send_query(path, data, callback, data_type = 'json', method = "POST") {
    data["phpaction"] = '1';
    data["token"] = $("#token").val();

    $.ajax({
        url: path,
        type: method,
        data: data,
        dataType: data_type,
        success: function (data) {
            callback(data);
        }
    });

    clear_data();
}

function send_image_query(path, data, callback, data_type = 'json', method = "POST") {
    data.append("phpaction", "1");
    data.append("token", $("#token").val());

    $.ajax({
        url: path,
        type: method,
        data: data,
        dataType: data_type,
        processData: false,
        contentType: false,
        success: function (data) {
            callback(data);
        }
    });

    // clear_data();
}

// вызов метода при нажатии на кнопку
function keyboard_event(input, func) {
    // $(input).keydown(function (event) {
    //     if (event.which === 13 && !event.shiftKey) {
    //         event.preventDefault();
    //         eval(func);
    //     }
    // });

    $(input).keyup(function (event) {
        eval(func);
    });
}

function shuffle_array(array) {
    let currentIndex = array.length, randomIndex;

    while (currentIndex !== 0) {

        randomIndex = Math.floor(Math.random() * currentIndex);
        currentIndex--;

        [array[currentIndex], array[randomIndex]] = [
            array[randomIndex], array[currentIndex]];
    }

    return array;
}

function copy_to_clipboard(link, element) {
    navigator.clipboard.writeText(link);

    const text = $(element).find('span').text();

    $(element).find('span').text('Copied');
    setTimeout(function () {
        $(element).find('span').text(text);
    }, 3000);
}

function open_playlist_window() {
    setTimeout(() => {
        $('.check_playlist').addClass('active');
        $('.check_playlist').removeClass('inactive');
        // add +50 margin to playlist window

        // get window y position
        let window_y = $('body main > .container').offset().top;
        const head_panel_height = $('.head_panel').height();
        const margin_top = head_panel_height - (window_y - 80);
        // const margin_top = 50;
        const height = head_panel_height - (margin_top - 120);

        console.log(head_panel_height, margin_top, height);

        if (window_y >= 30) {
            $('.check_playlist.active').css('margin-top', 'calc(100vh + '+margin_top+'px)');
            $('.check_playlist.active').css('height', 'calc(100vh - '+height+'px)');
        } else {
            $('.check_playlist.active').css('height', 'calc(100vh - 90px)');
        }
        // $('body main').css('overflow-y', 'hidden');
    }, 0)
}

function close_playlist_window() {
    $('.check_playlist').addClass('inactive');
    $('.check_playlist').removeClass('active');
    setTimeout(function () {
        $('.check_playlist').remove();
    }, 500);
    $('body main').css('overflow-y', 'overlay');
}

function set_title(element, title) {
    // change dynamic title of tooltip
    $(element).attr('data-bs-original-title', title);
    $(element).attr('title', title);
    // $(element).tooltip('show');
    // element.attr('title', title).attr('data-bs-original-title', title).tooltip('update')
}

function open_noty_window() {
    if ($('.notification_window').hasClass('inactive')) {
        $('.notification_window').removeClass('inactive');
        $('.notification_window').addClass('active');
    } else {
        $('.notification_window').removeClass('active');
        $('.notification_window').addClass('inactive');
    }
}

function remove_duplicate_characters(string) {
    return string
        .split('')
        .filter(function (item, pos, self) {
            return self.indexOf(item) === pos;
        })
        .join('');
}

function format_seconds_as_time(secs) {
    const hr = Math.floor(secs / 3600);
    let min = Math.floor((secs - (hr * 3600)) / 60);
    let sec = Math.floor(secs - (hr * 3600) - (min * 60));

    if (min > 10) {
        min = "0" + min;
    }
    if (sec < 10) {
        sec = "0" + sec;
    }

    return min + ':' + sec;
}

function redirect_blank_page(url, element) {
    $(element).click(function (e) {
        if (e.target !== this) {
            return;
        }
        window.open(url, '_blank')
    });
}

function get_duration(src, cb) {
    var audio = new Audio();
    $(audio).on("loadedmetadata", function(){
        cb(audio.duration);
    });
    audio.src = src;
}