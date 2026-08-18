<script language="javascript">

function loginout(){
$.confirm({
    title: '提示',
    content: '你确定要退出吗？',
    buttons: {
        confirm: {
            text: '确定',
            btnClass: 'btn-primary',
            action: function(){ window.parent.location.href="login.php?logout"; }
        },
        cancel: { text: '取消' }
    }
});
}
</script>
<script type="text/javascript" src="/assets/admin/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="/assets/admin/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="/assets/admin/js/main.min.js"></script>
</body>
</html>
<?php $DB->close(); ?>