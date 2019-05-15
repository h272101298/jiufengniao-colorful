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
}
