<?php
include_once('./_common.php');

$w = isset($_GET['w']) ? $_GET['w'] : '';

$update = false;
$cm_title = '회원가입';

if ($w == 'update') {
	$update = true;
    $cm_title = '회원정보 수정';
}

include_once(CM_PATH.'/head.php');

//회원가입/수정 스킨
$action = CM_MB_URL."/register_form_update.php";
include_once(CM_TEMPLATE_PATH.'/skin/member_skin/register_form.skin.php');

include_once(CM_PATH.'/tail.php'); 
?> 