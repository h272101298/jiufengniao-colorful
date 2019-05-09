<?php
/**
 * Created by PhpStorm.
 * User: zeng
 * Date: 2019-05-08
 * Time: 17:03
 */

namespace App\Modules\Proxy;


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
}