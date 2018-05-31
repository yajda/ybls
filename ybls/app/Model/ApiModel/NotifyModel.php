<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31/031
 * Time: 11:51
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;

class NotifyModel extends Model
{
    protected $table = 'notify';

    /**
     * 通知详情
     * @return mixed
     */
    public static function notify(){
        return self::first();
    }
}