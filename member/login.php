<?php
include_once('./_common.php');

$cm_title = '로그인';
include_once(CM_PATH.'/head_sub.php');

//로그인 스킨
$action = CM_MB_URL."/login_check.php";
include_once(CM_TEMPLATE_PATH.'/skin/member_skin/login.skin.php');

include_once(CM_PATH.'/tail_sub.php');