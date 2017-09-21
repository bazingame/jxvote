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
    $prize_list = array('0922'=>'理发体验一次','0923'=>'面膜1张','0924'=>'电话卡1','0925'=>'奶茶1杯','0926'=>'代金劵1张','0927'=>'果汁2杯','0928'=>'水果茶1杯','0929'=>'果汁2杯','0930'=>'抵用券1张','1001'=>'奶茶1杯','1002'=>'阿道夫小礼包','1003'=>'随机礼品1份','1004'=>'终极大奖');
    $key_name = array('1004','1003','1002','1001','0930','0929','0928','0927','0926','0925','0924','0923','0922');
    $register_info = $personal_info[0];
    $album_info = json_decode($register_info['album_info'],true);
    $register_count = json_decode($register_info['register_count'],true);
    foreach (array_reverse($register_count['detail']) as $key =>$value){
        if($value=='1'){
            $last_reg_date = $key_name[$key];
            break;
        }
    }
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
        <div class="userInformation" <?php  if(!$isRegister){echo 'style="display:none";';}?>><img src="./images/circleCorrect.png" style="width:5%;margin-right:7%;">目前最新已获奖品<span id="informationWordss">[<?php echo $prize_list[$last_reg_date]; ?>]</span></div>
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
               <ul class="Labels">
                   <li>自拍</li>
                   <li>自拍</li>
                </ul>
           </div>
           <div id="albumBtns">
               <div class="albumBtn "onclick="location.href='./personal.php?id=<?php echo $register_info['Id'];?>'">查看相册</div>
               <div class="albumBtn" id="reviseInformation">修改信息</div>
           </div>
        </div>
         <div class="awardBox">
         <div class="wordAward">奖项设置</div>
         <div class="awardText">123123</div>
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
</body>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
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
            alert('请凭此页面联系三翼招商君qq2092674603领取奖品！');
        }
    }
    $('#goPrize').click(function() {
        if (<?php echo $times; ?>) {
            location.href = './lottery.php';
        }
        else{
            alert('Sorry，你没有足够的抽奖机会~')
        }
    });
    $('#gly').click(function() {
        alert('管理员QQ是1684577735，有什么事联系她吧~')
    });
    </script>