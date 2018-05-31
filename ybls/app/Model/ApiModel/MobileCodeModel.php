<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/30/030
 * Time: 11:50
 */

namespace App\Model\ApiModel;


use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Database\Eloquent\Model;

class MobileCodeModel extends Model
{
    protected $table = 'mobile_code';

    public $timestamps = false;

    const TIMES = 10000; //每个ip每天获取验证码次数

    const SEND_TIME = 18000; //手机验证码有效时间

    /**
     * 添加验证码
     * @param $mobile
     * @param $code
     * @param $type
     * @return bool
     */
    public static function addRow($mobile, $code, $type)
    {
        $date = date("Y-m-d", TIME_NOW);
        $ip = self::getIP();
        //检查次数是否过多
        $count = self::where('send_ip', '=', $ip)
            ->where('send_date', '=', $date)
            ->count();
        if ($count >= self::TIMES) {
            BaseApiController::error("您获取验证码次数已达到上线");
        }
        $saveData = [
            "mobile" => $mobile,
            "send_time" => TIME_NOW,
            "send_ip" => $ip,
            "send_code" => $code,
            "send_date" => $date,
            "type" => $type
        ];
        return self::insert($saveData);
    }

    /**
     * 验证验证码
     * @param $mobile
     * @param $code
     * @param $type
     * @return bool
     */
    public static function getNewCode($mobile, $code, $type)
    {
        $row = self::where('mobile', '=', $mobile)
            ->where('type', '=', $type)
            ->select(['id', 'send_code', 'send_time', 'is_used'])
            ->orderByDesc('send_time')
            ->first();
        if (empty($row->send_code) || $row->send_code != $code || $row->is_used == 1) {
            BaseApiController::error("验证码错误");
        }
        if ($row->send_time < TIME_NOW - 1800) {
            BaseApiController::error("验证码已过期");
        }
        //把验证码修改为已使用
        self::where('id', '=', $row->id)
            ->update(['is_used' => 1]);
        return true;
    }

    /**
     * 获取客户端ip
     * @return array|false|string
     */
    public static function getIP()
    {
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = "Unknow";
        return $ip;
    }
}