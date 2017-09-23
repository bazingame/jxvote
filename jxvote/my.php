<?php
include_once '../class/DataBase.class.php';
include_once "../class/WeiXin.class.php";
include_once '../class/User.class.php';
include_once '../class/Sign.class.php';
header("Content-type:text/html;charset=utf-8");
/*获取UA*/
$UA = $_SERVER['HTTP_USER_AGENT'];
if (preg_match('/MicroMessenger/', $UA)) {
    $isWx = 1 ;
    $action = './changeData.php';
    $changeInfo = './changeInfo.php';
}
else{
    $isWx = 0 ;
    $action = '';
}


session_start();
$openid = $_SESSION['openId'];
$sign = new Sign();
$times = $sign->getPrizeChance();

//判断是否报名
$DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
$DB->select("candidate", "*", "openId = '$openid'");
$personal_info = $DB->fetchArray(MYSQL_ASSOC);

if(empty($personal_info)){
    $isRegister = 0;
    echo '<script>alert("请先报名吧！")</script>';
}else{
    $isRegister = 1;
    $prize_to_date = array('0922'=>'理发体验一次','0923'=>'面膜1张','0924'=>'电话卡1张','0925'=>'奶茶1杯','0926'=>'代金劵1张','0927'=>'果汁2杯','0928'=>'水果茶1杯','0929'=>'果汁2杯','0930'=>'抵用券1张','1001'=>'奶茶1杯','1002'=>'阿道夫小礼包','1003'=>'随机礼品1份','1004'=>'终极大奖');
//    $key_name = array('1004','1003','1002','1001','0930','0929','0928','0927','0926','0925','0924','0923','0922');
    $register_info = $personal_info[0];
    $album_info = json_decode($register_info['album_info'],true);
    $register_count = json_decode($register_info['register_count'],true);
//    foreach (array_reverse($register_count['detail']) as $key =>$value){
//        if($value=='1'){
//            $last_reg_date = $key_name[$key];
//            break;
//        }
//    }

    $DB->select("candidate", "*", "openId = '$openid'");
    $data = $DB->fetchArray(MYSQL_ASSOC);
    $prize_list = json_decode($data[0]['prize'],true);
//    print_r($prize_list);
//    if($d)
    $prize_show = '';
    foreach ($prize_list as $key=>$value){
        $prize_show = '<span class="">'.$prize_to_date[$key].'['.$value.']</span>';
    }
//    print_r($prize_show);

}
unset($DB);

$data = $result[0];
$nickname = $_SESSION['nickname'];                      //得到用户昵称
$openid   = $_SESSION['openId'];
$imgdata  = $_SESSION['headImgurl'];
//echo $openid.$nickname;
//echo $imgdata;


$data_prize_json  = $data['prize'];
$data_prize_array = json_decode($data_prize_json, 1);      //得到数据表数据
$data_prize_array = $data_prize_array['prize'];            //得到奖品ID数据
$data_prize_array = array_splice($data_prize_array,1);     //去掉第一个无用元素



$user = new User($_SESSION['openId'], $_SESSION['nickName']);
$user->timePlus();


?>

<!doctype html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,init-scale=1.0,max-scale=1.0,userscalable=no"/>
    <link href="./css/css.css" type="text/css" rel="stylesheet">
    <link href="./css/my2.css" type="text/css" rel="stylesheet">
    <link href="./css/mui.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="./Font-Awesome-4.4.0/css/font-awesome.min.css">
    <script src="./js/mui.min.js"></script>
    <script type="text/javascript">
    function uaCheck(){
        if (!<?php echo $isWx; ?>){
　　　     alert("微信端才有数据哦,关注“湘潭大学三翼校园”吧~");  
        }
    }
// 　　window.onload = uaCheck(); 
    </script>
    <link rel="stylesheet" href="./css/index.css">
</head>
<body>
    <div id="layer2" onclick="hidelayer()"></div>

    <div id="container">
         <div id="userPhoto"><img src="<?php
             if($isWx){
                 echo $imgdata;
             }else{
                 echo './images/kelaosi.jpg';
             }
             ?>"></div>
        <ul id="userUl">
               <li class="userLi"><img src="./images/paiLogo.jpg">排名:<?php if($isRegister){echo $register_info['rank'];} ?></li>
                <li class="userLi"><img src="./images/fangLogo.jpg">访问:<?php if($isRegister){echo $register_info['vister_count'];} ?></li>
                <li class="userLi" style="margin:0;"><img src="./images/touLogo.jpg">票数:<?php if($isRegister){echo $register_info['vote_count'];} ?></li>
        </ul>
        <div class="userInformation"  <?php  if(!$isRegister){echo 'style="display:none";';}?> ><img src="./images/littlePerson.png"><?php  echo $register_info['name'];?><span id="informationWords">[已签到: <?php echo $register_count['count']; ?>天]</span></div>
        <div class="userInformation" <?php  if(!$isRegister){echo 'style="display:none";';}?>><img src="./images/circleCorrect.png" style="width:5%;margin-right:7%;">目前已获奖品<span id="informationWordss">[<?php echo $prize_show; ?>]</span></div>
        <div class="blackBtn" onclick="alert('管理员QQ是1004168799，有什么问题问他吧')">联系管理员<img src="./images/whiteQQ.png"></div>
        <div class="blackBtn" onclick="location.href='https://www.sky31.com'">三翼工作室<img src="./images/whiteSanYi.png"></div>
        <img src="./images/sanYi.png" id="userSanYi">
        <div class="albumBox" <?php  if(!$isRegister){echo 'style="display:none";';}?>>
           <div id="opacityPage">
             <div id="userAlbum">
               <img src="../class/recordings/<?php echo $album_info['cover']; ?>">
               <span><?php echo $album_info['subject']; ?></span>
               <span style="margin-left:70%;"></span>
             </div>
<!--               <ul class="Labels">-->
<!--                   <li>自拍</li>-->
<!--                   <li>自拍</li>-->
<!--                </ul>-->
           </div>
           <div id="albumBtns">
               <div class="albumBtn "onclick="location.href='./personal.php?id=<?php echo $register_info['Id'];?>'">查看相册</div>
<!--               <div class="albumBtn" id="reviseInformation" onclick="location.href='./revise.php'">修改信息</div>-->
               <div class="albumBtn" id="reviseInformation" onclick="alert('请联系管理员')">修改信息</div>
           </div>
        </div>
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
        </div>
    </div>
    <nav class="bottom-nav"> 
        <div class="btn-d">
            <div class=" bottomNavBtn2" style="width:60%;height:60%;" onclick="location.href = './index.php'"> <span>首页</span></div>
        </div>
        <div class="btn-d ">
             <img src="./images/cross.png">
             <div class="bottomSign" style="margin:0px;width: 100%;height: 100%;" onclick="location.href = './register.php'"> 签到</div>
        </div>
        <div class="btn-d ">
             <div class=" bottomNavBtn" style="width:60%;height:60%;color:#fff;" > <span>个人</span></div>
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

<script>
    var isshow=0;
    var isshow0=0;
    $('#show').click(function() {
        var obj=document.getElementById('award');
        if(isshow)
        {
            obj.style.height="0";
            document.getElementById('down').style.transform="rotateZ(0deg)";
            document.getElementById('down').style.MozTransform="rotateZ(0deg)";
            document.getElementById('down').style.WebkitTransform="rotateZ(0deg)";
            document.getElementById('down').style.MsTransform="rotateZ(0deg)";
            document.getElementById('down').style.OTransform="rotateZ(0deg)";
            isshow=0;
        }
        else
        {
            obj.style.height="32%";
            document.getElementById('down').style.transform="rotateZ(180deg)";
            document.getElementById('down').style.MozTransform="rotateZ(180deg)";
            document.getElementById('down').style.WebkitTransform="rotateZ(180deg)";
            document.getElementById('down').style.MsTransform="rotateZ(180deg)";
            document.getElementById('down').style.OTransform="rotateZ(180deg)";
            isshow=1;
        }
    });
    // function showhide()
    // {
        
    // }
    $('#show0').click(function(){
        var obj=document.getElementById('award0');
        if(isshow0)
        {
            obj.style.height="0";
            document.getElementById('down0').style.transform="rotateZ(0deg)";
            document.getElementById('down0').style.MozTransform="rotateZ(0deg)";
            document.getElementById('down0').style.WebkitTransform="rotateZ(0deg)";
            document.getElementById('down0').style.MsTransform="rotateZ(0deg)";
            document.getElementById('down0').style.OTransform="rotateZ(0deg)";
            isshow0=0;
        }
        else
        {
            obj.style.height="36%";
            document.getElementById('down0').style.transform="rotateZ(180deg)";
            document.getElementById('down0').style.MozTransform="rotateZ(180deg)";
            document.getElementById('down0').style.WebkitTransform="rotateZ(180deg)";
            document.getElementById('down0').style.MsTransform="rotateZ(180deg)";
            document.getElementById('down0').style.OTransform="rotateZ(180deg)";
            isshow0=1;
        }
    });
    // function showhide0()
    // {
        
    // }
    function Clear()
    {
        obj=document.getElementById('password');
        obj.style.color="black";
        console.log(obj.value);
        if(obj.value=="Password")
        obj.value="";
    }
    function showlayer()
    {
        var obj=document.getElementById('layer2');
        var obj2=document.getElementById('window');
        obj.style.display="block";
        obj2.style.display="block";
        setTimeout(function(){obj.style.opacity="0.8";obj2.style.height="30%"},100);
    }
    function hidelayer()
    {
        var obj=document.getElementById('layer2');
        var obj2=document.getElementById('window');
        obj.style.opacity="0";
        obj2.style.height="0";
        setTimeout(function(){obj.style.display="none";obj2.style.display="none";},350);
    }
    function upload()
    {
        obj=document.getElementById('password');
        if(obj.value=="sky31")
        {
            hidelayer();
            alert('领取成功！');
            location.href = "./clearPrize.php?openid=<?php echo $openid; ?>";
        }
        else
        {
//            alert('请凭此页面联系三翼招商君qq2092674603领取奖品！');
        }
    }

    </script>