<?php
/**
 * Created by PhpStorm.
 * User: zeng
 * Date: 2019-05-27
 * Time: 10:09
 */

namespace App\Modules\Score;


use Illuminate\Support\Facades\DB;
use function PHPSTORM_META\type;

class ScoreHandle
{
    public function addScoreProduct($id,$data)
    {
        $product = $id?ScoreProduct::find($id):new ScoreProduct();
        foreach ($data as $key => $value){
            $product->$key = $value;
        }
        if ($product->save()){
            return $product->id;
        }
        return false;
    }
    public function getScoreProducts($page=1,$limit=10)
    {
        $db = DB::table('score_products');
        $count = $db->count();
        $data = $db->limit($limit)->offset(($page-1)*$limit)->orderBy('id','DESC')->get();
        return [
            'count'=>$count,
            'data'=>$data
        ];
    }
    public function getScoreProduct($id)
    {
        return ScoreProduct::find($id);
    }
    public function delScoreProduct($id)
    {
        return ScoreProduct::find($id)->delete();
    }
    public function getScoreProductImages($product_id)
    {
        return ScoreProductImage::where('product_id','=',$product_id)->get();
    }
    public function delScoreProductImages($product_id)
    {
        return ScoreProductImage::where('product_id','=',$product_id)->delete();
    }
    public function addScoreProductImage($product_id,$url)
    {
        $image = new ScoreProductImage();
        $image->product_id = $product_id;
        $image->url = $url;
        if ($image->save()){
            return $image->id;
        }
        return false;
    }
}