<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29/029
 * Time: 9:26
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;

class ItemModel extends Model
{
    protected $table="item";

    /**
     * 查询项目
     * @param int $statue
     * @return mixed
     */
    public static function itemindex($statue = 0){
        return self::where('statue','=',$statue)
            ->select('id','item_type_id','lawyer_id','name','img','ctime','statue')
            ->get();
    }

    /**
     * 根据id查询项目详情
     * @param $id
     * @return mixed
     */
    public static function iteminfo($id){
        return self::where('id','=',$id)
            ->select('id','name','img','ctime','item_type_id','lawyer_id')
            ->get();
    }
}