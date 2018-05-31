<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25/025
 * Time: 13:04
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AnliModel extends Model
{
    protected $table = 'anli';

    /**
     * 获取推荐案例
     * @return mixed
     */
    public static function getrecommendanli(){
        return self::select('id','title','img','content')->where('recommend','=',1)->get();
    }

    /**
     * 根据项目类型id获取案例信息
     * @param $id
     * @return mixed
     */
    public static function getanliinfo($id){
        return self::where('item_type_id','=',$id)
            ->get();
    }


    /**
     * 根据案例id查询点赞次数
     * @param $an_id
     * @return mixed
     */
    public static function praise($an_id){
        return self::where('id','=',$an_id)
            ->select('praise')
            ->get();
    }

    /**
     * 根据案例id，更新案例的点赞次数
     * @param $an_id
     * @param $num
     * @return mixed
     */
    public static function updatepra($an_id,$num){
        return self::where('id','=',$an_id)
            ->update(['praise' => $num]);
    }


    /**
     * 根据案例id查询收藏次数
     * @param $an_id
     * @return mixed
     */
    public static function collect($an_id){
        return self::where('id','=',$an_id)
            ->select('collect')
            ->get();
    }

    /**
     * 根据案例id，更新案例的收藏次数
     * @param $an_id
     * @param $num
     * @return mixed
     */
    public static function updatecollect($an_id,$num){
        return self::where('id','=',$an_id)
            ->update(['collect' => $num]);
    }

    /**
     * 根据案例id查询查看次数
     * @param $an_id
     * @return mixed
     */
    public static function look($an_id){
        return self::where('id','=',$an_id)
            ->select('look')
            ->get();
    }

    /**
     * 根据案例id，更新案例的查看次数
     * @param $an_id
     * @param $num
     * @return mixed
     */
    public static function updatelook($an_id,$num){
        return self::where('id','=',$an_id)
            ->update(['look' => $num]);
    }

    /**
     * 获取案例
     * @return mixed
     */
    public static function getanli(){
        return self::select('id','item_type_id','title','img','content','look','praise','collect')
            ->get();
    }

    /**
     * 根据案例id，获取案例详情
     * @param $an_id
     * @return mixed
     */
    public static function getfirstanli($an_id){
        return self::where('id','=',$an_id)
            ->select('id','item_type_id','title','img','content','look','praise','collect')
            ->first();
    }

}