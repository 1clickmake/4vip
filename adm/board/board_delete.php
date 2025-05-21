<?php
include_once './_common.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => '잘못된 접근 방식입니다.']);
    exit;
}

$boardId = $_POST['board_id'] ?? '';

if (empty($boardId)) {
    echo json_encode(['error' => '게시판 ID가 누락되었습니다.']);
    exit;
}

$deleteResult = process_data_delete('cm_board_list', ['board_id' => $boardId]);
if ($deleteResult !== false) {
		echo json_encode(['success' => true]);
}else{
	echo json_encode(['error' => '삭제 중 오류가 발생했습니다: ' . $e->getMessage()]);
}
?>