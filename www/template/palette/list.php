            <!-- 分类导航条 -->
            <nav class="category-nav">
                <div class="category-scroll">
                    <button type="button" class="category-item active" data-cat="0" aria-pressed="true">全部</button>
                    <?php
                    $groups = $site->getGroups();
                    while ($group = $DB->fetch($groups)) {
                        echo '<button type="button" class="category-item" data-cat="' . (int) $group["group_id"] . '" aria-pressed="false">' . theme_e($group["group_name"]) . '</button>';
                    }
                    ?>
                </div>
            </nav>

            <!-- 链接列表 -->
            <div class="links-container" id="linksContainer">
                <?php
                $html = array(
                    'g1' => '<div class="link-group" data-group-id="{group_id}">',
                    'g2' => '<div class="group-header"><span class="group-icon">{group_icon}</span><h3 class="group-title">{group_name}</h3></div><div class="group-links">',
                    'g3' => '</div></div>',
                    'l1' => '<div class="link-item-wrapper">',
                    'l2' => '<a href="{link_url}" title="{link_name_text}" target="_blank" rel="nofollow noopener" class="link-card"><span class="link-card-icon">{link_icon}</span><span class="link-card-name">{link_name}</span></a>',
                    'l3' => '</div>',
                );
                lists($html);
                ?>
            </div>
        </div>
    </div>
