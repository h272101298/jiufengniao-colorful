<?php

namespace App\Http\Controllers;

use App\Modules\Member\MemberConfig;
use App\Modules\Member\MemberHandle;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    //
    public function __construct()
    {
        $this->handle = new MemberHandle();
    }

    public function addMemberConfigs(Request $post)
    {
        $configs = $post->configs;
        if (!empty($configs)){
            foreach ($configs as $config){
                $this->handle->addMemberConfig($config['score'],$config['level']);
            }
        }
        return jsonResponse(['msg'=>'ok']);
    }
    public function getMemberConfigs()
    {
        $data = $this->handle->getMemberConfigs();
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }

//    public function g
}
