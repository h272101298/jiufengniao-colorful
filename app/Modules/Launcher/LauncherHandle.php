<?php
/**
 * Created by PhpStorm.
 * User: zeng
 * Date: 2019-06-01
 * Time: 14:54
 */

namespace App\Modules\Launcher;


class LauncherHandle
{
    public function addLauncherImage($id,$data)
    {
        $image = $id?LauncherImage::find($id):new LauncherImage();
        foreach ($data as $key=>$value){
            $image->$key = $value;
        }
        if ($image->save()){
            return $image->id;
        }
        return false;
    }
    public function getLauncherImages()
    {
        return LauncherImage::all();
    }
    public function delLauncherImage($id)
    {
        return LauncherImage::find($id)->delete();
    }
}