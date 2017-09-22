<?php
function NoRand($begin=1000000000,$end=9999999999,$limit=10){
    $rand_array=range($begin,$end);
    shuffle($rand_array);//调用现成的数组随机排列函数
    return array_slice($rand_array,0,$limit);//截取前$limit个
}
print_r(NoRand());
?>