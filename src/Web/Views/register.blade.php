<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>注册</title>
    <link rel="stylesheet" type="text/css" href="http://at.alicdn.com/t/font_748879_kwzwugo4aj.css">
    <link rel="stylesheet" type="text/css" href="{{asset('web/style/style.css')}}">
    <script src="http://cdn.bootcss.com/jquery/2.2.1/jquery.min.js"></script>
</head>

<body>
<div class="dialog lg-dialog" style="display: block">
    <div class="mask"></div>
    <div class="content reg-lg" style="display: block;">
        <div class="close iconfont icon-guanbi"></div>
        <div class="dialog-head">
            <h3>欢迎注册</h3>
            <p>登录或注册即同意<a href="">《用户服务协议》</a> </p>
        </div>
        <div class="form">
            <div class="input input-qu">
                <input type="text" name="phone" placeholder="请输入手机号" class="phone">
                <p class="error"></p>
            </div>
            <div class="input input-line">
                <div class="yzm">
                    <input type="text" name="sms_code" placeholder="请输入验证码" class="ver">
                </div>
                <button class="getV">获取验证码</button>
                <p class="error"></p>
            </div>
            <div class="input pwd-input">
                <input type="password" name="password" placeholder="请输入密码" class="pass eth_register">
                <div class="view iconfont icon-icon-test4"></div>
                <p class="error"></p>
            </div>
        </div>
        <div class="submit-row">
            <button class="submit">注册</button>
            <div class="other">
                已有账号？<a href="{{ URL::route('user.get-login')}}" class="reg to-lg">去登录</a> 或 微信登录 <a href="javascript:void(0);" class="wx iconfont icon-icon-test10"></a>
            </div>
        </div>
    </div>
</div>
</body>
<script src="{{asset('web/lib/common.js')}}"></script>
<script>

    $('.view').click(function () {
        var input = $(this).siblings('input');
        var type = input.attr('type');
        if(type === 'password') {
            input.attr('type', 'text');
            $(this).addClass('view-active');
        }else {
            input.attr('type', 'password');
            $(this).removeClass('view-active');
        }
    });

    //按键注册  
    $(".submit").click(function () {
        register();
    });
    //回车注册
    $('.eth_register').keydown(function (event) {
        if (event.keyCode == 13) {
            register();
        }
    });

    function register() {
        var phone = $("input[name='phone']").val();
        if (!phone) {
            toast('请输入手机号', 2000);
            return;
        }
        var sms_code = $("input[name='sms_code']").val();
        if (!sms_code) {
            toast('请输入短信验证码', 2000);
            return;
        }
        var password = $("input[name='password']").val();
        if (!password) {
            toast('请输入密码', 2000);
            return;
        }
        loading(true, 'Loading...');
        $.ajax({
            type: "POST",
            url: "{{ URL::route('user.register')}}",
            data: {
                phone: phone,
                sms_code: sms_code,
                password: password,
            },
            dataType: "json",
            success: function (data) {
                loading(false, '');
                if(data.status==1){
                    toast(data.msg, 2000);
                }else{
                    toast(data.msg, 2000);
                }
            },
            error : function (msg ) {
                loading(false, '');
                var json=JSON.parse(msg.responseText);
                if(json.errors){
                    $.each(json.errors, function(idx, obj) {
                        toast(obj[0], 2000);
                        return false;
                    });
                }else{
                    toast(json.msg, 2000);
                }
            },
        }, "json");
    }

    $('.getV').click(function () {
        var phone = $("input[name='phone']").val();
        if (!phone) {
            toast('请输入手机号', 2000);
            return;
        }
        if ($(this).attr('data-send') == 'send') {
            return
        } else {
            var rat = $(this);
            $.ajax({
                type: "POST",
                url: "{{ URL::route('user.send-sms')}}",
                data: {
                    phone: phone,
                    type: 1,
                },
                dataType: "json",
                success: function (data) {
                    rat.attr('data-send', 'send').addClass('btn-send');
                    rat.text('重新发送60s');
                    var t = 59;
                    var _this = this;
                    var timer = setInterval(function () {
                        if (t < 1) {
                            clearInterval(timer);
                            rat.attr('data-send', '').text('获取验证码').removeClass('btn-send');
                            ;
                        } else {
                            rat.text('重新发送' + t + 's');
                            t--;
                        }
                    }, 1000)
                },
                error : function (msg ) {
                var json=JSON.parse(msg.responseText);
                if(json.errors){
                    $.each(json.errors, function(idx, obj) {
                        toast(obj[0], 2000);
                        return false;
                    });
                }else{
                    toast(json.msg, 2000);
                }
            },
            }, "json");
        }
    })
</script>
</html>