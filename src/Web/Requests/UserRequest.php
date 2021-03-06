<?php

namespace App\Http\Requests\Web;

use App\Models\Web\User;
use App\Models\Web\Sms;
use Illuminate\Support\Facades\Hash;

class UserRequest
{

    //逻辑验证
    public static function logicVerification($type, $data)
    {
        $msg = '';
        foreach ($type as $key => $value) {
            switch ($value) {
                case 'sms_code': //短信验证码  验证
                    $sms = Sms::checkCode($data['phone'], $data['sms_code']);
                    if (time() - strtotime($sms['created_at']) > 180) {
                        $msg = '验证码已失效，请重新发送';
                    }
                    if (empty($sms)) {
                        $msg = '无效的短信验证码';
                    }
                    break;
                case 'phone_already_exists': //手机号码已注册
                    $check_phone = User::getUserPhone($data['phone']);
                    if ($check_phone) {
                        $msg = '手机号' . $data['phone'] . '已注册，可直接登录';
                    }
                    break;
                case 'phone_not_exists': //手机号码未注册
                    $check_phone = User::getUserPhone($data['phone']);
                    if (empty($check_phone)) {
                        $msg = '该手机号未注册，请注册';
                    }
                    break;
                case 'phone_lock': //验证手机号是否被锁定
                    $msg = User::getUserPhoneLock($data['phone']);
                    if ($msg) {
                        $msg = '您的密码输错已超过5次，请修改密码或 20分钟后重试';
                    }
                    break;
                case 'twice_password_accordance': //验证新密码与旧密码是否一致
                    $password = User::getUserPassword($data['phone']);
                    if ($password) {
                        if (Hash::check($data['password'], $password->password)) {
                            $msg = '新密码与旧密码一致';
                        }
                    }
                    break;
                case 'phone_login': //手机号已注册  是否登录
                    $check_phone = User::getUserPhone($data['phone']);
                    if ($check_phone) {
                        $msg = '手机号已注册是否使用该手机号登录？';
                    }
                    break;
                case 'bind_phone': //手机号已绑定其他账号
                    $check_phone = User::getUserPhone($data['phone']);
                    if ($check_phone) {
                        $msg = '手机号已绑定其它账号';
                    }
                    break;
                case 'sms_frequently': //短信发送频繁
                    $frequently = Sms::getPhoneNum($data['phone']);
                    if ($frequently && $frequently > 4) {
                        $msg = '短信发送过于频繁';
                    }
                    break;
            }
        }
        if ($msg) {
            return response()->json(['status' => '0', 'msg' => $msg], 422);
        } else {
            return false;
        }
    }
}
