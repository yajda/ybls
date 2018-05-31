<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29/029
 * Time: 15:13
 */

namespace App\Http\Controllers\Api;


use App\Model\ApiModel\AboutModel;
use App\Model\ApiModel\AnliModel;
use App\Model\ApiModel\CollectModel;
use App\Model\ApiModel\FollowModel;
use App\Model\ApiModel\GoodatModel;
use App\Model\ApiModel\LawyerModel;
use App\Model\ApiModel\LeagueModel;
use App\Model\ApiModel\PraiseModel;
use App\Model\ApiModel\SuggestionModel;
use App\Model\ApiModel\UserModel;
use Illuminate\Support\Facades\Input;

class MyController extends BaseApiController
{
    /**
     * 我的
     * @return mixed
     */
    public function My(){

        $pram = Input::get();
        $user_id = $pram['user_id'];

        $user = UserModel::getuserinfo($user_id);
        if(!$user){
            self::error("用户不存在");
        }
        return $user;
    }

    /**
     * 修改密码
     */
    public function updatepassword(){
        $param = Input::get();
        //检查参数
        self::checkParam($param,['new_password','password','again_password']);
        $user = UserModel::getuserinfo($param['user_id']);

        //判断密码是否正确
        $password = LoginController::getPassword($param['password']);
        $new_password = LoginController::getPassword($param['new_password']);
        if($password !== $user->password){
            self::error("密码错误");
        }
        //判断新密码与确认密码是否一致
        if($param['new_password'] != $param['again_password']){
            self::error("确认密码与新密码不一致");
        }
        $result = UserModel::changePassword($user->phone,$new_password);
        if(!$result){
            self::error("修改密码失败");
        }
        self::success("密码修改成功");
    }


    /**
     * 修改个人信息
     */
    public function userinfo()
    {
        $param = Input::get();

        $saveData = [
            "avatar"    => !isset($param['avatar']) ? null : $param['avatar'],
            "username"  => !isset($param['username']) ? null : $param['username'],
            "sex"       => !isset($param['sex']) ? null : $param['sex'],
            "age"  => !isset($param['age']) ? null : $param['age'],
            "area"      => !isset($param['area']) ? null : $param['area'],
            "phone"     => !isset($param['phone']) ? null : $param['phone']
        ];
        foreach ($saveData as $k => $v) {
            if ($v === null) {
                unset($saveData[$k]);
            }
        }
        if (empty($saveData)) {
            self::error("修改失败");
        }
        $result = UserModel::saveUserInfo($saveData, $param['user_id']);
        if ($result !== false) {
            self::success([], "修改成功");
        }
        self::error("修改失败");
    }


    /**
     * 我的收藏
     * @return array
     */
    public function mycollect(){

        $param = Input::get();
        //用户收藏的案例id集合
        $anli_id = CollectModel::getanliid($param['user_id']);

        if (!$anli_id) {
            self::error("未发现案例");
        }
        $an = [];
        $data = [];
        foreach($anli_id as $k =>$val){

            $data[$k] = AnliModel::getfirstanli($val['anli_id']);

            //查询点赞该案例的用户
            $an_user = PraiseModel::getpraise($val['anli_id']);

            foreach ($an_user as $v) {
                $an[] = $v['user_id'];
            }
            //判断用户是否已点赞该案例
            if (in_array($param['user_id'], $an)) {
                $data[$k]['is_praise'] = 1;
            }else{
                $data[$k]['is_praise'] = 0;
            }

            $data[$k]['is_collect'] = 1;
        }
        return $data;
    }


    /**
     * 我的关注
     * @return array
     */
    public function myfollow(){
        $param = Input::get();

        if(!isset($param['user_id'])){
            self::error("用户id无效");
        }
        $res = FollowModel::follow($param['user_id']);
        if($res->isEmpty()){
            self::error("该用户未关注律师");
        }
        $data = [];
        foreach($res as $k => $val){
            $data[$k] = LawyerModel::getlawyerinfo($val->lawyer_id);
            //获取擅长领域
            $good_at = GoodatModel::good_at($val->lawyer_id);

            $str = '';
            foreach($good_at as $v){
                $str .= $v['item_name'].",";
            }
            $data[$k]['good_at'] = $str . $data[$k]['good_at'];
        }
        return $data;
    }


    /**
     * 上传图片
     */
    public function upload()
    {
        try {
            if (
                (!empty($_FILES[0]) && is_array($_FILES[0])) ||
                (!empty($_FILES['image0']) && is_array($_FILES['image0']))
            ) {
                ksort($_FILES);
                $images = [];
                foreach ($_FILES as $val) {
                    $images[] = $this->uploadImage($val, true);
                }
                self::success($images);
            } elseif (is_array($_FILES ['image']['type'])) {
                $this->uploadImages();
            } else {
                $this->uploadImage($_FILES ['image']);
            }
        } catch (\Exception $e) {
            self::error($e->getMessage());
        }
    }

    /**
     * 上传一张图片
     * @param $image
     * @param bool $type
     * @return bool|string
     */
    private function uploadImage($image, $type = false)
    {
        if (!empty($image)) {
            // 创建文件名
            $name = md5(TIME_NOW . rand(100, 999));
            // 是否是图片文件
            if (!is_numeric(strpos($image['type'], 'image/')))
                self::error('请上传图片文件');
            // 建立文件名
            $fixed = str_replace('image/', '.', $image['type']); //后缀名
            $fileName = $name . $fixed;

            //创建当天目录
            $dir = $ymd = date('Ymd', TIME_NOW);
            $dir = public_path('/pic/' . $dir);
            if (!file_exists($dir)) {
                if (!mkdir($dir,0777,true)) {
                    /*目录创建失败*/
                    self::error('目录创建失败');
                }
            }
            $dir .= "/";
            $fileName = $this->checkFileName($dir . $fileName, $name, $fixed);
            move_uploaded_file($image["tmp_name"], $dir . $fileName);
            $fileName = "/pic/" . $ymd . "/" . $fileName;
            $data = [$fileName];
            if ($type) {
                return $fileName;
            }
            self::success($data);
        } else {
            self::error("请上传图片");
        }
        return false;
    }

    /**
     * 上传多张图片
     */
    private function uploadImages()
    {
        $data = [];
        //创建当天目录
        $dir = $ymd = date('Ymd', TIME_NOW);
        $dir = public_path('/pic/' . $dir);
        if (!file_exists($dir)) {
            if (!mkdir($dir)) {
                /*目录创建失败*/
                self::error('目录创建失败');
            }
        }
        $dir .= "/";

        //判断是否全部是图片文件
        foreach ($_FILES['image']['type'] as $k => $v) {
            if (!is_numeric(strpos($_FILES['image']['type'][$k], 'image/')))
                self::error('请上传图片文件');
        }

        foreach ($_FILES['image']['type'] as $k => $v) {
            // 创建文件名
            $name = md5(TIME_NOW . rand(1000, 9999));
            // 建立文件名
            $fixed = str_replace('image/', '.', $_FILES['image']['type'][$k]); //后缀名
            $fileName = $name . $fixed;

            $fileName = $this->checkFileName($dir . $fileName, $name, $fixed);
            move_uploaded_file($_FILES['image']["tmp_name"][$k], $dir . $fileName);
            $fileName = "/pic/" . $ymd . "/" . $fileName;

            $data[] = $fileName;
        }
        self::success($data);
    }

    /**
     * 判断图片地址是否存在
     * @param string $dir 路径
     * @param string $name 名称
     * @param string $fixed 后缀名
     * @return string 返回文件名称
     */
    private function checkFileName($dir, $name, $fixed = ".png")
    {
        if (file_exists($dir)) {
            $newName = $name . "_1";
            $dir = str_replace($name, $newName, $dir);
            return $this->checkFileName($dir, $newName, $fixed);
        } else {
            return $name . $fixed;
        }
    }


    /**
     * 关于我们
     * @return mixed
     */
    public function about(){

        $data = AboutModel::about();
        $data->title = '关于我们';

        return view('api.about',['data' => $data]);
    }


    public function league(){
        $data = LeagueModel::league();
        if(!$data){
            self::error("加载失败");
        }
        $data->title = '加盟申请';
        return view('api.about',['data' => $data]);
    }


    /**
     * 意见反馈
     */
    public function suggestion(){
        $param = Input::get();
        $content = $param['content'];
        $user_id = $param['user_id'];
        $res = SuggestionModel::suggestion($content,$user_id);
        if($res){
            self::success("已接受到您的反馈");
        }else{
            self::error("反馈失败，请重新提交");
        }
    }

}