<?php
include '../class/WeiXin.class.php';
include '../class/User.class.php';
include_once '../class/Sign.class.php';
include_once '../class/View.class.php';

header("Content-type:text/html;charset=utf-8");
/*获取UA*/
$UA = $_SERVER['HTTP_USER_AGENT'];
if (preg_match('/MicroMessenger/', $UA)) {
    $isWx = 1 ;
}
else{
    $isWx = 0 ;
}

if($isWx) {
    /*初始化对象并获取用户数据*/
    $weixin = new WeiXin();
    $userInfo = $weixin->getUserInfo();

    /*解析用户数据*/
    $userInfo = json_decode($userInfo, 1);
    session_start();
    $openId = $userInfo['openid'];
    $nickName = $userInfo['nickname'];       //用户昵称
    $headImgurl = substr($userInfo['headimgurl'], 0, -2) . "/132"; //用户头像

    /*数据存入session*/
    if (!isset($_SESSION['openId']) || !isset($_SESSION['nickName']) || !isset($_SESSION['headImgurl'])) {
        $_SESSION['openId'] = $openId;
        $_SESSION['nickName'] = $nickName;
        $_SESSION['headImgurl'] = $headImgurl;
    }

    $openId = $_SESSION['openId'];
    $nickName = $_SESSION['nickName'];
    $headImgurl =  $_SESSION['headImgurl'];
    $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
    $DB->select("candidate", "*", "openId = '$openId'");
    $personal_info = $DB->fetchArray(MYSQL_ASSOC);
    if(empty($personal_info)){
        $isRegister = 0;
    }else{
        $isRegister = 1;
    }


    $user = new User($_SESSION['openId'], $_SESSION['nickName']);
    $user->timePlus();
}

if($_GET['id']){
    $usr = new User('','');
    $usr->addVisterPersonal($_GET['id']);
}
$id = $_GET['id'];

$view = new View();
$data = $view->getPersonalAlbum($id)[0];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>军训时光记 - 三翼工作室</title>
    <meta name="viewport"content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
      <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/my2.css">
    <link rel="stylesheet" href="css/allPhotoPage.css">
</head>
<body>
    <div class="background"></div>
     <div class="bannerTop">
        <img src="./images/bannerTop2.jpg" alt="">
        <div class="phoneAfrica">
            <img src="./images/phoneAfrica.png" alt="">
        </div>
     </div>
      <div class="container"  style="padding: 15px 10px 15px 10px;">

            <div class="opacityPage">
             <div class="userNamePart">
                 <img src="./images/heart.png" alt="">
                 <?php
//                    $subject = json_decode($data['name'],true);
                 echo $data['name']; ?>
                 <img src="./images/littlePerson.png" alt="" style="width:auto; height:50%;">
             </div>
             <ul class="photoPageNav">
                 <?php
                 $rank = $data['rank'];
                 $visiter_count = $data['vister_count'];
                 $vote_count = $data['vote_count'];
                 $html = <<<HTML
                 <li>排名:{$rank}</li>
                 <li>访问:{$visiter_count}</li>
                 <li>票数:{$vote_count}</li>
HTML;
                echo $html;

                 ?>
             </ul>
             <div id="voteBtn" pid = "<?php echo $id; ?>">投一票</div>
         </div>
          <?php
            $album_info = json_decode($data['photo_list'],true);
            foreach (array_reverse($album_info) as $key => $value){
                $month = substr($key,0,2);
                $day = substr($key,2,2);
                $sign_time = $month.'.'.$day;
                $img = '';
                foreach ($value['pic'] as $val){
                    $img .= '<img src="../class/recordings/'.$val.'" alt="">';
                }
                $html = <<<HTML
                    <div class="onePhotoPartBox">
                        <div class="onePhotoPart">
                           <div class="divideLine"></div>
                            {$img}
                            <span class="itdPhotoWord">{$value['words']}</span></br>
                            <span style="margin-left:66%;">--</span>
                            <span class="dateWord">{$sign_time}</span>
                            <img src="./images/giftBox.png" alt="" class="giftBox" style="width:10%;">
                             <div class="giftWord"><img src="./images/yellowCircle.png" alt=""></div>
                        </div>
                        <!--<ul class="Labels">-->
                           <!--<li>自拍</li>-->
                           <!--<li>自拍</li>-->
                    <!--</ul>-->
                   </div>
HTML;
                echo $html;
            }
          ?>


          <div class="awardBox">
              <div id="settingPart">
                  <div class="wordAward">奖项设置</div>
                  <div class="awardText" style="margin-top:40px;">
                      <div><span class="wordModeI">投票奖</span><span class="wordModeII">(No.1-40,壕礼等你)</span></div>
                      <div><span class="wordModeI">特别奖</span><span class="wordModeII">(最创意，最搞怪，最情怀)</span></div>
                      <div><span class="wordModeI">打卡奖</span><span class="wordModeII">(每日不同，超乎想象)</span></div>
                      <div><span class="wordModeI">幸运大奖</span><span class="wordModeII">(来试试运气？)</span></div>
                      <div><span class="wordModeI">参与奖</span><span class="wordModeII">(点击底部签到进行报名，即可得奖)</span></div>
                      <div id="showList"><span class="wordModeI">【点击查看礼品清单】</span></div>
                  </div>
              </div>
              <div id="receiveWayPart">
                  <div class="wordAward">领奖方式</div>
                  <div class="awardText">
                      <div><span class="wordModeI">投票奖</span><span class="wordModeII">、</span><span class="wordModeI">特别奖</span><span class="wordModeII">和</span><span class="wordModeI">幸运大奖</span><span class="wordModeII">于10月8日军训之夜当晚到三翼摆点处领取，工作人员会提前通知</span></div>
                      <div><span class="wordModeI">特别奖</span><span class="wordModeII">请凭兑奖码到相应的门店登记领取</span></div>
                      <div><span class="wordModeII">(每日前n名获兑奖码，领取时间:9月22日--10月12日)</span></div>
                  </div>
              </div>
              <div id="chooseWayPart">
                  <div class="wordAward">评选方式</div>
                  <div class="awardText">
                      <div><span class="wordModeI">投票奖:</span><span class="wordModeII">关注三翼校园官方微信平台，输入我要投票，进入投票系统，选择自己最喜欢的三个参赛相册进行投票。</span></div>
                      <div><span class="wordModeI">特别奖:</span><span class="wordModeII">由三翼评审人员综合贴吧评论，官q评论，以及大众评审评选出"最佳创意奖" "最佳搞怪奖" 以及"最具情怀奖"</span></div>
                      <div><span class="wordModeI">打卡奖:</span><span class="wordModeII">每日打卡前n名即可获得</span></div>
                      <div><span class="wordModeI">幸运大奖:</span><span class="wordModeII">由赞助商随机抽取幸运报名选手赠送</span></div>
                      <div><span class="wordModeI">参与奖:</span><span class="wordModeII">参赛或投票即可领取</span></div>
                  </div>
              </div>
          </div>

            <div class="theSideLine">
                <div class="circleGrayTop"></div>
                <div class="circleGrayBottom"></div>
            </div>
    </div>
    <nav class="bottom-nav"> 
        <div class="btn-d">
            <div class=" bottomNavBtn2" style="width:60%;height:60%;" onclick="location.href = './index.php'"> <span>首页</span></div>
        </div>


        <div class="btn-d "  <?php  if(!$isRegister){echo 'style="display:none";';}?>>
            <img src="./images/cross.png">
            <div class="bottomSign" style="margin:0px;width: 100%;height: 100%;" <?php  if($isRegister){echo 'style="display:none";';}?> onclick="javascript:if (!<?php echo $isWx;?>) {alert('请进入三翼校园公众号，点击下方菜单或回复军训时光记使用该功能')}else{location.href = './register.php'}"> 签到</div>
        </div>


        <div class="btn-d "  <?php  if($isRegister){echo 'style="display:none";';}?>>
            <img src="./images/cross.png">
            <div class="bottomSign" style="margin:0px;width: 100%;height: 100%;" onclick="javascript:if (!<?php echo $isWx;?>) {alert('请进入三翼校园公众号，点击下方菜单或回复军训时光记使用该功能')}else{location.href = './sign.php'}"> 报名</div>
        </div>


        <div class="btn-d ">
            <div class=" bottomNavBtn2" style="width:60%;height:60%;color:black;" onclick="javascript:if (!<?php echo $isWx;?>) {location.href = './my2.php'}else{location.href = './<?php if($isRegister){echo 'my.php';}else{echo 'my2.php';}?>'}"> <span>个人</span></div>
        </div>
    </nav>


    <div class="giftList">
        <div class="giftWords">
            <div class="wordAward2">投票奖</div>
            <div><span class="wordModeI">冠军：</span><span class="wordModeII">奖金666元 +校级荣誉证书 +价值798元的90户外俱乐部“张家界徒步探险”两日双人游+价值800元的宝岛眼镜店眼镜1副+价值1000元的青鸟琴行吉他1把</span></div>
            <div><span class="wordModeI">亚军：</span><span class="wordModeII">奖金520元+校级荣誉证书+价值598元的90户外俱乐部“武功山穿越”两日双人游+价值500元的宝岛眼镜店眼镜1副+价值208的瑞诗凯瑜伽卡1张</span></div>
            <div><span class="wordModeI">季军：</span><span class="wordModeII">奖金321 元 +校级荣誉证书+价值398元的90户外俱乐部 “浏阳溯溪”两日双人游+价值300元的宝岛眼镜店眼镜1副+价值208的瑞诗凯瑜伽卡1张</span></div>
            <div><span class="wordModeI">第四名：</span><span class="wordModeII">奖金233元+价值673元的康力健身季卡1张+价值278元的厨子当家价值套餐1份+价值208元的瑞诗凯瑜伽卡1张</span></div>
            <div><span class="wordModeI">第五名：</span><span class="wordModeII">奖金123元+价值364元的康力健身月卡1张+价值188元的厨子当家价值套餐1份+价值208元的瑞诗凯瑜伽卡1张</span></div>
            <div><span class="wordModeI">No 6 — No 10 ：</span><span class="wordModeII">价值300元的青鸟琴行月卡1张+价值60元的口琴1把+价值60元的新东方课外书2本+价值20元厨子当家代金劵1张+风信子时尚花艺馆盆栽1盆</span></div>
            <div><span class="wordModeI">No 11— No 20：</span><span class="wordModeII">价值300元的青鸟琴行月卡1张+价值60元的新东方课外书2本+价值20元厨子当家代金劵1张+风信子时尚花艺馆盆栽1盆</span></div>
            <div><span class="wordModeI"> No 21— No 40 ：</span><span class="wordModeII">价值100元的康力健身3次卡1张+价值108元的新东方考研资料2份附带试听课</span></div>
            <div class="wordAward2">特别奖</div>
            <div><span class="wordModeI">最佳创意奖：</span><span class="wordModeII">奖金66.6元+校级荣誉证书+价值200元的启逸理发店烫发名额1个+价值50元的厨子当家代金劵2张+价值60元的口琴1把+风信子时尚花艺馆鲜花1束</span></div>
            <div><span class="wordModeI">最佳搞怪奖：</span><span class="wordModeII">奖金66.6元+校级荣誉证书+价值200元的启逸理发店烫发名额1个+价值50元的厨子当家代金劵2张+价值60元的口琴1把+风信子时尚花艺馆鲜花1束</span></div>
            <div><span class="wordModeI">最具情怀奖：</span><span class="wordModeII">奖金66.6元+校级荣誉证书+价值200元的启逸理发店烫发名额1个+价值50元的厨子当家代金劵2张+价值60元的口琴1把+风信子时尚花艺馆鲜花1束</span></div>
            <div class="wordAward2">打卡奖</div>
            <div><span class="wordModeI">打卡时间段： 12:00—24:00 凭兑奖码截图到店登记领取福利</span></div>
            <div><span class="wordModeI">22号 前100名</span><span class="wordModeII">联建虞美人蛋糕店随机礼品1份（招牌原味奶茶/丝袜奶茶/金桔柠檬/早餐包/小清新/杨枝甘露）</span></div>
            <div><span class="wordModeI">23号 前100名</span><span class="wordModeII">联建腾升超市内美妆小屋面膜1张和免费体验券</span></div>
            <div><span class="wordModeI">24号 前100名</span><span class="wordModeII">东门正对面中国电信价值200元的电话卡1张</span></div>
            <div><span class="wordModeI">25号 前150名</span><span class="wordModeII">联建乐茶醇香奶盖茶店奶茶1杯</span></div>
            <div><span class="wordModeI">26号 前150名</span><span class="wordModeII">金瀚林商业街一楼启逸 Show 35元理发代金劵1张</span></div>
            <div><span class="wordModeI">27号 前100名</span><span class="wordModeII">太平市场茶香如语果汁2杯（西瓜/梨子/柠檬/芒果）</span></div>
            <div><span class="wordModeI">28号 前100名</span><span class="wordModeII">太平市场水果Bang霸王水果茶1杯</span></div>
            <div><span class="wordModeI">29号 前60名</span><span class="wordModeII">太平市场茶香如语果汁2杯（西瓜/梨子/柠檬/芒果）</span></div>
            <div><span class="wordModeI">30号 前60名</span><span class="wordModeII">联建麦香园蛋糕店领取抵用券1张（5元/3元/2元）</span></div>
            <div><span class="wordModeI">1 号 前50名</span><span class="wordModeII">联建乐茶醇香奶盖茶店奶茶1杯</span></div>
            <div><span class="wordModeI">2号 前150名</span><span class="wordModeII">金瀚林商业街三楼Beauty上妆园阿道夫小礼包</span></div>
            <div><span class="wordModeI">3号 前170名</span><span class="wordModeII">金瀚林商业街一楼启逸 Show理发体验1次</span></div>
            <div><span class="wordModeI">4号 前N名</span><span class="wordModeII">活动终极神秘大奖（祝你行大运赢大礼，关注8号晚军训之夜，翼宝永远爱你们(๑˃̵ᴗ˂̵)و）</span></div>
            <div class="wordAward2">幸运大奖</div>
            <div><span class="wordModeII">由赞助商随机抽取幸运报名选手赠送价值888元的聚happy轰趴馆体验一次（私人影院+ktv+麻将棋牌+休息室+电玩室+运动区）</span></div>
            <div class="wordAward2">参与奖</div>
            <div><span class="wordModeII">所有参赛选手凭参与活动的截图，享受宝岛眼镜提供的全场眼镜打5折再减50元的优惠。</span></div>
            <div><span class="wordModeII">凡是报名、投票的可以凭截图享受宝岛眼镜提供的三个月内意外事故免费换新；一年之内，免费保修的服务。</span></div>
            <div><span class="wordModeII">凡是报名、投票的可以凭截图到新体育馆康力健身领取单次体验卡一张（或在10月8日军训之夜当晚到三翼摆点处领取）</span></div>
            <div class="wordAward2">关于领奖</div>
            <div><span class="wordModeII">投票奖、特别奖和幸运大奖于10月8日军训之夜当晚三翼摆点处领取，工作人员会提前通知。</span></div>
            <div><span class="wordModeII">打卡奖请凭兑奖码到相应的门店登记领取。（每日前n名获兑奖码，领取时间：9月22日—10月12日）</span></div>
        </div>
        <div id="closeList">关闭</div>
    </div>
    <div id="coverPage"></div>


    <script src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>
        $("#showList").click(function(){
            $(".giftList").css("display","block");
            $("#coverPage").css("display","block");
        })
        $("#closeList").click(function(){
            $(".giftList").css("display","none");
            $("#coverPage").css("display","none");
        })
    </script>
    <script>
        var voting=false;
        $('#voteBtn').on("click",function(){
            if (<?php echo $isWx; ?>) {
                if(voting)return false;
                var cur=$(this);
                var pid=$(this).attr("pid");
                var counter=$(".voteC[pid="+pid+"]");
                var counterNum=parseInt(counter.html());
                voting=true;
                $(this).html("ing...");
                $.ajax({
                    url:"./vote.php?id="+pid,
                    type:"get",
                    success:function(data){
                        try{
                            var jsonD=JSON.parse(data);
                            if(jsonD.code==0){
                                alert(jsonD.msg);
                                counterNum++;
                                counter.html(counterNum);
                            }
                            else{
                                alert(jsonD.msg);
                            }
                        }
                        catch(e){
                            alert("解析错误");
                        }
                        voting=false;
                        cur.html("投票");
                    }
                });
            }
            else{
                alert("投票已经截止咯。");
            }
        });

    </script>
</body>

</html>