<?php
include_once '../class/DataBase.class.php';
include_once '../class/WeiXin.class.php';
include_once '../class/View.class.php';
date_default_timezone_set('PRC');

/**
* 报名的相关操作
*/
class Sign
{
    function __construct()
    {
        session_start();
        $data = $this->checkChance();
        $this->chanceA = $data['chanceA'];
        $this->chanceB = $data['chanceB'];
    }

    /*个人类型报名*/
    function sign($name,$sid,$department, $QQ, $tel, $album_subject){
            $date = date("Y-m-d H:i:s");
            if (!isset($_SESSION['nickName']) || !isset($_SESSION['openId'])) {
                echo "缺失数据！";
                return 0;
            }
            $nickName = $_SESSION['nickName'];
            $openId = $_SESSION['openId'];

            $personal_info = array('sid'=>$sid,'name'=>$name,'college'=>$department,'phone'=>$tel,'qq'=>$QQ);
            $personal_info = json_encode($personal_info,JSON_UNESCAPED_UNICODE);

            $album_info = array('subject'=>$album_subject,'cover'=>'');
            $album_info = json_encode($album_info,JSON_UNESCAPED_UNICODE);

            $view = new View();
            $count = $view->getTotalVotes();
            $sign_num = $count[0]['sign_up_count'];
            $rank = $sign_num+1;

            $vister_count = $vote_count = 0;

            $register_count = '{"count":"0","detail":{"0922":"0","0923":"0","0924":"0","0925":"0","0926":"0","0927":"0","0928":"0","0929":"0","0930":"0","1001":"0","1002":"0","1003":"0","1004":"0"}}';

            //插入数据
            $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
            $userInfo = array('name'=>$name, 'openId'=>$openId, 'personal_info'=>$personal_info, 'album_info'=>$album_info, 'rank'=>$rank, 'vister_count'=>$vister_count, 'vote_count'=>$vote_count, 'register_count'=>$register_count, 'update_time'=>$date);
//             print_r($userInfo);

            $DB->insert("candidate", $userInfo);     //插入报名数据
            // print_r($DB->printMessage());

            $DB->update("count",array('sign_up_count'=>$rank) , "Id = 1");   //总人数加一
            return true;
    }


    //传签到照片
    function signPic($openId,$words,$serverID){
        $date = date("Y-m-d H:i:s");

        $weixin = new WeiXin();
        $imgData_json = $weixin->downloadfile($serverID);
        $imgArr = json_decode($imgData_json,true);
        $time = date("m").date("d");
        $photo_list = array(
                            $time=>array('pic'=>$imgArr,
                                        'words'=>$words,
                                        'label'=>array('开心','开心') ) );

        //首张为封面，先判断有无封面
        $cover = $this->getCover($openId);
        if($cover == ''){
            $cover_url = $imgArr[0];
            $this->setCover($openId,$cover_url);
        }


        //获取原有照片列表信息
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select('candidate','photo_list',"openId ='$openId'");
        $data = $DB->fetchArray(MYSQL_ASSOC);
        $data = $data[0]['photo_list'];
        $photo_old = json_decode($data,true);
//        return $photo_old;
//        return json_encode($photo_old,JSON_UNESCAPED_UNICODE);
        // 判断是否已签到
        if(array_key_exists($time,$photo_old)){     //已签到
            //将pic信息合并
            $thisDaypic = array_merge($imgArr,$photo_old[$time]['pic']);
            $thisDayInfo = array('pic'=>$thisDaypic,'words'=>$words,'lebel'=>array('开心'));
            $thisDayInfo = array($time=>$thisDayInfo);
            //今日与全部信息合并
            $photo_list = array_merge($photo_old,$thisDayInfo);
            $photo_list = json_encode($photo_list,JSON_UNESCAPED_UNICODE);
            $DB->update('candidate',array('photo_list'=>$photo_list),"openId='$openId'");   //再次打卡会失败！
            //未参与抽奖返回-1
            $code = -1;
            return $code;
        }else{      //未签到
            //获取兑奖码 (中奖为5位数字，否则为0)
            $code = $this->getPrizeCode();
            if($code!=0){
               $res =  $this->addPrize($openId,$time,$code);
            }
            $this->setReg($date,$openId);
            //判断是否第一次
            if($photo_old==''){
            }else{
                $photo_list = array_merge($photo_list,$photo_old);
            }
            $photo_list = json_encode($photo_list,JSON_UNESCAPED_UNICODE);
//            return  $photo_list;
        }
        $data = $photo_list;
//        return $data;
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->update('candidate',array('photo_list'=>$data,'update_time'=>$date),"openId='$openId'");   //再次打卡会失败！
//        $DB->update_1('candidate','photo_list',$data,"openId='$openId'");
//        $DB->update('candidate',array('update_time'=>$date),"openId='$openId'");
//        $sql = "UPDATE candidate SET photo_list = '$data' WHERE openId = '$openId'";
//        $DB->query($sql);

        return $code;
//        return $photo_list;
    }


    //获取兑奖码
    function getPrizeCode(){
        $date = date("m").date("d");
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select('register_limit','*',"date ='$date'");
        $data = $DB->fetchArray(MYSQL_ASSOC);
        $limit = $data[0]['limit'];
        $now_num = $data[0]['monitor'];
        if($now_num<$limit){
            $now_num++;
            $DB->update('register_limit',array('monitor'=>$now_num),"date = '$date'");
            $DB->select('prize_code','code',"date ='$date' AND code_order = '$now_num' ");
            $data = $DB->fetchArray(MYSQL_ASSOC);
            $code = $data[0]['code'];
            return $code;
        }else{
            return 0;
        }
    }


    function getCover($openId){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select('candidate','*',"openId='$openId'");
        $data = $DB->fetchArray(MYSQL_ASSOC);
        $album_info = $data[0]['album_info'];
        $album_info = json_decode($album_info,true);
        return $album_info['cover'];
    }

    function setCover($openId,$cover){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select('candidate','*',"openId='$openId'");
        $data = $DB->fetchArray(MYSQL_ASSOC);
        $album_info = $data[0]['album_info'];
        $album_info = json_decode($album_info,true);
        $album_info['cover'] = $cover;
        $res = json_encode($album_info,JSON_UNESCAPED_UNICODE);
        $DB->update('candidate',array('album_info'=>$res),"openId='$openId'");
//        return $album_info['cover'];
    }

    //设置register_count (有点问题)

    function setReg($date,$openId){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select('candidate','*',"openId='$openId'");
        $data = $DB->fetchArray(MYSQL_ASSOC);
        $register_list = $data[0]['register_count'];
        $res = json_decode($register_list,true);
        $res['count']++;
        $res['detail'][$date] = 1;
        $res = json_encode($res,JSON_UNESCAPED_UNICODE);
        $DB->update('candidate',array('register_count'=>$res),"openId = '$openId'");
    }

    //记录兑奖码
    function addPrize($openId,$date,$code){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select('candidate','prize',"openId='$openId'");
        $data = $DB->fetchArray(MYSQL_ASSOC);
        $data = $data[0]['prize'];
//        return $data;
        $data = json_decode($data,true);
//        return $data;
        if($data==''){
            $res = array($date=>$code);
        }else{
            $thisPrize = array($date=>$code);
            $res = array_merge($thisPrize,$data);
//            return $res;
        }
        $res = json_encode($res,JSON_UNESCAPED_UNICODE);
        $DB->update('candidate',array('prize'=>$res),"openId = '$openId'");
    }



    /*检查报名机会*/
    function checkChance(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $openId = $_SESSION['openId'];
        $DB->select("userinfo", "*", "openid = '$openId'");      //数据查询
        $data = $DB->fetchArray(MYSQL_ASSOC);
        $data = $data[0]['sign'];
        $data = json_decode($data, 1);
        return $data;
    }

    /*抽奖机会+1*/
    private function plusChance($DB){
        $openid   = $_SESSION['openId'];
        $DB->select("userinfo", "prize", "openid = '$openid'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = $result[0]['prize'];
        $data = json_decode($data, 1);
        $chance = $data['chance'];
        $chance += 1;
        $data['chance'] = $chance;
        $json_data = json_encode($data);
        $userInfo = array('prize' => $json_data);
        $DB->update("userinfo", $userInfo, "openid = '$openid'");
        // print_r($DB->printMessage());
    }

    /*抽奖机会+1  帮别人加*/
    function plusChanceOther($openid){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select("userinfo", "prize", "openid = '$openid'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = $result[0]['prize'];
        $data = json_decode($data, 1);
        $chance = $data['chance'];
        $chance += 1;
        $data['chance'] = $chance;
        $json_data = json_encode($data);
        $userInfo = array('prize' => $json_data);
        $DB->update("userinfo", $userInfo, "openid = '$openid'");
        // print_r($DB->printMessage());
    }

    /*抽奖机会-1*/
    function subChance(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $openid   = $_SESSION['openId'];
        $DB->select("userinfo", "prize", "openid = '$openid'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = $result[0]['prize'];
        $data = json_decode($data, 1);
        $chance = $data['chance'];
        $chance -= 1;
        $data['chance'] = $chance;
        $json_data = json_encode($data);
        $userInfo = array('prize' => $json_data);
        $DB->update("userinfo", $userInfo, "openid = '$openid'");
        // print_r($DB->printMessage());
        return true;
    }


    /*获取报名机会数据*/
    public function getChanceData(){
        $data = $this->checkChance();
        $data = json_encode($data);
        return $data;
    }

    /*获取抽奖机会*/
    function getPrizeChance(){
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $openid   = $_SESSION['openId'];
        $DB->select("userinfo", "prize", "openid = '$openid'");
        $result = $DB->fetchArray(MYSQL_ASSOC);
        $data = $result[0]['prize'];
        $data = json_decode($data, 1);
        $times = $data['chance'];
        return $times;
    }
}

//$aa = new Sign();
//$res = $aa->setReg('0923','oYeDBjmVqf0RhrTflYBfTBBmTo5Y');
//print_r($res);
