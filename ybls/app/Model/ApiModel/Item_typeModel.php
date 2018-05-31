<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/24/024
 * Time: 18:13
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Item_typeModel extends Model
{
    protected $table = 'item_type';

    /**
     * 获取所有项目类型信息
     * @return mixed
     */
    public static function getitemtype(){
        return self::select('id','item_name','img')->get();
    }


    public static function getitemtypeinfo($id){
        return self::where('id','=',$id)->get();
    }



}