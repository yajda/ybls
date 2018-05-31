<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25/025
 * Time: 10:18
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LawyerModel extends Model
{
    protected $table = 'lawyer';


    /**
     * 获取律师列表
     * @return mixed
     */
    public static function getonlinelawyer(){
        return self::select('id','username','online','statue','avatar')
            ->where('statue','<>','0')
            ->orderBy('online','DESC')
            ->orderBy('statue')
            ->get();
    }


    /**
     * 根据项目类型id 获取擅长该领域的律师信息
     * @param $id   项目类型id
     * @return mixed
     */
    public static function getitemtypelawyerinfo($id){

        return DB::table('lawyer')
            ->join('good_at','good_at.lawyer_id','=','lawyer.id')
            ->select('lawyer.id','lawyer.username','lawyer.statue','lawyer.avatar','lawyer.online')
            ->where('statue','<>','0')
            ->where('good_at.item_type','=',$id)
            ->orderBy('online','DESC')
            ->orderBy('statue')
            ->get();

    }

    /**
     * 根据id获取律师信息
     * @param $id
     * @return mixed
     */
    public static function getlawyerinfo($id)
    {
        return self::where('lawyer.id','=',$id)
            ->select('id','username','viewpoint','online','statue','content','avatar','good_at')
            ->first();

    }

    /**
     * 律师列表
     * @param $param
     * @return mixed
     */
    public static function lawyerlist($param){
        $contant = "statue <> 0";
        if(isset($param['item_type_id'])){
            $lawyer_id = DB::table('good_at')
                ->select('lawyer_id')
                ->where('item_type', '=', $param['item_type_id'])
                ->get();

            $user = "(";
            foreach($lawyer_id as $v){
                if(!$v->lawyer_id){
                    return false;
                }
                $user .= "$v->lawyer_id , ";
            }
            if($user == "("){
                return $lawyer_id;
            }
            $user = substr($user,0,-2);
            $user .=")";
            $contant .= " AND id IN ". $user ;
        }
        if(isset($param['statue'])){
            $contant .= " AND statue = ".$param['statue'];
        }
        if(isset($param['online'])){
            $contant .= " AND online = ".$param['online'];
        }
        return self::whereRaw($contant)
            ->orderBy('online','DESC')
            ->orderBy('statue')
            ->get();
    }

}