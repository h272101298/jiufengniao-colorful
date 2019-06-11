<?php
/**
 * Created by PhpStorm.
 * User: zeng
 * Date: 2019-05-08
 * Time: 17:03
 */

namespace App\Modules\Proxy;


use App\Modules\User\WeChatUser;
use Illuminate\Support\Facades\DB;

class ProxyHandle
{
    /*
     * 添加或修改分销申请
     */
    public function addProxyApply($id = 0,$data)
    {
        $apply = $id?ProxyApply::find($id):new ProxyApply();
        foreach ($data as $key=>$value){
            $apply->$key = $value;
        }
        if ($apply->save()){
            return $apply->id;
        }
        return false;
    }

    /**
     * @param $user_id
     * @return mixed
     * 获取用户的分销申请
     */
    public function getUserProxyApply($user_id)
    {
        return ProxyApply::where('user_id','=',$user_id)->first();
    }

    /**
     * @param $user_id
     * @return mixed
     * 获取用户是否存在申请
     */
    public function isUserProxyApply($user_id)
    {
        return ProxyApply::where('user_id','=',$user_id)->count();
    }

    public function getProxyApplies(int $page=1,int $limit = 10,int $user_id = 0,int $state = 0)
    {
        $db = DB::table('proxy_applies');
        if ($state){
            $db->where('state','=',$state);
        }
        if($user_id){
            $db->where('user_id','=',$user_id);
        }
        $count = $db->count();
        $data = $db->orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
        return [
            'count'=>$count,
            'data'=>$data
        ];
    }
    public function getProxyApply($id)
    {
        return ProxyApply::find($id);
    }
    public function addUserProxy($user_id)
    {
        $userProxy = new UserProxy();
        $userProxy->user_id = $user_id;
        if ($userProxy->save()){
            return true;
        }
        return false;
    }
    public function isProxy($user_id)
    {
        return UserProxy::where('user_id','=',$user_id)->count();
    }
    public function delUserProxy($user_id)
    {
        return UserProxy::where('user_id','=',$user_id)->delete();
    }
    public function getUserProxyAmount($user_id)
    {
        return ProxyAmount::where('user_id','=',$user_id)->first();
    }
    public function getProxyAmounts($page=1,$limit=10,$format=0)
    {
        $db = DB::table('proxy_amounts');
        $count = $db->count();
        $data = $db->orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
        if ($format==1&&count($data)!=0){
            foreach ($data as $datum){
                $datum->user = WeChatUser::find($datum->user_id);
            }
        }
        return [
            'data'=>$data,
            'count'=>$count
        ];
    }

    public function getProxyAmountListCount($user_id)
    {
        $db = ProxyAmountList::where('user_id','=',$user_id);
        $today = $db->whereDay('created_at',date('Y-m-d',time()))->sum('amount');
        $yesterday = $db->whereDay('created_at',date("Y-m-d",strtotime("-1 day")))->sum('amount');
        return [
            'today'=>$today,
            'yesterday'=>$yesterday
        ];
    }
    public function getProxyUsers($user_id,$page=1,$limit=10)
    {
        return ProxyList::where('proxy_id','=',$user_id)->orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
    }
    public function getProxyList($page=1,$limit=10,$format=0)
    {
        $db =DB::table('user_proxies');
        $count = $db->count();
        $data = $db->orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
        if ($format){
            if (count($data)!=0){
                foreach ($data as $datum){
                    $datum->user = WeChatUser::find($datum->user_id);
                }
            }
        }
        return [
            'data'=>$data,
            'count'=>$count
        ];
    }
    public function getProxyAmountConfig()
    {
        return ProxyAmountConfig::first();
    }
    public function setProxyAmountConfig($data)
    {
        $config = ProxyAmountConfig::first();
        if (empty($config)){
            $config = new ProxyAmountConfig();
        }
        foreach ($data as $key=>$value){
            $config->$key = $value;
        }
        if ($config->save()){
            return true;
        }
        return false;
    }
    public function addWithdraw($id,$data)
    {
        $withdraw = $id?Withdraw::find($id):new Withdraw();
        foreach ($data as $key=>$value){
            $withdraw->$key = $value;
        }
        if ($withdraw->save()){
            return $withdraw->id;
        }
        return false;
    }
    public function delWithdraw($id)
    {
        return Withdraw::find($id)->delete();
    }
    public function getWithdraws($page=1,$limit=10,$format=0)
    {
        $db = DB::table('withdraws');
        $count = $db->count();
        $data = $db->orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
        if ($format==1&&count($data)!=0){
            foreach ($data as $datum){
                $datum->user = WeChatUser::find($datum->user_id);
            }
        }
        return [
            'data'=>$data,
            'count'=>$count
        ];
    }
}