
document.addEventListener("DOMContentLoaded", function () {
  var lazyImages = [].slice.call(document.querySelectorAll("img.lazyload"));
  if ("IntersectionObserver" in window) {
    let lazyImageObserver = new IntersectionObserver(function (entries, observer) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          let lazyImage = entry.target;
          lazyImage.src = lazyImage.dataset.src;
          lazyImage.classList.remove("lazyload");
          lazyImage.classList.add("lazyloaded");
          lazyImageObserver.unobserve(lazyImage);
        }
      });
    });
    lazyImages.forEach(function (lazyImage) {
      lazyImageObserver.observe(lazyImage);
    });
  } else {
    lazyImages.forEach(function (lazyImage) {
      lazyImage.src = lazyImage.dataset.src;
      lazyImage.classList.remove("lazyload");
      lazyImage.classList.add("lazyloaded");
    });
  }
});
function toggleNightMode() {
  var html = document.documentElement;
  var nightMode = html.classList.contains("night-mode");
  html.classList.toggle("night-mode");
  localStorage.setItem("nightMode", !nightMode);
  var nightModeBtn = document.querySelector(".night-mode-btn");
  nightModeBtn.src = nightMode
    ? "/site/static/light_mode.svg"
    : "/site/static/night_mode.svg";
}
function updateTime() {
  const now = new Date();
  const hours = String(now.getHours()).padStart(2, "0");
  const minutes = String(now.getMinutes()).padStart(2, "0");
  const seconds = String(now.getSeconds()).padStart(2, "0");
  const time = `${hours}:${minutes}<span>:${seconds}</span>`;
  document.getElementById("time").innerHTML = time;
  const day = now.getDate();
  const month = now.getMonth() + 1;
  const year = now.getFullYear();
  const weekday = now.toLocaleString("zh-CN", { weekday: "long" });
  const date = `${year}年${month}月${day}日`;
  document.getElementById("date").textContent = date;
  document.getElementById("weekday").textContent = weekday;
}
updateTime();
setInterval(updateTime, 1000);

function convertToSpan(html) {
  // 提取关键词列表
  var keywords = html.innerText.split(',');

  // 构建包含关键词的 span 标签
  var spanHtml = '';
  for (var i = 0; i < keywords.length; i++) {
    spanHtml += '<span>' + keywords[i].trim() + '</span>';
  }

  // 替换原始 HTML 中的关键词部分
  html.innerHTML = spanHtml;
}

// 使用示例
var element = document.getElementById("site_keyword");
convertToSpan(element);
