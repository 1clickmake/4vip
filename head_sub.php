<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
$cm_site_title = $config['site_title'];
if(isset($cm_title) && $cm_title !== ""){
	$cm_site_title .= "/".$cm_title;
}
?>
<!doctype html>
<html lang="<?php echo htmlspecialchars($lang->getCurrentLanguage()); ?>">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $cm_site_title;?></title>
		
		<script src="<?php echo CM_URL?>/js/cm.js"></script>
		<?php
		if (defined('CM_IS_ADMIN')) {
			include_once CM_ADMIN_PATH.'/add/add.stylesheet.php';
			// 관리자/CSS 폴더 내 모든 CSS 파일 로드
			$css_path = CM_ADMIN_PATH.'/css';
			if (is_dir($css_path)) {
				$css_files = glob($css_path.'/*.css');
				if ($css_files) {
					foreach ($css_files as $css_file) {
						$css_url = str_replace(CM_ADMIN_PATH, CM_ADMIN_URL, $css_file);
						echo '<link rel="stylesheet" href="'.$css_url.'?ver='.time().'">'."\n";
					}
				}
			}
		}else{
			include_once CM_TEMPLATE_PATH.'/add/add.stylesheet.php';
			// 템플릿/CSS 폴더 내 모든 CSS 파일 로드
			$css_path = CM_TEMPLATE_PATH.'/css';
			if (is_dir($css_path)) {
				$css_files = glob($css_path.'/*.css');
				if ($css_files) {
					foreach ($css_files as $css_file) {
						$css_url = str_replace(CM_TEMPLATE_PATH, CM_TEMPLATE_URL, $css_file);
						echo '<link rel="stylesheet" href="'.$css_url.'?ver='.time().'">'."\n";
					}
				}
			}
		}


		if (defined('CM_IS_ADMIN')) {
			include_once CM_ADMIN_PATH.'/add/add.script.head.php';
			// 관리자/JS 폴더 내 모든 JavaScript 파일 로드
			$js_path = CM_ADMIN_PATH.'/js';
			if (is_dir($js_path)) {
				$js_files = glob($js_path.'/*.js');
				if ($js_files) {
					foreach ($js_files as $js_file) {
						$js_url = str_replace(CM_ADMIN_PATH, CM_ADMIN_URL, $js_file);
						echo '<script src="'.$js_url.'?ver='.time().'"></script>'."\n";
					}
				}
			}
		}else{
			include_once CM_TEMPLATE_PATH.'/add/add.script.head.php';
			// 템플릿/JS 폴더 내 모든 JavaScript 파일 로드
			$js_path = CM_TEMPLATE_PATH.'/js';
			if (is_dir($js_path)) {
				$js_files = glob($js_path.'/*.js');
				if ($js_files) {
					foreach ($js_files as $js_file) {
						$js_url = str_replace(CM_TEMPLATE_PATH, CM_TEMPLATE_URL, $js_file);
						echo '<script src="'.$js_url.'?ver='.time().'"></script>'."\n";
					}
				}
			}
		}
		
		$fName = get_First_FolderName($current_url);
		?>
		<script src="<?php echo CM_URL?>/js/common.js"></script>

		<?php if($recaptcha_site && $recaptcha_secret && is_AllowedFolder()){ //구를 리캡챠?>

		<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $recaptcha_site;?>"></script>
		
	
		<?php } ?>
		
	</head>
	<body>
