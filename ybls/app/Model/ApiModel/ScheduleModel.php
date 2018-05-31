<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29/029
 * Time: 10:04
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;

class ScheduleModel extends Model
{
    protected $table = 'schedule';


    public static function schedule($item_type_id){
        return self::where('item_type_id','=',$item_type_id)
            ->select('schedule','content','title','img','statue')
            ->get();
    }
}