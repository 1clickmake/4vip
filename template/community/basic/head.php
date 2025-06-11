<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
include_once CM_TEMPLATE_PATH.'/lib/menu.lib.php'; 
include_once(CM_TEMPLATE_PATH.'/sidemenu.php');
?>
<div class="fixed-top">
	<div class="container-fluid p-0 bg-light">
		<div class="container">
			<div class="py-2 text-end">
				<?php if($is_member){?>
				<a href="#">다운로드</a>
				<a class="ms-3" href="<?php echo CM_MB_URL?>/mypage.php">마이페이지</a>
				<a class="ms-3" href="<?php echo CM_MB_URL?>/register_form.php?w=update">정보수정</a>
				<a class="ms-3" href="<?php echo CM_MB_URL?>/logout.php">logout</a>
				<?php if($is_admin){?>
				<a class="ms-3 text-danger" href="<?php echo CM_ADMIN_URL?>">관리자</a>
				<?php } ?>
				<?php } else { ?>
				<a class="" href="<?php echo CM_MB_URL?>/login.php">login</a>
				<a class="ms-3"  href="<?php echo CM_MB_URL?>/register_form.php">회원가입</a>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="container-fluid p-0" style="background:#333;">
		<div class="container clearfix">
			<div class="float-start"><h1><a href="<?php echo CM_URL?>" class="text-white"><?php echo $config['site_title'];?></a></h1></div>
			<nav class="custom-navbar float-end">
				<?php generateMegaMenu(); ?>
			</nav>

			<script>
			$(document).ready(function() {
				// .dropdown 클래스를 가진 li 요소에 hover 이벤트 바인딩
				$('.custom-navbar ul li.dropdown').hover(
					function() {
						// 마우스가 요소 위로 올라갔을 때:
						$(this).children('ul').stop(true, true).slideDown(200); // 200ms 동안 펼쳐짐
					},
					function() {
						// 마우스가 요소에서 벗어났을 때:
						$(this).children('ul').stop(true, true).slideUp(200); // 200ms 동안 접힘
					}
				);
			});
			</script>
		</div>
	</div>
</div>