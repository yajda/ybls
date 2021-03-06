<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28/028
 * Time: 15:47
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;

class PraiseModel extends Model
{
    protected $table = 'praise';

    /**
     * 根据案例id获取用户id
     * @param $an_id
     * @return mixed
     */
    public static function getpraise($an_id){
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
    public static function delpra($an_id,$user_id){
        return self::where('anli_id','=',$an_id)
            ->where('user_id','=',$user_id)
            ->delete();
    }


    public static function addpra($an_id,$user_id){
        return self::insertGetId(['anli_id' => $an_id,'user_id' => $user_id]);
    }

}