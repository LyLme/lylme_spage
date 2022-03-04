<?php if(basename($_SERVER['PHP_SELF']) == basename(__FILE__)) header("Location:/"); ?>
  <div style="display:none;" class="back-to" id="toolBackTop"> 
   <a title="返回顶部" onclick="window.scrollTo(0,0);return false;" href="#top" class="back-top"></a> 
  </div> 

  <div class="mt-5 mb-3 footer text-muted text-center"> 
  <!--备案信息-->
  <?php if($conf['icp'] != NULL){
  echo '<img src="./assets/img/icp.png" width="16px" height="16px" /><a href="http://beian.miit.gov.cn/" class="icp" target="_blank" _mstmutation="1" _istranslated="1">'.$conf['icp'].'</a>'; } ?> 
  <!--版权信息-->
  <p> <?php echo $conf['copyright']; ?></p>
  <!--网站统计-->
 <?php if($conf['wztj'] != NULL){echo $conf["wztj"];}?>
  </div>  
    <script>
!
function() {
	function g() {
		h(), i(), j(), k()
	}
	function h() {
		d.checked = s()
	}
	function i() {
		var a = document.querySelector('input[name="type"][value="' + p() + '"]');
		a && (a.checked = !0, l(a))
	}
	function j() {
		v(u())
	}
	function k() {
		w(t())
	}
	function l(a) {
		for (var b = 0; b < e.length; b++) e[b].classList.remove("s-current");
		a.parentNode.parentNode.parentNode.classList.add("s-current")
	}
	function m(a, b) {
		window.localStorage.setItem("superSearch" + a, b)
	}
	function n(a) {
		return window.localStorage.getItem("superSearch" + a)
	}
	function o(a) {
		f = a.target, v(u()), w(a.target.value), m("type", a.target.value), c.focus(), l(a.target)
	}
	function p() {
		var b = n("type");
		return b || a[0].value
	}
	function q(a) {
		m("newWindow", a.target.checked ? 1 : -1), x(a.target.checked)
	}
	function r(a) {
		return a.preventDefault(), "" == c.value ? (c.focus(), !1) : (w(t() + c.value), x(s()), s() ? window.open(b.action, +new Date) : location.href = b.action, void 0)
	}
	function s() {
		var a = n("newWindow");
		return a ? 1 == a : !0
	}
	function t() {
		return document.querySelector('input[name="type"]:checked').value
	}
	function u() {
		return document.querySelector('input[name="type"]:checked').getAttribute("data-placeholder")
	}
	function v(a) {
		c.setAttribute("placeholder", a)
	}
	function w(a) {
		b.action = a
	}
	function x(a) {
		a ? b.target = "_blank" : b.removeAttribute("target")
	}
	var y, a = document.querySelectorAll('input[name="type"]'),
		b = document.querySelector("#super-search-fm"),
		c = document.querySelector("#search-text"),
		d = document.querySelector("#set-search-blank"),
		e = document.querySelectorAll(".search-group"),
		f = a[0];
	for (g(), y = 0; y < a.length; y++) a[y].addEventListener("change", o);
	d.addEventListener("change", q), b.addEventListener("submit", r)
}();

    </script>

	<script>
			(function(a) {
				a.fn.scrollToTop = function(c) {
					var d = {
						speed: 800
					};
					c && a.extend(d, {
						speed: c
					});
					return this.each(function() {
						var b = a(this);
						a(window).scroll(function() {
							100 < a(this).scrollTop() ? b.fadeIn() : b.fadeOut()
						});
						b.click(function(b) {
							b.preventDefault();
							a("body, html").animate({
								scrollTop: 0
							},
							d.speed)
						})
					})
				}
			})(jQuery);
			$(function() {
				ahtml = '<a href="javascript:void(0)" title="回到顶部" id="toTop" style="display:none;position:fixed;bottom:66px;right:15px;width:50px;height:50px;border-radius:50%;overflow:hidden;background-image:url(\'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAWBAMAAADZWBo2AAAALVBMVEUAAAB5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl4rtNiAAAADnRSTlMARHe7Zu7dMxGIIqqZzHSj3DwAAAB/SURBVBjTYwADPgYk8OABgs2HLPUAjBA6+JAk4FJ8UJLqYLKxsQNTXhIDs/GWBoZCPcEFeop6CnyKvhMYGOQYGJIYmBL4BBgfgDjsrxi4nvMJsCSAOCChh3yHjjqAZV4wcDznO6TFANYTwsASwCfAAOFMFRCdAOd0v3vdAOIAANnHHKk0/kXuAAAAAElFTkSuQmCC\');background-repeat:no-repeat;background-position:center;z-index:999;cursor:pointer;border:1px solid #d8d8d8;box-sizing:border-box;opacity:0.9;"></a>';
				$("body").append(ahtml);
				$("#toTop").scrollToTop(300);
			});
		</script>
 </body>
</html>