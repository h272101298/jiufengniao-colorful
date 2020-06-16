<?php

namespace App\Http\Controllers;

use App\Http\Requests\PicturePost;
use App\Modules\Picture\PictureHandle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class PictureController extends Controller
{
    //
    public function __construct()
    {
        $this->handle = new PictureHandle();
    }

    public function addPicture(PicturePost $post)
    {
        $user_id = getRedisData($post->token);
        $type = $post->type?$post->type:2;
        $state = $type==2?0:1;
        if ($this->handle->addPicture(0,
            [
                'user_id'=>$user_id,
                'url'=>$post->url,
                'type'=>$type,
                'state'=>$state
            ]));
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
    public function getUserPictures()
    {
        $user_id = getRedisData(Input::get('token'));
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $data = $this->handle->getUserCollect($user_id,$page,$limit);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function getSystemPictures()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $type = Input::get('type',1);
        $data = $this->handle->getPictures($page,$limit,$type);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function getPicturesApi()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $data = $this->handle->getPicturesApi($page,$limit,1,0,2);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function delPicture()
    {
        $id = Input::get('id');
        $this->handle->delPicture($id);
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
    public function getPictures()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $user_id= getRedisData(Input::get('token'));
        $type = Input::get('type',1);
        $data = $this->handle->getPictures($page,$limit,$type);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function checkPicture()
    {
        $id = Input::get('id');
        $pass = Input::get('pass',2);
        if ($pass==1){
            $this->handle->addPicture($id,['state'=>1]);
        }else{
            $this->handle->delPicture($id);
        }
        return jsonResponse([
            'msg'=>"ok"
        ]);
    }
//    public function addPicture(){}
}
