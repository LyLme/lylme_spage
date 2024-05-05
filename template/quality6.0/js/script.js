$(document).ready(function () {
  var index_id = localStorage.getItem("index_id")
    ? localStorage.getItem("index_id")
    : 0;
  localStorage.setItem("index_id", index_id);
  sethint(index_id);
  $(".search-container").children().eq(index_id).show();
});
$(".lg").click(function () {
  var count = $(".search-container").children(".ss").length;
  var index = $(".lg").index(this);
  if (count <= index + 1) {
    var index = -1;
  }
  $(".search-container").children(".ss").hide();
  $(".search-container")
    .children()
    .eq(index + 1)
    .show();
  sethint(index + 1);
  localStorage.setItem("index_id", index + 1);
});
function sethint(id) {
  $(".soinput").attr("placeholder", solist()[id][1]);
}
function go(input_text) {
  var index_id = localStorage.getItem("index_id");
  var sourl = solist()[index_id][2];
  if (input_text == "") {
    var input_text = $(".soinput").val();
  }
  var url = sourl + input_text;
  if (navigator.userAgent.match(/mobile/i)) {
    window.location.href = url;
  } else {
    window.open(url, "_blank");
  }
}
$(document).ready(function () {
  var wid = $("body").width();
  if (wid < 640) {
  } else {
    $(".wd").focus();
  }
  $("#menu").click(function (event) {
    $(this).toggleClass("on");
    $(".list").toggleClass("closed");
    $(".mywth").toggleClass("hidden");
  });
  $("#content").click(function (event) {
    $(".on").removeClass("on");
    $(".list").addClass("closed");
    $(".mywth").removeClass("hidden");
    $("#word").hide();
  });
  $(".mywth").click(function (event) {
    var wt = $("body").width();
    if (wt < 750 || wt == 750) {
      window.location.href = "/weather/";
    }
  });
});
$(function () {
  $(".wd").keyup(function () {
    var keywords = $(this).val();
    if (keywords == "") {
      $("#word").hide();
      return;
    }
    $.ajax({
      url: "https://suggestion.baidu.com/su?wd=" + keywords,
      dataType: "jsonp",
      jsonp: "cb",
      beforeSend: function () {},
      success: function (data) {
        $("#word").empty().show();
        if (data.s == "") {
          $("#word").hide();
        }
        $.each(data.s, function () {
          $("#word").append(
            '<li><svg class="icon" style=" width: 15px; height: 15px; opacity: 0.5;" aria-hidden="true"><use xlink:href="#icon-sousuo"></use></svg> ' +
              this +
              "</li>",
          );
        });
      },
      error: function () {
        $("#word").empty().show();
        $("#word").hide();
      },
    });
  });
  $(document).on("click", "#word li", function () {
    var word = $(this).text();
    $(".wd").val(word);
    $("#word").hide();
    go(word);
  });
  $(".wd").keypress(function (event) {
    if (event.which === 13) {
      var word = $(".soinput").val();
      go(word);
    }
  });
});
$("#content").click(function (event) {
  $(".mywth").removeClass("hidden");
});

var breadcrumb = document.querySelector(".breadcrumb");
var stickyOffsetTop = breadcrumb.offsetTop;
window.addEventListener("scroll", function () {
  if (window.pageYOffset >= stickyOffsetTop) {
    breadcrumb.classList.add("fixed-top");
  } else {
    breadcrumb.classList.remove("fixed-top");
  }
});

document.addEventListener('DOMContentLoaded', function() {
    var tipText = document.querySelector('.tip-text');
    var hasSeenTip = localStorage.getItem('hasSeenTip');
    if (!hasSeenTip) {
        showTip(tipText);
        localStorage.setItem('hasSeenTip', true);
    } else {
        tipText.style.visibility = 'hidden';
        tipText.style.opacity = 0;
        tipText.style.bottom = '100%';
    }
});
function showTip(tipTextElement) {
    tipTextElement.style.visibility = 'visible';
    tipTextElement.style.opacity = 1;
    tipTextElement.style.bottom = 'calc(100% + 1px)';
    tipTextElement.style.transition = 'opacity 0.3s, bottom 0.3s';
    setTimeout(function() {
        tipTextElement.style.opacity = 0;
        tipTextElement.style.visibility = 'hidden';
        tipTextElement.style.bottom = '100%';
        tipTextElement.style.transition = 'opacity 0.3s, visibility 0s 0.3s, bottom 0.3s';
    }, 5000);
}