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
        if ($this->handle->addPicture(0,
            [
                'user_id'=>$user_id,
                'url'=>$post->url,
                'type'=>2,
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
    public function getPictures()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $user_id = getRedisData(Input::get('token'));
        $type = Input::get('type',1);
        switch ($type){
            case 1:
                $data = $this->handle->getPictures($page,$limit,1,0,2);
                break;
            case 2:
                $data = $this->handle->getPictures($page,$limit,0,0,2,$user_id);
                break;
            case 3:
                $data = $this->handle->getPictures($page,$limit,0,$user_id,2);
                break;
        }
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
}
