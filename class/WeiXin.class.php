<?php
include_once '../class/DataBase.class.php';
error_reporting(0);
/**
* 获取access token & jsapi_ticket & 网页授权认证的access_token & JDK签名
* 使用的时候注意目录结构：根目录下class文件夹放类、两个本地json文件、以及各个子项目文件夹
* 参数：appId & secret
*/
class WeiXin
{
    protected $appId;
    protected $secret;
    protected $url;
    function __construct($appId="wxc5d217408956f8ea", $secret="143ac50a4abb8a47c9ac8f330fc1972a")
    {
        $this->appId  = $appId;
        $this->secret = $secret; 
        $this->getAccessToken2($appId, $secret);
    }

    /*获取普通access_token(单值)*/
    function getAccessToken($appId, $secret){
        $res = file_get_contents('../access_token.json');
        $result = json_decode($res, true);
        $expires_time = $result["expires_time"];
        $access_token = $result["access_token"];
        if (time() >= ($expires_time + 7200) || $access_token == ""){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appId."&secret=".$secret;
            $res = file_get_contents($url);
            $result = json_decode($res, true);
            $access_token = $result["access_token"];
            $expires_time = time();
            file_put_contents('../access_token.json', '{"access_token": "'.$access_token.'", "expires_time": '.$expires_time.'}');
        }
        $this->token = $access_token;
        return 0;
    }

    public function getAccessToken2($appId, $secret){
        //从数据库中获取
        $db = new DataBase(DB_HOST,DB_USER,DB_PWD,'oauth2');
//        $db = new DataBase('oauth2',);
        $db->select( 'access_token','*', 'id = 0');
        $data = $db->fetchArray(MYSQL_ASSOC);
//        $result = $result[0];
        //判断是否有
        if ($data) {
//            $data = $db->results;
            $access_token = $data[0]['token'];
            $expires_in   = $data[0]['expires_in']; //有效时间
            $expires_time = $data[0]['expires_time']; //录入时间
            //重新获取
            if (time() >= ($expires_time + $expires_in) || $access_token == "") {
                $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appId."&secret=".$secret;
                $res = file_get_contents($url);
                $result = json_decode($res, true);
                $access_token = $result["access_token"];
                $expires_in   = $result['expires_in']; //有效时间
                $expires_time = time(); //录入时间
                $date = date("Y-m-d H:i:s"); //可观时间
//                $db->query("UPDATE access_token SET token = '$access_token', expires_in = '$expires_in', expires_time = '$expires_time', date = '$date' WHERE id = 0");
                $db->update('access_token',array('token'=>$access_token,'expires_time'=>$expires_time,'expires_in'=>$expires_in,'date'=>$date),"id = 0");
            }else{
                $url  = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=". $access_token ."&openid=". $this->testId ."&lang=zh_CN";
                $res  = file_get_contents($url);
                $data = json_decode($res, 1);
                if (!isset($data['nickname'])) {
                    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appId."&secret=".$secret;
                    $res = file_get_contents($url);
                    $result = json_decode($res, true);
                    $access_token = $result["access_token"];
                    $expires_in   = $result['expires_in']; //有效时间
                    $expires_time = time(); //录入时间
                    $date = date("Y-m-d H:i:s"); //可观时间
                    $db->update('access_token',array('token'=>$access_token,'expires_time'=>$expires_time,'expires_in'=>$expires_in,'date'=>$date),"id = 0");
                }
            }
        }else{
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appId."&secret=".$secret;
            $res = file_get_contents($url);
            $result = json_decode($res, true);
            $access_token = $result["access_token"];
            $expires_in   = $result['expires_in']; //有效时间
            $expires_time = time(); //录入时间
            $date = date("Y-m-d H:i:s"); //可观时间
//            $db->query("INSERT INTO access_token (token,expires_in,expires_time,date) VALUES ('$access_token','$expires_in','$expires_time','$date') ");
            $db->insert('access_token',array('token'=>$access_token,'expires_time'=>$expires_time,'expires_in'=>$expires_in,'date'=>$date));
        }
        $this->token = $access_token;
        return 0;
    }

    function getToken(){
        return $this->token;
    }

    /*获取jsapi_ticket(单值)*/
    private function getJsTicket($token){
        $res = file_get_contents('../JsTicket.json');
        $result = json_decode($res, true);
        $expires_time = $result["expires_time"];
        $jsTicket = $result["ticket"];
        if (time() >= ($expires_time + 7200) || $jsTicket == ""){
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$token."&type=jsapi";
            $res = file_get_contents($url);
            $result = json_decode($res, true);
            $jsTicket = $result["ticket"];
            $expires_time = time();
            file_put_contents('../JsTicket.json', '{"ticket": "'.$jsTicket.'", "expires_time": '.$expires_time.'}');
        }
        return $jsTicket;
    }

    /*获取网页授权认证的access_token(数组)*/
    private function getAccreditToken($code){
        $res = file_get_contents('../AccreditToken.json');
        $result = json_decode($res, true);
        $expires_time = $result["expires_time"];
        $access_token = $result["access_token"];
        if (time() >= ($expires_time + 7200) || $access_token == ""){
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->appId&secret=$this->secret&code=$code&grant_type=authorization_code";
            $res = file_get_contents($url);
            $result = json_decode($res, true);
            file_put_contents('../AccreditToken.json', "$res");
        }
        return $result;
    }    

    /*获取JDk签名*/
    function getSignature(){
        $url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; //获取地址栏完整url（带参数）
        $jsTicket  = $this->getJsTicket($this->token);
        $timestamp = time();
        $noncestr  = "JiaoWoSuiJiZiFuChuan";
        $string    = "jsapi_ticket=".$jsTicket."&noncestr=".$noncestr."&timestamp=".$timestamp."&url=".$url;
        $signature = sha1($string);
        $signArray = array("timestamp"=>"$timestamp", "noncestr"=>"$noncestr", "signature"=>"$signature", "appid"=>"$this->appId");
        $signJson  = json_encode($signArray);
        return $signJson;
    }

    /*获取用户信息*/
    function getUserInfo(){
        $signature = $this->getSignature();                        //获取jdk签名
        preg_match('/code=(.*?)&/', $_SERVER["QUERY_STRING"], $data);   //获取认证授权code
        $code = $data[1];
//        echo "<script>alert('".$code."')</script>";
        $AccreditTokenData = $this->getAccreditToken($code);           //获取网页认证授权access_token
        $AccreditToken = $AccreditTokenData['access_token'];
        $openId = $AccreditTokenData['openid'];                         //获取用户openid
        $userInfo = file_get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=$AccreditToken&openid=$openId&lang=zh_CN");
        return $userInfo;
    }

    /*重新获取失败的图片*/
    function getPic($media_id){
        $token = $this->token;
        $url = "https://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$token&media_id=$media_id";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpinfo = curl_getinfo($ch);
        curl_close($ch);
        $fileInfo = array_merge(array('header' => $httpinfo), array('body' => $package));
        $this->saveWeixinFile($media_id.".jpeg", $fileInfo["body"]);
        return $media_id;
    }

    /*下载图片（一般用这个）*/
    public function downloadfile($mediaID){
        $token = $this->token;
        $mediaData = json_decode($mediaID, 1);
        $imgData = array();
        foreach ($mediaData as $value) {
            $imgData[] = $value.".jpeg";
        }
        $imgData_json = json_encode($imgData);
        foreach ($mediaData as $value) {
            $url = "https://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$token&media_id=$value";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $package = curl_exec($ch);
            $httpinfo = curl_getinfo($ch);
            curl_close($ch);
            $fileInfo = array_merge(array('header' => $httpinfo), array('body' => $package));
            $this->saveWeixinFile($value.".jpeg", $fileInfo["body"]);
        }
        return $imgData_json;
    }

    function saveWeixinFile($filename, $filecontent)
    {
        $dir=dirname(__FILE__)."/recordings/";
        if(!is_dir($dir)){
            mkdir($dir,0777);
        }
        $local_file = fopen($dir=dirname(__FILE__)."/recordings/".$filename, 'w');
        if (false !== $local_file){
            if (false !== fwrite($local_file, $filecontent)) {
                fclose($local_file);
            }
        }
    }
}
