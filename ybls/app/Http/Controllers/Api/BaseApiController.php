<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/23/023
 * Time: 18:21
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

class BaseApiController extends Controller
{
    const SUCCESS_CODE = 200;       //成功代码
    const ERROR_CODE = 201;         //失败代码
    const LOGIN_ERROR_CODE = 202;   //登录超时代码

    /**
     * 原样输出数组
     * @param $array
     */
    public static function _dump($array){
        echo "<pre>";
        print_r($array);
        echo "<pre>";
    }


    /**
     * 输出json
     * @param $array
     */
    public static function _json($array){
        echo json_encode($array);
        exit(0);
    }


    /**
     * 打印错误信息
     * @param string $msg 提示信息
     * @param array $data 返回数据（如果有）
     * @param bool $login 是否是登录超时
     */
    public static function error($msg = '', $data = [], $login = false)
    {
        if (empty($data)) $data = null;
        $json = array(
            "code" => $login ? self::LOGIN_ERROR_CODE : self::ERROR_CODE,
            "msg" => $msg,
            "data" => $data
        );
        echo(json_encode($json));
        exit(0);
    }

    /**
     * 打印成功信息
     * @param array $data 返回数据
     * @param string $msg 提示信息
     */
    public static function success($data = [], $msg = '')
    {
        if (empty($data)) $data = null;
        $json = array(
            "code" => self::SUCCESS_CODE,
            "msg" => $msg,
            "data" => $data
        );
        /*if (config('app.debug')) {  //debug 模式开启日志
            LogErrorApiModel::addRow($msg);
        }*/
        echo(json_encode($json));
        exit(0);
    }

    /**
     * 严格检查参数
     * @param array $data 需要检查的数据
     * @param array $param 需要检查的键值
     * @param string $message
     */
    public static function checkParam($data, $param, $message = '参数不完整')
    {
        if (empty($data)) {
            self::error($message);
        }
        foreach ($param as $val) {
            if (!isset($data[$val])) {
                self::error($message);
            }
        }
    }

    /**
     * 是否是最后一页
     * @param $page
     * @param $total
     * @param $pageSize
     * @return int
     */
    public static function isLastPage($page, $total, $pageSize)
    {
        $totalPage = ceil($total / $pageSize);
        return $page >= $totalPage ? 1 : 0;
    }

    /**
     * 获取where的like条件
     * @param $string
     * @return string
     */
    public function getWhereLike($string)
    {
        if (empty($string)) return "";
        return "%" . implode('%', $this->strToOne($string)) . "%";
    }

    /**
     * 字符串分割成数组
     * @param $string
     * @return array
     */
    public function strToOne($string)
    {
        if ($string == "" || is_array($string)) {
            return array();
        }
        $list = array();
        $start = 0;
        $length = mb_strlen($string, 'utf8');
        while (count($list) < $length) {
            $list[] = mb_substr($string, $start, 1, 'utf8');
            $start++;
        }
        $newList = array();
        $i = 0;
        foreach ($list as $k => $v) {
            if (strlen($v) != 1) {
                if (isset($newList[$i])) {
                    $i++;
                }
                $newList[$i] = $v;
                $i++;
            } else {
                if (!isset($newList[$i])) {
                    $newList[$i] = "";
                }
                if ($v == " ") {
                    $i++;
                    continue;
                }
                $newList[$i] .= $v;
            }
        }
        return $newList;
    }
}