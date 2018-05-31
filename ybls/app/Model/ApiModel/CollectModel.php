<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28/028
 * Time: 17:06
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;

class CollectModel extends Model
{
    protected $table = 'collect';

    /**
     * 根据案例id获取用户id
     * @param $an_id
     * @return mixed
     */
    public static function getcollect($an_id){
        return self::where('anli_id','=',$an_id)
            ->select('user_id')
            ->get();
    }


    /**
     * 根据案例id，用户id，删除用户点赞记录
     * @param $an_id
     * @param $user_id
     * @return mixed
     */
    public static function delcollect($an_id,$user_id){
        return self::where('anli_id','=',$an_id)
            ->where('user_id','=',$user_id)
            ->delete();
    }


    /**
     * 插入数据
     * @param $an_id
     * @param $user_id
     * @return mixed
     */
    public static function addcollect($an_id,$user_id){
        return self::insertGetId(['anli_id' => $an_id,'user_id' => $user_id]);
    }

    /**
     * 根据用户id获取案例id
     * @param $user_id
     * @return mixed
     */
    public static function getanliid($user_id){
        return self::where('user_id','=',$user_id)
            ->select('anli_id')
            ->get();
    }
}