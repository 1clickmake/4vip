<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
?>
		<div class="container-fluid">
		<header>
				<div class="container text-center">
					<a href="<?php echo CM_URL?>">home</a>
					<?php if($is_member){?>
					<a href="<?php echo CM_MB_URL?>/mypage.php">mypage</a>
					<a href="<?php echo CM_MB_URL?>/register_form.php?w=update">정보수정</a>
					<a href="<?php echo CM_MB_URL?>/logout.php">logout</a>
					<?php if($is_admin){?>
					<a href="<?php echo CM_ADMIN_URL?>">관리자</a>
					<?php } ?>
					<?php } else { ?>
					
					<a href="<?php echo CM_MB_URL?>/login.php">login</a>
					<a href="<?php echo CM_MB_URL?>/register_form.php">가입</a>
					<?php } ?>
				</div>
			</header>