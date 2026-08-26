<?php include("../include/common.php"); ?>
<html lang="zh-cn">

<head>
  <meta charset="UTF-8">
  <title>关于 - <?php echo explode("-", $conf['title'])[0]; ?></title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <link href="/assets/css/buble.css" type="text/css" rel="stylesheet">
  <style>
    :root {
      --about-theme: #0074d9;
      --about-theme: var(--theme-color, #0074d9);
    }

    html,
    body {
      height: 100%;
    }

    body {
      overflow: auto !important;
      background: linear-gradient(135deg, #f5f8fc 0%, #eaf1fb 100%);
      background-attachment: fixed;
      min-height: 100vh;
      margin: 0;
      padding: 0 0 48px;
      box-sizing: border-box;
      font-family: Source Sans Pro, Helvetica Neue, Arial, sans-serif;
    }

    body:not(.ready) {
      overflow: auto !important
    }

    .topbar {
      position: sticky;
      top: 0;
      z-index: 50;
      width: 100%;
      height: 60px;
      background: rgba(255, 255, 255, .85);
      backdrop-filter: saturate(180%) blur(10px);
      -webkit-backdrop-filter: saturate(180%) blur(10px);
      border-bottom: 1px solid rgba(0, 0, 0, .06);
      box-shadow: 0 2px 12px rgba(31, 45, 61, .04);
    }

    .topbar-inner {
      max-width: 860px;
      height: 100%;
      margin: 0 auto;
      padding: 0 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .topbar .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 1.1rem;
      font-weight: 600;
      color: #2c3e50;
      text-decoration: none;
    }

    .topbar .brand .logo {
      width: 30px;
      height: 30px;
      border-radius: 8px;
      background: var(--about-theme);
      color: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      font-weight: 700;
    }

    .topbar nav {
      display: flex;
      align-items: center;
      gap: 4px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .topbar nav li {
      list-style: none;
    }

    .topbar nav a,
    .topbar nav .nav-link {
      display: inline-block;
      padding: 8px 14px;
      border-radius: 8px;
      color: #5a6b7b;
      text-decoration: none;
      font-size: .92rem;
      transition: all .2s ease;
    }

    .topbar nav a:hover,
    .topbar nav .nav-link:hover {
      background: rgba(0, 116, 217, .08);
      color: var(--about-theme);
    }

    .topbar nav a.active {
      background: var(--about-theme);
      color: #fff;
      box-shadow: 0 4px 12px rgba(0, 116, 217, .25);
    }

    .topbar .brand .logo-img {
      width: 30px;
      height: 30px;
      border-radius: 8px;
      object-fit: cover;
    }

    .nav-toggle {
      display: none;
    }

    .nav-burger {
      display: none;
      width: 40px;
      height: 40px;
      border-radius: 8px;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      gap: 5px;
      cursor: pointer;
      transition: background .2s ease;
    }

    .nav-burger:hover {
      background: rgba(0, 116, 217, .08);
    }

    .nav-burger span {
      display: block;
      width: 20px;
      height: 2px;
      border-radius: 2px;
      background: #5a6b7b;
      transition: all .25s ease;
    }

    .nav-toggle:checked~.nav-burger span:nth-child(1) {
      transform: translateY(7px) rotate(45deg);
    }

    .nav-toggle:checked~.nav-burger span:nth-child(2) {
      opacity: 0;
    }

    .nav-toggle:checked~.nav-burger span:nth-child(3) {
      transform: translateY(-7px) rotate(-45deg);
    }

    .about-wrap {
      width: 100%;
      max-width: 860px;
      margin: 48px auto 0;
      padding: 0 16px;
      box-sizing: border-box;
      animation: about-fade-up .6s cubic-bezier(.22, .61, .36, 1) both;
    }

    @keyframes about-fade-up {
      from {
        opacity: 0;
        transform: translateY(24px)
      }

      to {
        opacity: 1;
        transform: translateY(0)
      }
    }

    .about-header {
      text-align: center;
      margin-bottom: 28px;
    }

    .about-header .badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 16px;
      border-radius: 999px;
      background: var(--about-theme);
      color: #fff;
      font-size: 13px;
      letter-spacing: .08em;
      box-shadow: 0 6px 18px rgba(0, 116, 217, .25);
    }

    .about-header h1 {
      margin: 16px 0 6px;
      font-size: 2rem;
      font-weight: 600;
      color: #2c3e50;
    }

    .about-header .subtitle {
      margin: 0;
      color: #7f8c8d;
      font-size: .95rem;
    }

    #main {
      max-width: 100%;
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 10px 40px rgba(31, 45, 61, .08);
      padding: 40px 44px;
      margin: 0;
      border: 1px solid rgba(0, 0, 0, .04);
    }

    #main>:first-child {
      margin-top: 0 !important
    }

    p.footer {
      margin-top: 48px;
      padding-top: 20px;
      border-top: 1px solid #eee;
      color: #95a5a6;
      font-size: .85rem;
    }

    p.footer a {
      text-decoration: none;
      color: var(--about-theme);
      transition: color .3s;
    }

    p.footer a:hover {
      color: #0056a3;
    }

    @media screen and (max-width:768px) {
      body {
        padding: 0 0 24px;
      }

      .topbar-inner {
        padding: 0 12px;
      }

      .nav-burger {
        display: flex;
      }

      .topbar nav {
        position: absolute;
        top: 60px;
        left: 0;
        right: 0;
        flex-direction: column;
        align-items: stretch;
        gap: 2px;
        background: rgba(255, 255, 255, .97);
        backdrop-filter: saturate(180%) blur(10px);
        -webkit-backdrop-filter: saturate(180%) blur(10px);
        border-bottom: 1px solid rgba(0, 0, 0, .06);
        box-shadow: 0 8px 24px rgba(31, 45, 61, .08);
        padding: 8px 12px;
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        visibility: hidden;
        transition: max-height .3s ease, opacity .25s ease, visibility .25s;
      }

      .nav-toggle:checked~nav {
        max-height: 70vh;
        overflow-y: auto;
        opacity: 1;
        visibility: visible;
      }

      .topbar nav a,
      .topbar nav .nav-link {
        display: block;
        padding: 12px 14px;
        font-size: .92rem;
        white-space: nowrap;
      }

      .about-wrap {
        margin-top: 24px;
        padding: 0 12px;
      }

      .about-header h1 {
        font-size: 1.5rem;
      }

      #main {
        border-radius: 0;
        border-left: none;
        border-right: none;
        padding: 28px 22px;
      }
    }
  </style>
</head>

<body>

  <header class="topbar">
    <div class="topbar-inner">
      <a class="brand" href="/">
        <?php if (!empty($conf['logo'])): ?>
          <?php $logo_src = $conf['logo']; if (strpos($logo_src, './') === 0) { $logo_src = '../' . substr($logo_src, 2); } ?>
          <img class="logo-img" src="<?php echo $logo_src; ?>" alt="logo">
        <?php else: ?>
          <span class="logo"><?php echo mb_substr(explode("-", $conf['title'])[0], 0, 1, 'UTF-8'); ?></span>
        <?php endif; ?>
        <span><?php echo explode("-", $conf['title'])[0]; ?></span>
      </a>
      <input type="checkbox" id="nav-toggle" class="nav-toggle">
      <label for="nav-toggle" class="nav-burger" aria-label="菜单">
        <span></span><span></span><span></span>
      </label>
      <nav>
        <li><a href="/">首页</a></li>
        <?php
			$tagslists = $site->getTags();
			while ($taglists = $DB->fetch($tagslists)) {
				$tag_link = $taglists["tag_link"];
				$about_active = in_array($tag_link, array('/about', '/about/', './about', './about/'), true) ? ' active' : '';
				echo '<li class="nav-item"><a class="nav-link' . $about_active . '" href="' . $tag_link . '"';
				if ($taglists["tag_target"] == 1) {
					echo ' target="_blank"';
				}
				echo '>' . $taglists["tag_name"] . '</a></li>
				    ';
			}
			?>
      </nav>
    </div>
  </header>

  <div class="about-wrap">
    <div class="about-header">
      <span class="badge">ABOUT</span>
      <h1>关于我们</h1>
      <p class="subtitle">了解 <?php echo explode("-", $conf['title'])[0]; ?> 的更多信息</p>
    </div>

    <div class="markdown-section" id="main">
      <?php
    echo($conf['about_content']);
?>
      <center>
        <p class="footer"><?php echo $conf['copyright'] ?></p>
      </center>
    </div>
  </div>
</body>

</html>
