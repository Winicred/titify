<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $config->name ?></title>
    <style>
        html {
            height: 100%;
            background: rgb(221, 221, 221) none repeat scroll 0% 0%;
        }

        h1 {
            margin-top: 20%;
            text-align: center;
            font-family: arial;
            font-size: 35px;
        }
    </style>
</head>
<body>
<script>
    let timetogo = <?php echo $config->ban_time ?>;
    let timer = window.setInterval(function () {
        let str = timetogo;
        document.getElementById('counter').innerHTML = str;

        if (timetogo <= 0) {
            document.getElementById('result').innerHTML = '<h1>Вы разблокированы!</h1>';
            window.clearInterval(timer);
        }
        timetogo--;
    }, 1000);
</script>
<div id="result">
    <h1>Вы заблокированы на <span id="counter"><?php echo $config->ban_time ?></span> сек. за флуд<h1>
</div>
</body>
</html>
<?php exit(); ?>