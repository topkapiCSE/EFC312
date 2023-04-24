<!<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="<?php echo VIEW_URL; ?>Home/style.css">
</head>
<body>
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
