<?php
include_once './_common.php';
include_once CM_ADMIN_PATH.'/admin.head.php';

// user_no가 제공되었는지 확인
if (!isset($_GET['user_no']) || !is_numeric($_GET['user_no'])) {
    echo "<script>alert('잘못된 회원 번호입니다.'); window.location.href='member_list.php';</script>";
    exit;
}

$user_no = (int)$_GET['user_no'];

// 회원 데이터 조회
try {
    $stmt = $pdo->prepare("SELECT * FROM cm_users WHERE user_no = :user_no");
    $stmt->execute(['user_no' => $user_no]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo "<script>alert('회원을 찾을 수 없습니다.'); window.location.href='member_list.php';</script>";
        exit;
    }
} catch (PDOException $e) {
    echo "<script>alert('오류: " . $e->getMessage() . "'); window.location.href='member_list.php';</script>";
    exit;
}
?>

    <!-- Main Content -->
    <div class="main-content shifted" id="mainContent">
		
		<div class="container-fluid">
			<h2 class="my-4">회원 정보 수정</h2>
			<form action="member_form_update.php" method="POST" id="memberForm">
				<input type="hidden" name="user_no" value="<?php echo htmlspecialchars($user['user_no']); ?>">
				<div class="mb-3">
					<label for="user_id" class="form-label">회원 아이디</label>
					<input type="text" class="form-control" id="user_id" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>" readonly>
				</div>
				<div class="mb-3">
					<label for="user_name" class="form-label">이름</label>
					<input type="text" class="form-control" id="user_name" name="user_name" value="<?php echo htmlspecialchars($user['user_name']); ?>" required>
				</div>
				<div class="mb-3">
					<label for="user_password" class="form-label">비밀번호 (변경하지 않으려면 공란으로 두세요)</label>
					<input type="password" class="form-control" id="user_password" name="user_password">
				</div>
				<div class="mb-3">
					<label for="user_email" class="form-label">이메일</label>
					<input type="email" class="form-control" id="user_email" name="user_email" value="<?php echo htmlspecialchars($user['user_email'] ?? ''); ?>">
				</div>
				<div class="mb-3">
					<label for="user_hp" class="form-label">휴대폰 번호</label>
					<input type="text" class="form-control" id="user_hp" name="user_hp" value="<?php echo htmlspecialchars($user['user_hp'] ?? ''); ?>">
				</div>
				<div class="mb-3">
					<label for="user_lv" class="form-label">회원 레벨</label>
					<input type="number" class="form-control" id="user_lv" name="user_lv" value="<?php echo htmlspecialchars($user['user_lv']); ?>" required min="1">
				</div>
				<div class="mb-3">
					<label for="user_point" class="form-label">보유포인트</label>
					<?php echo number_format($user['user_point']); ?>
				</div>
				<div class="mb-3 form-check">
					<input type="checkbox" class="form-check-input" id="user_block" name="user_block" value="1" <?php echo $user['user_block'] == 1 ? 'checked' : ''; ?>>
					<label class="form-check-label" for="user_block">차단</label>
				</div>
				<div class="mb-3 form-check">
					<input type="checkbox" class="form-check-input" id="user_leave" name="user_leave" value="1" <?php echo $user['user_leave'] == 1 ? 'checked' : ''; ?>>
					<label class="form-check-label" for="user_leave">탈퇴</label>
				</div>
				<button type="submit" name="action" value="update" class="btn btn-primary">회원 정보 수정</button>
				<button type="submit" name="action" value="delete" class="btn btn-danger" onclick="return confirm('정말로 이 회원을 삭제하시겠습니까?');">회원 삭제</button>
				<a href="member_list.php" class="btn btn-secondary">목록</a>
			</form>
		</div>
		
    </div>

    
<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>