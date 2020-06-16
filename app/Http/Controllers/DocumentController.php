<?php

namespace App\Http\Controllers;

use App\Modules\Document\DocumentHandle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class DocumentController extends Controller
{
    //
    public function __construct()
    {
        $this->handle = new DocumentHandle();
    }
    public function addDocument(Request $post)
    {
        $id = $post->id;
        $data = [
            'title'=>$post->title,
            'detail'=>$post->detail
        ];
        if ($this->handle->addDocument($id,$data)){
            return jsonResponse([
                'msg'=>'ok'
            ]);
        }
        throw new \Exception('error');
    }
    public function getDocuments()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $fixed = Input::get('fixed',0);
        $data = $this->handle->getDocuments($page,$limit,$fixed);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function getDocument()
    {
        $id = Input::get('id');
        $data = $this->handle->getDocument($id);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function delDocument()
    {
        $id = Input::get('id');
        $data = $this->handle->delDocument($id);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
}
