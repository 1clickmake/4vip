<?php
include_once './_common.php';
include_once CM_BOARD_PATH.'/board.lib.php';
include_once CM_PATH.'/head.php';

if ($boardNum <= 0) {
    echo "<script>alert('유효하지 않은 게시글입니다.'); history.back();</script>";
    exit;
}

try {
    // 게시글 정보 가져오기
    $stmt = $pdo->prepare("SELECT * FROM cm_board WHERE board_id = :board_id AND board_num = :board_num");
	$stmt->bindParam(':board_id', $boardId);
    $stmt->bindParam(':board_num', $boardNum);
    $stmt->execute();
    
    $write = $stmt->fetch();
    
    if (!$write) {
        echo "<script>alert('존재하지 않는 게시글입니다.'); location.href='list.php?board={$boardId}';</script>";
        exit;
    }
    
    // 첨부파일 목록 가져오기
    $stmt = $pdo->prepare("
        SELECT * FROM cm_board_file
        WHERE board_id = :board_id AND board_num = :board_num
        ORDER BY file_id ASC
    ");
	$stmt->bindParam(':board_id', $boardId);
    $stmt->bindParam(':board_num', $boardNum);
    $stmt->execute();
    
    $files = $stmt->fetchAll();
    
} catch (PDOException $e) {
    echo "<script>alert('오류가 발생했습니다: " . $e->getMessage() . "'); history.back();</script>";
    exit;
}


//스킨경로
$bo_title = "게시글 수정";
$writeBtn = "수정";
$formAction = CM_BOARD_URL."/edit_update.php";
include_once CM_BOARD_PATH.'/board_skin/basic/write.skin.php';

include_once CM_PATH.'/tail.php';
?>