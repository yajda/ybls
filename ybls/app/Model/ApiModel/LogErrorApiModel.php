<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29/029
 * Time: 13:01
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Console\Input\Input;

class LogErrorApiModel extends Model
{
    protected $table = 'log_error_api';

    /**
     * API请求错误日志
     * @param $message
     * @param int $type
     */
    public static function addRow($message, $type = 1)
    {
        $data = [
            "message" => $message,
            "c_time" => date("Y-m-d H:i:s", TIME_NOW),
            "type" => self::$type[$type],
            "request" => "",
            "get_url" => Route::current()->uri,
        ];
        if ($type == 1) {
            $data['request'] = Input::get();
            $data['request'] = json_encode($data['request']);
        }
        self::insert($data);
    }
}