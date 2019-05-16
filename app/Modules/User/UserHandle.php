<?php
/**
 * Created by PhpStorm.
 * User: zeng
 * Date: 2019-04-28
 * Time: 14:20
 */

namespace App\Modules\User;


use Illuminate\Support\Facades\DB;

class UserHandle
{
    //添加或修改用户
    public function addWeChatUser(int $id = 0,array $data)
    {
        $user = $id?WeChatUser::find($id):new WeChatUser();
        foreach ($data as $datum => $value){
            $user->$datum = $value;
        }
        if ($user->save()){
            return $user->id;
        }
        return false;
    }
    //根据id获取微信
    public function getWeChatUserById(int $id)
    {
        return WeChatUser::find($id);
    }
    //获取微信用户
    public function getWeChatUsers(int $page,int $limit,string $open_id = '',string $nickname = '')
    {
        $db = DB::table('we_chat_users');
        if (strlen($open_id)!=0) {
            $db->where('open_id','like','%'.$open_id.'%');
        }
        if (strlen($nickname)!=0){
            $db->where('nickname','like','%'.$nickname.'%');
        }
        $count = $db->count();
        $data = $db->orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
        return [
            'count'=>$count,
            'data'=>$data
        ];
    }
    //根据open_id获取微信用户
    public function getWeChatUserByOpenId(string $open_id)
    {
        return WeChatUser::where('open_id','like','%'.$open_id.'%')->first();
    }
    public function getUserInfoByUserId($user_id)
    {
        return UserInfo::where('user_id','=',$user_id)->first();
    }
    //设置用户信息
    public function addUserInfoByUserId($user_id,$data)
    {
        $info = UserInfo::where('user_id','=',$user_id)->first();
        if (empty($info)){
            $info = new UserInfo();
            $info->user_id = $user_id;
        }
        foreach ($data as $key => $value){
            $info->$key = $value;
        }
        if($info->save()){
            return $info->id;
        }
        return false;
    }
    public function addAttentionUser($user_id,$attention_id)
    {
//        $attention =
    }
    //添加地址
    public function addUserAddress($id,$data)
    {
        $address = $id?UserAddress::find($id):new UserAddress();
        foreach ($data as $key => $value){
            $address->$key = $value;
        }
        if ($address->save()){
            return $address->id;
        }
        return false;
    }

    /**
     * @param $id
     * @return bool
     * 设置默认地址
     */
    public function setDefaultAddress($id)
    {
        $address = UserAddress::find($id);
        UserAddress::where('user_id','=',$address->user_id)->update(['default'=>0]);
        $address->default = 1;
        $address->save();
        return true;
    }

    /**
     * @param $user_id
     * 获取用户地址个数
     * @return mixed
     */
    public function getUserAddressCount($user_id)
    {
        return UserAddress::where('user_id','=',$user_id)->count();
    }
    //根据id删除用户地址
    public function delAddressById($id)
    {
        $address = UserAddress::find($id);
        return $address->delete();
    }
    public function getAddressById($id)
    {
        return UserAddress::find($id);
    }
    public function getUserAddresses($user_id)
    {
        $addresses = UserAddress::where('user_id','=',$user_id)->orderBy('default','DESC')->orderBy('id','DESC')->get();
        if (count($addresses)!=0){
            foreach ($addresses as $address){
                $address->city = explode(',',$address->city);
            }
        }
        return $addresses;
    }
    public function getUserAddress($id)
    {
        $address = UserAddress::find($id);
        if (!empty($address)){
            $address->city = explode(',',$address->city);
        }
        return $address;
    }

    public function addSign()
    {

    }
    public function getTodaySign($user_id)
    {
        return SignRecord::where('user_id','=',$user_id)->whereDate()->count();
    }
}