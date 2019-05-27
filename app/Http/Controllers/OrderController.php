<?php

namespace App\Http\Controllers;

use App\Modules\Good\GoodHandle;
use App\Modules\Order\OrderHandle;
use App\Modules\Score\ScoreHandle;
use App\Modules\User\UserHandle;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    public function __construct()
    {
        $this->handle = new OrderHandle();
    }

    public function makeOrder(Request $post)
    {

        $userHandle = new UserHandle();
        $type = $post->type?$post->type:'origin';
        $product_id = $post->product_id;
        $number = $post->number;
        $price = 0;
        $origin_price=0;
        $picture = $post->picture;
        $address_id = $post->address_id;
        $address = $userHandle->getUserAddress($address_id,0);
        $user_id = getUserToken($post->token);
        $group = $post->group;
        $orderSn = self::makePaySn($user_id);
        switch ($type){
            case 'origin':
                $goodHandle = new GoodHandle();
                $product = $goodHandle->getGood($product_id);
                $origin_price = $product->price;
                $price = ($origin_price*$number)*100;
                break;
            case 'score':
                $scoreHandle = new ScoreHandle();
                $product = $scoreHandle->getScoreProduct($product_id);
                $origin_price = $product->score;
                $price = $origin_price;
                break;
        }
        $data = [
            'orderSn'=>$orderSn,
            'number'=>$number,
            'user_id'=>$user_id,
            'product_id'=>$product_id,
            'origin_price'=>$origin_price,
            'price'=>$price,
            'picture'=>$picture,
            'type'=>$type,
        ];
        $result = $this->handle->addOrder(0,$data);
        if ($result){
            $addressData = [
                'name'=>$address->name,
                'city'=>$address->city,
                'address'=>$address->address,
                'phone'=>$address->phone,
                'order_id'=>$result
            ];
            $this->handle->addOrderAddress(0,$addressData);
        }
        return jsonResponse([
            'msg'=>'ok',
            'data'=>[
                'number'=>$orderSn
            ]
        ]);
    }
    public function payOrder(Request $post)
    {
        $url = $post->getScheme() . '://' . $post->getHttpHost() . '/api/pay/notify';
        $user_id = getRedisData($post->token);
        $order_id = $post->order_id;
        $repay = $post->repay?$post->repay:0;
        $user = WeChatUser::findOrFail($user_id);
        $amountPay = $post->amountPay?$post->amountPay:0;
        $goodsCheck=$this->handle->goodsNumCheck($order_id);
        if(!$goodsCheck){
            return response()->json([
                'msg'=>'库存不足!'
            ],400);
        }
        if ($repay){
            $order = Order::where('number', '=', $order_id)->first();
            if ($order->state!='created'){
                return jsonResponse([
                    'msg' => '订单已支付！'
                ], 400);
            }
            if ($amountPay){
                $amount = $this->handle->getUserAmount($user_id);
                if ($amount<$order->price){
                    throw new \Exception('余额不足！');
                }
                $this->handle->setUserAmount($user_id,$amount-$order->price);
                $data = [
                    'state' => 'paid'
                ];
                $this->handle->addOrder($order->id,$data);
                return response()->json([
                    'msg' => 'ok'
                ]);
            }
            //$price = Order::where('group_number', '=', $order_id)->sum('price');
            $wxPay = getWxPay($user->open_id);
            $data = $wxPay->pay($order_id, '购买商品', ($order->price) * 100, $url);
            $notify_id = $wxPay->getPrepayId();
            Order::where('number', '=', $order_id)->update(['notify_id' => $notify_id]);
            return response()->json([
                'msg' => 'ok',
                'data' => $data
            ]);
        }
        $count = Order::where('group_number', '=', $order_id)->where('state', '!=', 'created')->count();
        if ($count != 0) {
            return jsonResponse([
                'msg' => '订单已支付！'
            ], 400);
        }
        $order_user = Order::where('group_number', '=', $order_id)->pluck('user_id')->first();
        if ($order_user != $user_id) {
            return jsonResponse([
                'msg' => '无权操作！'
            ], 403);
        }
        $price = Order::where('group_number', '=', $order_id)->sum('price');
        $product_id=$this->handle->orderId_exchange_productId($order_id);
        $res=$this->handle->goodsNumDec($product_id);
        if(!$res){
            return response()->json([
                'msg'=>'库存不足!'
            ],400);
        }
        if ($amountPay){
            $amount = $this->handle->getUserAmount($user_id);
            if ($amount<$price){
                throw new \Exception('余额不足！');
            }
            $this->handle->setUserAmount($user_id,$amount-$price);
            $data = [
                'state' => 'paid'
            ];
            Order::where('group_number', '=', $order_id)->update(['state'=>'paid']);
            return response()->json([
                'msg' => 'ok'
            ]);
        }
        $wxPay = getWxPay($user->open_id);
        $data = $wxPay->pay($order_id, '购买商品', ($price) * 100, $url);
        $notify_id = $wxPay->getPrepayId();
        Order::where('group_number', '=', $order_id)->update(['notify_id' => $notify_id]);
        return response()->json([
            'msg' => 'ok',
            'data' => $data
        ]);
    }
}
