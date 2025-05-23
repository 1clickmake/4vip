<?php
include_once './_common.php';
include_once CM_BOARD_PATH.'/board.lib.php';
include_once CM_PATH.'/head.php';

if ($boardNum <= 0) {
	alert('유효하지 않은 게시글입니다.');
}

//게시물 보기 접근 제한
if($bo['view_lv'] > 0 ){
	if(!$is_admin ){
		if($is_guest || $member['user_lv'] < $bo['view_lv']){
			alert('목록을 볼 권한이 없습니다.');
		}
	}
}

try {
    // 게시글 정보 가져오기
    $sql = "SELECT * FROM cm_board WHERE board_id = :board_id AND board_num = :board_num";
    $params = [
        ':board_id' => $boardId,
        ':board_num' => $boardNum
    ];
    
    $view = sql_fetch($sql, $params);
    
    if (!$view) {
        echo "<script>alert('존재하지 않는 게시글입니다.'); location.href='list.php?board={$boardId}';</script>";
        exit;
    }
	
	// 조회수 증가
	if($view['ip'] !== $ip){
		$update_data = ['view_count' => $view['view_count'] + 1];
		$where_conditions = [
			'board_id' => $boardId,
			'board_num' => $boardNum
		];
		process_data_update('cm_board', $update_data, $where_conditions);
	}
    
    // 첨부파일 목록 가져오기
    $file_sql = "SELECT * FROM cm_board_file 
                 WHERE board_id = :board_id AND board_num = :board_num 
                 ORDER BY file_id ASC";
    $file_params = [
        ':board_id' => $boardId,
        ':board_num' => $boardNum
    ];
    $files = sql_all_list($file_sql, $file_params);
    
} catch (PDOException $e) {
    echo "<script>alert('오류가 발생했습니다: " . $e->getMessage() . "'); history.back();</script>";
    exit;
}

//스킨경로
$bo_title = "게시글 보기";
include_once CM_TEMPLATE_PATH.'/skin/board_skin/'.$bo['board_skin'].'/view.skin.php';

include_once CM_PATH.'/tail.php';
?>