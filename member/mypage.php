<?php
include_once('./_common.php');

if(!$is_member){
	alert('회원만 접근 가능합니다.');
}
$cm_title = '마이페이지';
include_once(CM_PATH.'/head.php');

//마이페이지 스킨
include_once(CM_TEMPLATE_PATH.'/skin/member_skin/mypage.skin.php');

include_once(CM_PATH.'/tail.php');