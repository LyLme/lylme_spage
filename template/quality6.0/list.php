        <nav>
            <ul class="breadcrumb breadcrumb-pipe">
                <li class="breadcrumb-item fs-16px category-all active"><a href="javascript:show_tool_list(0)">全部</a></li>
                <?php
                    $groups = $site->getGroups();
                    while ($group = $DB->fetch($groups)) {
                        echo ' <li class="breadcrumb-item fs-16px category-item" data-id="' . $group["group_id"] . '"><a href="javascript:show_tool_list(' . $group["group_id"] . ')">' . $group["group_name"] . '</a></li>
                        ' . "\n";
                    }
                ?>
            </ul>
        </nav>
        <div class="nk-content nk-content-lg nk-content-fluid">
            <div class="container-xl">
                <div class="nk-content-body">
                    <div id="toollist">
                        <?php
                        $html = array(
                            'g1' => '<div class="card card-preview category-card" data-category-id="{group_id}">',
                            'g2' => '<div class="card-inner mt-3"><div class="nya-title nk-ibx-action-item progress-rating"><em class="icon ni ni-setting"></em><span class="nk-menu-text font-weight-bold"><span class="tool-icon">{group_icon}</span>{group_name}</span></div><div class="row g-2">',
                            'g3' => '</div></div></div>',
                            'l1' => '<div class="col-lg-3 col-md-4 col-6">',
                            'l2' => '<a href="{link_url}" data-id="{link_id}" target="_blank" class="btn btn-wider btn-block btn-xl btn-outline-light tool-link"><span class="link-icon">{link_icon}</span>{link_name}</a>',
                            'l3' => '</div>',
                        );
                        lists($html);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <script>
        var tool_list = <?php echo(json_encode(listjson()))?>;
        var tools = [];
        var searchkw = '';
        function show_category_btn(catid){
            if(catid == 0){
                $("#link_content").show();
                $(".category-all").addClass("active");
                $(".category-item").removeClass("active");
            }else{
                $("#link_content").hide();
                $(".category-all").removeClass("active");
                $(".category-item").removeClass("active");
                $.each($(".category-item"), function(index,value){
                    if($(value).attr('data-id') == catid){
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
        ${value.icon && (value.icon.startsWith('<svg') ?
                `<span class="tool-icon">${value.icon}</span>` :
                `<span class="tool-icon"><img src="${value.icon}" alt="${value.title}"></span>`)}<span class="nk-menu-text font-weight-bold">${value.title}</span>
        </div>
            <div class="row g-2">`;
                $.each(value.items, function(index, item) {
                    tools.push(item);
                    html += `
                    <div class="col-lg-3 col-md-4 col-6">
                        <a href="site-${item.id}.html" data-id="${item.id}" class="btn btn-wider btn-block btn-xl btn-outline-light tool-link" ${item.out ? 'target="_blank"' : ''}>
        ${item.icon && item.icon.trim() ? (item.icon.startsWith('<svg') ?
            `<span class="link-icon">${item.icon}</span>` :
            `<span class="link-icon"><img src="${item.icon}"></span>`) :
            `<span class="link-icon"><img src="/assets/img/default-icon.png"></span>`}${item.title || ''}
                        </a>
                    </div>`;
                });
                html += `
            </div>
        </div>
</div>`;
    });
    $("#toollist").html(html);
    bind_statistics();
}
function bind_statistics(){
    $(".tool-link").click(function(){
	    var id = $(this).attr('data-id');
        $.ajax({
            type : "POST",
            url : "/clitool",
            data : {id: id},
            dataType : 'json',
            async: true,
            success : function(data) {
                console.log('statistics ok '+id)
            }
        });
    });
}
</script>