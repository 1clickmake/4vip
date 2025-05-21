<?php
include_once './_common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // POST 방식이 아닌 접근은 허용하지 않습니다.
    alert('잘못된 접근 방식입니다.');
}

    // 폼에서 넘어온 데이터를 받습니다.
    $action = $_POST['action'] ?? '';
	$groupId = $_POST['group_id'] ?? '';
    $boardId = $_POST['board_id'] ?? '';
    $boardName = $_POST['board_name'] ?? '';
    $board_skin = $_POST['board_skin'] ?? '';
	$write_lv = $_POST['write_lv'] ?? 0;
	$list_lv = $_POST['list_lv'] ?? 0;
	$view_lv = $_POST['view_lv'] ?? 0;

    // 간단한 데이터 유효성 검사 (여기서는 기본적인 것만 하고, 더 상세히 할 수 있습니다)
    if (empty($groupId) || empty($boardId) || empty($boardName) || empty($board_skin)) {
        // 필수 값이 비어있으면 오류 처리
		alert('필수 입력 값이 누락되었습니다.');

    }

    if (!preg_match('/^[a-zA-Z0-9!@#$%^&*()_+=\-\[\]{};\':\\"\\|,.<>\/?~]*$/', $boardId)) {
         alert('게시판 아이디 형식이 올바르지 않습니다. 영문, 숫자, 기호만 사용 가능합니다.');
    }

	$sql = "SELECT * FROM `cm_board_group` WHERE `group_id` = :group_id";
	$params = [':group_id' => $groupId]; // ':이름' => 값 형태
	$gr = sql_fetch($sql, $params);
	
	if($action === "update"){
		
		$boardData = [
			'group_id' => $groupId,
			'group_name' => $gr['group_name'],
			'board_name' => $boardName,
			'board_skin' => $board_skin,
			'write_lv' => $write_lv,
			'list_lv' => $list_lv,
			'view_lv' => $view_lv
		];
		
		$whereConditions = [
            'board_id' => $boardId
        ];
		
		$updateResult = process_data_update('cm_board_list', $boardData, $whereConditions);
		
		// 결과 확인
		if ($updateResult !== false) {
			// 성공
			alert('게시판  수정이 완료되었습니다.', './board_list.php');
		} else {
			// 실패 (함수 내부에서 오류 로그는 남겼을 겁니다)
			alert('게시판 수정 중 오류가 발생했습니다.');
		}
		
	}else{
		// 배열의 키는 데이터베이스 테이블의 컬럼 이름과 같아야 합니다.
		$boardData = [
			'group_id' => $groupId,
			'group_name' => $gr['group_name'],
			'board_id' => $boardId,
			'board_name' => $boardName,
			'board_skin' => $board_skin,
			'write_lv' => $write_lv,
			'list_lv' => $list_lv,
			'view_lv' => $view_lv,
			// created_at 컬럼은 DB에서 CURRENT_TIMESTAMP로 자동 입력되므로 여기에 포함시키지 않아도 됩니다.
		];

		//  process_data_insert 함수를 호출하여 데이터 삽입 시도
		$insertResult = process_data_insert('cm_board_list', $boardData);

		// 결과 확인
		if ($insertResult !== false) {
			// 성공
			alert('게시판 생성이 완료되었습니다.', './board_list.php');
		} else {
			// 실패 (함수 내부에서 오류 로그는 남겼을 겁니다)
			alert('게시판 생성 중 오류가 발생했습니다.');
		}
	}


?>