<?php
/**
 * Created by PhpStorm.
 * User: zeng
 * Date: 2019-05-29
 * Time: 15:09
 */

namespace App\Modules\Member;


class MemberHandle
{
    public function addMemberConfig($score,$level)
    {
        $config = MemberConfig::where('level','=',$level)->first();
        if (empty($config)){
            $config = new MemberConfig();
            $config->level = $level;
        }
        $config->score = $score;
        if ($config->save()){
            return $config->id;
        }
        return false;
    }
    public function getMemberConfigs()
    {
        return MemberConfig::all();
    }
    public function getMemberConfig($level)
    {
        return MemberConfig::where('level','=',$level)->first();
    }
}