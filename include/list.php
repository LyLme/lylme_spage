<?php
include 'common.php';
// 获取网站
$links = mysqli_query($con, "SELECT * FROM `lylme_links`");
//获取分类
$groups = mysqli_query($con,"SELECT * FROM `lylme_groups`");
$i = 0;
while($group = mysqli_fetch_assoc($groups)) {
	//输出分组图标和标题
	echo '<ul class="mylist row">
	<li class="title">'
				.$group["group_icon"]. 
				    '<sapn>
			'.$group["group_name"].
				    '</sapn>
	</li>';
	while($link = mysqli_fetch_array($links)) {
		// 循环每个链接
		$group_links = mysqli_query($con,"SELECT * FROM `lylme_links` WHERE `group_id` = ".$group["group_id"]);
		// 返回指定分组下的所有字段
		$link_num=mysqli_num_rows($group_links);
		// 释放结果集
		//echo gettype($fieldcount);
		if($link_num > $i ) {
			$i = $i+1;
			echo "\n\n".'<li class="col-3 col-sm-3 col-md-3 col-lg-1">
	<a rel="nofollow" href="'.$link["url"].'" target="_blank">
		'.$link["icon"].'
		<span>
			'.$link["name"].'
		</span>
	</a>
</li>';
		}
		if($link_num == $i) {
			echo '</ul>'."\n\n" ;
			#输出分类结束标签
						$i = 0;
			break;
			//跳出当前循环
		}
		//获取分类下的网站数量
	}
}
mysqli_close($con);
?>