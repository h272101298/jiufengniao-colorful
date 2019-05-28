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

}