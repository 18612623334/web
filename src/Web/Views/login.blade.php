<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>登录</title>
    <link rel="stylesheet" type="text/css" href="http://at.alicdn.com/t/font_748879_kwzwugo4aj.css">
    <link rel="stylesheet" type="text/css" href="{{asset('web/style/style.css')}}">
    <script src="http://cdn.bootcss.com/jquery/2.2.1/jquery.min.js"></script>
</head>
<body>
<div class="dialog lg-dialog" style="display: block">
    <div class="mask"></div>
    <div class="content account-lg" style="display: block">
        <div class="close iconfont icon-guanbi"></div>
        <div class="dialog-head">
            <h3>欢迎登录</h3>
            <p>登录或注册即同意<a href="">《用户服务协议》</a> </p>
        </div>
        <div class="form">
            <div class="input">
                <input type="text" name="phone" placeholder="请输入手机号" class="phone">
                <p class="error"></p>
            </div>
            <div class="input pwd-input">
                <input type="password" name="password" placeholder="请输入密码" class="pass eth_login">
                <p class="error"></p>
                <div class="view iconfont icon-icon-test4"></div>
            </div>
        </div>
        <div class="action">
            <a href="{{ URL::route('user.get-quick-login')}}" class="a toPhone-lg">快捷登录</a>
            <a href="{{ URL::route('user.get-forget-password')}}" class="b fg-pass">忘记密码？</a>
        </div>
        <div class="submit-row">
            <button class="submit">登录</button>
            <div class="other">
                没有账号？<a href="{{ URL::route('user.get-register')}}" class="reg to-reg">去注册</a> 或 微信登录 <a href="javascript:void(0);" class="wx iconfont icon-icon-test10"></a>
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

    //按键登录  
    $(".submit").click(function () {
        login();
    });
    //回车登录
    $('.eth_login').keydown(function (event) {
        if (event.keyCode == 13) {
            login();
        }
    });

    function login() {
        var phone = $("input[name='phone']").val();
        if (!phone) {
            toast('请输入手机号', 2000);
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
            url: "{{ URL::route('user.login')}}",
            data: {
                phone: phone,
                password: password,
            },
            dataType: "json",
            success: function (data) {
                loading(false, '');
                toast(data.msg, 2000);
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
</script>
</html>