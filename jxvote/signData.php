<?php
/*报名*/
include_once '../class/Sign.class.php';
header("Content-type:text/html;charset=utf-8");
$sign = new Sign();

session_start();
$openid = $_SESSION['openId'];

$time = date("m").date("d");

$prize_list = array('0922'=>'金瀚林商业街一楼逸启Show理发体验1次','0923'=>'联建腾升超市内美妆小屋面膜1张和免费体验券','0924'=>'东门正对面中国电信价值200元的电话卡1张','0925'=>'联建乐茶醇香奶盖茶店奶茶1杯','0926'=>'金瀚林商业街一楼逸启Show35元理发代金劵1张','0927'=>'太平市场茶香如语果汁2杯（西瓜/梨子/柠檬/芒果）','0928'=>'太平市场水果Bang霸王水果茶1杯','0929'=>'太平市场茶香如语果汁2杯（西瓜/梨子/柠檬/芒果）','0930'=>'联建麦香园蛋糕店领取抵用券1张（5元/3元/2元）','1001'=>'联建乐茶醇香奶盖茶店奶茶1杯','1002'=>'金瀚林商业街三楼Beauty上妆园阿道夫小礼包','1003'=>'联建虞美人蛋糕店随机礼品1份（招牌原味奶茶/丝袜奶茶/金桔柠檬）','1004'=>'活动终极神秘大奖');


$today_prize = $prize_list[$time];

    if($_GET['type']=='pic'){
        $words = $_POST['words'];
        $serverId = $_POST['serverId'];
        $res = $sign->signPic($openid,$words,$serverId);
//        echo $res;
        if($res == '0'){
            echo '{"code":"0","res":"打卡成功！但今日抽奖人数已达上限了:-("}';
        }else if($res == '-1'){
            echo '{"code":"0","res":"今天又一次打卡成功啦"}';
        }else{
            $time = date("m").date("d");
            $word = '恭喜您获得'.$today_prize.'！您的兑奖码是:'.$res.'!请凭兑奖码在有效时间内到门店登记领取';
            if($time=='1004'){
                echo '{"code":"1","res":"中秋快乐！！翼宝永远爱你","pic":"./images/richman/'.$time.'.jpg"}';
            }else if($time =='1005'|| $time == '1006' || $time =='1007' || $time=='1008'){
                echo '{"code":"1","res":"","pic":"./images/richman/'.$time.'.jpg"}';                
            }else{
                echo '{"code":"1","res":"'.$word.'","pic":"./images/richman/'.$time.'.jpg"}';
            }
        }
    }else if($_GET['type']=='revise'){
        $status = $sign->revise($_POST['name'], $_POST['sid'], $_POST['department'], $_POST['QQ'], $_POST['tel'], $_POST['album_subject']);
        if ($status) {
            echo "<script>alert('信息修改成功！继续上传照片吧！');location.href='register.php'</script>";
        }
    }else {
        $status = $sign->sign($_POST['name'], $_POST['sid'], $_POST['department'], $_POST['QQ'], $_POST['tel'], $_POST['album_subject']);
        if ($status) {
            echo "<script>alert('信息上传成功！请开始上传照片吧！上传至少一张照片才可以参与投票哦！');location.href='register.php'</script>";
        }
    }



