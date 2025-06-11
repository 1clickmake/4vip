$(function() {
	// 맨 위로 스크롤
	$("#top_btn").on("click", function() {
		$("html, body").animate({scrollTop:0}, '500');
			return false;
		});

	// PWA 설치 버튼 클릭 이벤트
	$("#pwa-install-btn").on("click", function() {
		if (window.pwaInstaller && window.pwaInstaller.promptInstall) {
			window.pwaInstaller.promptInstall();
		}
	});

	// 스크롤 시 버튼 표시/숨김
	var lastScrollTop = 0;
	$(window).scroll(function() {
		var st = $(this).scrollTop();
		if (st > 300) {
			$("#top_btn, #pwa-install-btn").fadeIn(200);
		} else {
			$("#top_btn, #pwa-install-btn").fadeOut(200);
		}
		lastScrollTop = st;
	});

	// PWA 설치 가능 시 설치 버튼 표시
	if ('serviceWorker' in navigator && window.matchMedia('(display-mode: browser)').matches) {
		// 데스크톱에서는 10초 후에 설치 버튼 표시
		setTimeout(function() {
			$("#pwa-install-btn").fadeIn(300);
		}, 10000);
	}
});

var $loadingSpinner = $('#loadingSpinner').hide();
var $loadingOverlay = $('#loadingOverlay').hide();
$(document).ajaxStart(function () {
	$loadingSpinner.show();
	$loadingOverlay.show();
})
.ajaxStop(function () {
	$loadingSpinner.hide();
	$loadingOverlay.hide();
});
