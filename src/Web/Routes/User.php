<?php

use Illuminate\Support\Facades\Config;

//不需要认证的接口
Route::middleware('web')->domain(Config::get('constants.WEB_URL'))->namespace('Web')->group(function () {

    //注册
    Route::get('/user/get-register', 'UserController@getRegister')->name('user.get-register');
    Route::post('/user/register', 'UserController@register')->name('user.register');

    //登录
    Route::get('/user/get-login', 'UserController@getLogin')->name('user.get-login');
    Route::post('/user/login', 'UserController@login')->name('user.login');

    //忘记密码
    Route::get('/user/get-forget-password', 'UserController@getForgetPassword')->name('user.get-forget-password');
    Route::post('/user/forget-password', 'UserController@forgetPassword')->name('user.forget-password');

    //快捷登录
    Route::get('/user/get-quick-login', 'UserController@getQuickLogin')->name('user.get-quick-login');
    Route::post('/user/quick-login', 'UserController@quickLogin')->name('user.quick-login');

    //第三方登录
    Route::get('/user/weixin-login-url', 'UserController@weixinLoginUrl')->name('user.weixin-login-url');
    Route::get('/user/callback', 'UserController@callback')->name('user.callback');

    //绑定手机号
    Route::get('/user/get-bind-phone', 'UserController@getBindPhone')->name('user.get-bind-phone');
    Route::post('/user/bind-phone', 'UserController@bindPhone')->name('user.bind-phone');

    //发送短信验证码
    Route::post('/user/send-sms', 'UserController@sendSms')->name('user.send-sms');


    //获取用户信息 （暂时未用） 
    Route::get('/user/get-user-info', 'UserController@getUserInfo')->name('user.get-user-info');

    //绑定第三方
    Route::post('/user/bind-tripartite', 'UserController@bindTripartite')->name('user.bind-tripartite');

    //修改手机号码
    Route::post('/user/modify-phone', 'UserController@modifyPhone')->name('user.modify-phone');

    //修改密码
    Route::post('/user/modify-password', 'UserController@modifyPassword')->name('user.modify-password');

    //退出
    Route::get('/user/logout', 'UserController@logout')->name('user.logout');

});


