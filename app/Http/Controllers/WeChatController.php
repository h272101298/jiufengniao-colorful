<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginPost;
use App\Http\Requests\UserAdressPost;
use App\Http\Requests\UserInfoPost;
use App\Libraries\Wxxcx;
use App\Modules\Good\GoodHandle;
use App\Modules\Proxy\ProxyHandle;
use App\Modules\System\TxConfig;
use App\Modules\User\UserHandle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

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
        $proxy_id = $post->proxy_id;
        if ($sessionKey){
            $info = $WX->decode($post->encryptedData,$post->iv);
            $info = json_decode($info);
            $user = $this->handle->getWeChatUserByOpenId($info->openId);
            if ($user){
                $token = CreateNonceStr(10);
                setUserToken($token,$user->id);
                $user->save();
                $proxyHandle = new ProxyHandle();
                $is_proxy = $proxyHandle->isProxy($proxy_id);
                if ($is_proxy){
                    $proxyHandle->addProxyList(0,[
                        'user_id'=>$user,
                        'proxy_id'=>$proxy_id
                    ]);
                }
                return response()->json([
                    'msg'=>'ok',
                    'data'=>[
                        'token'=>$token,
                        'id'=>$user->id
                    ]
                ]);
            }else{
                $result = $this->handle->addWeChatUser(0, [
                    'open_id'=>$info->openId,
                    'nickname'=>$info->nickName,
                    'avatarUrl'=>$info->avatarUrl
                    ]);
                $token = CreateNonceStr(10);
                setUserToken($token,$result);
                $proxyHandle = new ProxyHandle();
                $is_proxy = $proxyHandle->isProxy($proxy_id);
                if ($is_proxy){
                    $proxyHandle->addProxyList(0,[
                        'user_id'=>$result,
                        'proxy_id'=>$proxy_id
                    ]);
                }
                return response()->json([
                    'msg'=>'ok',
                    'data'=>[
                        'token'=>$token,
                        'id'=>$result
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
    public function getUserInfo()
    {
        $user_id = getRedisData(Input::get('token'));
        $info = $this->handle->getUserInfoByUserId($user_id);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$info
        ]);
    }
    public function addUserInfo(UserInfoPost $post)
    {
        $user_id = getRedisData($post->token);
        $this->handle->addUserInfoByUserId($user_id,[
            'phone'=>$post->phone,
            'name'=>$post->name,
            'sex'=>$post->sex,
//            'birthday'=>$post->birthday,
//            'desc'=>$post->desc
        ]);
        return response()->json([
            'msg'=>'ok'
        ]);
    }
    public function addUserAddress(UserAdressPost $post)
    {
        $user_id = getRedisData($post->token);
        $id = $post->id?$post->id:0;
        if ($id){
            $address = $this->handle->getAddressById($id);
            if ($address->user_id != $user_id){
                throw new \Exception('无权操作！');
            }
        }
        $this->handle->addUserAddress($id,[
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
    public function delUserAddress(Request $post)
    {
        $user_id = getRedisData($post->token);
        $address = $this->handle->getAddressById($post->id);
        if ($address->default==1){
            throw new \Exception('不能删除默认地址！');
        }
        if ($address->user_id != $user_id){
            throw new \Exception('无权操作！');
        }
        $this->handle->delAddressById($post->id);
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
    public function setDefaultAddress(Request $post)
    {
        $user_id = getRedisData($post->token);
        $id = $post->id;
        $address = $this->handle->getAddressById($id);

        if ($address->user_id != $user_id){
            throw new \Exception('无权操作！');
        }
        $this->handle->setDefaultAddress($id);
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
    public function getUserAddresses()
    {
        $user_id = getRedisData(Input::get('token'));
        $data = $this->handle->getUserAddresses($user_id);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function getUserAddress()
    {
//        $user_id = getRedisData(Input::get('token'));
        $id = Input::get('id');
        $data = $this->handle->getUserAddress($id);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function getDefaultAddress()
    {
        $user_id = getRedisData(Input::get('token'));
        $data = $this->handle->getDefaultAddress($user_id);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function getAttentionCount()
    {
        $user_id = getUserToken(Input::get('token'));
        $data = $this->handle->getUserAttentionCount($user_id);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function setAttention()
    {
        $attention_id = Input::get('attention_id');
        $user_id = getRedisData(Input::get('token'));
        $count = $this->handle->checkAttentionUser($user_id,$attention_id);
        if ($count){
            $this->handle->delAttentionUser($user_id,$attention_id);
        }else{
            $this->handle->addAttentionUser($user_id,$attention_id);
        }
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
    public function getWeChatUsers()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $open_id = Input::get('open_id','');
        $nickname = Input::get('nickname','');
        $data = $this->handle->getWeChatUsers($page,$limit,$open_id,$nickname,1);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function makeQrcode(Request $post)
    {
        $data = $post->all();
        $page = $data['page'];
        unset($data['page']);
        $scene = '';
        foreach ($data as $key =>$value){
            $scene .=$key.'='.$value.'&';
        }
        $scene = substr($scene,0,-1);
        $config = TxConfig::first();
        $wx = new Wxxcx($config->app_id,$config->app_secret);
        $data = [
            'scene'=>$scene,
            'page'=>$page
        ];
        $data = json_encode($data);
        $token = getRedisData('access_token');
        if (!$token){
            $token = $wx->getAccessToken();
            $token = $token['access_token'];
            setRedisData('access_token',$token,7100);
        }
//        var_dump($token);
//        dump($data);
        $qrcode = $wx->get_http_array('https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$token,$data,'json');
        return response()->make($qrcode,200,['content-type'=>'image/jpeg']);
    }
    public function getMyAttentions()
    {
        $user_id = getRedisData(Input::get('token'));
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $data = $this->handle->getUserAttentions($user_id,$page,$limit);
        if (!empty($data['data'])){
            foreach ($data['data'] as $datum){
                $datum->user = $this->handle->getWeChatUserById($datum->attention_id);
            }
        }
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function getMyFans()
    {
        $user_id = getRedisData(Input::get('token'));
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $data = $this->handle->getUserFans($user_id,$page,$limit);
        if (!empty($data['data'])){
            foreach ($data['data'] as $datum){
                $datum->user = $this->handle->getWeChatUserById($datum->user_id);
            }
        }
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function getUser()
    {
        $user_id = Input::get('user_id');
        $data = $this->handle->getWeChatUserById($user_id);
        if ($data){
            $goodHandle = new GoodHandle();
            $data->attentions = $this->handle->getUserAttentionCount($user_id);
            $data->collects = $goodHandle->countUserCollects($user_id);
        }
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function getMyScore()
    {
        $user_id = getRedisData(Input::get('token'));
        $data = $this->handle->getUserScore($user_id);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
}
