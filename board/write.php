<?php
include_once './_common.php';
include_once CM_BOARD_PATH.'/board.lib.php';
include_once CM_PATH.'/head.php';

//스킨경로
$bo_title = "게시글 작성";
$writeBtn = "등록";
$formAction = CM_BOARD_URL."/write_update.php";
include_once CM_BOARD_PATH.'/board_skin/basic/write.skin.php';

include_once CM_PATH.'/tail.php';
?>