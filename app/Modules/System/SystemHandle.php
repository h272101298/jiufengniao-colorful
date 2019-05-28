<?php
/**
 * Created by PhpStorm.
 * User: zeng
 * Date: 2019-05-27
 * Time: 16:31
 */

namespace App\Modules\System;


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

}