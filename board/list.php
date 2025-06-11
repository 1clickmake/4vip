<?php
include_once './_common.php';
include_once CM_BOARD_PATH.'/board.lib.php';
$cm_title = $bo['board_name'];
include_once CM_PATH.'/head.php';

// 게시물 리스트 접근 제한
if ($bo['list_lv'] > 0) {
    if (!$is_admin) {
        if ($is_guest || $member['user_lv'] < $bo['list_lv']) {
            alert('목록을 볼 권한이 없습니다.');
        }
    }
}

//공지글 출력
$sqlNot = "SELECT * FROM cm_board WHERE board_id = :board_id AND reply_depth = '0' AND notice_chk = '1' ORDER BY board_num DESC";
$params_notice = [ // 변수명 충돌 방지
	':board_id' => $boardId
];
$notice_posts = sql_all_list($sqlNot, $params_notice);

// 검색 조건 설정
$search_conditions = [
    ['field' => 'board_id', 'operator' => '=', 'value' => $boardId ?? ''],
	['field' => 'notice_chk', 'operator' => '<>', 'value' => '1']
];

// 검색어가 있는 경우에만 검색 조건 추가
if (!empty($_GET['search_keyword'])) {
    $search_field = $_GET['search_field'] ?? 'title';
    
    switch ($search_field) {
        case 'title':
            $search_conditions[] = ['field' => 'title', 'operator' => 'LIKE', 'value' => $_GET['search_keyword']];
            break;
        case 'content':
            $search_conditions[] = ['field' => 'content', 'operator' => 'LIKE', 'value' => $_GET['search_keyword']];
            break;
        case 'name':
            $search_conditions[] = ['field' => 'name', 'operator' => 'LIKE', 'value' => $_GET['search_keyword']];
            break;
        case 'title_content':
            $search_conditions[] = ['field' => 'title', 'operator' => 'LIKE', 'value' => $_GET['search_keyword']];
            $search_conditions[] = ['field' => 'content', 'operator' => 'LIKE', 'value' => $_GET['search_keyword']];
            break;
    }
}

$result = sql_board_list('cm_board', [
    'page' => $_GET['page'] ?? 1,
    'per_page' => 30,
    'order_by' => 'thread_id',
    'order_dir' => 'DESC',
    'debug' => false,
    'search' => $search_conditions
]);

$rows = $result['list'];
$total_pages = $result['total_pages'];
$page = $result['current_page'];
$total_rows = $result['total_rows'];

// 목록 번호 계산
$start_number = $total_rows - ($page - 1) * 10;

// 검색 조건이 있는지 확인
$has_search = !empty($_GET['search_keyword']);

include_once CM_TEMPLATE_PATH.'/skin/board_skin/'.$bo['board_skin'].'/list.skin.php';
include_once CM_PATH.'/tail.php';
?>