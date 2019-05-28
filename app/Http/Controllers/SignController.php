<?php

namespace App\Http\Controllers;

use App\Modules\Sign\SignHandle;
use App\Modules\User\UserHandle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

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

    public function sign()
    {
        $userHandle = new UserHandle();
        $user_id = getRedisData(Input::get('token'));
        if ($this->handle->checkSign($user_id,1)){
            return jsonResponse([
                'msg'=>'今天已签到！ '
            ],400);
        }
        $continue = $this->handle->getContinueSign($user_id);
        if ($continue){
            if ($continue['date']==date('Y-m-d',strtotime('-1 days'))){
                if ($continue['count']!=7){
                    $step =  '1';
                    $count = $continue['count']+1;
                }else{
                    $step = '2';
                    $count = 1;
                }
                $continue['count'] = $count;
                $continue['date'] = date('Y-m-d',time());
                $this->handle->setContinueSign($user_id,$continue);
            }else{
                $step = '3';
                $count = 1;
                $continue['count'] = $count;
                $continue['date'] = date('Y-m-d',time());
                $this->handle->setContinueSign($user_id,$continue);
            }
        }else{
            $step = '4';
            $count = 1;
            $continue['count'] = $count;
            $continue['date'] = date('Y-m-d',time());
            $this->handle->setContinueSign($user_id,$continue);
        }
        $config = $this->handle->getSignConfig($count);
        if (!empty($config)){
            if ($config->type==1){
                $data = [
                    'user_id'=>$user_id,
                    'type'=>'2',
                    'score'=>$config->reward,
                    'remark'=>'签到获得'
                ];
                $userHandle->addScoreRecord(0,$data);
                $userHandle->addUserScore($user_id,$config->reward);
            }
        }
        $this->handle->addSignRecord($user_id);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$count,
            'step'=>$step,
            'continue'=>$continue
        ]);
    }

}
