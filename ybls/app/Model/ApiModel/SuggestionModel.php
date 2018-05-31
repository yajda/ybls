<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/30/030
 * Time: 10:59
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;

class SuggestionModel extends Model
{
    protected $table = 'suggestion';

    public static function suggestion($content,$user_id){
        return self::insertGetId(['suggestion' => $content,'user_id' => $user_id]);
    }
}