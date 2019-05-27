<?php

namespace App\Http\Controllers;

use App\Libraries\WxPay;
use App\Modules\Good\GoodHandle;
use App\Modules\Order\OrderHandle;
use App\Modules\Score\ScoreHandle;
use App\Modules\User\UserHandle;
use App\Modules\User\WeChatUser;
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
//        $repay = $post->repay?$post->repay:0;
        $user = WeChatUser::findOrFail($user_id);
        $order = $this->handle->getOrder($order_id);
        if ($order->user_id!=$user_id){
            return jsonResponse(['msg'=>'无权操作！'],422);
        }
        $wxPay = getWxPay($user->open_id);
        $data = $wxPay->pay($order->orderSn, '购买商品', $order->price, $url);
        $notify_id = $wxPay->getPrepayId();
        $this->handle->addOrder($order->id,['notify_id' => $notify_id]);
        return response()->json([
            'msg' => 'ok',
            'data' => $data
        ]);
    }
    public function notifyOrder(Request $post)
    {
        $data = $post->getContent();
        $wx = WxPay::xmlToArray($data);
        $wspay = getWxPay($wx['openid']);
        $data = [
            'appid' => $wx['appid'],
            'cash_fee' => $wx['cash_fee'],
            'bank_type' => $wx['bank_type'],
            'fee_type' => $wx['fee_type'],
            'is_subscribe' => $wx['is_subscribe'],
            'mch_id' => $wx['mch_id'],
            'nonce_str' => $wx['nonce_str'],
            'openid' => $wx['openid'],
            'out_trade_no' => $wx['out_trade_no'],
            'result_code' => $wx['result_code'],
            'return_code' => $wx['return_code'],
            'time_end' => $wx['time_end'],
            'total_fee' => $wx['total_fee'],
            'trade_type' => $wx['trade_type'],
            'transaction_id' => $wx['transaction_id']
        ];
        $sign = $wspay->getSign($data);
        if ($sign == $wx['sign']) {
            $order = $this->handle->getOrderBySn($wx['out_trade_no']);
            $order->state = 2;
            $order->save();
            return 'SUCCESS';
        }
        return 'ERROR';
    }
}
