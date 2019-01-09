<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{

    protected function getRandomString($len, $chars = null)
    {
        if (is_null($chars)) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        mt_srand(10000000 * (double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }

    /**
     * 认证用户 行使登录
     * @return array
     */
    protected function authentication($user)
    {
        Auth::login($user);
        $user = Auth::user();
        $user = $user->only(['phone', 'nickname', 'header']);
        return array_merge($user);
    }


    /**
     * Token 验证
     * @return array
     */
    protected function checkToken($token)
    {
        $res = User::getUid($token);
        $info = $res ? $res['id'] : '';
        return $info;
    }
}
