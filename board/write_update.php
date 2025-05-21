<?php
include_once './_common.php';

// POST 데이터 확인
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	if($recaptcha_site && $recaptcha_secret){
		// reCAPTCHA 검증
		if (empty($_POST['g-recaptcha-response'])) {
			alert('캡챠 인증이 필요합니다.');
		}

		$recaptcha_response = $_POST['g-recaptcha-response'];
		$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}");
		$captcha_success = json_decode($verify);

		if ($captcha_success->success == false) {
			alert('캡챠 인증에 실패했습니다.');
		}
	}

    // 필수 입력값 검증
    $board_id = filter_input(INPUT_POST, 'board_id', FILTER_SANITIZE_SPECIAL_CHARS);
    
    // 회원/비회원에 따른 필수 항목 검증
    if ($is_member) {
        // 회원인 경우 필수 항목
        $required_fields = ['board_id', 'user_id', 'title', 'content'];
    } else {
        // 비회원인 경우 필수 항목
        $required_fields = ['board_id', 'email', 'name', 'password', 'title', 'content'];
    }
    
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
    if ($is_member) {
        // 회원인 경우 회원 정보 사용
        $email = filter_var($member['user_email'], FILTER_SANITIZE_EMAIL);
        $name = filter_var($member['user_name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $user_id = $member['user_id'];
        
        // 회원 비밀번호는 이미 해시된 값으로 DB에 저장되어 있으므로 그대로 사용
        // 실제 비밀번호 값은 필요 없으며, 회원 인증은 세션으로 처리
        $password =  $member['user_password'];
    } else {
        // 비회원인 경우 입력값 사용
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $user_id = 0; // 비회원은 user_id = 0
        
        // 비밀번호 해시 처리
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
    $content = $_POST['content']; // HTML 내용은 별도 보안 처리
    $ip = $_SERVER['REMOTE_ADDR'];

    try {
        // 스팸 필터 처리
        include_once './write_update_spam_filter.php';
        
        // XSS 방지를 위한 HTML 퍼지 처리
        // 여기에 HTML Purifier 등의 라이브러리를 사용하는 코드를 추가
        
        // 트랜잭션 시작
        $pdo->beginTransaction();

        // 1. 게시글 데이터 insert
        $bo = get_board($board_id);
        
        $boardData = [
            'group_id' => $bo['group_id'],
            'board_id' => $board_id,
			'user_id' => $user_id,
            'email' => $email,
            'name' => $name,
            'title' => $title,
            'content' => $content,
            'ip' => $ip,
            'reg_date' => date('Y-m-d H:i:s')
        ];
        
        // 비회원인 경우에만 비밀번호 저장
        if ($is_member || !empty($password)) {
            $boardData['password'] = $password;
        }

        $board_num = process_data_insert('cm_board', $boardData);
        if ($board_num === false) {
            throw new Exception("게시글 등록 실패");
        }

        // 2. 파일 업로드 처리
        if (!empty($_FILES['files']['name'][0])) {
            $upload_dir = CM_DATA_PATH.'/board/'.$board_id.'/';
            
            // 업로드 디렉토리 생성
            if (!file_exists($upload_dir) && !mkdir($upload_dir, 0777, true)) {
                throw new Exception("업로드 디렉토리 생성 실패");
            }

            // 허용된 파일 확장자 목록
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'txt'];
            
            foreach ($_FILES['files']['name'] as $i => $filename) {
                if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) {
                    continue; // 오류가 있는 파일은 건너뜀
                }
                
                // 파일 확장자 검사
                $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if (!in_array($file_ext, $allowed_extensions)) {
                    throw new Exception("허용되지 않는 파일 형식입니다: " . $file_ext);
                }
                
                // 파일 크기 제한 (20MB)
                if ($_FILES['files']['size'][$i] > 20 * 1024 * 1024) {
                    throw new Exception("파일 크기가 너무 큽니다. 최대 20MB까지 허용됩니다.");
                }

                $tmp_name = $_FILES['files']['tmp_name'][$i];
                $stored_filename = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $file_ext;
                $upload_file_path = $upload_dir . $stored_filename;

                // 파일 업로드 및 악성코드 스캔 (있다면)
                if (function_exists('virus_scan')) {
                    if (!virus_scan($tmp_name)) {
                        throw new Exception("악성코드가 발견되었습니다.");
                    }
                }

                if (move_uploaded_file($tmp_name, $upload_file_path)) {
                    $fileData = [
                        'board_id' => $board_id,
                        'board_num' => $board_num,
                        'original_filename' => $filename,
                        'stored_filename' => $stored_filename,
                        'file_size' => $_FILES['files']['size'][$i],
                        'file_type' => $_FILES['files']['type'][$i],
                        'upload_date' => date('Y-m-d H:i:s')
                    ];

                    if (process_file_insert('cm_board_file', $fileData) === false) {
                        unlink($upload_file_path); // DB 저장 실패시 파일 삭제
                        throw new Exception("파일 정보 저장 실패");
                    }
                } else {
                    throw new Exception("파일 업로드 실패");
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
    header('Location: ' . get_board_url('list', $board_id));
    exit;
}