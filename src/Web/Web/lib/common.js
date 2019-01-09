$('.dialog .close').click(function () {
    $(this).parents('.dialog').hide();
});
$('.dialog .mask').click(function () {
    $(this).parents('.dialog').hide();
});
$('.dialog .close-btn').click(function () {
    $(this).parents('.dialog').hide();
});
var toastTimer;
function toast(text, t) {
    $('.toast').remove();
    clearTimeout(toastTimer);
    $('body').append("<div class='toast'>"+ text +"</div>");
    toastTimer = setTimeout(function () {
        $('.toast').remove();
    },t)
}

function randomString(len) {
    len = len || 32;
    var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
    var maxPos = $chars.length;
    var pwd = '';
    for (var i = 0; i < len; i++) {
        pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}

function loading(action, text) {
    $('.loading-mask').remove();
    if(action) {
        $('body').append("<div class='loading-mask'><div class='loading'><svg" +
            "   width=\"40px\" height=\"40px\" viewBox=\"0 0 40 40\" enable-background=\"new 0 0 40 40\">\n" +
            "  <path opacity=\"0.2\" fill=\"#FF6700\" d=\"M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946\n" +
            "    s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634\n" +
            "    c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z\"/>\n" +
            "  <path fill=\"#FF6700\" d=\"M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0\n" +
            "    C22.32,8.481,24.301,9.057,26.013,10.047z\">\n" +
            "    <animateTransform attributeType=\"xml\"\n" +
            "      attributeName=\"transform\"\n" +
            "      type=\"rotate\"\n" +
            "      from=\"0 20 20\"\n" +
            "      to=\"360 20 20\"\n" +
            "      dur=\"0.5s\"\n" +
            "      repeatCount=\"indefinite\"/>\n" +
            "    </path>\n" +
            "  </svg>"+ text +"</div></div>");
    }
}

function getQueryString(key) {
    var qs = location.search.substr(1), // 获取url中"?"符后的字串
        args = {}, // 保存参数数据的对象
        items = qs.length ? qs.split("&") : [], // 取得每一个参数项,
        item = null,
        len = items.length;

    for(var i = 0; i < len; i++) {
        item = items[i].split("=");
        var name = decodeURIComponent(item[0]),
            value = decodeURIComponent(item[1]);
        if(name) {
            args[name] = value;
        }
    }
    if(args.hasOwnProperty(key)) {
        return args[key]
    }else {
        return '';
    }
}
$('.topFixed').click(function () {
    $("html,body").finish().animate({"scrollTop":"0px"}, 400);
});

$(window).scroll(function () {
    var windowHeight = $(window).height();
    var scrollTop = $(window).scrollTop();
    if(scrollTop > (windowHeight * (2 / 3))) {
        $('.topFixed').show();
    }else {
        $('.topFixed').hide();
    }
});

window.APIHOST = 'http://pc-hde.ibangoo.com';