<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginPost;
use App\Http\Requests\UserAdressPost;
use App\Http\Requests\UserInfoPost;
use App\Libraries\Wxxcx;
use App\Modules\User\UserHandle;

class WeChatController extends Controller
{
    public function __construct()
    {
        $this->handle = new UserHandle();
    }

    //
    public function createUser(LoginPost $post)
    {
        $WX = new Wxxcx(\config('wxxcx.app_id'),\config('wxxcx.app_secret'));
        $sessionKey = $WX->getSessionKey($post->code);
        if ($sessionKey){
            $info = $WX->decode($post->encryptedData,$post->iv);
            $info = json_decode($info);
            $user = $this->handle->getWeChatUserByOpenId($info->openId);
            if ($user){
                $token = CreateNonceStr(10);
                setUserToken($token,$user->id);
                $user->save();
                return response()->json([
                    'msg'=>'ok',
                    'data'=>[
                        'token'=>$token
                    ]
                ]);
            }else{
                $user = $this->handle->addWeChatUser(0, [
                    'open_id'=>$info->openId,
                    'nickname'=>$info->nickName,
                    'avatarUrl'=>$info->avatarUrl
                    ]);
                $token = CreateNonceStr(10);
                setUserToken($token,$user->id);
                return response()->json([
                    'msg'=>'ok',
                    'data'=>[
                        'token'=>$token
                    ]
                ]);
            }
        }else{
            return \jsonResponse([
                'msg'=>'ERROR',
                'data'=>$WX
            ],400);
        }
    }
    public function addUserInfo(UserInfoPost $post)
    {
        $user_id = getRedisData($post->token);
        $this->handle->addUserInfoByUserId($user_id,[
            'phone'=>$post->phone,
            'name'=>$post->name,
            'sex'=>$post->sex,
            'birthday'=>$post->birthday,
            'desc'=>$post->desc
        ]);
        return response()->json([
            'msg'=>'ok'
        ]);
    }
    public function addUserAddress(UserAdressPost $post)
    {
        $user_id = getRedisData($post->token);
        $id = $this->handle->addUserAddress(0,[
            'user_id'=>$user_id,
            'city'=>implode(',',$post->city),
            'address'=>$post->address,
            'phone'=>$post->phone,
            'name'=>$post->name
        ]);
        if ($post->default&&$post->default==1){
            $this->handle->setDefaultAddress($id);
        }
        return response()->json([
            'msg'=>'ok'
        ]);
    }
//    public function
}
