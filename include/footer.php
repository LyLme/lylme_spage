<!--
作者: D.Young  Github：https://github.com/5iux/5iux.github.io
二开: LyLme    Github：https://github.com/lylme/lylme_spage
-->
<div style="display:none;" class="back-to" id="toolBackTop"> 
	<a title="返回顶部" onclick="window.scrollTo(0,0);return false;" href="#top" class="back-top"></a> 
</div> 

<div class="mt-5 mb-3 footer text-muted text-center"> 
	<img src="./assets/img/icp.png" width="16px" height="16px" />
	<a href="http://beian.miit.gov.cn/" class="icp" target="_blank" _mstmutation="1" _istranslated="1">京ICP备xxxxxxxxxx号</a>  <!--备案信息-->
	<p id='copyright'> Copyright &copy;<?php echo date("Y");?> By <a href="/">LyLme</a>六零导航页 .</p>  <!--版权信息-->
</div> 

<script>
//切换搜索引擎
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
   


	</body>
</html>