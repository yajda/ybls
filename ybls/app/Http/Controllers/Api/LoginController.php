<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/23/023
 * Time: 18:17
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Model\ApiModel\MobileCodeModel;
use App\Model\ApiModel\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class LoginController extends BaseApiController
{
    const PASSWORD_SALT = 'et_ybls_per';

    /**
     * 注册
     * @param Request $request
     */
    public function register(Request $request){
        //获取数据
        $res = Input::get();
        self::checkParam($res,['phone','password','mobile_code']);
        //手机号码验证
        $len = strlen($res['phone']);
        if($len != 11){
            self::error("手机号码错误");
        }
        //手机验证码是否正确
        if($res['mobile_code'] != 123456){
            self::error("验证码错误");
        }
        //MobileCodeModel::getNewCode($res['phone'], $res['mobile_code'], 1);

        //获取用户信息
        $user = UserModel::getRowForLogin($res['phone']);
        $password = self::getPassword($res['password']);
        if($user){
            //忘记密码
            $result = UserModel::changePassword($res['phone'],$password);
            if ($result) {
                $data = $this->setUserToken($user);
                self::success($data,"密码修改成功");
            }else{
                self::error("密码修改失败");
            }
        }else{
            //手机号不存在，开始注册
            $userinfo = [
                'phone' =>  $res['phone'],
                'password' =>   $password,
                'ctime' =>  TIME_NOW,
                'username' => $this->getDefaultUsername(),
                "avatar" => '/avatar/00'.rand(1,8).'jpg'
            ];
            $newuser = UserModel::addUser($userinfo);
            if(!$newuser){
                $this->error("用户注册失败");
            }else{
                $userparam = UserModel::getRowForLogin($res['phone']);
                $list = $this->setUserToken($userparam);
                self::success($list,"用户注册成功");
            }
        }

    }


    /**
     * 登录
     */
    public function login(){
        $request = Input::get();
        //检查参数
        self::checkParam($request,['phone','password']);
        //手机号码验证
        $len = strlen($request['phone']);
        if($len != 11){
            self::error("手机号码错误");
        }
        //判断用户名是否存在
        $user = UserModel::getRowForLogin($request['phone']);
        if(!$user){
            self::error("用户名不存在");
        }
        //判断密码是否正确
        $password = self::getPassword($request['password']);
        if($password !== $user->password){
            self::error("密码错误");
        }
        $return_data = $this->setUserToken($user);
        self::success($return_data,"登录成功");

    }



    /**
     * 设置用户登录时返回的信息
     * @param $user
     * @return array
     */
    private function setUserToken($user)
    {
        $avatar = '/avatar/00'.rand(1,8).'jpg';
        $return_data = [];
        //获取用户token
        $return_data['user_token'] = UserModel::setToken($user->id);
        //数据库报错才会出现这里的错误，基本可以忽略
        if ($return_data['user_token'] === false) {
            self::error("用户token获取失败");
        }
        //登录成功，整合需要返回的信息给前端
        $return_data['user_id'] = $user->id;
        $return_data['username'] = $user->username;
        $return_data['phone'] = $user->phone;
        $return_data['avatar'] = $user->avatar == '' ? $avatar : $user->avatar;
        $return_data['sex'] = $user->sex == null ? '' : $user->sex;
        return $return_data;
    }


    /**
     * 获取手机验证码
     * type 1注册，忘记密码，2绑定手机号
     */
    /*public function getMobileCode()
    {
        $param = Input::get();
        self::checkParam($param, ['mobile', 'type']);
        if ($param['type'] == 2) {
            //检验手机号是否存在
            $result1 = UserModel::checkMobile($param['mobile']);
            if ($result1) {
                self::error("手机号已被绑定，请更换手机号");
            }
        }
        $code = rand(111111, 999999);
        // $code = 123456;
        $result1 = MobileCodeModel::addRow($param['mobile'], $code, $param['type']);
        if (!$result1) {
            self::error("验证码保存失败");
        }
        $result2 = SendSmsController::sendMobileCode($param['mobile'], $code);
        if ($result2->Message != "OK") {
            self::error($result2->Message);
        }
        //发送短信给用户
        self::success([], "短信发送成功");
    }*/


    /**
     * 密码加密
     * @param  string $string [description]
     * @return [type]         [description]
     */
    public static function getPassword($string = ''){
        return 's' . substr(md5($string . self::PASSWORD_SALT), 1);
    }

    /**
     * 生成昵称
     * @return string
     */
    public function getDefaultUsername()
    {
        /*$string = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';
        $arr = explode(",", $string);
        $nickname = '';
        for ($i = 0; $i < 8; $i++) {
            $nickname .= $arr[rand(0, 25)];
        }*/
        $nickname = "YBLS";
        return $nickname . "_" . self::formatUserNameNum(UserModel::getUserNumber() + 1);
    }

    /**
     * 格式化用户昵称
     * @param $num
     * @return string
     */
    private static function formatUserNameNum($num)
    {
        if ($num < 10) {
            $num = "000" . $num;
        } elseif ($num < 100) {
            $num = "00" . $num;
        } elseif ($num < 1000) {
            $num = "0" . $num;
        } else {
            $num = "" . $num;
        }
        return $num;
    }

}