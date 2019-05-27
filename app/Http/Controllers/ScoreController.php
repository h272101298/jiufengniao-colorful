<?php

namespace App\Http\Controllers;

use App\Modules\Score\ScoreHandle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ScoreController extends Controller
{
    //
    public function __construct()
    {
        $this->handle = new ScoreHandle();
    }

    public function addScoreProduct(Request $post)
    {
        $id = $post->id?$post->id:0;
        $data = [
            'score'=>$post->score,
            'title'=>$post->title,
            'detail'=>$post->detail,
            'share_title'=>$post->share_titile,
            'cover'=>$post->cover
        ];
        $result = $this->handle->addScoreProduct($id,$data);
        if ($result){
            $pictures = $post->pictures;
            if (count($pictures)!=0){
                foreach ($pictures as $picture){
                    $this->handle->addScoreProductImage($result,$picture);
                }
            }
            return jsonResponse([
                'msg'=>'ok'
            ]);
        }
        throw new \Exception('error');
    }
    public function delScoreProduct()
    {
        $id = Input::get('id');
        $this->handle->delScoreProduct($id);
        $this->handle->delScoreProductImages($id);
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
    public function getScoreProducts()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $data = $this->handle->getScoreProducts($page,$limit);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function getScoreProduct()
    {
        $id = Input::get('id');
        $product = $this->handle->getScoreProduct($id);
        $product->pictures = $this->handle->getScoreProductImages($id);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$product
        ]);
    }
}
