<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/30/030
 * Time: 15:22
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;

class FollowModel extends Model
{
    protected $table = 'follow';

    /**
     * 根据用户id查询关注的所有律师id;
     * @param $user_id
     * @return mixed
     */
    public static function follow($user_id){
        return self::where('user_id','=',$user_id)
            ->select('lawyer_id')
            ->get();
    }

    /**
     * 插入关注记录
     * @param $lawyer_id
     * @param $user_id
     * @return mixed
     */
    public static function addfollow($lawyer_id,$user_id){
        return self::insertGetId(['lawyer_id' => $lawyer_id,'user_id' => $user_id]);
    }

    /**
     * 删除关注记录
     * @param $lawyer_id
     * @param $user_id
     * @return mixed
     */
    public static function delfollow($lawyer_id,$user_id){
        return self::where('lawyer_id','=',$lawyer_id)
            ->where('user_id','=',$user_id)
            ->delete();
    }
}