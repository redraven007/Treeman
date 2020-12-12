<?php
    // TODO スコアランキング機能
    // TODO 棋譜提出ロジック実装
    // test commit
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>treeman</title>
    <script type="text/javascript" src="http://code.jquery.com/jquery-2.2.3.min.js"></script>
</head>
<body>
<style>
    .full-width {
        width:100%;
    }
    .full-height {
        height: 90vh;
    }
    .thin {
        width:19%;
        height:10%;
    }
    .thick {
        width:40%;
        height:10%;
    }
    .branch {
        background-color:#000000;
    }
    .treeman {
        background-color:#FF0000;
    }
    .time-gauge {
        width:100%;
    }
    .start {
        z-index: 999;
    }
</style>
<div class="center main" style="display: none; margin: auto;">
    <meter class="time-gauge" min="0" max="1000" value="1000" low="250" high="750"></meter>
    <table id="tree" class="full-width full-height">
        <tr>
            <td id="1-1" class="thick"></td>
            <td id="1-2" class="thin"></td>
            <td id="1-3" class="thick"></td>
        </tr>
        <tr>
            <td id="2-1" class="thick"></td>
            <td id="2-2" class="thin"></td>
            <td id="2-3" class="thick"></td>
        </tr>
        <tr>
            <td id="3-1" class="thick"></td>
            <td id="3-2" class="thin"></td>
            <td id="3-3" class="thick"></td>
        </tr>
        <tr>
            <td id="4-1" class="thick"></td>
            <td id="4-2" class="thin"></td>
            <td id="4-3" class="thick"></td>
        </tr>
        <tr>
            <td id="5-1" class="thick"></td>
            <td id="5-2" class="thin"></td>
            <td id="5-3" class="thick"></td>
        </tr>
        <tr>
            <td id="6-1" class="thick"></td>
            <td id="6-2" class="thin"></td>
            <td id="6-3" class="thick"></td>
        </tr>
    </table>
</div>
<div class="start full-height full-width" style="display: block;">
    <div style="margin: auto;">press Space to Start</div>
    <div id="highScore"></div>
</div>
<script>
    // TODO 画像の追加
    // cssにbackground-image適用で行けそう

    // TODO スマホ対応
    // touchstartにイベントリスナ追加
    // ダブルタップの動作吸収

    let score = -3;
    let timeReduction = 1;
    let timeGauge = $(".time-gauge");
    let startFLg = 0;
    let maxScore = 0;
    let timer;
    $(function () {
        // keydownにイベントリスナをセット
        // TODO スマホ対応するときはここに追記
        $(window).on('keydown', function(e) {
            if (e.keyCode === 37) {
                moveTreeman('left');
            } else if (e.keyCode === 39) {
                moveTreeman('right');
            } else if (e.keyCode === 32) {
                if (startFLg !== 1) startGame();
            }
        });
    })

    /**
     * treemanを移動
     * @param direction
     */
    function moveTreeman(direction) {
        if (direction === 'left') {
            if ($('#6-1').hasClass('treeman')) {
                proceed();
            } else {
                $('#6-3').removeClass('treeman');
                $('#6-1').addClass('treeman');
            }
        } else {
            if ($('#6-3').hasClass('treeman')) {
                proceed();
            } else {
                $('#6-1').removeClass('treeman');
                $('#6-3').addClass('treeman');
            }
        }
        judgement();
    }

    /**
     * treeman生存確認
     */
    function judgement () {
        if (($('#6-1').hasClass('treeman') && $('#6-1').hasClass('branch'))
         || ($('#6-3').hasClass('treeman') && $('#6-3').hasClass('branch'))
         || timeGauge.val() <= 0
        ) {
            alert("GAME OVER\r\nSCORE " + score);
            gameOver();
        }
    }

    /**
     * ゲームオーバー処理
     */
    function gameOver () {
        startFLg = 0;
        stopTimer();
        if (maxScore < score) {
            maxScore = score;
        }
        $('.start').show();
        $('.main').hide();
        $('#highScore').html("high score = " + maxScore);
    }

    /**
     * 進行処理
     */
    function proceed () {
        generateBranch();
        proceedBranch();
        score++;
        let leftTime = timeGauge.val();
        timeGauge.val(leftTime + 50);
    }

    /**
     * 左右どちらかにランダムに枝を生やすもしくは生やさない処理
     */
    function generateBranch () {
        switch (getRandomInt(2)) {
            case 0:
                if (!$('#2-3').hasClass('branch')) {
                    $('#1-1').addClass('branch');
                }
                break;
            case 1:
                if (!$('#2-1').hasClass('branch')) {
                    $('#1-3').addClass('branch');
                }
                break;
        }
    }

    /**
     * 枝を一本分進める処理
     */
    function proceedBranch() {
        for (let i = 6; i > 0; i--) {
            if (i === 6) {
                $('#' + i + '-1').removeClass('branch');
                $('#' + i + '-3').removeClass('branch');
            }
            if ($('#' + (i - 1) + '-1').hasClass('branch')) {
                $('#' + (i - 1) + '-1').removeClass('branch');
                $('#' + i + '-1').addClass('branch');
            } else if ($('#' + (i - 1) + '-3').hasClass('branch')) {
                $('#' + (i - 1) + '-3').removeClass('branch');
                $('#' + i + '-3').addClass('branch');
            }
        }
    }

    /**
     * 木の初期表示
     */
    function initializeTree () {
        $('#tree').find('.branch').each(function(){
            $(this).removeClass('branch');
        })
        proceed(); // 枝を3本分
        proceed(); // 進ませるための
        proceed(); // ダサい処理
        moveTreeman('right');
    }

    function getRandomInt(max) {
        return Math.floor(Math.random() * Math.floor(max));
    }

    /**
     * ゲーム開始時の処理
     */
    function startGame () {
        initializeTree();
        $('.start').hide();
        $('.main').show();
        startFLg = 1;
        startTimer();
    }

    function startTimer() {
        timer = setInterval(function(){
            timeReduction = timeReduction * 1.01;
            let leftTime = timeGauge.val();
            timeGauge.val(leftTime - timeReduction);
            judgement();
        } , 100);
    }

    function stopTimer() {
        clearInterval(timer);
    }

</script>
</body>
</html>
