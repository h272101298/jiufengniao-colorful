<?php

namespace App\Http\Controllers;

use App\Modules\System\SystemHandle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class SystemController extends Controller
{
    //
    public function __construct()
    {
        $this->handle = new SystemHandle();
    }

    public function upload(Request $request)
    {
        if (!$request->hasFile('file')){
            return response()->json([
                'msg'=>'空文件'
            ],422);
        }
        $file = $request->file('file');
        $name = $file->getClientOriginalName();
        $name = explode('.',$name);
        if (count($name)!=2){
            return response()->json([
                'msg'=>'非法文件名!'
            ],422);
        }
        $allow =  [
            'pem',
            'mp4',
        ];
        if (!in_array(strtolower($name[1]),$allow)){
            return response()->json([
                'msg'=>'不支持的文件格式'
            ],422);
        }
        $md5 = md5_file($file);
        $name = $name[1];
        $name = $md5.'.'.$name;
        if (!$file){
            return response()->json([
                'msg'=>'空文件'
            ],422);
        }
        if ($file->isValid()){
            $destinationPath = 'uploads';
            $file->move($destinationPath,$name);
            return response()->json([
                'msg'=>'ok',
                'data'=>[
                    'url'=>$destinationPath.'/'.$name,
                ]
            ]);
        }
    }

    public function addTxConfig(Request $post)
    {
        $data = [
            'app_id'=>$post->app_id,
            'app_secret'=>$post->app_secret,
            'api_key'=>$post->api_key,
            'mch_id'=>$post->mch_id,
            'ssl_cert'=>$post->ssl_cert,
            'ssl_key'=>$post->ssl_key
        ];
        if ($this->handle->setTxConfig($data)){
            return jsonResponse([
                'msg'=>'ok'
            ]);
        }
        return jsonResponse([
            'msg'=>'系统错误！'
        ],400);
    }
    public function getTxConfig()
    {
        $data = $this->handle->getTxConfig();
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function setNotifyConfig(Request $post)
    {
        $title = $post->title;
        $content = $post->get('content');
        if ($this->handle->setNotifyConfig($title,$content)){
            return jsonResponse([
                'msg'=>'ok'
            ]);
        }
        throw new \Exception('ERROR');
    }
    public function getNotifyConfigs()
    {
        $data = $this->handle->getNotifyConfigs();
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function getNotifyConfig()
    {
        $data = $this->handle->getNotifyConfig(Input::get('title'));
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function delNotifyConfig()
    {
        $id = Input::get('id');
        $this->handle->delNotifyConfig($id);
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
}
