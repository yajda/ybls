<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/23/023
 * Time: 17:58
 */
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Api', 'prefix' => '/api'/*, 'domain' => config('app.url')*/], function () {

    /* 用户注册 */
    Route::post('/reg', 'LoginController@register');

    /* 用户登录 */
    Route::post('/login', 'LoginController@login');

    /* 获取手机验证码 */
    Route::post('/mobileCode', 'LoginController@getMobileCode');

   // Route::group(['middleware' => 'verify'], function () {

        /* 首页 */
        Route::post('/home','HomeController@home_index');

        /* 项目类型详情 */
        Route::post('/home/itinfo','HomeController@itemtypeinfo');

        /* 律师列表 */
        Route::post('home/lawyer','HomeController@lawyer');

        /* 律师详情 */
        Route::post('/home/law','HomeController@lawyerinfo');

        /* 推荐案例详情 */
        Route::post('/home/anli','HomeController@anliinfo');

        /* 点赞 */
        Route::post('/home/pra','HomeController@praise');

        /* 收藏 */
        Route::post('/home/collect','HomeController@collect');

        /* 项目 */
        Route::post('/item','ItemController@itemindex');

        /* 项目详情 */
        Route::post('/item/itinfo','ItemController@iteminfo');

        /* 发现 */
        Route::post('/found','FoundController@anli');

        /* 我的 */
        Route::post('/my','MyController@my');

        /* 关注 */
        Route::post('/home/follow','HomeController@follow');

        /* 我的关注 */
        Route::post('my/follow','MyController@myfollow');

        /* 上传头像 */
        Route::post('/my/upload','MyController@upload');

        /* 修改个人信息 */
        Route::post('/my/userinfo','MyController@userinfo');

        /* 我的收藏 */
        Route::post('/my/collect','MyController@mycollect');

        /* 关于我们 */
        Route::get('/about','MyController@about');

        /* 加盟 */
        Route::get('/league','MyController@league');

        /* 反馈意见 */
        Route::post('/suggestion','MyController@suggestion');

        /* 修改密码 */
        Route::post('/changepassword','MyController@updatepassword');

        /* 消息 */
        Route::post('/news','NewsController@news');

        /* 通知详情 */
        Route::get('/notify','NewsController@notifyinfo');

   // });

});