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
    $board_num = filter_input(INPUT_POST, 'board_num', FILTER_VALIDATE_INT);
    
    if (empty($board_id) || empty($board_num)) {
        alert('잘못된 접근입니다.');
    }
    
    // 게시글 정보 가져오기
    $write = sql_fetch("SELECT * FROM cm_board WHERE board_id = ? AND board_num = ?", 
                      [$board_id, $board_num]);
    
    if (!$write) {
        alert('존재하지 않는 게시글입니다.');
    }
    
    // 회원/비회원 게시글 권한 확인
    $is_user = false;
    
    if ($is_member) {
        // 회원인 경우, 작성자 member_id와 현재 로그인한 회원의 member_id 비교
        if ($write['user_id'] == $member['user_id']) {
            $is_user = true;
        } else if ($is_admin) { // 관리자인 경우도 수정 가능
            $is_user = true;
        }
    } else {
        // 비회원인 경우, 비밀번호 확인 필요
        if (!empty($_POST['password'])) {
            if (password_verify($_POST['password'], $write['password'])) {
                $is_user = true;
            }
        }
    }
    
    if (!$is_user) {
        alert('글을 수정할 권한이 없습니다.');
    }
    
    // 회원/비회원에 따른 필수 항목 검증
    if ($is_member) {
        // 회원인 경우 필수 항목
        $required_fields = ['board_id', 'board_num', 'user_id', 'title', 'content'];
    } else {
        // 비회원인 경우 필수 항목
        $required_fields = ['board_id', 'board_num', 'email', 'name', 'title', 'content'];
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
    } else {
        // 비회원인 경우 입력값 사용
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    }
    
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
    $content = $_POST['content']; // HTML 내용은 별도 보안 처리
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';
    $delete_files = isset($_POST['delete_files']) ? (array)$_POST['delete_files'] : [];

    try {
        // XSS 방지를 위한 HTML 퍼지 처리
        // 여기에 HTML Purifier 등의 라이브러리를 사용하는 코드를 추가
        
        // 트랜잭션 시작
        $pdo->beginTransaction();

        // 1. 게시글 데이터 업데이트
        $boardData = [
            'title' => $title,
            'content' => $content,
            'update_date' => date('Y-m-d H:i:s')
        ];
        
        if (!$is_member) {
            // 비회원인 경우 이메일, 이름 업데이트
            $boardData['email'] = $email;
            $boardData['name'] = $name;
            
            // 비밀번호가 입력된 경우에만 업데이트
            if (!empty($password)) {
                $boardData['password'] = $password;
            }
        }

        $whereConditions = [
            'board_id' => $board_id,
            'board_num' => $board_num
        ];

        if (!process_data_update('cm_board', $boardData, $whereConditions)) {
            throw new Exception("게시글 수정 실패");
        }

        // 2. 파일 삭제 처리
        if (!empty($delete_files)) {
            $upload_dir = CM_DATA_PATH.'/board/'.$board_id.'/';
            
            foreach ($delete_files as $file_id) {
                // 파일 정보 조회
                $file_info = sql_fetch("SELECT stored_filename FROM cm_board_file WHERE file_id = ? AND board_id = ? AND board_num = ?", 
                    [$file_id, $board_id, $board_num]);
                
                if ($file_info) {
                    // 실제 파일 삭제
                    $file_path = $upload_dir . $file_info['stored_filename'];
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                    
                    // DB에서 파일 정보 삭제
                    if (!process_file_delete('cm_board_file', ['file_id' => $file_id])) {
                        throw new Exception("파일 정보 삭제 실패");
                    }
                }
            }
        }

        // 3. 새 파일 업로드 처리
        if (!empty($_FILES['files']['name'][0])) {
            $upload_dir = CM_DATA_PATH.'/board/'.$board_id.'/';
            
            // 업로드 디렉토리 생성
            if (!file_exists($upload_dir) && !mkdir($upload_dir, 0777, true)) {
                throw new Exception("업로드 디렉토리 생성 실패");
            }

            foreach ($_FILES['files']['name'] as $i => $filename) {
                if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
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
                        unlink($upload_file_path);
                        throw new Exception("파일 정보 저장 실패");
                    }
                }
            }
        }

        // 트랜잭션 커밋
        $pdo->commit();
        
        // 성공 메시지와 함께 게시글 보기 페이지로 이동
        alert('게시글이 수정되었습니다.', get_board_url('view', $board_id, $board_num));


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