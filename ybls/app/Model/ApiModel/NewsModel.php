<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31/031
 * Time: 11:19
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;

class NewsModel extends Model
{
    protected $table = 'news';

    /**
     * 查询消息列表
     * @param $user_id  用户id
     * @param $statue   消息识别码
     * @return mixed
     */
    public static function news($user_id,$statue){
        return self::where('user_id','=',$user_id)
            ->where('statue','=',$statue)
            ->get();
    }
}