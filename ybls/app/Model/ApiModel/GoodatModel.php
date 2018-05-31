<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28/028
 * Time: 13:29
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;

class GoodatModel extends Model
{

    protected $table = 'good_at';

    /**
     * 根据id获取律师擅长领域
     * @param $id
     * @return mixed
     */
    public static function good_at($id){
        return self::where('lawyer_id','=',$id)
            ->select('item_name')
            ->get();
    }
}