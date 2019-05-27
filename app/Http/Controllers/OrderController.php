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
            ];
            $this->handle->addOrderAddress($result,$addressData);
        }
        return jsonResponse([
            'msg'=>'ok',
            'data'=>[
                'number'=>$orderSn
            ]
        ]);
    }
}
