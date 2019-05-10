<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProxyApplyPost;
use App\Modules\Proxy\ProxyHandle;
use App\Modules\User\UserHandle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

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
        $user_id = getRedisData(Input::get('token'));
        $data = $this->handle->getProxyUsers($user_id);
        if (!empty($data)){
            foreach ($data as $datum){
                $datum->user = $userHandle->getWeChatUserById($datum->user_id);
            }
        }
        return $data;
    }

}
