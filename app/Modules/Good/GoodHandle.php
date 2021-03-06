<?php
/**
 * Created by PhpStorm.
 * User: zeng
 * Date: 2019-05-17
 * Time: 14:55
 */

namespace App\Modules\Good;


use App\Modules\User\WeChatUser;
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
    public function getTypes($page=1,$limit=10)
    {
        $count = Type::count();
        $data = Type::orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
        return [
            'count'=>$count,
            'data'=>$data
        ];
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
    public function delComment($id)
    {
        return Comment::find($id)->delete();
    }
    public function getGoodComments($good_id)
    {
        return Comment::where('good_id','=',$good_id)->orderBy('id','DESC')->get();
    }

    public function addGood($id,$data)
    {
        $good = $id?Good::find($id):new Good();
        foreach ($data as $key=>$value){
            $good->$key = $value;
        }
        if ($good->save()){
            return $good->id;
        }
        return false;
    }
    public function getGoods($page=1,$limit=10,$type=0)
    {
        $db = DB::table('goods');
        if ($type){
            $db->where('type_id','=',$type);
        }
        $count = $db->count();
        $data = $db->orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
        return [
            'count'=>$count,
            'data'=>$data
        ];

    }
    public function setRecommend($id)
    {
        Good::where('recommend','=',1)->update(['recommend'=>0]);
        $good = Good::find($id);
        $good->recommend = 1;
        $good->save();
    }
    public function getRecommend()
    {
        return Good::where('recommend','=',1)->first();
    }
    public function delGood($id)
    {
        $good = Good::find($id);
        return $good->delete();
    }

    public function getGood($id)
    {
        return Good::find($id);
    }

    public function addDetailImage($detail_id,$url)
    {
        $image = new GoodDetailImage();
        $image->detail_id = $detail_id;
        $image->url = $url;
        if ($image->save()){
            return true;
        }
        return false;
    }
    public function getDetailImages($detail_id)
    {
        return GoodDetailImage::where('detail_id','=',$detail_id)->get();
    }

    public function addDetail($id,$data)
    {
        $detail = $id?GoodDetail::find($id):new GoodDetail();
        foreach ($data as $key => $value){
            $detail->$key = $value;
        }
        if($detail->save()){
            return $detail->id;
        }
        return false;
    }
    public function getDetail($id)
    {
        return GoodDetail::find($id);
    }
    public function getDetails($page=1,$limit =10 ,$type_id = 0,$user_id=0,$format=0,$search='')
    {
        $db = DB::table('good_details');
        if ($type_id){
            $db->where('type_id','=',$type_id);
        }
        if ($user_id){
            $db->where('user_id','=',$user_id);
        }
        if (strlen($search)!=0){
            $db->where('title','like','%'.$search.'%');
        }
        $count = $db->count();
        $data = $db->orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
        if ($format&&count($data)!=0){
            foreach ($data as $datum){
                $datum->user = WeChatUser::find($datum->user_id);
                $datum->image = GoodDetailImage::where('detail_id','=',$datum->id)->pluck('url')->first();
                $datum->collect = GoodCollect::where('detail_id','=',$datum->id)->count();
            }
        }
        return [
            'count'=>$count,
            'data'=>$data
        ];
    }
    public function likeDetail($user_id,$detail_id)
    {
        $like = DetailLike::where('user_id','=',$user_id)->where('detail_id','=',$detail_id)->first();
        if (empty($like)){
            $like = new DetailLike();
            $like->user_id = $user_id;
            $like->detail_id = $detail_id;
            $like->save();
        }
        return true;
    }
    public function checkLikeDetail($user_id,$detail_id)
    {
        return DetailLike::where('user_id','=',$user_id)->where('detail_id','=',$detail_id)->count();
    }
    public function dislikeDetail($user_id,$detail_id)
    {
        $like = DetailLike::where('user_id','=',$user_id)->where('detail_id','=',$detail_id)->first();
        if ($like){
            $like->delete();
        }
        return true;
    }
    public function countDetailLikes($detail_id)
    {
        return DetailLike::where('detail_id','=',$detail_id)->count();
    }
    public function countLikeDetails($user_id)
    {
        return DetailLike::where('user_id','=',$user_id)->count();
    }
    public function checkDetailCollect($user_id,$detail_id)
    {
        return GoodCollect::where('user_id','=',$user_id)->where('detail_id','=',$detail_id)->count();
    }
    public function collectDetail($user_id,$detail_id)
    {
        $collect = GoodCollect::where('user_id','=',$user_id)->where('detail_id','=',$detail_id)->first();
        if (empty($collect)){
            $collect = new GoodCollect();
            $collect->user_id = $user_id;
            $collect->detail_id = $detail_id;
            $collect->save();
        }
        return true;
    }
    public function unCollectDetail($user_id,$detail_id)
    {
        $collect = GoodCollect::where('user_id','=',$user_id)->where('detail_id','=',$detail_id)->first();
        if ($collect){
            $collect->delete();
        }
        return true;
    }
    public function getUserCollects($user_id,$page=1,$limit=10)
    {
        $db = GoodCollect::where('user_id','=',$user_id);
        $count = $db->count();
        $data = $db->orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
        return [
            'count'=>$count,
            'data'=>$data
        ];
    }
    public function countUserCollects($user_id)
    {
        return GoodCollect::where('user_id','=',$user_id)->count();
    }
}