<?php

namespace App\Http\Controllers;

use App\Modules\Good\GoodHandle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class GoodController extends Controller
{
    //
    public function __construct()
    {
        $this->handle = new GoodHandle();
    }

    public function addType(Request $post)
    {
        $title = $post->title;
        $icon = $post->icon;
        $id = $post->id ? $post->id : 0;
        $result = $this->handle->addType($id, [
            'title' => $title,
            'icon' => $icon
        ]);
        if ($result) {
            return jsonResponse([
                'msg' => 'ok'
            ]);
        }
        throw new \Exception('ERROR');
    }

    public function getTypes()
    {
        $page = Input::get('page', 1);
        $limit = Input::get('limit', 10);
        $data = $this->handle->getTypes($page, $limit);
        return jsonResponse([
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    public function delType()
    {
        $id = Input::get('id');
        $this->handle->delType($id);
        return jsonResponse([
            'msg' => 'ok'
        ]);
    }

    public function addBanner(Request $post)
    {
        $id = $post->id ? $post->id : 0;
        $good_id = $post->good_id ? $post->good_id : 0;
        $type = $post->type ? $post->type : 1;
        $url = $post->url;
        $result = $this->handle->addBanner($id, [
            'good_id' => $good_id,
            'url' => $url,
            'type' => $type
        ]);
        if ($result) {
            return jsonResponse([
                'msg' => 'ok'
            ]);
        }
        throw new \Exception('ERROR');
    }

    public function getBanners()
    {
        $page = Input::get('page', 1);
        $limit = Input::get('limit', 10);
        $type = Input::get('type', 1);
        $data = $this->handle->getBanners($page, $limit, $type);
        return jsonResponse([
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    public function delBanner()
    {
        $id = Input::get('id');
        $this->handle->delBanner($id);
        return jsonResponse([
            'msg' => 'ok'
        ]);
    }

    public function addGood(Request $post)
    {
        $recommend = $post->recommend;
        $id = $post->id ? $post->id : 0;
        $data = [
            'type_id' => $post->type_id,
            'name' => $post->name ? $post->name : '',
            'base_pic' => $post->base_pic,
            'mask_pic' => $post->mask_pic,
            'price' => $post->price,
            'group_price' => $post->group_price
        ];
        $result = $this->handle->addGood($id, $data);
        if ($result) {
            if ($recommend) {
                $this->handle->setRecommend($result);
            }
            return jsonResponse([
                'msg' => 'ok'
            ]);
        }
        throw new \Exception('ERROR');
    }

    public function getGoods()
    {
        $type_id = Input::get('type_id', 0);
        $page = Input::get('page', 1);
        $limit = Input::get('limit', 10);
        $data = $this->handle->getGoods($page, $limit, $type_id);
        return jsonResponse([
            'msg' => 'ok',
            'data' => $data
        ]);
    }
    public function getRecommend()
    {
        $data = $this->handle->getRecommend();
        return jsonResponse([
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    public function setRecommend()
    {
        $id = Input::get('id');
        $data = $this->handle->setRecommend($id);
        return jsonResponse([
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    public function delGood()
    {
        $id = Input::get('id');
        $this->handle->delGood($id);
        return jsonResponse([
            'msg' => 'ok'
        ]);
    }

    public function getGood()
    {
        $id = Input::get('id');
        $data = $this->handle->getGood($id);
        return jsonResponse([
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    public function addGoodDetail(Request $post)
    {
        $good = $this->handle->getGood($post->good_id);
        $data = [
            'user_id' => getRedisData($post->token),
            'good_id' => $post->good_id,
            'type_id' => $good->type_id,
            'title' => $post->title,
            'detail' => $post->detail,
            'self_visible' => $post->self_visible,
            'base_pic' => $post->base_pic
        ];
        $result = $this->handle->addDetail(0, $data);
        $pics = $post->pics;
        if (!empty($pics)) {
            foreach ($pics as $pic) {
                $this->handle->addDetailImage($result, $pic);
            }
        }
        return jsonResponse([
            'msg' => 'ok'
        ]);
    }

    public function getGoodDetails()
    {
        $page = Input::get('page', 1);
        $limit = Input::get('limit', 10);
        $type_id = Input::get('type_id', 0);
        $token = Input::get('token');
        $user_id = 0;
        if ($token){
            $user_id = getRedisData($token);
        }
        $data = $this->handle->getDetails($page, $limit, $type_id, $user_id,1);
        return jsonResponse([
            'msg' => 'ok',
            'data' => $data
        ]);
    }
}
