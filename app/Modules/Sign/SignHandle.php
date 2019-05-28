<?php
/**
 * Created by PhpStorm.
 * User: zeng
 * Date: 2019-05-27
 * Time: 17:24
 */

namespace App\Modules\Sign;


class SignHandle
{
    public function setSignConfig($id,$data)
    {
        $config = $id?SignConfig::find($id):new SignConfig();
        foreach ($data as $key => $value){
            $config->$key = $value;
        }
        if ($config->save()){
            return $config->id;
        }
        return false;
    }
    public function getSignConfigs()
    {
        $configs = SignConfig::all();
        return $configs;
    }
    public function addSignRecord($user_id)
    {
        $record = new SignRecord();
        $record->user_id = $user_id;
        if ($record->save()){
            return true;
        }
        return false;
    }
    public function checkSign($user_id,$today=1)
    {
        $db = SignRecord::where('user_id','=',$user_id);
        if ($today){
            $db->whereDate('created_at', date('Y-m-d',time()));
        }
        return $db->count();
    }
    public function setContinueSign($user_id,$data)
    {
        $data = serialize($data);
        setRedisData('sign'.$user_id,$data);
    }
    public function getContinueSign($user_id)
    {
        $data = getRedisData('sign'.$user_id);
        if (!empty($data)){
            return unserialize($data);
        }
        return false;
    }
}