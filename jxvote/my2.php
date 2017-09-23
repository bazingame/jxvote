<?php
include_once '../class/DataBase.class.php';
include_once "../class/WeiXin.class.php";
include_once '../class/User.class.php';
include_once '../class/Sign.class.php';
header("Content-type:text/html;charset=utf-8");
session_start();
if(isset($_SESSION['canVote'])){
    $openId = $_SESSION['openId'];
    $nickName = $_SESSION['nickName'];
    $headImgurl = $_SESSION['headImgurl'];
    $canVote = $_SESSION['canVote'];
    $isRegister = $_SESSION['isRegister'];
}else{//未设置此session时，判断是否微信登录和是否关注，即是否可获得用户信息
    //获取UA,判断微信
    $UA = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/MicroMessenger/', $UA)) {
        $isWx = 1 ;
    }else{
        $isWx = 0 ;
    }

    if($isWx){
        //初始化微信对象获取用户数据判断是否关注以及是否公众号内打开
        $weixin = new WeiXin();
        $userInfo = $weixin->getUserInfo2();
        if($userInfo=='0'){
            $isSubcribe =0;
        }else{
            $isSubcribe =1;
        }

        //关注，则可投票
        if($isSubcribe){
            $canVote = 1;
        }else{
            $canVote = 0;
        }

    }else{
        $canVote = 0;
    }

    //如果可投票,相当于可登陆
    if($canVote){
        //设置SESSION,解析用户数据
        $userInfo = json_decode($userInfo, true);
        $openId = $userInfo['openid'];
        $nickName = $userInfo['nickname'];       //用户昵称
        $headImgurl = substr($userInfo['headimgurl'], 5, -2) . "/132"; //用户头像
        $headImgurl = 'https:'.$headImgurl;
        $_SESSION['openId'] = $openId;
        $_SESSION['nickName'] = $nickName;
        $_SESSION['headImgurl'] = $headImgurl;
        $_SESSION['canVote'] = $canVote;

        //判断是否已报名
        $DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
        $DB->select("candidate", "*", "openId = '$openId'");
        $personal_info = $DB->fetchArray(MYSQL_ASSOC);
        if(empty($personal_info)){
            $isRegister = 0;
        }else{
            $isRegister = 1;
            $personal_id = $personal_info[0]['Id'];
        }
    }else{
        $isRegister = 0;
    }
    $_SESSION['isRegister'] = $isRegister;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>军训时光记 - 三翼工作室</title>
       <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,init-scale=1.0,max-scale=1.0,userscalable=no"/>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/my2.css">
    <script src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js">
</script>
</head>
<body>
    <div class="background" style="z-index:-1;"></div>
     <img src="./images/myWord2.png" alt="" id="myWordImg2">
     <div id="myWord2">
         <p>观察力，摄影和军训，能搭配出多少可能？</p>
         <p>军训，能带来多少变化呢？</p>
         <p>创建自己的军训相册，</p>
         <p>开始自己的 军训时光记！</p>
     </div>
     <div class="jxzyPart">
          <img src="./images/jxzyImg.jpg">
          <div class="wordJxzy">
              <div>合作活动:军训之夜</div>
              <div>10月8日晚7点整 第一文化广场 （一田）</div>
              <div style="text-align:center;">青春无敌，Fun肆来袭！</div>
          </div>
     </div>
    <div class="jxzyPart">
          <img src="./images/90Logo.jpg">
          <div class="wordJxzy">
              <div>冠名赞助商:90户外俱乐部</div>
              <div>--有温度的旅行，有成长的扩展</div>
          </div>
     </div>
     <img src="./images/90.jpg" id="the90">
     <div class="awardBox">
         <div id="settingPart">
         <div class="wordAward">奖项设置</div>
         <div class="awardText">
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
            <img src="./images/90.jpg">
     </div>
       <nav class="bottom-nav"> 
        <div class="btn-d">
            <div class=" bottomNavBtn2" style="width:60%;height:60%;" onclick="location.href = './index.php'"> <span>首页</span></div>
        </div>
        <div class="btn-d ">
             <img src="./images/cross.png">
             <div class="bottomSign" style="margin:0px;width: 100%;height: 100%;" onclick="javascript:if (!(<?php echo $canVote;?>)) {alert('请进入三翼校园公众号，点击下方菜单或回复军训时光记使用该功能')}else{location.href = './sign.php'}"> 报名</div>
        </div>
        <div class="btn-d ">
             <div class=" bottomNavBtn" style="width:60%;height:60%;color:black;"> <span>个人</span></div>
        </div>
    </nav>
    <div class="giftList">
        <div class="giftWords">
            <div class="wordAward2">投票奖</div>
            <div><span class="wordModeI">冠军：</span><span class="wordModeII">奖金666元 +校级荣誉证书 +价值798元的90户外俱乐部“张家界徒步探险”两日双人游+价值800元的宝岛眼镜店眼镜1副+价值1000元的青鸟琴行吉他1把</span></div>
            <div><span class="wordModeI">亚军：</span><span class="wordModeII">奖金520元+校级荣誉证书+价值598元的90户外俱乐部“武功山穿越”两日双人游+价值500元的宝岛眼镜店眼镜1副+价值208的瑞诗凯诗形体艺术中心学习月卡1张</span></div>
            <div><span class="wordModeI">季军：</span><span class="wordModeII">奖金321 元 +校级荣誉证书+价值398元的90户外俱乐部 “浏阳溯溪”两日双人游+价值300元的宝岛眼镜店眼镜1副+价值208的瑞诗凯诗形体艺术中心学习月卡1张</span></div>
            <div><span class="wordModeI">第四名：</span><span class="wordModeII">奖金233元+价值673元的康力健身俱乐部季卡1张+价值278元的厨子当家价值套餐1份+价值208元的瑞诗凯诗形体艺术中心学习月卡1张</span></div>
            <div><span class="wordModeI">第五名：</span><span class="wordModeII">奖金123元+价值364元的康力健身俱乐部月卡1张+价值188元的厨子当家价值套餐1份+价值208元的瑞诗凯诗形体艺术中心学习月卡1张</span></div>
            <div><span class="wordModeI">No 6 — No 10 ：</span><span class="wordModeII">价值300元的青鸟琴行月卡1张+价值60元的口琴1把+价值60元的新东方课外书2本+价值20元的厨子当家代金劵1张+风信子时尚花艺馆盆栽1盆</span></div>
            <div><span class="wordModeI">No 11— No 20：</span><span class="wordModeII">价值300元的青鸟琴行月卡1张+价值60元的新东方课外书2本+价值20元厨子当家代金劵1张+风信子时尚花艺馆盆栽1盆</span></div>
            <div><span class="wordModeI"> No 21— No 40 ：</span><span class="wordModeII">价值100元的康力健身俱乐部3次卡1张+价值108元的新东方考研资料2份附带试听课</span></div>
            <div class="wordAward2">特别奖</div>
            <div><span class="wordModeI">最佳创意奖：</span><span class="wordModeII">奖金66.6元+校级荣誉证书+价值200元的逸启 Show 烫发名额1个+价值50元的厨子当家代金劵2张+价值60元的口琴1把+风信子时尚花艺馆鲜花1束</span></div>
            <div><span class="wordModeI">最佳搞怪奖：</span><span class="wordModeII">奖金66.6元+校级荣誉证书+价值200元的逸启 Show 烫发名额1个+价值50元的厨子当家代金劵2张+价值60元的口琴1把+风信子时尚花艺馆鲜花1束</span></div>
            <div><span class="wordModeI">最具情怀奖：</span><span class="wordModeII">奖金66.6元+校级荣誉证书+价值200元的逸启 Show 烫发名额1个+价值50元的厨子当家代金劵2张+价值60元的口琴1把+风信子时尚花艺馆鲜花1束</span></div>
            <div class="wordAward2">打卡奖</div>
            <div><span class="wordModeI">打卡时间段： 12:00—24:00 凭兑奖码截图到店登记领取福利</span></div>
            <div><span class="wordModeI">22号 前100名</span><span class="wordModeII">金瀚林商业街一楼逸启 Show 理发体验1次</span></div>
            <div><span class="wordModeI">23号 前100名</span><span class="wordModeII">联建腾升超市内美妆小屋面膜1张和免费体验券</span></div>
            <div><span class="wordModeI">24号 前100名</span><span class="wordModeII">东门正对面中国电信价值200元的电话卡1张</span></div>
            <div><span class="wordModeI">25号 前150名</span><span class="wordModeII">联建乐茶醇香奶盖茶店奶茶1杯</span></div>
            <div><span class="wordModeI">26号 前150名</span><span class="wordModeII">金瀚林商业街一楼逸启 Show  35元理发代金劵1张</span></div>
            <div><span class="wordModeI">27号 前100名</span><span class="wordModeII">太平市场茶香如语果汁2杯（西瓜/梨子/柠檬/芒果）</span></div>
            <div><span class="wordModeI">28号 前100名</span><span class="wordModeII">太平市场水果Bang霸王水果茶1杯</span></div>
            <div><span class="wordModeI">29号 前60名</span><span class="wordModeII">太平市场茶香如语果汁2杯（西瓜/梨子/柠檬/芒果）</span></div>
            <div><span class="wordModeI">30号 前60名</span><span class="wordModeII">联建麦香园蛋糕店领取抵用券1张（5元/3元/2元）</span></div>
            <div><span class="wordModeI">1 号 前50名</span><span class="wordModeII">联建乐茶醇香奶盖茶店奶茶1杯</span></div>
            <div><span class="wordModeI">2号 前150名</span><span class="wordModeII">金瀚林商业街三楼Beauty上妆园阿道夫小礼包</span></div>
            <div><span class="wordModeI">3号 前170名</span><span class="wordModeII">联建虞美人蛋糕店随机礼品1份（招牌原味奶茶/丝袜奶茶/金桔柠檬/早餐包/小清新/杨枝甘露）</span></div>
            <div><span class="wordModeI">4号 前N名</span><span class="wordModeII">活动终极神秘大奖</span></div>
            <div class="wordAward2">幸运大奖</div>
            <div><span class="wordModeII">由赞助商随机抽取幸运报名选手赠送价值888元的聚happy轰趴馆体验一次（私人影院+ktv+麻将棋牌+休息室+电玩室+运动区）</span></div>
            <div class="wordAward2">参与奖</div>
            <div><span class="wordModeII">所有参赛选手凭参与活动的截图，享受宝岛眼镜提供的全场眼镜打5折再减50元的优惠。</span></div>
            <div><span class="wordModeII">凡是报名、投票的可以凭截图享受宝岛眼镜提供的三个月内意外事故免费换新；一年之内，免费保修的服务。</span></div>
            <div><span class="wordModeII">凡是报名、投票的可以凭截图到新体育馆康力健身俱乐部领取单次体验卡一张（或在10月8日军训之夜当晚到三翼摆点处领取）</span></div>
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
    <div style="display: none"><script src="https://s22.cnzz.com/z_stat.php?id=1264506451&web_id=1264506451" language="JavaScript"></script></div>

</body>
</html>