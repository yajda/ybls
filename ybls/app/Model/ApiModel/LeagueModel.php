<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31/031
 * Time: 11:00
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;

class LeagueModel extends Model
{
    protected $table = 'league';


    public static function league(){
        return self::select('img','content')
            ->first();
    }
}