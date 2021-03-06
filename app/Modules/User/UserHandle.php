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
    public function getWeChatUsers(int $page,int $limit,string $open_id = '',string $nickname = '',int $format=0)
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
        if ($format==1&&count($data)!=0){
            foreach ($data as $datum){
                $datum->userInfo = $this->getUserInfoByUserId($datum->id);
            }
        }
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
    public function getUserAddress($id,$format=1)
    {
        $address = UserAddress::find($id);
        if (!empty($address)&&$format==1){
            $address->city = explode(',',$address->city);
        }
        return $address;
    }
    public function getDefaultAddress($user_id)
    {
        return UserAddress::where('user_id','=',$user_id)->where('default','=',1)->first();
    }
    public function addUserScore($user_id,$score)
    {
        $userScore = UserScore::where('user_id','=',$user_id)->first();
        if (empty($userScore)){
            $userScore = new UserScore();
            $userScore->score = 0 ;
            $userScore->real_score = 0 ;
            $userScore->user_id = $user_id;
        }
        $userScore->real_score += $score;
        if ($score>0){
            $userScore->score += $score;
        }
        if ($userScore->save()){
            return true;
        }
        return false;
    }
    public function getUserScore($user_id)
    {
        $score = UserScore::where('user_id','=',$user_id)->first();
        return $score;
    }
    public function setUserScore($user_id,$score)
    {
        $userScore = UserScore::where('user_id','=',$user_id)->first();
        if (empty($userScore)){
            $userScore = new UserScore();
            $userScore->score = 0 ;
            $userScore->real_score = 0 ;
            $userScore->user_id = $user_id;
        }
        $userScore->score = $score;
        $userScore->real_score = $score;

        if ($userScore->save()){
            return true;
        }
        return false;
    }
    public function addScoreRecord($id=0,$data)
    {
        if ($id){
            $record = ScoreRecord::find($id);
        }else{
            $record = new ScoreRecord();
        }
        foreach ($data as $key => $value){
            $record->$key = $value;
        }
        if ($record->save()){
            return true;
        }
        return false;
    }
    public function getScoreRecords($user_id=0,$type=0,$page=1,$limit=10)
    {
        $db = DB::table('score_records');
        if ($user_id){
            $db->where('user_id','=',$user_id);
        }
        if ($type){
            $db->where('type','=',$type);
        }
        $count = $db->count();
        $data = $db->orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
        return [
            'count'=>$count,
            'data'=>$data
        ];
    }
    public function getUserAttentionCount($user_id)
    {
        $db = DB::table('attentions');
        $attentionSum = $db->where('user_id','=',$user_id)->count();
        $fanSum = $db->where('attention_id','=',$user_id)->count();
        return [
            'attentionSum'=>$attentionSum,
            'fanSum'=>$fanSum
        ];
    }
    public function getUserAttentions($user_id,$page=1,$limit=10)
    {
        $db = Attention::where('user_id','=',$user_id);
        $count = $db->count();
        $data = $db->orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
        return [
            'data'=>$data,
            'count'=>$count
        ];
    }
    public function getUserFans($user_id,$page=1,$limit=10)
    {
        $db = Attention::where('attention_id','=',$user_id);
        $count = $db->count();
        $data = $db->orderBy('id','DESC')->limit($limit)->offset(($page-1)*$limit)->get();
        return [
            'data'=>$data,
            'count'=>$count
        ];
    }
    public function addAttentionUser($user_id,$attention_id)
    {
        $attention = Attention::where('user_id','=',$user_id)->where('attention_id','=',$attention_id)->first();
        if (empty($attention)){
            $attention = new Attention();
            $attention->user_id = $user_id;
            $attention->attention_id = $attention_id;
            $attention->save();
        }
        return true;
    }
    public function delAttentionUser($user_id,$attention_id)
    {
        $attention = Attention::where('user_id','=',$user_id)->where('attention_id','=',$attention_id)->first();
        if ($attention){
            $attention->delete();
        }
        return true;
    }
    public function checkAttentionUser($user_id,$attention_id)
    {
        return Attention::where('user_id','=',$user_id)->where('attention_id','=',$attention_id)->count();
    }
    public function addTransfer($id,$data)
    {
        $transfer = $id?Transfer::find($id):new Transfer();
        foreach ($data as $key => $value){
            $transfer->$key = $value;
        }
        if ($transfer->save()){
            return $transfer->id;
        }
    }
    public function getTransfer($state)
    {
        return Transfer::where('state','=',$state)->get();
    }

}