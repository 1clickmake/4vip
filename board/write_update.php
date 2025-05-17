<?php
include_once './_common.php';


// POST 데이터 확인
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
    // reCAPTCHA 검증
	if (empty($recaptcha_response)) {
        alert('캡챠 인증이 필요합니다.');
    }

    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}");
    $captcha_success = json_decode($verify);

    if ($captcha_success->success == false) {
        alert('캡챠 인증에 실패했습니다.');
    }
	// reCAPTCHA 검증 끝

    // 필수 입력값 검증
    $required_fields = ['board_id', 'email', 'name', 'password', 'title', 'content'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        alert('다음 필수 항목을 입력해 주세요: ' . implode(', ', $missing_fields));
    }

    // 입력값 필터링
    $board_id = filter_input(INPUT_POST, 'board_id', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
    $content = $_POST['content'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $ip = $_SERVER['REMOTE_ADDR'];

    try {
		
		include_once './write_update_spam_filter.php';
		
        // 트랜잭션 시작
        $pdo->beginTransaction();

        // 1. 게시글 데이터 insert
		$bo = get_board($board_id);
		
        $boardData = [
			'group_id' => $bo['group_id'],
            'board_id' => $board_id,
            'email' => $email,
            'name' => $name,
            'password' => $password,
            'title' => $title,
            'content' => $content,
            'ip' => $ip
        ];

        $board_num = process_data_insert('cm_board', $boardData);
        if ($board_num === false) {
            throw new Exception("게시글 등록 실패1");
        }

        // 2. 파일 업로드 처리
        if (!empty($_FILES['files']['name'][0])) {
            $upload_dir = CM_DATA_PATH.'/board/'.$board_id.'/';
            
            // 업로드 디렉토리 생성
            if (!file_exists($upload_dir) && !mkdir($upload_dir, 0777, true)) {
                throw new Exception("업로드 디렉토리 생성 실패");
            }

            foreach ($_FILES['files']['name'] as $i => $filename) {
                if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) {
                    continue; // 오류가 있는 파일은 건너뜀
                }

                $tmp_name = $_FILES['files']['tmp_name'][$i];
                $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
                $stored_filename = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $file_ext;
                $upload_file_path = $upload_dir . $stored_filename;

                if (move_uploaded_file($tmp_name, $upload_file_path)) {
                    $fileData = [
                        'board_id' => $board_id,
                        'board_num' => $board_num,
                        'original_filename' => $filename,
                        'stored_filename' => $stored_filename,
                        'file_size' => $_FILES['files']['size'][$i],
                        'file_type' => $_FILES['files']['type'][$i]
                    ];

                    if (process_file_insert('cm_board_file', $fileData) === false) {
                        unlink($upload_file_path); // DB 저장 실패시 파일 삭제
                        throw new Exception("파일 정보 저장 실패");
                    }
                }
            }
        }

        // 트랜잭션 커밋
        $pdo->commit();
        
        // 성공 메시지와 함께 게시글 보기 페이지로 이동
        alert('게시글이 등록되었습니다.', get_board_url('view', $board_id, $board_num));

    } catch (Exception $e) {
        // 오류 발생시 롤백
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        alert($e->getMessage());
    }
} else {
    // POST 요청이 아닌 경우 목록으로 리다이렉트
    header('Location: list.php');
    exit;
}