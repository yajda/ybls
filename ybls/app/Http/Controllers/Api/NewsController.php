<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31/031
 * Time: 11:18
 */

namespace App\Http\Controllers\Api;


use App\Model\ApiModel\NewsModel;
use App\Model\ApiModel\NotifyModel;
use Illuminate\Support\Facades\Input;

class NewsController extends BaseApiController
{
    /**
     * 消息
     * @return mixed
     */
    public function news(){
        $param = Input::get();
        $statue = isset($param['statue'])?$param['statue']:0;

        $data = NewsModel::news($param['user_id'],$statue);

        if(!$data){
            self::success("该用户没有消息");
        }
        return $data;
    }

    /**
     * 通知详情
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function notifyinfo(){
        $data = NotifyModel::notify();

        return view('api.notify',['data' => $data]);
    }
}