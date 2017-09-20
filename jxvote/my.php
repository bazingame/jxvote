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

$DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
$DB->select("userinfo", "*", "openid = '$openid'");
$result = $DB->fetchArray(MYSQL_ASSOC);
unset($DB);

$data = $result[0];
$nickname = $data['nickname'];                      //得到用户昵称
$openid   = $data['openid'];
$imgdata  = $_SESSION['headImgurl'];

$data_prize_json  = $data['prize'];
$data_prize_array = json_decode($data_prize_json, 1);      //得到数据表数据
$data_prize_array = $data_prize_array['prize'];            //得到奖品ID数据
$data_prize_array = array_splice($data_prize_array,1);     //去掉第一个无用元素

$prize_array = array(
        array('name' => '8天在线赞助的本学期代取快递到寝室服务', 'num' => '20', 'tikect'=>'0'),
        array('name' => '无处可逃密室逃脱赞助的价值280元的免费券一张', 'num' => '8', 'tikect'=>'1'),
        array('name' => '无处可逃密室逃脱赞助的价值140元的免费券一张', 'num' => '4', 'tikect'=>'2'),
        array('name' => '无处可逃密室逃脱赞助的价值105元的免费券一张', 'num' => '3', 'tikect'=>'3'),
        array('name' => 'AF.ONE桌游赞助的价值228元的夜场免费券（饮料无限饮）一张', 'num' => '12', 'tikect'=>'4'),
        array('name' => '创1+团队赞助的价值135元的床上用品三件套', 'num' => '5', 'tikect'=>'5'),
        array('name' => '桃妆小铺赞助的价值48元的军训晒后修复体验劵一张', 'num' => '20', 'tikect'=>'6'),
        array('name' => '老男孩咖啡馆赞助的价值28元芒果慕斯一杯', 'num' => '20', 'tikect'=>'7'),
        array('name' => '星宇网咖私影会所赞助的10元代金券一张', 'num' => '40', 'tikect'=>'8'),
        array('name' => '星宇网咖私影会所赞助的20元代金券一张', 'num' => '40', 'tikect'=>'9'),
        array('name' => '星宇网咖私影会所赞助的30元代金券一张', 'num' => '40', 'tikect'=>'10'),
        array('name' => '福寿堂药店会赞助的藿香正气口服液一支', 'num' => '200', 'tikect'=>'11'),
        array('name' => '福寿堂药店会赞助的护眼贴一个', 'num' => '100', 'tikect'=>'12'),
        array('name' => '发如雪赞助的20元现金抵用券一张', 'num' => '2', 'tikect'=>'13'),
        array('name' => '小公举工作室赞助的10元代金券一张', 'num' => '100', 'tikect'=>'14'),
        array('name' => '名人文具店会赞助的书签一盒', 'num' => '48', 'tikect'=>'15'),
        array('name' => '万事屋提供的卡通笔一支', 'num' => '18', 'tikect'=>'16')
    );

$prize = $data_prize_array[0];

$prize_name = $prize_array[$prize]['name'];

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
    <div id="window">
            <p>联系三翼招商君QQ2092674603：</p>
            <p>输入招商君告诉你的秘密就可以领走奖品啦~</p>
            <input type="text" id="password" onfocus="Clear()" value="Password"></br>
            
            <button class="mui-btn mui-btn--small mui-btn--primary mui-btn--raised" style="position: absolute;left: 5%;top: 70%;" type='submit'  onclick="upload()">确认</button>
        </div>
    <div id="container">
         <div id="userPhoto"><img src="./images/kelaosi.jpg"></div>
        <ul id="userUl">
               <li class="userLi"><img src="./images/paiLogo.jpg">排名:</li>
                <li class="userLi"><img src="./images/fangLogo.jpg">访问:</li>
                <li class="userLi" style="margin:0;"><img src="./images/touLogo.jpg">票数:</li>
        </ul>
        <div class="userInformation"><img src="./images/littlePerson.png">台湾小帅哥第一次来湘大</div>
        <div class="userInformation"><img src="./images/circleCorrect.png" style="width:5%;margin-right:7%;">目前最大已获得奖品</div>
        <div class="blackBtn">联系管理员<img src="./images/whiteQQ.png"></div>
        <div class="blackBtn">三翼工作室?<img src="./images/whiteSanYi.png"></div>
        <img src="./images/sanYi.png" id="userSanYi">
        <div class="albumBox">
           <div id="opacityPage">
             <div id="userAlbum">
               <img src="./images/boy.jpg">
               <span>今天再照一个，哈哈变梁杰理了</span>
               <span style="margin-left:70%;">--9.22</span>
             </div>
           </div>
           <div id="albumBtns">
               <div class="albumBtn">查看相册</div>
               <div class="albumBtn" id="reviseInformation">修改信息</div>
           </div>
        </div>
         <div class="awardBox">
         <div class="wordAward">奖项设置</div>
         <div class="awardText">123123</div>
        </div>
        <div id="award">
            <div class="aaward">
                <p style="line-height: 20px;">我的抽奖机会：<?php echo $times; ?></p>
                <p style="line-height: 20px;">我的邀请码：<?php echo $openid; ?></p>
                <p style="line-height: 20px;">将邀请码发送给好友，报名时填写邀请码并报名成功你就能增加一次抽奖机会啦！</p>
                <p style="line-height: 20px;">* 每人最多五个奖品。</p>
                <button class="mui-btn mui-btn--raised mui-btn--danger" style="margin-right: 42%;float: right;margin-top: 0%;" id="goPrize">去抽奖</button>
            </div>
            <div style="width: 88%;margin: 156px 0 0 5%;">
                
            </div>
        </div>
        <div id="award0">
            <div class="mui-divider"></div>
            <div class="aaward">
                <p style="margin-bottom: 0px;">
                    <?php
                        foreach ($data_prize_array as $key => $value) {
                            echo $prize_array[$value]['name']."<br/>";
                        }
                        // echo $prize_name; 
                    ?>
                </p>
                <button class="mui-btn mui-btn--raised mui-btn--danger" onclick="showlayer()" style="margin-right: 42%;float: right;margin-bottom: 10px;">领取</button>
            </div>
            <div style="width: 85%;margin-top: 160px;">
                <!-- <p>请评此页面联系三翼招商君qq2092674603领取奖品</p><br /> -->
            </div>
        </div>
        </div>
    </div>
    <nav class="bottom-nav"> 
        <div class="btn-d">
            <div class=" bottomNavBtn2" style="width:60%;height:60%;" onclick="location.href = './index.php'"> <span>首页</span></div>
        </div>
        <div class="btn-d ">
             <img src="./images/cross.png">
             <div class="bottomSign" style="margin:0px;width: 100%;height: 100%;" onclick="location.href = './sign.php'"> 签到</div>
        </div>
        <div class="btn-d ">
             <div class=" bottomNavBtn" style="width:60%;height:60%;color:#fff;" onclick="location.href = './my.php'"> <span>个人</span></div>
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