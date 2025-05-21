<?php
include_once './_common.php';
include_once CM_BOARD_PATH.'/board.lib.php';
$cm_title = $bo['board_name'];
include_once CM_PATH.'/head.php';

//게시물 리스트 접근 제한
if($bo['list_lv'] > 0 ){
	if(!$is_admin ){
		if($is_guest || $member['user_lv'] < $bo['list_lv']){
			alert('목록을 볼 권한이 없습니다.');
		}
	}
}

$result = sql_board_list( 'cm_board', [
    'page' => $_GET['page'] ?? 1,
    'per_page' => 10,
    'order_by' => 'board_num',
    'order_dir' => 'DESC',
    'debug' => false,
    'search' => [
		['field' => 'board_id', 'operator' => '=', 'value' => $boardId ?? ''],
        ['field' => 'title', 'operator' => 'LIKE', 'value' => $_GET['title'] ?? ''],
		['field' => 'content', 'operator' => 'LIKE', 'value' => $_GET['content'] ?? ''],
		['field' => 'name', 'operator' => 'LIKE', 'value' => $_GET['name'] ?? ''],
        //['field' => 'created_at', 'operator' => 'BETWEEN', 'value' => [$_GET['from'] ?? '', $_GET['to'] ?? '']],
        //['field' => 'board_type', 'operator' => 'IN', 'value' => explode(',', $_GET['types'] ?? '')],
    ]
]);

$rows = $result['list'];
$total_pages = $result['total_pages'];
$page = $result['current_page'];

//스킨경로
//$formAction = CM_BOARD_URL."/write_update.php";
include_once CM_BOARD_PATH.'/board_skin/basic/list.skin.php';

include_once CM_PATH.'/tail.php';
?>