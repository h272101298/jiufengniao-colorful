<?php

namespace App\Libraries;



class ExpressSearch
{
    private $EBusinessID;
    private $AppKey;
    private $ReqURL;
    public function __construct($businessId,$appKey,$reqUrl='http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx')
    {
        $this->AppKey = $appKey;
        $this->ReqURL = $reqUrl;
        $this->EBusinessID = $businessId;
    }

    /**
     * Json方式 查询订单物流轨迹
     */
    function getOrderTracesByJson($code,$logisticCode,$orderNumber=''){
        $requestData = sprintf("{'OrderCode':'','ShipperCode':'%s','LogisticCode':'%s'}",$code,$logisticCode);
        $requestData = utf8_decode($requestData);
//        $requestData= json_encode($requestData);
//        print_r($requestData);
        $datas = array(
            'EBusinessID' => $this->EBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, $this->AppKey);
//        var_dump($datas);
//        print_r($datas['DataSign']);
        $result=$this->sendPost($this->ReqURL, $datas);

        //根据公司业务处理返回的信息......

        return $result;
    }

    /**
     *  post提交数据
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据
     * @return url响应返回的html
     */
    function sendPost($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if(empty($url_info['port']))
        {
            $url_info['port']=80;
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);

        return $gets;
    }

    /**
     * 电商Sign签名生成
     * @param data 内容
     * @param appkey Appkey
     * @return DataSign签名
     */
    function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }

}


?>