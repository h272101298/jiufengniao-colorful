<?php
/**
 * Created by PhpStorm.
 * User: zeng
 * Date: 2019-05-27
 * Time: 14:22
 */

namespace App\Modules\Order;


use Illuminate\Support\Facades\DB;

class OrderHandle
{
    public function addOrder($id,$data)
    {
        $order = $id?Order::find($id):new Order();
        foreach ($data as $key=>$value){
            $order->$key = $value;
        }
        if ($order->save()){
            return $order->id;
        }
        return false;
    }
    public function getOrders($page=1,$limit=10,$user_id=[],$type)
    {
        $db = DB::table('orders');
        if (count($user_id)!=0){
            $db->whereIn('user_id','=',$user_id);
        }
        if ($type){
            $db->where('type','=',$type);
        }
        $count = $db->count();
        $data = $db->limit($limit)->offset(($page-1)*$limit)->orderBy('id','DESC')->get();
        return [
            'count'=>$count,
            'data'=>$data
        ];
    }
    public function addOrderAddress($id,$data)
    {
        $address = $id?OrderAddress::find($id):new OrderAddress();
        foreach ($data as $key=>$value){
            $address->$key = $value;
        }
        if ($address->save()){
            return $address->id;
        }
        return false;
    }
    public function delOrderAddress($order_id)
    {
        return OrderAddress::where('order_id','=',$order_id)->delete();
    }

}