$(function(){
    $('.button').click(function(){
        var result = Math.floor(Math.random() * 17); // 抽奖结果
        alert("恭喜你，抽中" + result);
        $('.result').text("恭喜你，抽中" + result);
    });
})