<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/30/030
 * Time: 10:50
 */

namespace App\Model\ApiModel;


use Illuminate\Database\Eloquent\Model;

class AboutModel extends Model
{
    protected $table = 'aboutus';

    public static function about(){
        return self::select('content','img')
            ->first();
    }
}