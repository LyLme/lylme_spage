document.addEventListener("DOMContentLoaded", function () {
  var bgImg = document.getElementById("bg-img");
  var bgVideo = document.getElementById("bg-video");
  var bgNone = document.getElementById("bg-none");
  var htmlTag = document.querySelector("html");
  var bgKey = "bgSetting";
  function setBackgroundImage() {
    localStorage.setItem(bgKey, "image");
    htmlTag.style.backgroundImage = backgroundimg;
    htmlTag.style.backgroundRepeat = "no-repeat";
    htmlTag.style.backgroundPosition = "center";
    htmlTag.style.backgroundAttachment = "fixed";
    htmlTag.style.backgroundSize = "cover";
    removeVideoBackground();
  }
  function setBackgroundVideo() {
    localStorage.setItem(bgKey, "video");
    htmlTag.style.backgroundImage = "none";
    var video = document.createElement("video");
    video.src = background_video;
    video.loop = true;
    video.muted = true;
    video.autoplay = true;
    video.style.position = "fixed";
    video.style.top = 0;
    video.style.left = 0;
    video.style.width = "100%";
    video.style.height = "100%";
    video.style.zIndex = -999;
    video.style.objectFit = "cover";
    htmlTag.appendChild(video);
    removeImageBackground();
  }
  function clearBackground() {
    localStorage.setItem(bgKey, "none");
    removeImageBackground();
    removeVideoBackground();
  }
  function removeImageBackground() {
    htmlTag.style.backgroundImage = "none";
  }
  function removeVideoBackground() {
    var videos = document.querySelectorAll("video");
    videos.forEach(function (video) {
      video.parentNode.removeChild(video);
    });
  }
  var bgSetting = localStorage.getItem(bgKey);
  if (bgSetting === "image") {
    setBackgroundImage();
  } else if (bgSetting === "video") {
    setBackgroundVideo();
  }
  bgImg.addEventListener("click", setBackgroundImage);
  bgVideo.addEventListener("click", setBackgroundVideo);
  bgNone.addEventListener("click", clearBackground);
});
function toggleNightMode() {
  var html = document.documentElement;
  var nightMode = html.classList.contains("night-mode");
  html.classList.toggle("night-mode");
  localStorage.setItem("nightMode", !nightMode);
  var nightModeBtn = document.querySelector(".night-mode-btn");
  nightModeBtn.src = nightMode
    ? "/template/quality6.0/img/day_mode.svg"
    : "/template/quality6.0/img/night_mode.svg";
}
function updateTime() {
  const now = new Date();
  const hours = String(now.getHours()).padStart(2, "0");
  const minutes = String(now.getMinutes()).padStart(2, "0");
  const seconds = String(now.getSeconds()).padStart(2, "0");
  const time = `${hours}:${minutes}:${seconds}`;
  document.getElementById("time").textContent = time;
  const date = now.getDate();
  const month = now.getMonth() + 1;
  const year = now.getFullYear();
  const weekday = now.toLocaleString("zh-CN", { weekday: "long" });
  const monthYear = `${year}年${month}月`;
  document.getElementById("date").textContent = `${date}日`;
  document.getElementById("weekday").textContent = weekday;
  document.getElementById("monthYear").textContent = monthYear;
}
updateTime();
setInterval(updateTime, 1000);

var currentDate = new Date();
var hours = currentDate.getHours();
var reminderText;
if (hours < 12) {
  reminderText = "早上好！新的一天开始了，希望您今天过得愉快！";
} else if (hours < 18) {
  reminderText = "下午好！工作/学习辛苦了，记得休息一下哦！";
} else {
  reminderText = "晚上好！一天即将结束，祝您有个好梦！";
}
var reminderElement = document.getElementById("reminder");
reminderElement.textContent = reminderText;