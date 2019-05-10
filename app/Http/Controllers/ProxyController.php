<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProxyApplyPost;
use App\Modules\Proxy\ProxyHandle;
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

}
