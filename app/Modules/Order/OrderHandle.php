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
    public function getOrders($page=1,$limit=10,$user_id=[],$type,$state=0)
    {
        $db = DB::table('orders');
        if (count($user_id)!=0){
            $db->whereIn('user_id',$user_id);
        }
        if (strlen($type)!=0){
            $db->where('type','=',$type);
        }
        if ($state){
            $db->where('state','=',$state);
        }
        $count = $db->count();
        $data = $db->limit($limit)->offset(($page-1)*$limit)->orderBy('id','DESC')->get();
        return [
            'count'=>$count,
            'data'=>$data
        ];
    }
    public function getOrder($id)
    {
        return Order::find($id);
    }
    public function getOrderBySn($sn)
    {
        return Order::where('orderSn','=',$sn)->first();
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
    public function getUserOrderCount($user_id)
    {
        $db = DB::table('orders')->where('user_id','=',$user_id);
        $unPay = $db->where('state','=',1)->count();
        $unDelivery = $db->where('state','=',2)->count();
        $unConfirm = $db->where('state','=',3)->count();
        $finish = $db->where('state','=',4)->count();
        $refund = $db->whereIn('state',[5,6])->count();
        return [
            'unPay'=>$unPay,
            'unDelivery'=>$unDelivery,
            'unConfirm'=>$unConfirm,
            'finish'=>$finish,
            'refund'=>$refund,
        ];
    }
    public function addGroupBuy($id,$data)
    {
        $groupBuy = $id?GroupBuy::find($id):new GroupBuy();
        foreach ($data as $key => $value){
            $groupBuy->$key = $value;
        }
        if ($groupBuy->save()){
            return $groupBuy->id;
        }
        return false;
    }
    public function checkGroupBuy($user_id,$good_id)
    {
        return GroupBuy::where('user_id','=',$user_id)->where('good_id','=',$good_id)->count();
    }
    public function getOrderGroupBuy($order_id)
    {
        return GroupBuy::where('order_id','=',$order_id)->first();
    }

}