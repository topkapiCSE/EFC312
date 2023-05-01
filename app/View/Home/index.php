<!<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="<?php echo VIEW_URL; ?>Home/style.css">
</head>
<style>
    .countdowner{
        z-index: 1;
        align-self: baseline;
        background: #0ABF30;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded" , function (){
        var counter = document.getElementById("countdown");
        var timeleft = <?php echo Services::jwt()->getExpireTime();?>;
        counter.innerHTML = secondsToHms(timeleft);

        var downloadTimer = setInterval(function(){
            if(timeleft <= 0){
                window.location.href = "<?php echo BASE_URL;?>";
                clearInterval(downloadTimer);
            }
            counter.innerHTML = secondsToHms(timeleft);
            timeleft -= 1;
        }, 1000);
    });

    function secondsToHms(d) {
        d = Number(d);
        var m = Math.floor(d % 3600 / 60);
        var s = Math.floor(d % 3600 % 60);

        m = m<10 ? "0"+m:m;
        s = s<10 ? "0"+s:s;
        return m + ":" + s;
    }


</script>
<body>
<div class="countdowner">
    Oturumunuz <span id="countdown"></span> sonra sonlanacaktır.
</div>

<div class="card">
    <div class="card-face-1">
        <div class="info">
            <form style="margin: 15px" action="<?php echo BASE_URL;?>/Home/add" method="post">
                <input name="title" placeholder="title">
                <div class="divider"></div>
                <textarea name="value" rows="5" placeholder="Value"></textarea><br>
                <input type="submit" value="Kaydet">
            </form>
        </div>
    </div>
</div>

<br>


<?php
for($i=0;$i<count($data);$i++){

?>
    <div class="card">
        <a href="<?php echo BASE_URL;?>/Home/delete/<?php echo $data[$i]["id"];?>"><span>X</span></a>
        <div class="card-face-1">
            <div class="info">
                <h1 class="name"><?php echo $data[$i]["title"];?></h1>
                <div class="divider"></div>
                <p class=""><?php echo $data[$i]["note"];?></p>
            </div>
        </div>
    </div>
<?php
}
?>
</body>
</html>
