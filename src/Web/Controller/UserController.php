<?php

namespace App\Http\Controllers\Web;

use Validator;
use App\Models\Web\User;
use App\Models\Web\Sms;
use App\Models\Web\UserTripartite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Web\UserRequest;
use App\Http\Requests\Web\LoginRequest;
use App\Http\Requests\Web\RegisterRequest;
use App\Http\Requests\Web\ForgetPasswordRequest;
use App\Http\Requests\Web\QuickLoginRequest;
use App\Http\Requests\Web\BindPhoneRequest;
use App\Http\Requests\Web\ModifyPhoneOneRequest;
use App\Http\Requests\Web\ModifyPhoneTwoRequest;
use App\Http\Requests\Web\SmsRequest;

class UserController extends BaseController
{

    /**
     * 注册
     * @access public
     * @param int $phone 手机号码
     * @param string $password 密码
     * @param int $sms_code 短信验证码
     * @return string
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->only(['phone', 'password', 'sms_code']);

        try {
            //逻辑验证
            $check_array = ['phone_already_exists'];
            $logic_verification = UserRequest::logicVerification($check_array, $data);
            if ($logic_verification) {
                return $logic_verification;
            }

            $user = new User();
            $user->phone = $data['phone'];
            $user->password = bcrypt($data['password']);
            $user->nickname = '用户_' . $this->getRandomString(6);
            $user->header = Config::get('constants.DEFAULT_HEADER');
            $user->save();

            // 注册的用户让其进行登陆状态
            $this->authentication($user);

            return response()->json(['msg' => '注册成功'], 200);

        } catch (\Exception $e) {
            return response()->json(['msg' => '注册失败'], 500);
        }
    }

    //获取登录页面
    public function getLogin()
    {
        return view('Web.login');
    }

    /**
     * 登录
     * @access public
     * @param int $phone 手机号码
     * @param string $password 密码
     * @return string
     */
    public function login(LoginRequest $request)
    {
        $data = $request->only(['phone', 'password']);

        //逻辑验证
        $check_array = ['phone_lock', 'phone_not_exists'];
        $logic_verification = UserRequest::logicVerification($check_array, $data);
        if ($logic_verification) {
            return $logic_verification;
        }

        if (Auth::guard('web')->attempt($data)) {

            User::loginSuccess($request->phone);

            return response()->json(['msg' => '登录成功'], 200);
        } else {
            User::loginError($request->phone);

            return response()->json(['msg' => '用户名或密码错误'], 402);
        }
    }

    //获取忘记密码页面
    public function getForgetPassword()
    {
        return view('Web.forgetPassword');
    }

    /**
     * 忘记密码
     * @access public
     * @param int $phone 手机号码
     * @param int $sms_code 短信验证码
     * @param string $password 密码
     * @param string $password_confirmation 确认密码
     * @return string
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $data = $request->only(['phone', 'sms_code', 'password', 'password_confirmation']);

        //逻辑验证
        $check_array = ['sms_code', 'twice_password_accordance', 'phone_not_exists'];
        $logic_verification = UserRequest::logicVerification($check_array, $data);
        if ($logic_verification) {
            return $logic_verification;
        }

        $user = User::where('phone', $data['phone'])->first();
        $user->password = bcrypt($data['password']);
        $user->save();

        //修改密码后 登录
        $this->authentication($user);

        return response()->json(['msg' => '修改成功'], 200);
    }

    //获取快捷登录页面
    public function getQuickLogin()
    {
        return view('Web.quickLogin');
    }

    /**
     * 快捷登录
     * @access public
     * @param int $phone 手机号码
     * @param int $sms_code 短信验证码
     * @return string
     */
    public function quickLogin(QuickLoginRequest $request)
    {
        $data = $request->only(['phone', 'sms_code']);

        //逻辑验证
        $check_array = ['sms_code'];
        $logic_verification = UserRequest::logicVerification($check_array, $data);
        if ($logic_verification) {
            return $logic_verification;
        }

        //判断用户是否存在
        $user = User::where('phone', $data['phone'])->first();
        try {
            if ($user) {

                $this->authentication($user);

                return response()->json(['msg' => '登录成功'], 200);
            } else {
                $user = new User();
                $user->phone = $data['phone'];
                $user->nickname = '用户_' . $this->getRandomString(6);
                $user->header = Config::get('constants.DEFAULT_HEADER');
                $user->save();

                $this->authentication($user);

                return response()->json(['msg' => '您已注册成功'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['msg' => '登录失败'], 500);
        }
    }


    /**
     * 微信登录
     *
     * @access public
     */
    public function weixinLoginUrl()
    {
        //应用唯一标识
        $app_id = Config::get('constants.APP_ID');
        //回调地址
        $redirect_url = Config::get('constants.REDIRECT_URL');
        //参数
        $response_type = Config::get('constants.RESPONSE_TYPE');
        //作用域
        $scope = Config::get('constants.SCOPE');
        //该参数可用于防止csrf攻击（跨站请求伪造攻击）
        //微信登录-----生成唯一随机串防CSRF攻击
        $state = md5(uniqid(rand(), true));
        $cookie_time = time() + 1 * 7 * 24 * 3600;
        $this->setCookie('state', $state, $cookie_time);

        $jump_url = $this->qrconnect($app_id, $redirect_url, $response_type, $scope, $state);
        header("Location:$jump_url");
    }


    /**
     * 生成扫码登录的URL   PART1 网站应用
     * @access public
     * @param int $appid 应用唯一标识
     * @param string $redirect_url 回调地址
     * @param string $response_type 参数
     * @param string $scope 作用域
     * @param string $state 防止csrf攻击（跨站请求伪造攻击）
     * @return string
     */
    public function qrconnect($app_id, $redirect_url, $response_type, $scope, $state)
    {
        $url = "https://open.weixin.qq.com/connect/qrconnect?appid=" . $app_id . "&redirect_uri=" . urlencode($redirect_url) . "&response_type=" . $response_type . "&scope=" . $scope . "&state=" . $state . "#wechat_redirect";
        return $url;
    }

    /**
     * 微信登录成功后回调
     * @access public
     */
    public function callback()
    {
        $code = $this->getInput('code');
        if (!isset($code) && empty($code)) {
            exit('非法参数!');
        }
        $token = $this->accessToken($code);
        if (isset($token['access_token'])) {


            //回调业务处理。。。。。。。。。。。。
            //如果微信登录过 跳首页  如果微信未登陆过 绑定手机号
            //业务根据需求  《自己写哈》

        } else {
            //登陆失败
            $url = 'http://' . Config::get('constants.WEB_URL');
            header("Location:$url");
        }
    }

    //获取绑定手机号页面
    public function getBindPhone()
    {
        return view('Web.bindPhone');
    }

    /**
     * 绑定手机号
     * @access public
     * @param string $phone 手机号
     * @param string $code 短信验证码
     * @return string
     */
    public function bindPhone(BindPhoneRequest $request)
    {
        $data = $request->all();

        //逻辑验证
        $check_array = ['sms_code', 'phone_login'];
        $logic_verification = UserRequest::logicVerification($check_array, $data);
        if ($logic_verification) {
            return $logic_verification;
        }

        try {
            //用户表
            $user = new User();
            $user->phone = $data['phone'];
            $user->nickname = '用户_' . $this->getRandomString(6);
            $user->header = ''; //第三方的头像
            $user->sex = '男'; //第三方的性别
            $user->save();

            $this->authentication($user);

            //第三方表
            $user_tripartite = new UserTripartite();
            $user_tripartite->user_id = Auth::id();
            $user_tripartite->type = '1'; //第三方类型 1=微信 2=QQ 3=微博
            $user_tripartite->open_id = '12321312'; //第三方标识ID
            $user_tripartite->save();

            return response()->json(['msg' => '绑定成功'], 200);
        } catch (\Exception $e) {
            return response()->json(['msg' => '绑定失败'], 500);
        }
    }

    /**
     * 发送短信验证码
     * @access public
     * @param int $phone 手机号码
     * @param int $type 1=注册,2=忘记密码,3=快捷登录,4=绑定手机号码,5=修改手机号码,6=修改密码
     * @return string
     */
    public function sendSms(SmsRequest $request)
    {
        $data = $request->only(['phone', 'type']);

        //逻辑验证
        switch ($data['type']) {
            case 1:
                $check_array = ['phone_already_exists', 'sms_frequently'];
                break;
            case 2:
                $check_array = ['phone_not_exists', 'sms_frequently'];
                break;
            case 3:
                $check_array = ['sms_frequently'];
                break;
            case 4:
                $check_array = ['phone_login', 'sms_frequently'];
                break;
            case 5:
                $check_array = ['bind_phone', 'sms_frequently'];
                break;
            case 6:
                $check_array = ['sms_frequently'];
                break;
        }
        $phone_logic_verification = UserRequest::logicVerification($check_array, $data);
        if ($phone_logic_verification) {
            return $phone_logic_verification;
        }

        //$code = mt_rand(100000,999999);
        //实际开发中  更换阿里云 短信
        //$rs = Sms::sendSms($code,$phone);

        //测试专用
        $rs = 1;
        $code = 111111;
        if ($rs == 1) {
            $user = new Sms();
            $user->phone = $data['phone'];
            $user->sms_code = $code;
            $user->save();
            return response()->json(['msg' => '发送成功'], 200);
        } else {
            return response()->json(['msg' => '发送失败，稍后重试'], 500);
        }
    }

    /**
     * 绑定第三方账号
     * @access public
     * @param int $resource_type 1=绑定,2=解除绑定
     * @param int $type 1=微信,2=QQ,3=微博
     * @param int $open_id 第三方标识ID
     * @return string
     */
    public function bindTripartite(Request $request)
    {
        $user = Auth::user()->toArray();

        $type = $request->input('type');
        $open_id = $request->input('open_id');
        $resource_type = $request->input('resource_type');
        if (empty($type) || empty($open_id) || empty($resource_type)) {
            return response()->json(['msg' => '参数错误'], 422);
        }
        $account_number = UserTripartite::getUserOpenId($type, $open_id);
        if ($resource_type == 1) {
            if ($account_number) {
                return response()->json(['msg' => '您已绑定其他账号'], 402);
            }
            $user_tripartite = new UserTripartite();
            $user_tripartite->user_id = $user['id'];
            $user_tripartite->type = $type;
            $user_tripartite->open_id = $open_id;
            $user_tripartite->save();
            return response()->json(['msg' => '为了您的账号安全，30天内不可解除账号关联'], 200);
        } else if ($resource_type == 2) {
            if (strtotime($account_number['created_at']) > time() - 30 * 24 * 3600) {
                return response()->json(['msg' => '解除失败，为了您的账户安全，30天内不可解除账号关联'], 402);
            }
            $user_tripartite = UserTripartite::where('user_id', $user['id'])->first();
            $user_tripartite->delete();
            return response()->json(['msg' => '解绑成功'], 200);
        }
    }

    /**
     * 修改手机号码
     * @access public
     * @param string $token
     * @param int $phone 手机号码
     * @param int $sms_code 短信验证码
     * @return string
     */
    public function modifyPhone(QuickLoginRequest $request)
    {
        $user = Auth::user()->toArray();

        $data = $request->only(['phone', 'sms_code']);

        //逻辑验证
        $check_array = ['sms_code'];
        $logic_verification = UserRequest::logicVerification($check_array, $data);
        if ($logic_verification) {
            return $logic_verification;
        }

        $user = User::find($user['id']);
        $user->phone = $data['phone'];
        $user->save();

        return response()->json(['msg' => '换绑手机号码成功'], 200);
    }

    /**
     * 修改密码
     * @access public
     * @param int $sms_code 短信验证码
     * @param int $type 1= 输入验证码 2 设置新密码
     * @param int $password 新密码
     * @param int $password_confirmation 确认密码
     * @return string
     */
    public function modifyPassword(ModifyPhoneOneRequest $request_one, ModifyPhoneTwoRequest $request_two, Request $request)
    {
        $user = Auth::user()->toArray();

        $type = $request->input('type');
        if ($type == 1) {
            $user = User::find($user['id']);
            $data = $request_one->only(['sms_code']);

            //逻辑验证
            $check_array = ['sms_code'];
            $data = array_add($data, 'phone', $user->phone);
            $logic_verification = UserRequest::logicVerification($check_array, $data);
            if ($logic_verification) {
                return $logic_verification;
            }

            return response()->json(['msg' => '短信验证成功'], 200);
        } else if ($type == 2) {
            $data = $request_two->only(['password', 'password_confirmation']);

            $user = User::find($user['id']);
            $user->password = bcrypt($data['password']);
            $user->save();
            return response()->json(['msg' => '密码更换成功'], 200);
        }
    }


    /**
     * 退出登录
     * @return array
     */
    public function logout(Request $request)
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        return response()->json(['msg' => '退出成功'], 200);
    }


    /**
     * 获取用户信息
     * @return array
     */
    public function getUserInfo()
    {
        $data = Auth::user();
        if ($data) {
            return response()->json(['status' => '1', 'msg' => '成功', 'data' => $data]);
        } else {
            return response()->json(['status' => '-1', 'msg' => '未登录']);
        }

    }
}
