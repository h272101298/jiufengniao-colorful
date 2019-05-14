<?php

namespace App\Http\Controllers;

use App\Http\Requests\PicturePost;
use App\Modules\Picture\PictureHandle;
use Illuminate\Http\Request;

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
                'type'=>2,
            ]));
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
//    public function
}
