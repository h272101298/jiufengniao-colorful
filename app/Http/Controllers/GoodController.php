<?php

namespace App\Http\Controllers;

use App\Modules\Good\GoodHandle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class GoodController extends Controller
{
    //
    public function __construct()
    {
        $this->handle = new GoodHandle();
    }

    public function addType(Request $post)
    {
        $title = $post->title;
        $icon = $post->icon;
        $id = $post->id?$post->id:0;
        $result = $this->handle->addType($id,[
            'title'=>$title,
            'icon'=>$icon
        ]);
        if ($result){
            return jsonResponse([
                'msg'=>'ok'
            ]);
        }
        throw new \Exception('ERROR');
    }
    public function getTypes()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $data = $this->handle->getTypes($page,$limit);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function delType()
    {
        $id = Input::get('id');
        $this->handle->delType($id);
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
    public function addBanner(Request $post)
    {
        $id = $post->id?$post->id:0;
        $good_id = $post->good_id?$post->good_id:0;
        $type = $post->type?$post->type:1;
        $url = $post->url;
        $result = $this->handle->addBanner($id,[
            'good_id'=>$good_id,
            'url'=>$url,
            'type'=>$type
        ]);
        if ($result){
            return jsonResponse([
               'msg'=>'ok'
            ]);
        }
        throw new \Exception('ERROR');
    }

}
