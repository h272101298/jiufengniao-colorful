<?php

namespace App\Http\Controllers;

use App\Libraries\WxPay;
use App\Modules\Good\GoodHandle;
use App\Modules\Order\OrderHandle;
use App\Modules\Score\ScoreHandle;
use App\Modules\System\SystemHandle;
use App\Modules\User\UserHandle;
use App\Modules\User\WeChatUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

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
        $group = 0;
        $group_id = $post->group_id?$post->group_id:0;
        $state = 1;
        DB::beginTransaction();
        try{
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
                    $state = 2;
                    break;
                case 'group':
                    $goodHandle = new GoodHandle();
                    $product = $goodHandle->getGood($product_id);
                    $origin_price = $product->group_price;
                    $price = ($origin_price*$number)*100;
                    $group = 1;
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
                'state'=>$state
            ];
            if ($group_id!=0){
                if ($this->handle->checkJoinGroup($group_id,$user_id)){
                    throw new \Exception('不能重复参团！');
                }
                $group = $this->handle->getGroupBuy($group_id);
                if ($group->state!=1){
                    throw new \Exception('该团购已过期！');
                }
            }
            $result = $this->handle->addOrder(0,$data);
            if ($result){
                $addressData = [
                    'name'=>$address->name,
                    'city'=>$address->city,
                    'address'=>$address->address,
                    'phone'=>$address->phone,
                    'order_id'=>$result
                ];
                if ($group){
                    $systemHandle = new SystemHandle();
                    $config = $systemHandle->getNotifyConfig('grouptime');
                    $this->handle->addGroupBuy(0,[
                        'user_id'=>$user_id,
                        'order_id'=>$result,
                        'good_id'=>$product_id,
                        'group_id'=>$group_id,
                        'end'=>empty($config)?time():time()+$config->content*60*60
                    ]);
                }
                $this->handle->addOrderAddress(0,$addressData);
            }
            DB::commit();
            return jsonResponse([
                'msg'=>'ok',
                'data'=>[
                    'number'=>$orderSn
                ]
            ]);
        }catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getMessage());
        }

    }
    public function payOrder(Request $post)
    {
        $url = $post->getScheme() . '://' . $post->getHttpHost() . '/api/pay/notify';
        $user_id = getRedisData($post->token);
        $order_id = $post->order_id;
        $repay = $post->repay?$post->repay:0;
        $user = WeChatUser::findOrFail($user_id);
        if ($repay){
            $order = $this->handle->getOrder($order_id);
        }else{
            $order = $this->handle->getOrderBySn($order_id);
        }
        if ($order->user_id!=$user_id){
            return jsonResponse(['msg'=>'无权操作！'],422);
        }
        $wxPay = getWxPay($user->open_id);
        $data = $wxPay->pay($order->orderSn, '购买商品', $order->price, $url,$post->getClientIp());
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
            if ($order->type =='group'){
                $groupBuy = $this->handle->getOrderGroupBuy($order->id);
                $this->handle->addGroupBuy($groupBuy->id,['state'=>1]);
                if ($groupBuy->group_id!=0){
                    $count = $this->handle->getGroupBuyCount($groupBuy->group_id);
                    if ($count+1==2){
                        $this->handle->addGroupBuy($groupBuy->id,['state'=>2]);
                        $this->handle->addGroupBuy($groupBuy->group_id,['state'=>2]);
                    }
                }
            }
            $order->state = 2;
            $order->save();
            return 'SUCCESS';
        }
        return 'ERROR';
    }
    public function getUserOrderCount()
    {
        $user_id = getUserToken(Input::get('token'));
        $data = $this->handle->getUserOrderCount($user_id);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function getUserOrders()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $user_id = getRedisData(Input::get('token'));
        $state = Input::get('state',1);
        $data = $this->handle->getOrders($page,$limit,[$user_id],'',$state);
        if (!empty($data['data'])){
            foreach ($data['data'] as $datum){
                if ($datum->type=='score'){
                    $scoreHandle = new ScoreHandle();
                    $datum->product = $scoreHandle->getScoreProduct($datum->product_id);
                }elseif($datum->type=='group'){
                    $goodHandle = new GoodHandle();
                    $datum->product = $goodHandle->getGood($datum->product_id);
                    $datum->group = $this->handle->getOrderGroupBuy($datum->id);
                }else{
                    $goodHandle = new GoodHandle();
                    $datum->product = $goodHandle->getGood($datum->product_id);
                }
            }
        }
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function cancelOrder()
    {
        $user_id = getRedisData(Input::get('token'));
        $order_id = Input::get('order_id');
        $order = $this->handle->getOrder($order_id);
        if ($order->type!='origin'&&$order->type==2){
            throw new \Exception('该订单类型不允许退款');
        }
        if ($order->state>2){
            throw new \Exception('该状态不允许退款！');
        }
        if ($user_id!=$order->user_id){
            throw new \Exception('无权操作！');
        }
        if ($order->state==1){
            $this->handle->delOrder($order_id);
        }else{
            $this->handle->addOrder($order_id,['state'=>5]);
        }
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
    public function getOrders()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $state = Input::get('state',1);
        $type = Input::get('type');
        $data = $this->handle->getOrders($page,$limit,[],$type,$state);
        if (!empty($data['data'])){
            foreach ($data['data'] as $datum){
                if ($datum->type=='score'){
                    $scoreHandle = new ScoreHandle();
                    $datum->product = $scoreHandle->getScoreProduct($datum->product_id);
                }elseif($datum->type=='group'){
                    $goodHandle = new GoodHandle();
                    $datum->product = $goodHandle->getGood($datum->product_id);
                    $datum->group = $this->handle->getOrderGroupBuy($datum->id);
                }else{
                    $goodHandle = new GoodHandle();
                    $datum->product = $goodHandle->getGood($datum->product_id);
                }
            }
        }
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function addExpress(Request $post)
    {
        $id = $post->id?$post->id:0;
        $data = [
            'title'=>$post->title,
            'code'=>$post->code
        ];
        $this->handle->setExpress($id,$data);
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
    public function getExpresses()
    {
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        $data = $this->handle->getExpresses($page,$limit);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function delExpress()
    {
        $id = Input::get('id');
        $this->handle->delExpress($id);
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
    public function getExpress()
    {
        $id = Input::get('id');
        $data = $this->handle->getExpress($id);
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function getOrder()
    {
        $id = Input::get('id');
        $data = $this->handle->getOrder($id);
        if ($data->type=='score'){
            $scoreHandle = new ScoreHandle();
            $data->product = $scoreHandle->getScoreProduct($data->product_id);
        }elseif($data->type=='group'){
            $goodHandle = new GoodHandle();
            $data->product = $goodHandle->getGood($data->product_id);
            $data->group = $this->handle->getOrderGroupBuy($data->id);
        }else{
            $goodHandle = new GoodHandle();
            $data->product = $goodHandle->getGood($data->product_id);
        }
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    public function deliveryOrder()
    {
        $order_id = Input::get('id');
        $express_id = Input::get('express_id',0);
        $express_number = Input::get('express_number','');
        $order = $this->handle->getOrder($order_id);
        if ($order->state!=2){
            throw new \Exception('当前状态不允许发货！');
        }
        $this->handle->addOrder($order_id,[
            'express_id'=>$express_id,
            'express_number'=>$express_number,
            'state'=>3
        ]);
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
    public function refundOrder()
    {
        $id = Input::get('id');
        $pass = Input::get('pass',1);
        if ($pass==1){
            $state = 6;
            $order = $this->handle->getOrder($id);
            $userHandle = new UserHandle();
            $user = $userHandle->getWeChatUserById($order->user_id);
            $userHandle->addTransfer(0,[
                'open_id'=>$user->open_id,
                'number'=>self::makePaySn($user->id),
                'amount'=>$order->price,
                'desc'=>'退款'
            ]);
        }else{
            $state = 7;
        }
        $this->handle->addOrder($id,['state'=>$state]);
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
    public function confirmOrder()
    {
        $user_id = getRedisData(Input::get('token'));
        $order_id = Input::get('order_id');
        $order = $this->handle->getOrder($order_id);
        if ($order->state!=3){
            throw new \Exception('该状态不允许收货！');
        }
        if ($user_id!=$order->user_id){
            throw new \Exception('无权操作！');
        }
        $this->handle->addOrder($order_id,['state'=>4]);
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
}
