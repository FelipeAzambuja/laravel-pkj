libpkj.intervals = [];
libpkj.timers = [];
libpkj.data = [];
libpkj.work = false;
libpkj.confirm = null;

libpkj.onload = function () {
    console.log('Carregado Arquivo');
    if (typeof $.fn.ajaxForm == 'undefined') {
        console.log('Não foi encontrado o jquery');
        var jquery = document.createElement("script");
        jquery.type = "text/javascript";
        jquery.src = "https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js";
        jquery.onload = function () {
            libpkj.onload();
        };
        document.head.insertBefore(jquery, document.head.getElementsByTagName("script")[0]);
        return true;
    }

    $(document).ready(function () {
        var update = function () {
            if(libpkj.work){
                return;
            }
            libpkj.work = true;
            $('input,select,a,button,img,textarea,form,li').each(function (i, e) {
                libpkj.bindElement(e);
            });
            libpkj.work = false;
        };
        update();
        setInterval(update,1000);

    });

};

libpkj.bindElement = function (e) {
    var je = $(e);
    if (je.attr('bind') !== undefined) {
        return;
    }
    je.attr('bind', true);
    var event_list = [
        'scroll',
        'click',
        'dblclick',
        'mousedown',
        'mouseup',
        'mousemove',
        'mouseover',
        'mouseout',
        'mouseenter',
        'mouseleave',
        'load',
        'resize',
        'keydown',
        'keypress',
        'keyup',
        'blur',
        'focus',
        'focusin',
        'focusout',
        'change',
        'select',
        'submit'
    ];
    for (const event_name of event_list) {
        var event = je.attr(event_name);
        if (event !== undefined) {
            if (e.tagName === 'FORM' && event_name === 'load') {
                libpkj._element_call(e, event_name);
            } else {
                je.on(event_name, function (ev) {
                    ev.preventDefault();
                    libpkj._element_call(e, event_name);
                });
            }

        }
    }
};

libpkj.call = function (f, data = [], page) {
    if (page === undefined || page === null || page === '') {
        page = location.href.replace(location.search, '');
    }
    $.ajaxSetup({
        xhrFields: {
            withCredentials: true
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
        },
        cache: false
    });
    var url = '';
    if (f[0] === '/') {
        url = libpkj.base + '/' + f;
    } else {
        url = page + '/' + f;
    }
    for (const key in libpkj.data) {
        const element = libpkj.data[key];
        data['vars_' + key] = element;
    }
    url = url.replace(/#/g, '');
    data = Object.assign({}, data);
    $.post(url, data, function (response) {
        eval(response);
    });
}

libpkj._element_call = function (e, event_name) {
    var jqe = $(e);
    var form = jqe.closest('form');
    var page = jqe.attr('page');
    var data = [];
    if (page === undefined) {
        page = location.href.replace(location.search, '');
    }
    $.ajaxSetup({
        xhrFields: {
            withCredentials: true
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
        },
        cache: false
    });
    var cmd = jqe.attr(event_name);
    if (cmd.indexOf('(') > -1) {
        var real_cmd = cmd.split('(')[0];
        var tmp = cmd.split('(')[1];
        var attr = tmp.split(')')[0];
        attr = attr.split(',');
        for (var i = 0; i < attr.length; i++) {
            data['post_' + i] = attr[i];
        }
        cmd = real_cmd;
    }
    for (var i in window) {
        if (window[i] !== null) {
            try {
                if (window[i].$data !== undefined) {
                    data['vue_' + i] = JSON.parse(JSON.stringify(window[i].$data));
                }
            } catch (e) {

            } finally {

            }
        }
    }
    for (const key in libpkj.data) {
        const element = libpkj.data[key];
        data['vars_' + key] = element;
    }
    var handler = function (response) {
        if (jqe.attr('lock') !== undefined) {
            jqe.removeClass('disabled');
        }
    };
    var url = '';
    if (cmd[0] === '/') {
        url = libpkj.base + '/' + cmd;
    } else {
        url = page + '/' + cmd;
    }
    url = url.replace(/#/g, '');
    data = Object.assign({}, data);
    $(form).ajaxSubmit({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'script',
        success: handler
    });
};

if (typeof jQuery == 'undefined') {
    var jquery = document.createElement("script");
    jquery.type = "text/javascript";
    jquery.src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js";
    jquery.onload = function () {
        libpkj.onload();
    };
    document.head.insertBefore(jquery, document.head.getElementsByTagName("script")[0]);
} else {
    $(function () {
        libpkj.onload();
    });
}


