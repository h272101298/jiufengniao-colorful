<?php
/**
 * Created by PhpStorm.
 * User: zeng
 * Date: 2019-05-14
 * Time: 15:23
 */

namespace App\Modules\Picture;


class PictureHandle
{
    public function addPicture($id,$data)
    {
        $picture = $id?Picture::find($id):new Picture();
        foreach ($data as $key=>$value){
            $picture->$key = $value;
        }
        if ($picture->save()){
            return $picture->id;
        }
        return false;
    }
    public function delPicture($id)
    {
        $picture = Picture::find($id);
        return $picture->delete();
    }
    public function unCollectPicture($user_id,$picture_id)
    {
        return PictureCollect::where('user_id','=',$user_id)->where('picture_id','=',$picture_id)->delete();
    }
    public function CollectPicture($user_id,$picture_id)
    {
        $collect = new PictureCollect();
        $collect->user_id = $user_id;
        $collect->picture_id = $picture_id;
        if ($collect->save()){
            return $collect->id;
        }
        return false;
    }
    public function isPictureCollect($user_id,$picture_id)
    {
        return PictureCollect::where('user_id','=',$user_id)->where('picture_id','=',$picture_id)->count();
    }
    public function getUserCollect($user_id,$page=1,$limit=10)
    {
        $db = PictureCollect::where('user_id','=',$user_id);
        return [
            'count'=>$db->count(),
            'data'=>$db->orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
        ];
    }

}