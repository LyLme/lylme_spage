<?php
mysqli_query($con,'set names utf8');
$links = mysqli_query($con, "SELECT * FROM `lylme_links`");  // 获取网站
$groups = mysqli_query($con,"SELECT * FROM `lylme_groups`");  // 获取分类
$i = 0;
while($group = mysqli_fetch_assoc($groups)) { //循环所有分组
	echo '<ul class="mylist row"><li class="title">'.$group["group_icon"].'<sapn>'.$group["group_name"].'</sapn></li>';	//输出分组图标和标题
	while($link = mysqli_fetch_array($links)) {  // 循环每个链接
		$group_links = mysqli_query($con,"SELECT * FROM `lylme_links` WHERE `group_id` = ".$group["group_id"]);		// 返回指定分组下的所有字段
		$link_num=mysqli_num_rows($group_links);  // 获取返回字段条目数量
		if($link_num > $i ) {
			$i = $i+1;
			echo "\n".'<li class="col-3 col-sm-3 col-md-3 col-lg-1"><a rel="nofollow" href="'.$link["url"].'" target="_blank">';
			if ($link["icon"]=='') {echo '<img src="/assets/img/default-icon.png" alt="默认'.$link["name"].'" />';   }
       else  if (!preg_match("/^<svg*/", $link["icon"])) {
            echo '<img src="'.$link["icon"].'" alt="'.$link["name"].'" />'; }
        
        else{echo $link["icon"];}
			echo '<span>'.$link["name"].'</span></a></li>';
			//输出图标和链接
		}
		if($link_num == $i) {
			//判断当前分组链接循环完毕
			echo '</ul>'."\n" ;  //输出分类结束标签
			$i = 0;
			break; //重置$i为0跳出当前循环
		}
	}
}
mysqli_close($con);
?>