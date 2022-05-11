//请求页面
function listTable(query){
	var url = window.document.location.href.toString();
	var queryString = url.split("?")[1];
	query = query || queryString;
	layer.closeAll();
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'GET',
		url : 'table_link.php?'+query,
		dataType : 'html',
		cache : false,
		success : function(data) {
			layer.close(ii);
			$("#listTable").html(data);
			$("#link").dragsort({
			    dragBetween: true,
				dragSelector: "tr", 
				dragEnd: showbutton, 
				placeHolderTemplate: "<tr></tr>",
			});
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}

//载入页面
$(document).ready(function(){
	if($("#listTable").length>0){
		listTable()
	}
});


//获取选中   
function get_check(){
	 var chk_value =[]; 
	$('input[name="link-check"]:checked').each(function(){ 
		chk_value.push($(this).val()); 
	}); 
	return  chk_value;
} 
 
//点击排序
function sort(id,order,gid){
    lightyear.loading('show');
    $.ajax({
        url:"ajax_link.php?submit=order",
        method:"POST",
        data:{id:id,order:order,gid:gid},
        success:function(data){
            console.log(data);
            lightyear.loading('hide');
            lightyear.notify('操作成功！', 'success', 1000);
            listTable();
            return true;
        },
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
    });
}

function on_link(){
   //多选启用
	if(get_check().length == 0){
		$.alert("未选择链接");
		return false;
	}
   lightyear.loading('show');
    $.ajax({
        url:"ajax_link.php?submit=on",
        method:"POST",
        data:{links:get_check()},
        success:function(data){
            console.log(data);
            lightyear.loading('hide');
            lightyear.notify('操作成功！', 'success', 1000);
            listTable();
            return true;
        },
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
    });
} 
function off_link(){
   //多选禁用
	if(get_check().length == 0){
		$.alert("未选择链接");
		return false;
	}
   lightyear.loading('show');
    $.ajax({
        url:"ajax_link.php?submit=off",
        method:"POST",
        data:{links:get_check()},
        success:function(data){
            console.log(data);
            lightyear.loading('hide');
            lightyear.notify('操作成功！', 'success', 1000);
            listTable();
            return true;
        },
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
    });
}

function del_link(id){
   //多选删除
   var link_id = [];
   link_id.push(id);
   link_id = id ? link_id :get_check();
   console.log(link_id);
	if(link_id.length == 0){
		$.alert("未选择链接");
		return false;
	}
    $.alert({
        title: '警告',
        content: '确定要删除吗？删除后不可恢复',
		buttons: {
			confirm: {
				text: '删除',
				btnClass: 'btn-danger',
				action: function(){
                   lightyear.loading('show');
                    $.ajax({
                        url:"ajax_link.php?submit=del",
                        method:"POST",
                        data:{
                            links:link_id
                            },
                        success:function(data){
                        console.log(data);
                        lightyear.loading('hide');
                        lightyear.notify('操作成功！', 'success', 1000);
                        listTable();
                        return true;
                        }
                    });
                    
				},
        		error:function(data){
        			layer.msg('服务器错误');
        			return false;
        		}
			},
            cancel: {
                text: '取消'
            }
		}
    }); 
} 

//全选
function check_all(){
     var ischecked = $("#check_all").prop('checked');
     if(ischecked == true){
       $('[name="link-check"]').prop('checked',true);
     }else{
      $('[name="link-check"]').prop('checked',false);
     }
}

//拖拽排序
$(document).ready(function(){  
	$("#link").dragsort({ itemSelector: "tr", 
	 dragEnd: showbutton, 
	dragBetween: true, dragSelector: "tr",placeHolderTemplate: "<tr></tr>" });  
});  

//显示保存
function showbutton() {  
	$("#save_order").show();
}

//保存拖拽排序
function save_order(){
    var link_array =[]; 
    var $inputArr = $('input[name="link-check"]');
    $inputArr.each(function(){
        link_array.push($(this).val());
    });
    
    lightyear.loading('show');
    $.ajax({
        url:"ajax_link.php?submit=allorder",
        method:"POST",
        data:{link_array:link_array},
        success:function(data){
            console.log(data);
            lightyear.loading('hide');
            lightyear.notify('操作成功！', 'success', 1000);
            listTable();
            return true;
        },
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
    });
}