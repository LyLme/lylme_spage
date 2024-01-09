<?php include("../include/common.php"); ?>
<html lang="zh-cn">

<head>
  <meta charset="UTF-8">
  <title>关于 - <?php echo explode("-", $conf['title'])[0]; ?></title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <link href="https://lf9-cdn-tos.bytecdntp.com/cdn/expire-1-M/docsify/4.12.2/themes/vue.min.css" type="text/css" rel="stylesheet">
  <style>
    body:not(.ready) {
      overflow: auto !important
    }

    #main {
      max-width: 90%
    }

    p.footer {
      margin-top: 60px
    }

    p.footer a {
      text-decoration: none
    }
  </style>
</head>

<body>

  <div class="markdown-section" id="main">
    <?php
    echo($conf['about_content']);
?>
    <center>
      <p class="footer"><?php echo $conf['copyright'] ?></p>
    </center>
  </div>
</body>

</html>