<?php 
include '../class/WeiXin.class.php';
include '../class/User.class.php';
include_once '../class/Sign.class.php';
include_once '../class/DataBase.class.php';
header("Content-type:text/html;charset=utf-8");
/*初始化对象并获取用户数据*/
// $weixin = new WeiXin();
// $userInfo = $weixin->getUserInfo(); 

// /*解析用户数据*/
// $userInfo = json_decode($userInfo, 1);
// session_start();
// $openId     = $userInfo['openid'];
// $nickName   = $userInfo['nickname'];       //用户昵称
// $headImgurl = substr($userInfo['headimgurl'], 0,-2)."/132" ; //用户头像

// /*数据存入session*/
// if (!isset($_SESSION['openId'])||!isset($_SESSION['nickName'])||!isset($_SESSION['headImgurl'])) {
//   $_SESSION['openId']     = $openId;
//   $_SESSION['nickName']   = $nickName;
//   $_SESSION['headImgurl'] = $headImgurl;
// }

// /*获取JDk签名并解析*/
// $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   //获取地址栏完整url（带参数）
// $signature = $weixin->getSignature($url);
// $signature = json_decode($signature, 1);


// $user = new User($_SESSION['openId'], $_SESSION['nickName']);
$id = $_GET['id'];

$DB = new DataBase(DB_HOST,DB_USER,DB_PWD,DB_NAME);
$DB->select("candidate", "*", "id = $id");
$result = $DB->fetchArray(MYSQL_ASSOC);
// print_r($result);
$data = $result[0];
$imgData = $data['img'];
$imgData = json_decode($imgData, 1); 
// print_r($result);
// print_r($DB->printMessage());


?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>个人信息 - 三翼工作室</title>
    <link rel="stylesheet" href="./css/personal.css">
    <link href="./css/mui.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="./Font-Awesome-4.4.0/css/font-awesome.min.css">
    <script src="./js/mui.min.js"></script>
</head>
<body>
    <div class="background"></div>
    <div class="container mui-panel mui--z3" style="padding-top: 8px">
    <legend>
    <?php 
    if ($data['type']=="A"){
        echo $data['name'];
    } 
    else{
        echo $data['team'];
    }  
    ?>
    </legend>
        <div class="info clearFix">
            <!-- <button class="mui-btn mui-btn--primary">162号：张铃崎</button> -->
        </div>

        <div class="info clearFix" style="float: left;">
            <div class="vote" style="margin-left: 0px;margin-right: 10px;">票数：<?php echo $data['votes']; ?></div>
            <div class="rank" style="margin-left: 0px;margin-right: 10px;">排名：132</div>
        </div>
        <div class="button-group" style="float: right;position: absolute;top: 8px;right: 15px;">
            <button class="mui-btn mui-btn--fab" style="margin-left: 20px;">关注</button>
            <button class="mui-btn mui-btn--fab mui-btn--primary" style="margin-left: 20px;">投票</button>
        </div>
        <div class="userInfo">
        <?php 
            foreach ($imgData as $value) {
                $html = <<<HTML
                    <img src="../class/recordings/$value" alt="name" class="mui-panel mui--z2" style="padding: 0px;">
HTML;
                echo $html;
            }
            
         ?>
            <div class="declaration">
                <div class="title">参赛宣言</div>
                <div class="content"><?php echo $data['introduce']; ?></div><br />
            </div>
            <div class="prize-set">
                <div class="title">奖项设置</div>
                <div class="content">
                    票数No.1：<span class="highlight">1000</span>元奖金+校级荣誉证书+由康力健身提供价值498元的学期卡一张<br/><br/>
                    票数No.2：<span class="highlight">500</span>元奖金+校级荣誉证书+由康力健身提供的价值248元的月卡一张<br/><br/>
                    票数No.3：<span class="highlight">100</span>元奖金+校级荣誉证书+由康力健身提供的价值128元的15天体验卡一张<br/><br/>
                    综合评选：“最佳创意搞怪奖”、“最佳视觉享受奖”、“最佳红色文化奖”赠送三翼精美明信片一套以及校级荣誉证书<br/><br/><br/>
                    *最佳创意搞怪奖将拥有卫星马场永久免费名额<br/><br/>
                    *最佳视觉享受奖将拥有万事屋提供的龙猫玩具<br/><br/>
                    *最佳红色文化奖将拥有万事屋提供的汽车模型<br/><br/><br/>
                    *凡报名者均可享康力健身<span class="highlight">6.8</span>折优惠<br/><br/><br/>
                    独家赞助：必胜客
                    <img src="./images/bskpic.jpg" alt="bsk">
                    *点餐前出示本人有效学生证，或在支付宝上“校园生活”中认证即可享必胜客欢乐餐厅堂食8折美食优惠（部分特别说明产品除外）<br/><br/>
                    *本活动最终解释权归三翼工作室所有
                </div>
            </div>
        </div>
        <div class="footer">Copyright &copy; 2004-2016湘潭大学三翼工作室</div>
    </div>
    
    <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="./js/personal.js"></script>
</body>
</html>