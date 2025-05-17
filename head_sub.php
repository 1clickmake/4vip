<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
$cm_site_title = $config['site_title'];
if(isset($cm_title) && $cm_title !== ""){
	$cm_site_title .= "/".$cm_title;
}
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $cm_site_title;?></title>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
		<?php
		if (defined('CM_IS_ADMIN')) {
			include_once CM_ADMIN_PATH.'/add/add.stylesheet.php';
		}else{
			include_once CM_TEMPLATE_PATH.'/add/add.stylesheet.php';
		}
		?>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script><!-- Summernote JS -->
		<script src="https://www.google.com/recaptcha/api.js?render=6LdVZjwrAAAAAO5TocN0rSdbTWvi6_5xWNReljeE"></script>
		<?php
		if (defined('CM_IS_ADMIN')) {
			include_once CM_ADMIN_PATH.'/add/add.script.head.php';
		}else{
			include_once CM_TEMPLATE_PATH.'/add/add.script.head.php';
		}
		?>
	</head>
	<body>
