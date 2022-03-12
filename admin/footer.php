<script language="javascript">
function loginout(){
if( confirm("你确实要退出吗？")){
window.parent.location.href="login.php?logout";
}
else{
return;
}
}
</script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="js/main.min.js"></script>
</body>
</html>
<?php $DB->close(); ?>