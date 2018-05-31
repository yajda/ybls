<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/23/023
 * Time: 18:48
 */

namespace App\Model\ApiModel;


use App\Http\Controllers\Api\UserToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserModel extends Model
{
    protected $table = 'user';

    /**
     * 根据电话查询用户信息
     * @param $phone
     * @return mixed
     */
    public static function getRowForLogin($phone){
       return self::where('phone','=',$phone)->first();
    }


    /**
     * 根据id查询用户数据
     * @param $id
     * @return mixed
     */
    public static function getuserinfo($id){
        return self::where('id','=',$id)
            ->first();
    }

    public static function updateuseravatar($id,$avatar){
        return self::where('id','=',$id)
            ->update(['avatar' => $avatar]);
    }

    /**
     * 更新用户信息
     * @param $saveData
     * @param $user_id
     * @return bool
     */
    public static function saveUserInfo($saveData, $user_id)
    {
        if (empty($saveData)) {
            return false;
        }
        try {
            return self::where('id', '=', $user_id)
                ->update($saveData);
        } catch (HttpException $e) {
            LogErrorApiModel::addRow($e->getMessage(), 2);
            return false;
        }
    }

    /**
     * 修改密码
     * @param  [type] $phone    [description]
     * @param  [type] $password [description]
     * @return [type]           [description]
     */
    public static function changePassword($phone,$password){
    	return DB::table('user')
            ->where('phone','=',$phone)
            ->update(['password' => $password]);
    }


    /**
     * 手机号是否存在
     * @param $mobile
     * @return bool
     */
    public static function checkMobile($mobile)
    {
        $where = '`phone` = "' . $mobile . '"';
        $user = self::getOneData($where, ['id']);
        if (empty($user->user_id)) {
            return false;
        }
        return true;
    }

    /**
     * 获取注册用户数量
     * @param int $type 用户类型
     * @param int $type_value 用户类型中的标签
     * @return int
     */
    public static function getUserNumber()
    {
        $num = self::where('id', '>', 0)->count();
        return $num;
    }

    /**
     * 新增一条数据并获得id
     * @param $data
     * @return int
     */
    public static function addUser($data)
    {
        return self::insertGetId($data);
    }



    /**
     * 用户登录时设置token
     * @param $user_id
     * @return bool|string
     */
    public static function setToken($user_id)
    {
        $token = UserToken::getUserToken($user_id);
        try {
            $result = self::where('id', '=', $user_id)
                ->update([
                    "user_token" => $token,
                    "last_login_time" => TIME_NOW
                ]);
            if ($result) {
                return $token;
            }
            return false;
        } catch (HttpException $e) {
            LogErrorApiModel::addRow($e->getMessage(), 2);
            return false;
        }
    }

    /**
     * 根据用户token获取用户信息
     * @param $token
     * @return UserModel|Model|null
     */
    public static function getLoginInfo($token)
    {
        return self::getRowForToken($token, [
            'id','username','sex','area','age','phone','avatar','ctime','last_login_time'
        ]);
    }


    /**
     * 根据用户token获取用户信息
     * @param $token
     * @param array $field
     * @return UserModel|Model|null
     */
    public static function getRowForToken($token, $field = ['*'])
    {
        return self::getOneData('`user_token` = "' . $token . '"', $field);
    }


    /**
     * 获取一条数据
     * @param $where
     * @param $field
     * @return Model|null|static
     */
    public static function getOneData($where, $field = ['*'])
    {
        return self::whereRaw($where)
            ->select($field)
            ->first();
    }
}