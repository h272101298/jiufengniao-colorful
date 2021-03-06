<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProxyApplyPost;
use App\Modules\Proxy\ProxyHandle;
use App\Modules\User\UserHandle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use mysql_xdevapi\Exception;

class ProxyController extends Controller
{
    //
    public function __construct()
    {
        $this->handle = new ProxyHandle();
    }

    public function addProxyApply(ProxyApplyPost $post)
    {
        $data = [
            'user_id'=>getRedisData($post->token),
            'phone'=>$post->phone,
            'name'=>$post->name,
//            'sex'=>$post->sex,
            'bank'=>$post->bank,
            'account'=>$post->account
        ];
        if ($this->handle->addProxyApply(0,$data)){
            return response()->json([
                'msg'=>'ok'
            ]);
        }
        throw new \Exception('系统错误！');
    }

    public function getUserProxyAmount()
    {
        $user_id = getRedisData(Input::get('token'));
        $amount = $this->handle->getUserProxyAmount($user_id);
        $list = $this->handle->getProxyAmountListCount($user_id);
        return response()->json([
            'msg'=>'ok',
            'data'=>[
                'amount'=>empty($amount)?0:$amount->real_amount,
                'today'=>$list['today'],
                'yesterday'=>$list['yesterday']
            ]
        ]);
    }
    public function getProxyUsers()
    {
        $userHandle = new UserHandle();
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $user_id = getRedisData(Input::get('token'));
        $data = $this->handle->getProxyUsers($user_id);
        if (!empty($data)){
            foreach ($data['data'] as $datum){
                $datum->user = $userHandle->getWeChatUserById($datum->user_id);
            }
        }
        return $data;
    }
    public function getProxyApplies()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $state = Input::get('state',0);
        $data = $this->handle->getProxyApplies($page,$limit,0,$state);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function passProxyApply()
    {
        $state = Input::get('state',3);
        $id = Input::get('id');
        $apply = $this->handle->getProxyApply($id);
        if ($state==2){
            $this->handle->addUserProxy($apply->user_id);
        }
        $this->handle->addProxyApply($id,['state'=>$state]);
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }

    public function getProxyList()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $data = $this->handle->getProxyList($page,$limit,1);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function getProxyAmountConfig()
    {
        $data = $this->handle->getProxyAmountConfig();
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function setProxyAmountConfig(Request $post)
    {
        $data = [
          'system'=>$post->system?$post->system:0,
          'level1'=>$post->level1?$post->level1:0,
          'level2'=>$post->level2?$post->level2:0,
          'level3'=>$post->level3?$post->level3:0,
        ];
        if ($this->handle->setProxyAmountConfig($data)){
            return jsonResponse([
                'msg'=>'ok'
            ]);
        }
        throw new Exception('error');
    }
    public function getProxyAmounts()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $data = $this->handle->getProxyAmounts($page,$limit,1);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function getWithdraws()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $data = $this->handle->getWithdraws($page,$limit,1);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function addWithdraw(Request $post)
    {
        $user_id = getRedisData($post->token);
        $amount = $post->amount;
        $data = [
            'user_id'=>$user_id,
            'amount'=>$amount
        ];
        $userAmount = $this->handle->getUserProxyAmount($user_id);
        if (empty($userAmount)||$userAmount->real_amount<$amount){
            return jsonResponse([
                'msg'=>'账户余额不足！'
            ],433);
        }
        if ($this->handle->addWithdraw(0,$data)){
            $this->handle->setUserProxyAmount($user_id,0-$amount);
            return jsonResponse([
                'msg'=>'ok'
            ]);
        }
        throw new \Exception('ERROR');
    }
    public function getProxyInfo(Request $post)
    {
        $user_id = getRedisData($post->token);
        $is_proxy = $this->handle->isProxy($user_id);
        $apply = $this->handle->getUserProxyApply($user_id);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>[
                'is_proxy'=>empty($is_proxy)?0:1,
                'apply'=>empty($apply)?0:1,
                'proxy_apply'=>empty($apply)?0:$apply->state
            ]
        ]);
    }

    public function getProxyApply()
    {
        $user_id = getRedisData(Input::get('token'));
        $data = $this->handle->getUserProxyApply($user_id);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function passWithdraw()
    {
        $id = Input::get('id');
        $state = Input::get('state',3);
        $withdraw = $this->handle->getWithdraw($id);
        if ($state==3){
            $this->handle->setUserProxyAmount($withdraw->user_id,$withdraw->amount);
        }
        $this->handle->addWithdraw($id,['state'=>$state]);
        return jsonResponse(['msg'=>'ok']);
    }
    public function getProxyUsersApi()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $user_id = getRedisData(Input::get('token'));
        $data = $this->handle->getProxyUsers($user_id,$page,$limit);
        if (!empty($data['data'])){
            $userHandle = new UserHandle();
            foreach ($data['data'] as $datum){
                $datum->user = $userHandle->getWeChatUserById($datum->user_id);
            }
        }
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }

}
