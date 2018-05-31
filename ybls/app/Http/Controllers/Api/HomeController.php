<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/24/024
 * Time: 18:11
 */

namespace App\Http\Controllers\Api;


use App\Model\ApiModel\AnliModel;
use App\Model\ApiModel\CollectModel;
use App\Model\ApiModel\ExperienceModel;
use App\Model\ApiModel\FollowModel;
use App\Model\ApiModel\GoodatModel;
use App\Model\ApiModel\Item_typeModel;
use App\Model\ApiModel\LawyerModel;
use App\Model\ApiModel\LookModel;
use App\Model\ApiModel\PraiseModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class HomeController extends BaseApiController
{
    /**
     * 首页
     * @return string
     */
    public function home_index(){

        //获取项目所有类型
        $item_type = Item_typeModel::getitemtype();

        //获取在线律师
        $onlinelawyer = LawyerModel::getonlinelawyer();

        //获取推荐案例
        $anli = AnliModel::getrecommendanli();

        $data = array(
            'item_type'    =>   $item_type,
            'onlinelawyer' =>  $onlinelawyer,
            'anli' =>  $anli
        );
        return $data;

    }


    /**
     * 项目类型详情
     * @return array
     */
    public function itemtypeinfo(){

        //获取项目类型id
        $res = Input::get();

        $id = $res['id'];

        //获取项目类型详情
        $itemtypeinfo = Item_typeModel::getitemtypeinfo($id);

        //获取推荐律师
        $lawyer = LawyerModel::getitemtypelawyerinfo($id);

        //获取相似案例
        $anliinfo = AnliModel::getanliinfo($id);

        $data = array(
            'itemtypeinfo'    =>   $itemtypeinfo,
            'lawyer' =>  $lawyer,
            'anliinfo' =>  $anliinfo
        );

        return $data;
    }


    /**
     * 律师列表
     * @return mixed
     */
    public function lawyer(){
        $param = Input::get();
        $onlinelawyer = LawyerModel::lawyerlist($param);
        foreach($onlinelawyer as $k => $val){

            //获取擅长领域
            $good_at = GoodatModel::good_at($val['id']);

            $str = '';
            foreach($good_at as $v){
                $str .= $v['item_name'].',';
            }
            $str = substr($str,0,-1);
            $str .="等,";
            $onlinelawyer[$k]['good_at'] = $str . $onlinelawyer[$k]['good_at'];
        }
        if($onlinelawyer->isEmpty()){
            self::error("没有相关律师");
        }
        return $onlinelawyer;
    }

    /**
     * 律师详情
     * @return mixed
     */
    public function lawyerinfo(){
        //获取律师id
        $res = Input::get();
        $id = $res['id'];

        //获取律师信息
        $lawyer = LawyerModel::getlawyerinfo($id);

        //获取擅长领域
        $good_at = GoodatModel::good_at($id);

        $str = '';
        foreach($good_at as $v){
            $str .= $v['item_name'].",";
        }

        //是否关注
        $lawyer_id = FollowModel::follow($res['user_id']);
        $list = [];
        foreach($lawyer_id as $v){
            $list[] = $v['user_id'];
        }

        //判断用户是否已关注该律师
        if(in_array($res['user_id'],$list)){
            //如果存在，则是已关注
            $lawyer[0]['is_follow'] = 1;
        }else{
            //不存在，未关注
            $lawyer[0]['is_follow'] = 1;

        }

        //获取从业经验
        $experience = ExperienceModel::experience($id);

        //整合信息
        $lawyer[0]['good_at'] = $str . $lawyer[0]['good_at'];
        $lawyer['experience'] = $experience;

        return $lawyer;
    }


    /**
     * 获取案例信息
     * @return mixed
     */
    public function anliinfo(){
        //获取数据
        $res = Input::get();
        $an_id = $res['id'];
        //$user_id = $res['user_id'];

        //查询查看该案例的用户
        $an_user = LookModel::getlook($an_id);
        $an = [];
        foreach($an_user as $v){
            $an[] = $v['user_id'];
        }

        //判断用户是否以查看了该案例
        if(!in_array(UserToken::_getUserId(),$an)){
            //如果不存在
            $num = AnliModel::look($an_id);
            $num = $num[0]['look'];
            $num = $num + 1;
            $pranum = AnliModel::updatelook($an_id,$num);
            if(!$pranum){
                self::error("查看次数失效");
            }
            $res = LookModel::addlook($an_id,UserToken::_getUserId());
            if(!$res){
                self::error("添加查看记录失败");
            }
        }
        $anliinfo = AnliModel::getanliinfo($an_id);
        return $anliinfo;
    }


    /**
     * 关注
     */
    public function follow(){
        //获取数据
        $parm = Input::get();
        $lawyer_id = $parm['lawyer_id'];
        $user_id = $parm['user_id'];

        //查询用户关注的所有律师
        $lawyerlist = FollowModel::follow($user_id);
        $an = [];
        foreach($lawyerlist as $v){
            $an[] = $v['lawyer_id'];
        }
        //判断律师是否被关注
        if(in_array($lawyer_id,$an)){
            //如果存在，则是取消关注操作
            $follow1 = FollowModel::delfollow($lawyer_id,$user_id);
            if(!$follow1){
                self::error("取消关注失败");
            }
            $data['is_follow'] = 0;
            self::success($data,"取消关注成功");
        }else{
            //不存在，进行关注操作
            $follow2 = FollowModel::addfollow($lawyer_id,$user_id);
            if(!$follow2){
                self::error("关注失败");
            }
            $data['is_follow'] = 1;
            self::success($data,"关注成功");
        }
    }



    /**
     * 点赞
     */
    public function praise(){
        //获取数据
        $parm = Input::get();
        $an_id = $parm['an_id'];
        $user_id = $parm['user_id'];

        $data = array(
          'anli_id'  => $an_id,
            'user_id' => $user_id,
        );
        //查询点赞该案例的用户
        $an_user = PraiseModel::getpraise($an_id);
        $an = [];
        foreach($an_user as $v){
            $an[] = $v['user_id'];
        }

        //判断用户是否已点赞该案例
        if(in_array($user_id,$an)){
            //如果存在，则是取消点赞操作
            $pra1 = PraiseModel::delpra($an_id,$user_id);
            DB::beginTransaction();
            if($pra1){
                $num = AnliModel::praise($an_id);
                $num = $num[0]['praise'];
                $num = $num - 1;
                $pranum1 = AnliModel::updatepra($an_id,$num);
                if($pranum1){
                    DB::commit();
                    $data['praise'] = $num;
                    self::success($data,"取消点赞成功");
                }else{
                    DB::rollback();//事务回滚
                    $data['praise'] = $num;
                    self::error($data,"取消点赞失败");
                }
            }else{
                self::error("取消点赞失败");
            }

        }else{
            //不存在，进行点赞操作
            $pra2 = PraiseModel::addpra($an_id,$user_id);
            DB::beginTransaction();
            if($pra2){
                $num = AnliModel::praise($an_id);
                $num = $num[0]['praise'];
                $num = $num + 1;
                $pranum2 = AnliModel::updatepra($an_id,$num);
                if($pranum2){
                    DB::commit();
                    $data['praise'] = $num;
                    self::success($data,"点赞成功");
                }else{
                    DB::rollback();//事务回滚
                    $data['praise'] = $num;
                    self::error($data,"点赞失败");
                }
            }else{
                self::error("点赞失败");
            }
        }
    }


    /**
     * 收藏
     */
    public function collect(){
        //获取数据
        $parm = Input::get();
        $an_id = $parm['an_id'];
        $user_id = $parm['user_id'];

        $data = array(
            'anli_id'  => $an_id,
            'user_id' => $user_id
        );
        //查询收藏该案例的用户
        $an_user = CollectModel::getcollect($an_id);
        $an = [];
        foreach($an_user as $v){
            $an[] = $v['user_id'];
        }

        //判断用户是否已收藏该案例
        if(in_array($user_id,$an)){
            //如果存在，则是取消收藏操作
            $pra1 = CollectModel::delcollect($an_id,$user_id);
            DB::beginTransaction();
            if($pra1){
                $num = AnliModel::collect($an_id);
                $num = $num[0]['collect'];
                $num = $num - 1;
                $pranum1 = AnliModel::updatecollect($an_id,$num);
                if($pranum1){
                    DB::commit();
                    $data['collect'] = $num;
                    self::success($data,"取消收藏成功");
                }else{
                    DB::rollback();//事务回滚
                    $data['collect'] = $num;
                    self::error($data,"取消收藏失败");
                }
            }else{
                self::error("取消收藏失败");
            }

        }else{
            //不存在，进行收藏操作
            $pra2 = CollectModel::addcollect($an_id,$user_id);
            DB::beginTransaction();
            if($pra2){
                $num = AnliModel::collect($an_id);
                $num = $num[0]['collect'];
                $num = $num + 1;
                $pranum2 = AnliModel::updatecollect($an_id,$num);
                if($pranum2){
                    DB::commit();
                    $data['collect'] = $num;
                    self::success($data,"收藏成功");
                }else{
                    DB::rollback();//事务回滚
                    $data['collect'] = $num;
                    self::error($data,"收藏失败");
                }
            }else{
                self::error("收藏失败");
            }
        }
    }
}