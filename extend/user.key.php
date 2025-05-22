<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가

// 구글 reCAPTCHA 검증
//$recaptcha_site = "6LdVZjwrAAAAAO5TocN0rSdbTWvi6_5xWNReljeE"; //사이트키
//$recaptcha_secret = "6LdVZjwrAAAAAJou2ysXMsENk7Da4ssfUWt79OGF"; //시크릿키
$recaptcha_site = $config['recaptcha_site_key'] ?? ''; //사이트키
$recaptcha_secret = $config['recaptcha_secret_key'] ?? ''; //시크릿키
$recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
