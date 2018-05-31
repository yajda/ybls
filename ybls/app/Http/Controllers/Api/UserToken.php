<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29/029
 * Time: 12:57
 */

namespace App\Http\Controllers\Api;


use App\Model\ApiModel\UserModel;

class UserToken extends BaseApiController
{
    /**
     * 用户token
     * @var
     */
    private static $user_token;

    private static $userInfo;

    const TIMESTAMP = 2592000; //token超时时间

    /**
     * 设置用户token
     * @param $user_id
     */
    public static function setUserToken($user_id)
    {
        self::$user_token = strtoupper(md5(TIME_NOW . $user_id . rand(100, 999)));
    }

    /**
     * 直接获取用户基本信息
     * @return array
     */
    public static function _getUserInfo()
    {
        return self::$userInfo;
    }

    /**
     * 直接获取用户token
     * @return string
     */
    public static function _getUserToken()
    {
        return self::$user_token;
    }

    /**
     * 直接获取用户id
     * @return int
     */
    public static function _getUserId()
    {
        return self::$userInfo['id'];
    }

    /**
     * 获取用户token
     * @param $user_id
     * @return string
     */
    public static function getUserToken($user_id)
    {
        self::setUserToken($user_id);
        return self::$user_token;
    }

    /**
     * 验证用户token
     * @param $token
     * @return array|bool
     */
    public static function verifyUserToken($token)
    {
        if (strlen($token) !== 32) {
            self::error('token错误', [], true);
        }
        //与数据库已存在的token进行比对
        $result = UserModel::getLoginInfo($token);
        var_dump($result);
        if (empty($result->id)) {
            self::error('您的账号已在其他设备登录', [], true);
        }
        // $result = $result->toArray();
        //登录是否超时
        if (TIME_NOW - $result->last_login_time >= self::TIMESTAMP) {
            self::error('登录超时，请重新登录', [], true);
        }
        self::$userInfo = $result->toArray();
        self::$user_token = $token;
        return true;
    }
}