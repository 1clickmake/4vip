<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
include_once(CM_TEMPLATE_PATH.'/sidemenu.php');
?>

<nav class="navbar navbar-expand-lg navbar-white bg-white fixed-top border-bottom">
  <div class="container">
    <a class="navbar-brand" href="<?php echo CM_URL?>"><?php echo strtoupper($config['site_title']);?></a>
    
    <!-- offcanvas를 열어주는 버튼 -->
    <a href="#" class="navbar-toggler border-0" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
        <i class="bi bi-list fs-3 text-info"></i>
    </a>
    
    <!-- Offcanvas 영역 -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#"><?php echo get_lang('MENU_DOWNLOAD', '다운로드'); ?></a>
          </li>
          
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Member
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <?php if($is_member){?>
					<li><a class="dropdown-item" href="<?php echo CM_MB_URL?>/mypage.php"><?php echo get_lang('MEMBER_MYPAGE', '마이페이지'); ?></a></li>
					<li><a class="dropdown-item" href="<?php echo CM_MB_URL?>/register_form.php?w=update">정보수정</a></li>
					<li><a class="dropdown-item" href="<?php echo CM_MB_URL?>/logout.php">logout</a></li>
					<?php if($is_admin){?>
					<li><a class="dropdown-item" href="<?php echo CM_ADMIN_URL?>">관리자</a></li>
					<?php } ?>
					<?php } else { ?>
					<li><a class="dropdown-item" href="<?php echo CM_MB_URL?>/login.php">login</a></li>
					<li><a class="dropdown-item"  href="<?php echo CM_MB_URL?>/register_form.php">회원가입</a></li>
			<?php } ?>
            </ul>
          </li>
		  
		  <li class="nav-item">
            <div id="language-selector" class="nav-link p-0">
                <form method="GET" action="" style="display: inline;">
                    <select name="lang" id="lang-select" class="form-select form-select-sm" onchange="this.form.submit()">
                        <?php
						$currentSelectedLang = $lang->getCurrentLanguage();
						$availableDisplayLangs = ['en' => 'English', 'ko' => '한국어']; // 언어 선택지에 표시될 이름
						foreach ($lang->getAvailableLanguages() as $langCode) {
							$selected = ($langCode === $currentSelectedLang) ? 'selected' : '';
							$displayName = $availableDisplayLangs[$langCode] ?? strtoupper($langCode);
							echo "<option value=\"{$langCode}\" {$selected}>{$displayName}</option>";
						}
						// 다른 GET 파라미터 유지 (필요한 경우)
						foreach ($_GET as $key => $value) {
							if ($key !== 'lang') {
								echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
							}
						}
						?>
                    </select>
                </form>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>

