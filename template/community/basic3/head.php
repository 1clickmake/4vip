<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
include_once CM_TEMPLATE_PATH.'/lib/menu.lib.php'; 
?>

<!-- Header -->
    <header class="header" id="header">
        <div class="header-content">
            <a href="<?php echo CM_URL?>" class="logo"><?php echo $config['site_title'];?></a>
            <nav class="mt-2">
				<?php ob_start(); ?>
				<?php if($is_member){?>
				<li><a href="<?php echo CM_MB_URL?>/mypage.php">마이페이지</a></li>
				<li><a href="<?php echo CM_MB_URL?>/register_form.php?w=update">정보수정</a></li>
				<li><a href="<?php echo CM_MB_URL?>/logout.php">logout</a></li>
				<?php if($is_admin){?>
				<a class="ms-3 text-danger" href="<?php echo CM_ADMIN_URL?>">관리자</a></li>
				<?php } ?>
				<?php } else { ?>
				<li><a href="<?php echo CM_MB_URL?>/login.php">로그인</a></li>
				<li><a href="<?php echo CM_MB_URL?>/register_form.php">회원가입</a></li>
				<?php } ?>
				<?php 
				$link_buttons = ob_get_contents();
				ob_end_clean();
				?>
                <?php generateMegaMenu(0, 1, 1, $link_buttons); ?>
            </nav>
        </div>
    </header>