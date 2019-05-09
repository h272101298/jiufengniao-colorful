<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProxyApplyPost;
use App\Modules\Proxy\ProxyHandle;
use Illuminate\Http\Request;

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

}
