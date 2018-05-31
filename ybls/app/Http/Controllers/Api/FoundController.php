<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29/029
 * Time: 14:05
 */

namespace App\Http\Controllers\Api;


use App\Model\ApiModel\AnliModel;
use App\Model\ApiModel\CollectModel;
use App\Model\ApiModel\PraiseModel;
use Illuminate\Support\Facades\Input;

class FoundController extends BaseApiController
{

    /**
     * 发现
     * @return mixed
     */
    public static function anli()
    {
        $pram = Input::get();
        $user_id = $pram['user_id'];

        $anli = AnliModel::getanli();

        if (!$anli) {
            self::error("未发现案例");
        }

        //判断案例是否已被用户点赞
        foreach ($anli as $key => $v) {
            //查询点赞该案例的用户
            $an_user = PraiseModel::getpraise($v['id']);
            $an = [];
            foreach ($an_user as $k) {
                $an[] = $k['user_id'];
            }
            //判断用户是否已点赞该案例
            if (in_array($user_id, $an)) {
                $anli[$key]['is_praise'] = 1;
            }else{
                $anli[$key]['is_praise'] = 0;
            }
        }


        //判断案例是否已被用户收藏
        foreach ($anli as $key => $v) {
            //查询收藏该案例的用户
            $an_user = CollectModel::getcollect($v['id']);
            $an = [];
            foreach ($an_user as $k) {
                $an[] = $k['user_id'];
            }
            //判断用户是否已收藏该案例
            if (in_array($user_id, $an)) {
                $anli[$key]['is_collect'] = 1;
            }else{
                $anli[$key]['is_collect'] = 0;
            }
        }
        return $anli;
    }
}