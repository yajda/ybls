<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29/029
 * Time: 9:25
 */

namespace App\Http\Controllers\Api;


use App\Model\ApiModel\ItemModel;
use App\Model\ApiModel\RemindModel;
use App\Model\ApiModel\ScheduleModel;
use Illuminate\Support\Facades\Input;

class ItemController extends BaseApiController
{

    /**
     * 项目
     * @return mixed
     */
    public function itemindex(){
        $pram = Input::get();
        if(!$pram){
            $statue = 0;
        }else{
            $statue = $pram['statue'];
        }

        $item = ItemModel::itemindex($statue);
        return $item;
    }


    /**
     * 项目详情
     * @return array
     */
    public function iteminfo(){
        $pram = Input::get();
        $item_id = $pram['id'];

        //获取项目详情
        $iteminfo = ItemModel::iteminfo($item_id);

        $data = [];
        if(!$iteminfo){
            self::error("项目不存在");
        }

        foreach($iteminfo as $v){
            $data['item_info'] = $v;
        }

        //获取项目类型 id
       $item_type_id = $data['item_info']['item_type_id'];

        //根据类型id查找进度模板
        $schedule = ScheduleModel::schedule($item_type_id);

        if(!$schedule){
            self::error("未查到相关进度");
        }
        $data['schedule'] = $schedule;
        foreach($schedule as $key => $v){
            //进度完成时会有历史提醒
            if($v['statue'] == 0){

                //根据进度和项目id查询历史提醒
                $hremind = RemindModel::hremind($v['schedule'],$item_id);

                if(!$hremind){
                    self::error('没有相关历史提醒');
                }

                foreach($hremind as $k => $value){
                    $data['hremind'][$k] = $value;
                }
            }

            //进度在进行中时会有重要提醒
            if($v['statue'] == 1){
                 //根据进度和项目id查询重要提醒
                 $remind = RemindModel::remind($v['schedule'],$item_id);

                 if(!$remind){
                     self::error('没有相关重要提醒');
                 }

                 foreach($remind as $k => $value){
                     $data['remind'][$k] = $value;
                 }
                break;
             }
        }
        return $data;
    }
}