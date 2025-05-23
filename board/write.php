<?php
include_once './_common.php';
include_once CM_BOARD_PATH.'/board.lib.php';
include_once CM_PATH.'/head.php';
//게시물 글작성 접근 제한
if($bo['write_lv'] > 0 ){
	if(!$is_admin ){
		if($is_guest || $member['user_lv'] < $bo['write_lv']){
			alert('글쓰기 권한이 없습니다.');
		}
	}
}
//스킨경로
$bo_title = "게시글 작성";
$writeBtn = "등록";
$formAction = CM_BOARD_URL."/write_update.php";
include_once CM_TEMPLATE_PATH.'/skin/board_skin/'.$bo['board_skin'].'/write.skin.php';
include_once CM_PATH.'/tail.php';
?>