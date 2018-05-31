<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28/028
 * Time: 13:31
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;

class ExperienceModel extends Model
{
    protected $table = "experience";

    /**
     * 根据id获取从业经验
     * @param $id
     * @return mixed
     */
    public static function experience($id){

        return self::where('lawyer_id','=',$id)
            ->select('experience')
            ->get();

    }
}