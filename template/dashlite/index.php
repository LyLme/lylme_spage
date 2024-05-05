<html lang="zh">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $conf['title'] ?></title>
    <meta name="keywords" content="<?php echo $conf['keywords'] ?>" />
    <meta name="description" content="<?php echo $conf['description'] ?>" />
    <link rel="stylesheet" href="<?php echo $templatepath; ?>/css/dashlite.css">
    <link rel="stylesheet" href="<?php echo $templatepath; ?>/css/style.css?v=1002">
    <link rel="stylesheet" href="/assets/js/layer.css" id="layuicss-layer">
</head>

<body class="nk-body npc-invest bg-lighter no-touch nk-nio-theme">
    <!-- wrap @s -->
    <div class="nk-wrap ">
        <!-- main header @s -->
        <div class="nk-header nk-header-fluid nk-header-fixed is-light">
            <div class="container-xl">
                <div class="nk-header-wrap link-between">
                    <div class="nk-menu-trigger mr-sm-2 d-lg-none">
                        <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="headerNav"><em class="icon ni ni-menu"></em></a>
                    </div>
                    <div class="nk-header-brand">
                        <a href="/" class="logo-link text-base"><img src="<?php echo $conf['logo'] ?>" class="hide-mb-sm"><?php echo explode("-", $conf['title'])[0]; ?></a>
                    </div>
                    <!-- .nk-header-brand -->
                    <div class="nk-header-tools nk-header-menu mobile-menu" data-content="headerNav">
                        <div class="nk-header-mobile">
                            <div class="nk-header-brand">
                                <a href="/" class="logo-link text-base">
                                    <span class="nio-version" style="font-size: 21px;position: inherit;"><?php echo explode("-", $conf['title'])[0]; ?></span>
                                </a>
                            </div>
                            <div class="nk-menu-trigger mr-n2">
                                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="headerNav"><em class="icon ni ni-arrow-left"></em></a>
                            </div>
                        </div>
                        <!-- Menu -->
                        <ul class="nk-menu nk-menu-main">
                            <li class="nk-menu-item active">
                                <a href="/" class="nk-menu-link nk-ibx-action-item" data-original-title="" title="">
                                    <!-- <em class="icon ni ni-home"></em> -->
                                    <span class="nk-menu-text">首页</span>
                                </a>
                            </li>
                            <?php
                            //输出导航菜单
                            $tagslists = $site->getTags();
                            while ($taglists = $DB->fetch($tagslists)) {

                                echo '
                        <li class="nk-menu-item">
                            <a href="' . $taglists["tag_link"] . '" class="nk-menu-link nk-ibx-action-item" data-original-title="" title=""';
                                if ($taglists["tag_target"] == 1) {
                                    echo ' target="_blank"';
                                }
                                echo '>
                                <!-- <em class="icon ni ni-home"></em>-->
                                <span class="nk-menu-text">' . $taglists["tag_name"] . '</span>
                            </a>
                        </li>';
                            }
                            ?>
                            <li class="nk-menu-item">
                                <a href="/apply" class="nk-menu-link nk-ibx-action-item" data-original-title="" title="" target="_blank">
                                    <!-- <em class="icon ni ni-home"></em>-->
                                    <span class="nk-menu-text">申请收录</span>
                                </a>
                            </li>

                            <?php
                            $groups = $site->getGroups(); // 获取分类
                            if (checkmobile()) {
                                echo (' <hr>');
                                while ($group = $DB->fetch($groups)) { //循环所有分组
                                    echo ' <li class="nk-menu-item" data-id="' . $group["group_id"] . '"><a class="nk-menu-link nk-ibx-action-item" href="javascript:show_tool_list(' . $group["group_id"] . ')">' . $group["group_name"] . '</a></li>
                                        ' . "\n";
                                }
                            }
                            ?>

                        </ul>
                    </div>
                    <!-- .nk-header-menu -->
                    <div class="no-gutters">
                        <ul class="nk-quick-nav">
                            <li class="dropdown">
                                <a href="javascript:;" class="progress-rating dark-switch">
                                    <span class="nk-menu-icon">
                                        <em class="icon ni ni-moon"></em>
                                        <em class="icon ni ni-sun d-none"></em>
                                    </span>
                                </a>
                            </li>

                            <!-- .dropdown -->
                        </ul>
                        <!-- .nk-quick-nav -->
                    </div>
                    <!-- .nk-header-tools -->
                </div>
                <!-- .nk-header-wrap -->
            </div>
            <!-- .container-fliud -->
        </div>
        <!-- main header @e -->
        <!-- content @s -->

        <div class="nk-content nk-content-lg nk-content-fluid pt-5 pb-5 bannerbg">
            <div class="container-xl">
                <div class="form-control-wrap circle">
                    <div class="form-text-hint-lx" onclick="search()">
                        <span class="overline-title"><em class="icon ni ni-search"></em></span>
                    </div>
                    <form id="searchForm" action="#" method="get" target="_blank">
                        <input type="text" class="form-control form-control-lx btn-round" name="word" id="searchkw" placeholder="搜索" autocomplete="off">
                    </form>
                </div>
            </div>
        </div>

        <?php
        if (!checkmobile()) {
            echo '<!-- content @s -->
                <div class="nk-content nk-content-fluid p-0">
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner p-2">
                                <div class="container-xl">
                                    <nav>
                                        <ul class="breadcrumb breadcrumb-pipe"><li class="breadcrumb-item fs-16px category-all active"><a href="javascript:show_tool_list(0)">全部</a></li>';

            while ($group = $DB->fetch($groups)) { //循环所有分组
                echo ' <li class="breadcrumb-item fs-16px category-item" data-id="' . $group["group_id"] . '"><a href="javascript:show_tool_list(' . $group["group_id"] . ')">' . $group["group_name"] . '</a></li>
                            ' . "\n";
            }
            echo '  </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content @s -->';
        }
        ?>

        <div class="nk-content nk-content-lg nk-content-fluid">
            <div class="container-xl">
                <div class="nk-content-body">
                    <div id="toollist">
                        <?php require_once("list.php"); ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>

    <!-- content @d -->
    <div class="nk-footer nk-footer-fluid bg-lighter">
        <div class="container-xl">
            <div class="nk-footer-wrap">
                <?php if (!empty($conf['wztj'])) {
                    echo '<p>' . $conf["wztj"] . '</p>';
                }
                ?>
                <div class="nk-footer-copyright">
                    <?php if (!empty($conf['icp'])) {
                        echo '<p><img src="./assets/img/icp.png" width="16px" height="16px" /><a href="http://beian.miit.gov.cn/" rel="nofollow" class="icp nav-link" target="_blank" _mstmutation="1" _istranslated="1">' . $conf['icp'] . '</a></p>';
                    }
                    ?>
                    <?php echo $conf['copyright'] ?>
                </div>
            </div>
        </div>
    </div>
    <!-- footer @e -->
    </div>
    <!-- wrap @e -->
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/layer.js"></script>
    <script src="<?php echo $templatepath; ?>/js/nioapp.min.js"></script>
    <script src="<?php echo $templatepath; ?>/js/script.js?v=1001"></script>
    <script src="<?php echo $templatepath; ?>/js/common.js?v=1002"></script>
    <script src="<?php echo $cdnpublic ?>/assets/js/svg.js"></script>
    <?php
    ?>
    <script>
        $(document).ready(function() {
            $('#searchForm').submit(function(e) {
                e.preventDefault();
            });
        });
        function search() {
            var searchWord = $('#searchkw').val();
            if(!searchWord){
                return false;
            }
            var searchindex = '<?php echo theme_config('search', "https://cn.bing.com/search?q=")?>';
            if (searchindex.includes('%s')) {
                // 替换 %s  
                var searchUrl = searchindex.replace('%s', searchWord);
            } else {
                // 如果不包含 %s，则将搜索词添加到 URL 末尾  
                var searchUrl = searchindex + searchWord;
            }
            window.open(searchUrl);
        }
    </script>
    <script>
        var tool_list = <?php echo (json_encode(listjson())) ?>;
        var tools = [];
        var searchkw = '';

        function show_category_btn(catid) {
            if (catid == 0) {
                $("#link_content").show();
                $(".category-all").addClass("active");
                $(".category-item").removeClass("active");
            } else {
                $("#link_content").hide();
                $(".category-all").removeClass("active");
                $(".category-item").removeClass("active");
                $.each($(".category-item"), function(index, value) {
                    if ($(value).attr('data-id') == catid) {
                        $(value).addClass("active");
                    }
                })
            }
        }

        function show_tool_list(catid) {
            searchkw = '';
            $("#searchkw").val('');
            show_category_btn(catid);
            tools = [];
            var html = '';
            $.each(tool_list, function(index, value) {
                if (catid != 0 && value.id != catid) return;
                html += `
<div class="card card-preview category-card" data-category-id="${value.id}">
    <div class="card-inner mt-3">
        <div class="nya-title nk-ibx-action-item progress-rating">
            <!-- ${value.icon} -->
            <span class="nk-menu-text font-weight-bold">${value.title}</span>
        </div>
    <div class="row g-2">`;
                $.each(value.items, function(index, value) {
                    tools.push(value);
                    html += `
            <div class="col-lg-3 col-md-4 col-6">
                <a href="${value.url}" data-id="${value.id}" class="btn btn-wider btn-block btn-xl btn-outline-light tool-link" ${value.out?'target="_blank"':''}>${value.title}</a>
            </div>`
                })
                html += `
        </div>
    </div>
</div>`;
            });
            $("#toollist").html(html);
            bind_statistics();
        }

        function show_search_list() {
            var list = tools.filter(v => {
                return v.title.indexOf(searchkw) !== -1 || v.keyword != null && v.keyword.indexOf(searchkw) !== -1
            })
            var html = '';
            html += `
<div class="card card-preview category-card">
    <div class="card-inner mt-3">
        <div class="nya-title nk-ibx-action-item progress-rating">
            <em class="icon ni ni-search"></em>
            <span class="nk-menu-text font-weight-bold">搜索结果</span>
        </div>
    <div class="row g-2">`;
            if (list.length > 0) {
                $.each(list, function(index, value) {
                    html += `
            <div class="col-lg-3 col-md-4 col-6">
                <a href="${value.url}" data-id="${value.id}" class="btn btn-wider btn-block btn-xl btn-outline-light tool-link" ${value.out?'target="_blank"':''}>${value.title}</a>
            </div>`
                })
            } else {
                html += `<p class="search-placeholder">暂无搜索结果</p>`
            }
            html += `
        </div>
    </div>
</div>`;
            $("#toollist").html(html);
            bind_statistics();
            $("#link_content").hide();
            $(".category-all").removeClass("active");
            $(".category-item").removeClass("active");
        }

        function watch_searchkw(kw) {
            if (kw != searchkw) {
                searchkw = kw;
                if (searchkw == '') {
                    show_tool_list(0)
                } else {
                    show_search_list()
                }
            }
        }

        function bind_statistics() {
            $(".tool-link").click(function() {
                var id = $(this).attr('data-id');
                $.ajax({
                    type: "POST",
                    url: "/clitool",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    async: true,
                    success: function(data) {
                        console.log('statistics ok ' + id)
                    }
                });
            });
        }
        $(document).ready(function() {
            show_tool_list(0);
            $("#searchkw").on('input', function() {
                watch_searchkw($(this).val().trim().toLowerCase())
            });
            $("#searchkw").change(function() {
                watch_searchkw($(this).val().trim().toLowerCase())
            });
            $(document).keydown(function(event) {
                if (event.ctrlKey && event.keyCode == 70) {
                    $("#searchkw").focus();
                    return false;
                }
            });
        })
    </script>


</body>

</html>