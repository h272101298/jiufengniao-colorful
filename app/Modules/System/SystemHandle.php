<?php
/**
 * Created by PhpStorm.
 * User: zeng
 * Date: 2019-05-27
 * Time: 16:31
 */

namespace App\Modules\System;


use Illuminate\Support\Facades\DB;

class SystemHandle
{
    public function getTxConfig()
    {
        return TxConfig::first();
    }
    public function setTxConfig($data)
    {
        $config = TxConfig::first();
        if (empty($config)){
            $config = new TxConfig();
        }
        foreach ($data as $key => $value){
            $config->$key = $value;
        }
        if ($config->save()){
            return true;
        }
        return false;
    }
    public function setNotifyConfig($title,$content)
    {
        $config = NotifyConfig::where('title','=',$title)->first();
        if (empty($config)){
            $config = new NotifyConfig();
            $config->title = $title;
        }
        $config->content = $content;
        if ($config->save()){
            return true;
        }
        return false;
    }
    public function getNotifyConfig($title)
    {
        return NotifyConfig::where('title','=',$title)->first();
    }
    public function getNotifyConfigs()
    {
        return NotifyConfig::all();
    }
    public function delNotifyConfig($id)
    {
        return NotifyConfig::find($id)->delete();
    }
    public function setLevelConfig($id,$data)
    {
        $config = $id?LevelConfig::find($id):new LevelConfig();
        foreach ($data as $key => $value){
            $config->$key = $value;
        }
        if ($config->save()){
            return $config->id;
        }
        return false;
    }
    public function delLevelConfig($id)
    {
        return LevelConfig::find($id)->delete();
    }

    public function getLevelConfig($id)
    {
        return LevelConfig::find($id);
    }
    public function getLevelConfigs($page=1,$limit=10)
    {
        $db = DB::table('level_configs');
        $count = $db->count();
        $data = $db->orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
        return [
            'data'=>$data,
            'count'=>$count
        ];
    }
    public function setPosterConfig($id,$data)
    {
        $poster = $id?PosterConfig::find($id):new PosterConfig();
        foreach ($data as $key=>$value){
            $poster->$key = $value;
        }
        if ($poster->save()){
            return $poster->id;
        }
        return false;
    }
    public function getPosterConfigs($page,$limit)
    {
        $db = DB::table('poster_configs');
        $count = $db->count();
        $data = $db->limit($limit)->offset(($page-1)*$limit)->get();
        return [
            'count'=>$count,
            'data'=>$data
        ];
    }
    public function delPosterConfig($id)
    {
        $poster = PosterConfig::find($id);
        $poster->delete();
        return true;
    }
    public function getPosterConfigByTitle($title)
    {
        return PosterConfig::where('title','=',$title)->first();
    }

}