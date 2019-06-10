<?php
/**
 * Created by PhpStorm.
 * User: zeng
 * Date: 2019-06-10
 * Time: 15:42
 */

namespace App\Modules\Document;


use Illuminate\Support\Facades\DB;

class DocumentHandle
{
    public function addDocument($id,$data)
    {
        $document = $id?Document::find($id):new Document();
        foreach ($data as $key => $value){
            $document->$key = $value;
        }
        if ($document->save()){
            return $document->id;
        }
        return false;
    }
    public function getDocument($id)
    {
        return Document::find($id);
    }
    public function getDocuments($page=1,$limit=10,$fixed=0)
    {
        $db = DB::table('documents');
        if ($fixed){
            $db->select(['id','title','created_at','updated_at']);
        }
        $count = $db->count();
        $data = $db->orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
        return [
            'count'=>$count,
            'data'=>$data
        ];
    }
    public function delDocument($id)
    {
        return Document::find($id)->delete();
    }
}