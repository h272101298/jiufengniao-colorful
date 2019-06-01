<?php

namespace App\Http\Controllers;

use App\Modules\Launcher\LauncherHandle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class LauncherController extends Controller
{
    //
    public function __construct()
    {
        $this->handle = new LauncherHandle();
    }
    public function addLauncherImage(Request $post)
    {
        $id = $post->id?$post->id:0;
        $data = [
            'url'=>$post->url,
            'state'=>$post->state?$post->state:1
        ];
        if ($this->handle->addLauncherImage($id,$data)){
            return jsonResponse(['msg'=>'ok']);
        }
        throw new \Exception('ERROR');
    }
    public function getLauncherImages()
    {
        $data = $this->handle->getLauncherImages();
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function delLauncherImage()
    {
        $id = Input::get('id');
        $this->handle->delLauncherImage($id);
        return jsonResponse(['msg'=>'ok']);
    }
}
