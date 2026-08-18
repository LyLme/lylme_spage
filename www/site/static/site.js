
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
}

/* 汉堡菜单：点击展开/收起 */
function toggleDropdown(e) {
  if (e) e.stopPropagation();
  var dropdown = document.querySelector(".dropdown");
  if (dropdown) dropdown.classList.toggle("open");
}

document.addEventListener("click", function (e) {
  var dropdown = document.querySelector(".dropdown");
  if (!dropdown) return;
  // 点击菜单内部（菜单项）后收起
  var target = e.target;
  while (target && target !== document) {
    if (target.classList && target.classList.contains("dropdown-content")) {
      dropdown.classList.remove("open");
      return;
    }
    target = target.parentNode;
  }
  // 点击菜单外部区域收起
  if (!dropdown.contains(e.target)) {
    dropdown.classList.remove("open");
  }
});
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

/* 复制链接 */
function copyLink() {
  var urlEl = document.getElementById("meta-url");
  var url = urlEl ? urlEl.textContent.trim() : window.location.href;
  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard
      .writeText(url)
      .then(function () {
        showToast("链接已复制");
      })
      .catch(function () {
        copyFallback(url);
      });
  } else {
    copyFallback(url);
  }
}

function copyFallback(url) {
  var ta = document.createElement("textarea");
  ta.value = url;
  ta.style.position = "fixed";
  ta.style.opacity = "0";
  document.body.appendChild(ta);
  ta.select();
  try {
    document.execCommand("copy");
    showToast("链接已复制");
  } catch (e) {
    showToast("复制失败，请手动复制");
  }
  document.body.removeChild(ta);
}

function showToast(msg) {
  var toast = document.getElementById("toast-msg");
  if (!toast) {
    toast = document.createElement("div");
    toast.id = "toast-msg";
    toast.className = "toast-msg";
    document.body.appendChild(toast);
  }
  toast.textContent = msg;
  toast.classList.add("show");
  clearTimeout(toast._timer);
  toast._timer = setTimeout(function () {
    toast.classList.remove("show");
  }, 1800);
}
