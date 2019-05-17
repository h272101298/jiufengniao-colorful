<?php
/**
 * Created by PhpStorm.
 * User: zeng
 * Date: 2019-05-17
 * Time: 14:55
 */

namespace App\Modules\Good;


use Illuminate\Support\Facades\DB;

class GoodHandle
{
    public function addType($id,$data)
    {
        $type = $id?Type::find($id):new Type();
        foreach ($data as $key => $value){
            $type->$key = $value;
        }
        if ($type->save()){
            return $type->id;
        }
        return false;
    }
    public function getTypes()
    {
        return Type::all();
    }
    public function getTypeById($id)
    {
        return Type::find($id);
    }
    public function delType($id)
    {
        return Type::find($id)->delete();
    }
    public function getBanners($page,$limit,$type)
    {
        $db = DB::table('banners');
        if ($type){
            $db->where('type','=',$type);
        }
        $count = $db->count();
        $data = $db->orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
        return [
            'data'=>$data,
            'count'=>$count
        ];
    }
    public function addBanner($id,$data)
    {
        $banner = $id?Banner::find($id):new Banner();
        foreach ($data as $key => $value){
            $banner->$key = $value;
        }
        if ($banner->save()){
            return $banner->id;
        }
        return false;
    }
    public function delBanner($id)
    {
        return Banner::find($id)->delete();
    }
    public function getBanner($id)
    {
        return Banner::find($id);
    }
    public function addComment($id,$data)
    {
        $comment = $id?Comment::find($id):new Comment();
        foreach ($data as $key=>$value){
            $comment->$key = $value;
        }
        if ($comment->save()){
            return $comment->id;
        }
        return false;
    }
    public function getGoodComments($good_id)
    {
        return Comment::where('good_id','=',$good_id)->orderBy('id','DESC')->get();
    }

}