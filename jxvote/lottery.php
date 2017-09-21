<?php
include '../class/LuckyKid.class.php';
include_once '../class/User.class.php';
error_reporting(0);

header("Content-type:text/html;charset=utf-8");

$user = new User($_SESSION['openId'], $_SESSION['nickName']);
$user->timePlus();

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
$json_data = json_encode_ex($prize_array);
$luck = new LuckyKid('1', $json_data);
$data = $luck->getResult();
// print_r($data);
$prize =  $data['prize'];

/*汉字json编码转换*/
function json_encode_ex($value)
{
    if ( version_compare(PHP_VERSION,'5.4.0','<'))
    {
        $str = json_encode($value);
        $str =  preg_replace_callback(
            "#\\\u([0-9a-f]{4})#i",
            function($matchs)
            {
                return  iconv('UCS-2BE', 'UTF-8',  pack('H4',  $matchs[1]));
            },
            $str
        );
        return  $str;
    }
    else
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}

?>


<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>抽奖页面 - 三翼工作室</title>
    <link rel="stylesheet" href="./css/lottery.css">
</head>
<body>
    <div class="button">点击抽奖</div>
    <div class="tip" style="width: 245px;height: auto;">快点击按钮试试运气吧！</div>
    <div class="result"></div>
    <div class="prize-list">
        <div style="background-color: rgba(0, 0, 0, 0.23);width: 100%;height: 85%;border-radius: 5px;">
            <div class="title" style="">&nbsp;抽奖奖品列表
                <ul>
                    <li>· 8天在线赞助的本学期代取快递到寝室20人</li>
                    <li>· 无处可逃密室逃脱赞助的价值280元的免费券8张</li>
                    <li>· 无处可逃密室逃脱赞助的价值140元的免费券4张</li>
                    <li>· 无处可逃密室逃脱赞助的价值105元的免费券3张</li>
                    <li>· AF.ONE桌游赞助的价值228元的夜场免费券（饮料无限饮）</li>
                    <li>· 创1+团队赞助的价值135元的床上用品三件套5份</li>
                    <li>· 桃妆小铺赞助的价值48元的军训晒后修复体验劵20张</li>
                    <li>· 老男孩咖啡馆赞助的价值28元芒果慕斯20杯</li>
                    <li>· 星宇网咖私影会所赞助的10元代金券40张</li>
                    <li>· 星宇网咖私影会所赞助的20元代金券40张</li>
                    <li>· 星宇网咖私影会所赞助的30元代金券40张</li>
                    <li>· 福寿堂药店会赞助的藿香正气口服液200支</li>
                    <li>· 福寿堂药店会赞助的护眼贴100个</li>
                    <li>· 发如雪赞助的20元现金抵用券2张</li>
                    <li>· 小公举工作室赞助的10元代金券100份</li>
                    <li>· 名人文具店会赞助的书签48盒</li>
                    <li>· 万事屋提供的卡通笔18支</li>
                </ul>
            </div>
        </div>
        
    </div>
    <div class="footer">Copyright &copy; 2004-2016 湘潭大学三翼工作室</div>

    <script src="apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>
        var i = 0;
        $(function(){
            $('.button').click(function(){
                if (i == 0) {
                    var result = "<?php echo $prize; ?>";
                    alert("恭喜你，抽中" + result);
                    $('.button').text("返回主页");
                    $('.result').text("奖品：" + result);
                    $('.tip').text("奖品会在个人页中“我的奖池”里面显示，现在点击返回查看你的照片吧~");
                    i++;                    
                }
                else{
                    location.href = './index.php?';
                }
            });
        })
    </script>
</body>
</html>