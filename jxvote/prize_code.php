<?php
include_once '../class/DataBase.class.php';
include_once '../class/WeiXin.class.php';
include_once '../class/VF.class.php';
header('Content-type:text/html;charset=utf-8');

if(isset($_GET['date'])) {
    $date = $_GET['date'];
    $DB = new DataBase(DB_HOST, DB_USER, DB_PWD, DB_NAME);
    $DB->select('prize_code', '*', "date='$date' ORDER BY code");
    $result = $DB->fetchArray(MYSQL_ASSOC);
    echo '<h1>'.$date.'日兑奖码(共'.count($result).'个)</h1></br>';
//    print_r($result);
    foreach ($result as $key=>$value){
        echo '<hr>';
        $res =  $key.':'.$value['code'];
        if($value['is_received']==1){
            $res .= '--√</br>';
        }else{
            $res .='--未领取 <button class="receive" style="width: 60px;height: 40px;font-size: 20px;">领取</button></br>';
        }
//        print_r($value);
        $res = '<span id = '.$value['Id'].'>'.$res.'</span>';
        echo $res;
    }
}else{
    echo "缺少参数";
}

if(isset($_GET['action'])&&$_GET['action']=='receive'){
    $id = $_GET['id'];
    $DB = new DataBase(DB_HOST, DB_USER, DB_PWD, DB_NAME);
    $DB->update('prize_code',array('is_received'=>'1'),"Id = '$id'");
}

?>

<script src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script>
    $(".receive").on('click',function () {
        var id = $(this).parent().attr('id');
        $.ajax({
            url: '<?php  echo $_SERVER['PHP_SELF'].'?action=receive'; ?>',
            type: 'GET',
            data:{
                id:id
            },
            success:function () {
                alert('领取成功');
                window.location.reload();
            }
        })

    })
</script>
