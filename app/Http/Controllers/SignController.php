<?php

namespace App\Http\Controllers;

use App\Modules\Sign\SignHandle;
use Illuminate\Http\Request;

class SignController extends Controller
{
    //
    public function __construct()
    {
        $this->handle = new SignHandle();
    }
    public function addSignConfig(Request $post)
    {
        $id = $post->id?$post->id:0;
        $data = [
            'days'=>$post->days,
            'type'=>$post->type?$post->type:1,
            'reward'=>$post->reward
        ];
        if ($this->handle->setSignConfig($id,$data)){
            return jsonResponse([
                'msg'=>'ok'
            ]);
        }
        throw new \Exception('error');
    }
    public function getSignConfigs()
    {
        $data = $this->handle->getSignConfigs();
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }

}
