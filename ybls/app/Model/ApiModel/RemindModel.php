<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29/029
 * Time: 10:25
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;

class RemindModel extends Model
{
    protected $table = 'remind';

    /**
     * 查询相关项目不同进度时的提醒
     * @param $schedule
     * @param $item_id
     * @return mixed
     */
    public static function remind($schedule,$item_id){

        return self::where('item_id','=',$item_id)
            ->where('schedule_statue','=',$schedule)
            ->select('datum','submit_time','submit_address')
            ->get();
    }

    /**
     * 查询相关项目的历史提醒
     * @param $item_id
     * @return mixed
     */
    public static function hremind($schedule,$item_id){

        return self::where('item_id','=',$item_id)
            ->where('schedule_statue','<=',$schedule)
            ->select('datum','submit_time','submit_address','ctime')
            ->get();
    }
}