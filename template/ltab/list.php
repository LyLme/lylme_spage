<?php
// ltab 风格链接列表渲染
$rel = $conf["mode"] == 2 ? '' : 'rel="nofollow"';

$groups = $DB->query("SELECT * FROM `lylme_groups` ORDER BY `group_order` ASC");
$sessionList = isset($_SESSION['list']) ? $_SESSION['list'] : [];

if (!function_exists('ltabRenderIcon')) {
    function ltabRenderIcon($icon, $alt = '') {
        if (empty($icon)) {
            return '<img src="/assets/img/default-icon.png" alt="' . htmlspecialchars(strip_tags($alt), ENT_QUOTES, 'UTF-8') . '" loading="lazy">';
        }
        $icon = htmlspecialchars_decode($icon);
        $trimmed = trim($icon);
        // 如果已经是svg代码，直接输出
        if (preg_match('/^<svg/i', $trimmed)) {
            return $icon;
        }
        // 如果是纯URL（不含HTML标签），用img包裹
        if (!preg_match('/</', $trimmed)) {
            return '<img src="' . htmlspecialchars($trimmed, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars(strip_tags($alt), ENT_QUOTES, 'UTF-8') . '" loading="lazy">';
        }
        // 其他HTML代码直接输出
        return $icon;
    }
}

while ($group = $DB->fetch($groups)) {
    if (isset($group["group_status"]) && $group["group_status"] == '0') {
        continue;
    }
    $groupPwd = isset($group['group_pwd']) ? $group['group_pwd'] : '';
    if (!empty($groupPwd) && !in_array((int)$groupPwd, $sessionList, true)) {
        continue;
    }
    
    $groupId = isset($group['group_id']) ? (int)$group['group_id'] : 0;
    $group_links = $DB->query("SELECT * FROM `lylme_links` WHERE `group_id` = " . $groupId . " ORDER BY `link_order` ASC;");
    $link_num = $DB->num_rows($group_links);
    if ($link_num == 0) continue;
    
    $groupIcon = isset($group['group_icon']) ? $group['group_icon'] : '';
    $groupName = isset($group['group_name']) ? $group['group_name'] : '';
    ?>
    <div class="ltab-group" id="group_<?php echo $groupId; ?>">
        <div class="group-title">
            <?php if($groupIcon) echo '<span class="group-icon">' . ltabRenderIcon($groupIcon, $groupName) . '</span>'; ?>
            <span><?php echo htmlspecialchars($groupName); ?></span>
        </div>
        <div class="ltab-grid">
            <?php
            $i = 0;
            while ($link = $DB->fetch($group_links)) {
                $linkPwd = isset($link['link_pwd']) ? $link['link_pwd'] : '';
                $lpwd = true;
                if (empty($groupPwd) && !empty($linkPwd) && !in_array((int)$linkPwd, $sessionList, true)) {
                    $lpwd = false;
                }
                $linkStatus = isset($link['link_status']) ? $link['link_status'] : 1;
                if (!$linkStatus || !$lpwd) continue;
                
                $linkName = isset($link['name']) ? $link['name'] : '';
                $linkUrl = isset($link['url']) ? $link['url'] : '';
                $linkIcon = isset($link['icon']) ? $link['icon'] : '';
                $linkNameText = $linkName;
                if (mb_strlen($linkName, 'utf-8') > 8) {
                    $linkName = mb_substr($linkName, 0, 8, 'utf-8') . '...';
                }
            ?>
            <a <?php echo $rel; ?> href="<?php echo htmlspecialchars($linkUrl); ?>" target="_blank" class="ltab-card" title="<?php echo htmlspecialchars($linkNameText); ?>">
                <div class="card-icon">
                    <?php echo ltabRenderIcon($linkIcon, $linkNameText); ?>
                </div>
                <div class="card-name"><?php echo htmlspecialchars($linkName); ?></div>
            </a>
            <?php } ?>
        </div>
    </div>
    <?php
}
