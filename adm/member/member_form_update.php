```php
<?php
include_once './_common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	alert('잘못된 요청 방식입니다.' );
}

// 폼 데이터 수집
$user_no = isset($_POST['user_no']) ? (int)$_POST['user_no'] : 0;
$user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
$action = isset($_POST['action']) ? trim($_POST['action']) : '';

if ($user_no <= 0 || !in_array($action, ['update', 'delete']) || $user_id === '') {
	alert('잘못된 요청입니다.', 'member_list.php');
    exit;
}

if ($action === 'delete') {
    // 회원 삭제 처리
    try {
        $stmt = $pdo->prepare("DELETE FROM cm_users WHERE user_no = :user_no");
        $stmt->execute(['user_no' => $user_no]);
		
		$stmt = $pdo->prepare("DELETE FROM cm_point WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
		
		alert('회원이 성공적으로 삭제되었습니다.', 'member_list.php');
    } catch (PDOException $e) {
        alert('삭제 오류: '.$e->getMessage() );
    }
}

// 업데이트 처리
$user_name = isset($_POST['user_name']) ? trim($_POST['user_name']) : '';
$user_password = isset($_POST['user_password']) ? trim($_POST['user_password']) : '';
$user_email = isset($_POST['user_email']) ? trim($_POST['user_email']) : '';
$user_hp = isset($_POST['user_hp']) ? trim($_POST['user_hp']) : '';
$user_lv = isset($_POST['user_lv']) ? (int)$_POST['user_lv'] : 1;
$user_block = isset($_POST['user_block']) && $_POST['user_block'] == '1' ? 1 : 0;
$user_leave = isset($_POST['user_leave']) && $_POST['user_leave'] == '1' ? 1 : 0;

// 입력 데이터 검증
if (empty($user_name) || $user_lv < 1 ) {
    echo "<script>alert('잘못된 입력 데이터입니다.'); window.history.back();</script>";
    alert('잘못된 입력 데이터입니다.');
}

// 이메일 중복 체크 (현재 회원 제외)
if (!empty($user_email)) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cm_users WHERE user_email = :user_email AND user_no != :user_no");
        $stmt->execute(['user_email' => $user_email, 'user_no' => $user_no]);
        if ($stmt->fetchColumn() > 0) {
            alert('이미 사용 중인 이메일입니다.');
        }
    } catch (PDOException $e) {
        alert('오류: '.$e->getMessage() );
    }
}

// 업데이트 쿼리 준비
$updateFields = [
    'user_name' => $user_name,
    'user_email' => $user_email ?: null,
    'user_hp' => $user_hp ?: null,
    'user_lv' => $user_lv,
    'user_block' => $user_block,
    'user_leave' => $user_leave
];
$updateSql = [];
$updateParams = [];

foreach ($updateFields as $field => $value) {
    $updateSql[] = "$field = :$field";
    $updateParams[":$field"] = $value;
}

// 비밀번호가 제공된 경우 업데이트
if (!empty($user_password)) {
    $updateSql[] = "user_password = :user_password";
    $updateParams[':user_password'] = password_hash($user_password, PASSWORD_DEFAULT);
}

$updateParams[':user_no'] = $user_no;

// 업데이트 실행
try {
    $sql = "UPDATE cm_users SET " . implode(', ', $updateSql) . " WHERE user_no = :user_no";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($updateParams);
	alert('회원 정보가 성공적으로 수정되었습니다.','member_form.php?user_no='.$user_no);
} catch (PDOException $e) {
	alert('수정 오류: '.$e->getMessage() );
}
?>
