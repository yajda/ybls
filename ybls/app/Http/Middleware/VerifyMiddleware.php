<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29/029
 * Time: 13:44
 */

namespace App\Http\Middleware;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Controllers\Api\UserToken;
use Closure;
use Illuminate\Support\Facades\Input;

class VerifyMiddleware
{
    public function handle($request, Closure $next)
    {
        $get = Input::get();
        if (empty($get['user_token'])) {
            BaseApiController::error('请登录', [], true);
        }
        //验证token
        UserToken::verifyUserToken($get['user_token']);
        return $next($request);
    }
}