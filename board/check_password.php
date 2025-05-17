<?php
include_once './_common.php';

// AJAX 요청 확인
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$boardId = isset($_POST['board_id']) ? $_POST['board_id'] : '';
    $boardNum = isset($_POST['board_num']) ? intval($_POST['board_num']) : 0;
    $input_password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if ($boardNum <= 0 || empty($input_password)) {
        echo 'error';
        exit;
    }

    
    try {
        // 게시글의 해시된 비밀번호 가져오기
        $stmt = $pdo->prepare("SELECT password FROM cm_board WHERE board_id = :board_id AND board_num = :board_num");
		$stmt->bindParam(':board_id', $boardId);
        $stmt->bindParam(':board_num', $boardNum);
        $stmt->execute();
        
        $result = $stmt->fetch();
        
        if (!$result) {
            echo 'error1';
            exit;
        }
        
        // 비밀번호 확인
        if (password_verify($input_password, $result['password'])) {
            echo 'success';
        } else {
            echo 'error2';
        }
        
    } catch (PDOException $e) {
        echo 'error3';
    }
} else {
    // 올바른 접근이 아닌 경우
    header('Location: list.php?board={$boardId}');
    exit;
}
?>