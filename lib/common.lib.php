<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 직접 접근 방지

/**
 * 모바일 기기 접속 여부를 확인합니다.
 * 
 * @return bool 모바일 기기 접속 시 true, PC 접속 시 false 반환
 */
function is_mobile(): bool {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // 모바일 기기 패턴
    $mobile_patterns = [
        'iPhone', 'iPod', 'iPad', 'Android', 'webOS', 'BlackBerry',
        'IEMobile', 'Opera Mini', 'Mobile', 'Mobile Safari',
        'Windows Phone', 'Symbian', 'Nokia', 'SonyEricsson',
        'LG', 'Samsung', 'HTC', 'Motorola', 'Nexus'
    ];
    
    // 모바일 기기 패턴 확인
    foreach ($mobile_patterns as $pattern) {
        if (stripos($user_agent, $pattern) !== false) {
            return true;
        }
    }
    
    // 모바일 브라우저 헤더 확인
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || 
        isset($_SERVER['HTTP_PROFILE']) || 
        (isset($_SERVER['HTTP_ACCEPT']) && 
         strpos($_SERVER['HTTP_ACCEPT'], 'text/vnd.wap.wml') !== false)) {
        return true;
    }
    
    return false;
}

// 변수 또는 배열의 이름과 값을 얻어냄. print_r() 함수의 변형
function print_r2($var)
{
    ob_start();
    print_r($var);
    $str = ob_get_contents();
    ob_end_clean();
    $str = str_replace(" ", "&nbsp;", $str);
    echo nl2br("<span style='font-family:Tahoma, 굴림; font-size:9pt;'>$str</span>");
}

/**
 * 현재 실행 중인 파일의 이름을 반환합니다.
 *
 * @return string 현재 파일명 (확장자 제외), 실패 시 빈 문자열 반환
 */
function get_current_filename(): string {
    $urlPath = $_SERVER['PHP_SELF'];
    return !empty($urlPath) ? pathinfo(basename($urlPath), PATHINFO_FILENAME) : '';
}

// 휴대폰번호의 숫자만 취한 후 중간에 하이픈(-)을 넣는다.
function get_hyphen_hp_number(string $hp): string
{
    $hp = preg_replace("/[^0-9]/", "", $hp);
    return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $hp);
}

/**
 * 문자열에서 숫자만 추출하여 반환하는 함수
 */
function get_only_number(string $str): string
{
    return preg_replace('/[^0-9]/', '', $str);
}

function get_formatDate($date, $format = 'Y-m-d H:i:s') {
    $dateTime = new DateTime($date);
    
    // 한글 요일 매핑 배열 (0: 일요일, 1: 월요일 ... 6: 토요일)
    $koreanWeekdays = ['일', '월', '화', '수', '목', '금', '토'];
    $dayOfWeek = (int)$dateTime->format('w'); // 0~6 숫자로 요일 추출
    $koreanWeekday = $koreanWeekdays[$dayOfWeek];
    
    // 형식 문자열에 "K"가 포함된 경우 한글 요일로 치환
    $format = str_replace('K', $koreanWeekday, $format);
    
    return $dateTime->format($format);
}


/**
 * 특정 디렉토리 안의 폴더 목록을 반환하는 함수
 *
 * @param string $directory 폴더 목록을 읽을 디렉토리 경로
 * @return array|false 폴더 목록 (배열) 또는 오류 발생 시 false
 */
function getSubdirectories(string $directory): array|false
{
    if (!is_dir($directory)) {
        error_log("디렉토리 '$directory'가 존재하지 않습니다.");
        return false;
    }

    $subdirectories = [];
    $items = scandir($directory);

    if ($items === false) {
        error_log("디렉토리 '$directory'를 읽는 데 실패했습니다.");
        return false;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $path = $directory . '/' . $item;
        if (is_dir($path)) {
            $subdirectories[] = $item;
        }
    }

    return $subdirectories;
}




/**
 * 게시판 URL을 생성합니다.
 *
 * @param string $fileName  URL을 생성할 PHP 파일명 (예: view, write)
 * @param string $board     게시판 이름
 * @param string $board_id  (선택 사항) 특정 게시물 ID
 * @return string 생성된 게시판 URL, board 또는 fileName이 비어있으면 CM_URL 반환
 */
function get_board_url(string $fileName, string $board_id, string $board_num = ''): string {
    if (empty($board_id) || empty($fileName)) {
        return CM_URL;
    }
	
	// $board_num 숫자가 아닌 경우 CM_URL 반환
    if (!empty($board_num) && !ctype_digit($board_num)) {
        return CM_URL;
    }

    $href = CM_BOARD_URL . "/" . $fileName . ".php?board=" . $board_id;
    if (!empty($board_num)) {
        $href .= "&id=" . $board_num;
    }

    return $href;
}

//게시판정보
function get_board(string $board_id){
	if (!empty($board_id)) {
		$sql = "SELECT * FROM `cm_board_list` WHERE `board_id` = :board_id";
		$params = [
			':board_id' => $board_id
		];
		return sql_fetch($sql, $params);
	}else{
		return false;
	}
}

//회원정보
function get_member(string $user_id){
	if (!empty($board_id)) {
		$sql = "SELECT * FROM `cm_users` WHERE `user_id` = :user_id";
		$params = [
			':user_id' => $user_id
		];
		return sql_fetch($sql, $params);
	}else{
		return false;
	}
}


//포인트 지급, 차감
function get_UserPoint($user_id, $point, $description, $action) {
	
	global $pdo;
	
    if (empty($user_id) || $point <= 0 || empty($description) || !in_array($action, ['add', 'cut'])) {
        throw new Exception("잘못된 입력입니다. 모든 필드를 확인해주세요.");
    }

    try {
        // 트랜잭션 시작
        $pdo->beginTransaction();

        // 회원 존재 확인
        $stmt = $pdo->prepare("SELECT user_point FROM cm_users WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new Exception("존재하지 않는 회원 아이디입니다.");
        }

        if ($action === 'add') {
            // 포인트 지급: cm_point에 내역 추가 및 cm_users의 user_point 증가
            $stmt = $pdo->prepare("INSERT INTO cm_point (user_id, point, description) VALUES (:user_id, :point, :description)");
            $stmt->execute([
                'user_id' => $user_id,
                'point' => $point,
                'description' => $description
            ]);

            $stmt = $pdo->prepare("UPDATE cm_users SET user_point = user_point + :point WHERE user_id = :user_id");
            $stmt->execute([
                'point' => $point,
                'user_id' => $user_id
            ]);
        } elseif ($action === 'cut') {
            // 포인트 차감: cm_point에서 해당 포인트 내역 삭제
            $stmt = $pdo->prepare("DELETE FROM cm_point WHERE user_id = :user_id AND point = :point AND description = :description");
            $stmt->execute([
                'user_id' => $user_id,
                'point' => $point,
                'description' => $description
            ]);
			
			$stmt = $pdo->prepare("UPDATE cm_users SET user_point = user_point - :point WHERE user_id = :user_id");
            $stmt->execute([
                'point' => $point,
                'user_id' => $user_id
            ]);

            // 삭제된 행이 없으면 예외 발생
            if ($stmt->rowCount() === 0) {
                throw new Exception("삭제할 포인트 내역이 없습니다.");
            }
        }

        // 트랜잭션 커밋
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        // 오류 발생 시 롤백
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * 알림 메시지를 표시하고 페이지를 이동합니다.
 *
 * @param string      $message      표시할 알림 메시지
 * @param string|null $redirect_url (선택 사항) 이동할 URL, null이면 이전 페이지로 이동
 * @return void
 */
function alert(string $message, string $redirect_url = null): void {
    $escaped_message = json_encode($message);

    echo '<script>';
    echo 'alert(' . $escaped_message . ');';

    if (!empty($redirect_url)) {
        echo 'window.location.href = ' . json_encode($redirect_url) . ';';
    } else {
        echo 'history.back();';
    }

    echo '</script>';
    exit;
}



/**
 * 데이터를 데이터베이스 테이블에 삽입합니다.
 *
 * @param string $tableName 삽입할 테이블 이름
 * @param array  $data      삽입할 데이터 (연관 배열, 컬럼명 => 값)
 * @return string|false 삽입된 레코드의 ID, 실패 시 false 반환
 */
function process_data_insert(string $tableName, array $data): string|false {
    global $pdo;

    if (empty($data)) {
        error_log("process_data_insert: 삽입할 데이터가 비어 있습니다.");
        return false;
    }

    $columns = array_keys($data);
    $placeholders = implode(', ', array_map(fn($col) => ":" . $col, $columns));
    $columnSql = implode(', ', array_map(fn($col) => "`" . str_replace("`", "``", $col) . "`", $columns));

    $sql = "INSERT INTO `" . str_replace("`", "``", $tableName) . "` ({$columnSql}) VALUES ({$placeholders})";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("process_data_insert 오류 ({$tableName}): " . $e->getMessage());
        return false;
    }
}

/**
 * 파일 메타데이터를 삽입합니다.
 *
 * @param string $tableName 삽입할 테이블 이름
 * @param array  $data      삽입할 데이터 (연관 배열)
 * @return string|false 삽입된 레코드의 ID, 실패 시 false 반환
 */
function process_file_insert(string $tableName, array $data): string|false {
    global $pdo;

    if (empty($data)) {
        error_log("process_file_insert: 삽입할 데이터가 비어 있습니다.");
        return false;
    }

    try {
        // 데이터베이스 연결 확인
        if (!$pdo) {
            error_log("process_file_insert: 데이터베이스 연결이 설정되지 않았습니다.");
            return false;
        }

        // SQL 쿼리 생성
        $columns = array_keys($data);
        $placeholders = implode(', ', array_map(fn($col) => ":" . $col, $columns));
        $columnSql = implode(', ', array_map(fn($col) => "`" . str_replace("`", "``", $col) . "`", $columns));

        $sql = "INSERT INTO `" . str_replace("`", "``", $tableName) . "` ({$columnSql}) VALUES ({$placeholders})";
        
        error_log("SQL Query: " . $sql);
        error_log("Parameters: " . print_r($data, true));

        // 쿼리 실행
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($data);
        
        if ($result === false) {
            $error = $stmt->errorInfo();
            error_log("process_file_insert 실패: " . print_r($error, true));
            return false;
        }

        // 삽입된 ID 확인
        $file_id = $pdo->lastInsertId();
        if ($file_id === false) {
            error_log("process_file_insert: lastInsertId 실패");
            return false;
        }

        error_log("파일 정보 저장 성공 - ID: " . $file_id);
        return $file_id;
    } catch (PDOException $e) {
        error_log("process_file_insert PDO 오류: " . $e->getMessage());
        error_log("PDO 오류 코드: " . $e->getCode());
        return false;
    } catch (Exception $e) {
        error_log("process_file_insert 일반 오류: " . $e->getMessage());
        return false;
    }
}

/**
 * 데이터베이스 테이블의 데이터를 업데이트합니다.
 *
 * @param string $tableName      업데이트할 테이블 이름
 * @param array  $data           업데이트할 데이터 (연관 배열, 컬럼명 => 값)
 * @param array  $whereConditions 업데이트 조건 (연관 배열, 컬럼명 => 값)
 * @return bool 업데이트 성공 시 true, 실패 시 false 반환
 */
function process_data_update(string $tableName, array $data, array $whereConditions): bool {
    global $pdo;

    if (empty($data) || empty($whereConditions)) {
        error_log("process_data_update: 업데이트할 데이터 또는 조건이 비어 있습니다.");
        return true; // 데이터나 조건이 비어있으면 성공으로 처리
    }

    $setParts = array_map(fn($col) => "`" . str_replace("`", "``", $col) . "` = :" . $col, array_keys($data));
    $setSql = implode(', ', $setParts);

    $whereParts = [];
    $whereData = [];
    foreach ($whereConditions as $col => $value) {
        $placeholder = ":where_" . $col;
        $whereParts[] = "`" . str_replace("`", "``", $col) . "` = " . $placeholder;
        $whereData[$placeholder] = $value;
    }
    $whereSql = implode(' AND ', $whereParts);

    $sql = "UPDATE `" . str_replace("`", "``", $tableName) . "` SET {$setSql} WHERE {$whereSql}";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge($data, $whereData));
        return true; // 업데이트 성공 여부와 관계없이 true 반환
    } catch (PDOException $e) {
        error_log("process_data_update 오류 ({$tableName}): " . $e->getMessage());
        return false;
    }
}

/**
 * 파일 메타데이터를 업데이트합니다. (process_data_update() 래퍼 함수)
 *
 * @param string $tableName      업데이트할 테이블 이름
 * @param array  $data           업데이트할 데이터 (연관 배열)
 * @param array  $whereConditions 업데이트 조건 (연관 배열)
 * @return bool 업데이트 성공 시 true, 실패 시 false 반환
 */
function process_file_update(string $tableName, array $data, array $whereConditions): bool {
    return process_data_update($tableName, $data, $whereConditions);
}

/**
 * 데이터베이스 테이블의 데이터를 삭제합니다.
 *
 * @param string $tableName      삭제할 테이블 이름
 * @param array  $whereConditions 삭제 조건 (연관 배열, 컬럼명 => 값)
 * @return bool 삭제 성공 시 true, 실패 시 false 반환
 */
function process_data_delete(string $tableName, array $whereConditions): bool {
    global $pdo;

    if (empty($whereConditions)) {
        error_log("process_data_delete: 삭제 조건이 비어 있습니다.");
        return true; // 조건이 비어있으면 성공으로 처리
    }

    $whereParts = [];
    $whereData = [];
    foreach ($whereConditions as $col => $value) {
        $placeholder = ":where_" . $col;
        $whereParts[] = "`" . str_replace("`", "``", $col) . "` = " . $placeholder;
        $whereData[$placeholder] = $value;
    }
    $whereSql = implode(' AND ', $whereParts);

    $sql = "DELETE FROM `" . str_replace("`", "``", $tableName) . "` WHERE {$whereSql}";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($whereData);
        return true; // 삭제 성공 여부와 관계없이 true 반환
    } catch (PDOException $e) {
        error_log("process_data_delete 오류 ({$tableName}): " . $e->getMessage());
        return false;
    }
}

/**
 * 파일 메타데이터를 삭제합니다. (process_data_delete() 래퍼 함수)
 *
 * @param string $tableName      삭제할 테이블 이름
 * @param array  $whereConditions 삭제 조건 (연관 배열)
 * @return bool 삭제 성공 시 true, 실패 시 false 반환
 */
function process_file_delete(string $tableName, array $whereConditions): bool {
    return process_data_delete($tableName, $whereConditions);
}

/**
 * Summernote 에디터 이미지 처리 및 저장
 *
 * @param array $fileInfo 업로드된 파일 정보($_FILES 배열)
 * @param string $board 게시판 이름
 * @return string|false 저장된 이미지 URL 또는 실패 시 false
 */
function process_editor_image_upload(array $fileInfo, string $dataname)
{
    // 업로드 오류 확인
    if (!isset($fileInfo['error']) || $fileInfo['error'] !== UPLOAD_ERR_OK) {
        error_log("이미지 업로드 오류: " . ($fileInfo['error'] ?? '알 수 없음'));
        return false;
    }

    // 파일 정보 추출
    $tmp_name = $fileInfo['tmp_name'];
    $original_filename = $fileInfo['name'];
    $file_type = $fileInfo['type'];

    // 허용된 이미지 타입인지 확인
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file_type, $allowed_types)) {
         error_log("업로드 오류: 허용되지 않는 파일 타입 ({$file_type})");
         return false;
    }

    // 파일 확장자 추출
    $file_ext = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

    // 저장 경로 설정
	if($dataname == "popup" || $dataname == "content"){
		$upload_dir = CM_DATA_PATH . '/' . $dataname . '/';
		$upload_url = CM_DATA_URL . '/' . $dataname ;
	}else{
		$upload_dir = CM_DATA_PATH . '/board/' . $dataname . '/editor/';
		$upload_url = CM_DATA_URL . '/board/' . $dataname . '/editor';
	}

    // 디렉토리 생성 (필요시)
    if (!file_exists($upload_dir) && !mkdir($upload_dir, 0777, true)) {
        error_log("업로드 오류: 디렉토리 생성 실패");
        return false;
    }

    // 고유 파일명 생성
    $stored_filename = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $file_ext;
    $upload_file_path = $upload_dir . $stored_filename;

    // 파일 저장
    if (move_uploaded_file($tmp_name, $upload_file_path)) {
        // URL 생성 및 반환
        if (!defined('CM_DATA_URL')) {
             error_log("업로드 오류: CM_DATA_URL 상수 미정의");
             if (file_exists($upload_file_path)) {
                 unlink($upload_file_path);
             }
             return false;
        }

        return $upload_url . '/' . $stored_filename;
		
    } else {
        error_log("업로드 오류: 파일 이동 실패");
        return false;
    }
}

function process_editor_image_delete(string $tableName, string $tableCol, array $whereConditions, string $editorDir): array {
    global $pdo;

    try {
        // WHERE 조건 생성
        $whereClause = [];
        $params = [];
        foreach ($whereConditions as $field => $value) {
            $whereClause[] = "`$field` = :$field";
            $params[":$field"] = $value;
        }
        $whereSql = implode(' AND ', $whereClause);

        // 게시물 콘텐츠 조회
        $sql = "SELECT `$tableCol` FROM `$tableName` WHERE $whereSql";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        if (!$row || empty($row[$tableCol])) {
            return ['success' => true, 'message' => '콘텐츠가 없거나 게시물이 존재하지 않습니다.'];
        }

        // content에서 <img> 태그의 src 속성 추출
        $content = $row[$tableCol];
        $pattern = '/<img[^>]+src=["\'](.*?)["\']/i';
        preg_match_all($pattern, $content, $matches);

        $deletedFiles = [];
        $errors = [];

        // 추출된 이미지 경로 처리
        if (!empty($matches[1])) {
            foreach ($matches[1] as $imgSrc) {
                // 절대 경로에서 파일 이름만 추출
                $fileName = basename($imgSrc);
                $filePath = rtrim($editorDir, '/') . '/' . $fileName;

                // 파일이 존재하는지 확인하고 삭제
                if (file_exists($filePath)) {
                    if (unlink($filePath)) {
                        $deletedFiles[] = $fileName;
                    } else {
                        $errors[] = "파일 삭제 실패: $fileName";
                    }
                }
            }
        }

        if (empty($errors)) {
            return ['success' => true, 'message' => '이미지 삭제 완료: ' . (empty($deletedFiles) ? '삭제된 파일 없음' : implode(', ', $deletedFiles))];
        } else {
            return ['success' => false, 'message' => '일부 이미지 삭제 실패: ' . implode(', ', $errors)];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => '이미지 삭제 중 오류 발생: ' . $e->getMessage()];
    }
}


/**
 * SQL 쿼리를 실행하고 단일 행을 반환합니다.
 *
 * @param string $sql    실행할 SQL 쿼리
 * @param array  $params (선택 사항) 쿼리 매개변수
 * @return array|false  결과 행 (연관 배열), 결과가 없거나 오류 발생 시 false 반환
 */
function sql_fetch(string $sql, array $params = []): array|false {
    global $pdo;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("sql_fetch 오류: " . $e->getMessage());
        return false;
    }
}

/**
 * 게시판외 일반페이지 목록을 조회합니다. 페이징처리 O
 */
function sql_list(array $options = []): array {
	
	global $pdo; // 데이터베이스 연결 객체인 $pdo에 접근

    if (!$pdo) {
        error_log("sql_fetch 오류: 데이터베이스 연결( \$pdo )이 설정되지 않았습니다.");
        return [];
    }
	
    $table = $options['table'] ?? '';
    $page = $options['page'] ?? 1;
    $per_page = $options['per_page'] ?? 10;
    $order_by = $options['order_by'] ?? 'id DESC';
    $conditions = $options['conditions'] ?? [];

    if (!$table) {
        throw new InvalidArgumentException('테이블명을 지정해주세요.');
    }

    $start = ($page - 1) * $per_page;
    $where_clauses = [];
    $params = [];

    foreach ($conditions as $idx => $cond) {
        $field = $cond['field'] ?? '';
        $operator = strtoupper($cond['operator'] ?? '=');
        $value = $cond['value'] ?? '';

        if (empty($field) || empty($operator)) {
            continue;
        }

        $param_key = ":cond$idx";

        if ($operator === 'IN' && is_array($value)) {
            $placeholders = [];
            foreach ($value as $i => $val) {
                $key = ":cond{$idx}_{$i}";
                $placeholders[] = $key;
                $params[$key] = $val;
            }
            $where_clauses[] = "$field IN (" . implode(', ', $placeholders) . ")";

        } elseif ($operator === 'BETWEEN' && is_array($value) && count($value) === 2) {
            $params[":cond{$idx}_start"] = $value[0];
            $params[":cond{$idx}_end"] = $value[1];
            $where_clauses[] = "$field BETWEEN :cond{$idx}_start AND :cond{$idx}_end";

        } elseif ($operator === 'LIKE') {
            // IP 주소 검색인 경우 (ip_address 필드)
            if ($field === 'ip_address') {
                $where_clauses[] = "$field $operator $param_key";
                $params[$param_key] = $value;
            } else {
                // 일반 LIKE 검색의 경우 기존처럼 양쪽에 % 추가
                $value = '%' . $value . '%';
                $where_clauses[] = "$field $operator $param_key";
                $params[$param_key] = $value;
            }
        } else {
            $where_clauses[] = "$field $operator $param_key";
            $params[$param_key] = $value;
        }
    }

    $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

    try {
        $count_sql = "SELECT COUNT(*) FROM $table $where_sql";
        $count_stmt = $pdo->prepare($count_sql);
        $count_stmt->execute($params);
        $total_rows = $count_stmt->fetchColumn();
        $total_pages = ceil($total_rows / $per_page);

        $list_sql = "
            SELECT * 
            FROM $table 
            $where_sql
            ORDER BY $order_by
            LIMIT :start, :per_page
        ";

        $stmt = $pdo->prepare($list_sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':per_page', (int)$per_page, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return [
            'list' => $rows,
			'current_page' => $page,
			'per_page' => $per_page,
            'total_rows' => $total_rows,
            'total_pages' => $total_pages
        ];
    } catch (PDOException $e) {
        throw new RuntimeException('DB 오류: ' . $e->getMessage());
    }
}

/**
 * 게시판 목록을 조회합니다. 페이징처리 O
*/
function sql_board_list(string $table, array $options = []): array {
	
	global $pdo; // 데이터베이스 연결 객체인 $pdo에 접근

    if (!$pdo) {
        error_log("sql_fetch 오류: 데이터베이스 연결( \$pdo )이 설정되지 않았습니다.");
        return false;
    }
	
    $page = intval($options['page'] ?? 1);
    $per_page = intval($options['per_page'] ?? 10);
    $start = ($page - 1) * $per_page;
    
    $searches = $options['search'] ?? [];  // 필드별 조건 배열
    $order_by = $options['order_by'] ?? 'id';
    $order_dir = strtoupper($options['order_dir'] ?? 'DESC');
    $debug = $options['debug'] ?? false;

    $where_clauses = [];
    $params = [];

    // 검색 조건 구성
    foreach ($searches as $i => $s) {
        $field = $s['field'] ?? '';
        $operator = strtoupper($s['operator'] ?? '=');
        $value = $s['value'] ?? null;

        $param_name = ":param_$i";

        if ($operator === 'LIKE') {
            $where_clauses[] = "$field LIKE $param_name";
            $params[$param_name] = "%$value%";
        } elseif ($operator === 'IN' && is_array($value)) {
            $in_params = [];
            foreach ($value as $j => $v) {
                $pname = ":param_{$i}_$j";
                $in_params[] = $pname;
                $params[$pname] = $v;
            }
            $where_clauses[] = "$field IN (" . implode(", ", $in_params) . ")";
        } elseif ($operator === 'BETWEEN' && is_array($value) && count($value) === 2) {
            $params[":param_{$i}_1"] = $value[0];
            $params[":param_{$i}_2"] = $value[1];
            $where_clauses[] = "($field BETWEEN :param_{$i}_1 AND :param_{$i}_2)";
        } else {
            // 일반 연산자 (=, >=, <=, <, > 등)
            $where_clauses[] = "$field $operator $param_name";
            $params[$param_name] = $value;
        }
    }

    $where_sql = count($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

    // 전체 개수 쿼리
    $count_sql = "SELECT COUNT(*) FROM {$table} $where_sql";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_rows = $count_stmt->fetchColumn();
    $total_pages = ceil($total_rows / $per_page);

    // 데이터 조회 쿼리
    $sql = "
        SELECT *,
            (SELECT COUNT(*) FROM cm_board_file WHERE board_num = {$table}.board_num) as file_count
        FROM {$table}
        $where_sql
        ORDER BY $order_by $order_dir
        LIMIT :start, :per_page
    ";

    if ($debug) {
        echo "<pre>SQL: $sql\nParams: " . print_r($params, true) . "</pre>";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();
    $rows = $stmt->fetchAll();

    return [
        'list' => $rows,
        'current_page' => $page,
        'per_page' => $per_page,
        'total_rows' => $total_rows,
        'total_pages' => $total_pages
    ];
}

/**
 * SQL 쿼리를 실행하고 전체 목록을 반환합니다. 간단한 리스트 조회 페이징처리 X
 *
 * @param string $sql    실행할 SQL 쿼리
 * @param array  $params (선택 사항) 쿼리 매개변수
 */
function sql_all_list(string $sql, array $params = [])
{
    global $pdo;

    if (!$pdo) {
        error_log("sql_list 오류: 데이터베이스 연결( \$pdo )이 설정되지 않았습니다.");
        return false;
    }

    try {
        // 쿼리 준비
        $stmt = $pdo->prepare($sql);

        // 파라미터 바인딩
        foreach ($params as $key => $value) {
            if ($key === ':limit' || $key === ':offset') {
                $stmt->bindValue($key, (int)$value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        // 쿼리 실행
        $executeResult = $stmt->execute();

        if ($executeResult === false) {
            error_log("sql_list 오류: 쿼리 실행 실패. [쿼리: " . $sql . "] [파라미터: " . print_r($params, true) . "]");
            return false;
        }

        // 결과의 모든 행을 연관 배열의 배열로 가져오기
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Statement 객체 닫기
        $stmt->closeCursor();

        return $results;

    } catch (PDOException $e) {
        error_log("sql_list 오류: " . $e->getMessage() . " [쿼리: " . $sql . "] [파라미터: " . print_r($params, true) . "]");
        return false;
    } catch (Exception $e) {
        error_log("sql_list 알 수 없는 오류: " . $e->getMessage() . " [쿼리: " . $sql . "] [파라미터: " . print_r($params, true) . "]");
        return false;
    }
}

/**
 * SQL 쿼리를 실행하고 결과 레코드의 수를 반환합니다.
 *
 * @param string $sql    실행할 SQL 쿼리
 * @param array  $params (선택 사항) 쿼리 매개변수
 * @return int          결과 레코드 수
 */
function sql_count(string $sql, array $params = []): int {
    global $pdo;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("sql_count 오류: " . $e->getMessage());
        return 0;
    }
}

/**
 * 페이지네이션 HTML 코드를 생성합니다.
 *
 * @param int   $current_page 현재 페이지 번호
 * @param int   $total_pages  전체 페이지 수
 * @param array $query_params  페이지 링크에 추가할 쿼리 매개변수
 * @param int   $range        현재 페이지 기준으로 표시할 페이지 범위
 * @return string 생성된 페이지네이션 HTML 코드
 */

function render_pagination(int $current_page, int $total_pages, array $query_params = [], int $range = 4): string {
    if ($total_pages <= 1) return '';

    $start_page = max(1, $current_page - $range);
    $end_page = min($total_pages, $current_page + $range);

    // query string 생성
    $base_query = $query_params;
    unset($base_query['page']); // 기존 page 제거

    $query_string = function($page) use ($base_query) {
        return '?' . http_build_query(array_merge($base_query, ['page' => $page]));
    };

    ob_start();
    ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($current_page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $query_string(1) ?>" aria-label="First">
                        <span aria-hidden="true">&laquo;&laquo;</span>
                    </a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="<?= $query_string($current_page - 1) ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $query_string($i) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $query_string($current_page + 1) ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="<?= $query_string($total_pages) ?>" aria-label="Last">
                        <span aria-hidden="true">&raquo;&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php
    return ob_get_clean();
}

/**
 * 정렬 아이콘을 생성합니다.
 *
 * @param string $current_field 현재 정렬 필드
 * @param string $current_order 현재 정렬 방향
 * @param string $field 비교할 필드
 * @return string 정렬 아이콘 HTML
 */
function get_sort_icon(string $current_field, string $current_order, string $field): string {
    if ($current_field !== $field) {
        return '<i class="fas fa-sort"></i>';
    }
    return $current_order === 'ASC' ? 
        '<i class="fas fa-sort-up"></i>' : 
        '<i class="fas fa-sort-down"></i>';
}

/**
 * 정렬 가능한 필드 목록을 반환합니다.
 *
 * @param string $table 테이블 이름
 * @return array 정렬 가능한 필드 목록
 */
function get_sortable_fields(string $table): array {
    $fields = [
        'cm_users' => ['user_no', 'user_id', 'user_name', 'user_email', 'user_hp', 'user_lv', 'user_point', 'created_at'],
        'cm_point' => ['id', 'user_id', 'point', 'description', 'created_at'],
        'cm_board' => ['board_num', 'board_id', 'name', 'title', 'reg_date']
    ];
    
    return $fields[$table] ?? [];
}

/**
 * 파일 확장자에 따른 Font Awesome 아이콘 클래스를 반환합니다.
 *
 * @param string $filename 파일명 또는 확장자
 * @return string Font Awesome 아이콘 클래스
 */
function get_file_icon_class(string $filename): string {
    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    // 파일 타입별 아이콘 클래스 설정
    switch($file_ext) {
        case 'pdf':
            return 'fa-file-pdf';
        case 'doc':
        case 'docx':
            return 'fa-file-word';
        case 'xls':
        case 'xlsx':
            return 'fa-file-excel';
        case 'ppt':
        case 'pptx':
            return 'fa-file-powerpoint';
        case 'zip':
        case 'rar':
            return 'fa-file-archive';
        case 'txt':
            return 'fa-file-alt';
        default:
            return 'fa-file';
    }
}

/**
 * 파일이 이미지인지 확인합니다.
 *
 * @param string $filename 파일명 또는 확장자
 * @return bool 이미지 파일 여부
 */
function is_image_file(string $filename): bool {
    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
}