<?php
include_once './_common.php';

// POST 데이터 확인
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<script>alert("잘못된 접근입니다."); location.href="popup_list.php";</script>';
    exit;
}

// 모드 확인 (insert, update, delete)
$mode = isset($_POST['mode']) ? $_POST['mode'] : '';

if (!in_array($mode, ['insert', 'update', 'delete'])) {
    echo '<script>alert("잘못된 요청입니다."); location.href="popup_list.php";</script>';
    exit;
}

// 팝업 상태값 초기화
$po_use = isset($_POST['po_use']) ? 1 : 0;

try {
    // 삭제 처리
    if ($mode === 'delete') {
        if (!isset($_POST['po_id']) || empty($_POST['po_id'])) {
            echo '<script>alert("삭제할 팝업을 선택해주세요."); location.href="popup_list.php";</script>';
            exit;
        }
        
        $po_id = intval($_POST['po_id']);
		
		//에디터 이미지 삭제
		$editorDir = CM_DATA_PATH.'/popup';
		process_editor_image_delete('cm_popup', 'po_content', ['po_id' => $po_id], $editorDir);
		
		//데이터 삭제
		$deleteResult = process_data_delete('cm_popup', ['po_id' => $po_id]);
        
        if ($deleteResult !== false) {
			// 성공
			alert('팝업이 삭제되었습니다.', './popup_list.php');
		} else {
			// 실패 (함수 내부에서 오류 로그는 남겼을 겁니다)
			alert('팝업 삭제중 오류가 발생했습니다.');
		}
    }
    
    // 등록 또는 수정 처리
    // 필수 입력 데이터 검증
    if (!isset($_POST['po_title']) || empty($_POST['po_title'])) {
        echo '<script>alert("팝업 제목을 입력해주세요."); history.back();</script>';
        exit;
    }
    
    $po_title = trim($_POST['po_title']);
    $po_content = isset($_POST['po_content']) ? trim($_POST['po_content']) : '';
    $po_top = isset($_POST['po_top']) ? intval($_POST['po_top']) : 0;
    $po_left = isset($_POST['po_left']) ? intval($_POST['po_left']) : 0;
    $po_width = isset($_POST['po_width']) ? intval($_POST['po_width']) : 400;
    $po_height = isset($_POST['po_height']) ? intval($_POST['po_height']) : 400;
    $po_start_date = isset($_POST['po_start_date']) ? $_POST['po_start_date'] : date('Y-m-d');
    $po_end_date = isset($_POST['po_end_date']) ? $_POST['po_end_date'] : date('Y-m-d', strtotime('+7 days'));
    $po_cookie_time = isset($_POST['po_cookie_time']) ? intval($_POST['po_cookie_time']) : 24;
    $po_url = isset($_POST['po_url']) ? trim($_POST['po_url']) : '';
    $po_target = (isset($_POST['po_target']) && $_POST['po_target'] === '_self') ? '_self' : '_blank';
    
    // 시작일이 종료일보다 늦을 경우 체크
    if (strtotime($po_start_date) > strtotime($po_end_date)) {
        echo '<script>alert("종료일은 시작일보다 이후여야 합니다."); history.back();</script>';
        exit;
    }
    
    if ($mode === 'insert') {
        // 신규 등록
        $sql = "INSERT INTO cm_popup 
                (po_title, po_content, po_top, po_left, po_width, po_height, 
                po_start_date, po_end_date, po_cookie_time, po_url, po_target, po_use) 
                VALUES 
                (:po_title, :po_content, :po_top, :po_left, :po_width, :po_height, 
                :po_start_date, :po_end_date, :po_cookie_time, :po_url, :po_target, :po_use)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':po_title', $po_title);
        $stmt->bindParam(':po_content', $po_content);
        $stmt->bindParam(':po_top', $po_top, PDO::PARAM_INT);
        $stmt->bindParam(':po_left', $po_left, PDO::PARAM_INT);
        $stmt->bindParam(':po_width', $po_width, PDO::PARAM_INT);
        $stmt->bindParam(':po_height', $po_height, PDO::PARAM_INT);
        $stmt->bindParam(':po_start_date', $po_start_date);
        $stmt->bindParam(':po_end_date', $po_end_date);
        $stmt->bindParam(':po_cookie_time', $po_cookie_time, PDO::PARAM_INT);
        $stmt->bindParam(':po_url', $po_url);
        $stmt->bindParam(':po_target', $po_target);
        $stmt->bindParam(':po_use', $po_use, PDO::PARAM_INT);
        
        $stmt->execute();
        
        echo '<script>alert("팝업이 등록되었습니다."); location.href="popup_list.php";</script>';
		
    } else if ($mode === 'update') {
        // 수정
        if (!isset($_POST['po_id']) || empty($_POST['po_id'])) {
            echo '<script>alert("수정할 팝업을 선택해주세요."); location.href="popup_list.php";</script>';
            exit;
        }
        
        $po_id = intval($_POST['po_id']);
        
        $sql = "UPDATE cm_popup SET 
                po_title = :po_title, 
                po_content = :po_content,
                po_top = :po_top,
                po_left = :po_left,
                po_width = :po_width,
                po_height = :po_height,
                po_start_date = :po_start_date,
                po_end_date = :po_end_date,
                po_cookie_time = :po_cookie_time,
                po_url = :po_url,
                po_target = :po_target,
                po_use = :po_use
                WHERE po_id = :po_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':po_title', $po_title);
        $stmt->bindParam(':po_content', $po_content);
        $stmt->bindParam(':po_top', $po_top, PDO::PARAM_INT);
        $stmt->bindParam(':po_left', $po_left, PDO::PARAM_INT);
        $stmt->bindParam(':po_width', $po_width, PDO::PARAM_INT);
        $stmt->bindParam(':po_height', $po_height, PDO::PARAM_INT);
        $stmt->bindParam(':po_start_date', $po_start_date);
        $stmt->bindParam(':po_end_date', $po_end_date);
        $stmt->bindParam(':po_cookie_time', $po_cookie_time, PDO::PARAM_INT);
        $stmt->bindParam(':po_url', $po_url);
        $stmt->bindParam(':po_target', $po_target);
        $stmt->bindParam(':po_use', $po_use, PDO::PARAM_INT);
        $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
        
        $stmt->execute();
        
		alert('팝업이 수정되었습니다.', 'popup_form.php?po_id='.$po_id);
    }

} catch (PDOException $e) {
    echo '<script>alert("오류가 발생했습니다: ' . $e->getMessage() . '"); history.back();</script>';
    exit;
}
?>