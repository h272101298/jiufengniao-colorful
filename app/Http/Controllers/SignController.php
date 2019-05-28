<?php

namespace App\Http\Controllers;

use App\Modules\Sign\SignHandle;
use Illuminate\Http\Request;

class SignController extends Controller
{
    //
    public function __construct()
    {
        $this->handle = new SignHandle();
    }
    public function addSignConfig(Request $post)
    {
        $configs = $post->configs;
        if (!empty($configs)){
            foreach ($configs as $config){
                $info = $this->handle->getSignConfig($config['days']);
                if ($info){
                    $id = $info->id;
                }else{
                    $id = 0;
                }
                $data = [
                    'days'=>$config['days'],
                    'type'=>$config['type'],
                    'reward'=>$config['reward']
                ];
                $this->handle->setSignConfig($id,$data);
            }
        }
        return jsonResponse([
            'msg'=>'ok'
        ]);
        throw new \Exception('error');
    }
    public function getSignConfigs()
    {
        $data = $this->handle->getSignConfigs();
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }

}
