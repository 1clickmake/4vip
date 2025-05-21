<?php
include_once './_common.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $site_title = filter_input(INPUT_POST, 'site_title', FILTER_SANITIZE_SPECIAL_CHARS);
    $admin_email = filter_input(INPUT_POST, 'admin_email', FILTER_SANITIZE_EMAIL);
    $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_SPECIAL_CHARS);
	$ip_access = filter_input(INPUT_POST, 'ip_access', FILTER_SANITIZE_SPECIAL_CHARS);
	$ip_block = filter_input(INPUT_POST, 'ip_block', FILTER_SANITIZE_SPECIAL_CHARS);
	$recaptcha_site_key = filter_input(INPUT_POST, 'recaptcha_site_key', FILTER_SANITIZE_SPECIAL_CHARS);
	$recaptcha_secret_key = filter_input(INPUT_POST, 'recaptcha_secret_key', FILTER_SANITIZE_SPECIAL_CHARS);
   
	$DataToUpdate = [
		'site_title' => $site_title,
		'admin_email' => $admin_email,
		'contact_number' => $contact_number,
		'ip_access' => $ip_access,
		'ip_block' => $ip_block,
		'recaptcha_site_key' => $recaptcha_site_key,
		'recaptcha_secret_key' => $recaptcha_secret_key
	];
	// 업데이트 조건 where
	$where = [
		'id' => 1 
	];
	$data_update = process_data_update('cm_config', $DataToUpdate, $where); // $pdo 인자 없이 호출
	
	// 삽입 결과 확인
    if ($data_update !== false) {
        // 삽입 성공
		alert('업데이트 되었습니다.', './config_form.php');
        exit;
    } else {
        // 삽입 실패 
        echo "<script>alert('오류가 발생했습니다.'); history.back();</script>";
        exit;
    }
}