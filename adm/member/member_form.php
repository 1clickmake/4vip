<?php
include_once './_common.php';
include_once CM_ADMIN_PATH.'/admin.head.php';

$user = [];
$is_new = true;

// user_no가 제공된 경우 수정 모드
if (isset($_GET['user_no']) && is_numeric($_GET['user_no'])) {
    $user_no = (int)$_GET['user_no'];
    $is_new = false;

    // 회원 데이터 조회
    try {
        $stmt = $pdo->prepare("SELECT * FROM cm_users WHERE user_no = :user_no");
        $stmt->execute(['user_no' => $user_no]);
        $user = $stmt->fetch();
        
        if (!$user) {
            alert('회원을 찾을 수 없습니다.', 'member_list.php');
            exit;
        }
    } catch (PDOException $e) {
        alert('오류: ' . $e->getMessage(), 'member_list.php');
        exit;
    }
}
?>

<!-- Main Content -->
<div class="main-content shifted" id="mainContent">
    <div class="container-fluid">
        <div class="form-container">
            <div class="form-card">
                <div class="form-header">
                    <h2><i class="fas fa-user-edit me-2"></i><?php echo $is_new ? '회원 신규 등록' : '회원 정보 수정'; ?></h2>
                    <?php if (!$is_new): ?>
                    <div class="member-id">회원 ID: <?php echo htmlspecialchars($user['user_id']); ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-body">
                    <form action="member_form_update.php" method="POST" id="memberForm">
                        <?php if (!$is_new): ?>
                        <input type="hidden" name="user_no" value="<?php echo htmlspecialchars($user['user_no']); ?>">
                        <?php endif; ?>
                        
                        <div class="form-group-wrapper">
                            <label for="user_id" class="form-label">
                                <i class="fas fa-id-card me-1"></i>회원 아이디
                            </label>
                            <div class="input-group-custom">
                                <input type="text" class="form-control" id="user_id" name="user_id" 
                                       value="<?php echo $is_new ? '' : htmlspecialchars($user['user_id']); ?>" 
                                       <?php echo $is_new ? 'required' : 'readonly'; ?>>
                                <i class="fas fa-lock input-icon"></i>
                            </div>
                        </div>
                        
                        <div class="form-group-wrapper">
                            <label for="user_name" class="form-label">
                                <i class="fas fa-user me-1"></i>이름
                            </label>
                            <div class="input-group-custom">
                                <input type="text" class="form-control" id="user_name" name="user_name" 
                                       value="<?php echo $is_new ? '' : htmlspecialchars($user['user_name']); ?>" required>
                                <i class="fas fa-pencil-alt input-icon"></i>
                            </div>
                        </div>
                        
                        <div class="form-group-wrapper">
                            <label for="user_password" class="form-label">
                                <i class="fas fa-key me-1"></i>비밀번호
                            </label>
                            <div class="input-group-custom">
                                <input type="password" class="form-control" id="user_password" name="user_password" 
                                       <?php echo $is_new ? 'required' : ''; ?>
                                       placeholder="<?php echo $is_new ? '비밀번호를 입력하세요' : '변경하지 않으려면 공란으로 두세요'; ?>">
                                <i class="fas fa-eye-slash input-icon"></i>
                            </div>
                        </div>
                        
                        <div class="form-group-wrapper">
                            <label for="user_email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>이메일
                            </label>
                            <div class="input-group-custom">
                                <input type="email" class="form-control" id="user_email" name="user_email" 
                                       value="<?php echo $is_new ? '' : htmlspecialchars($user['user_email'] ?? ''); ?>">
                                <i class="fas fa-at input-icon"></i>
                            </div>
                        </div>
                        
                        <div class="form-group-wrapper">
                            <label for="user_hp" class="form-label">
                                <i class="fas fa-mobile-alt me-1"></i>휴대폰 번호
                            </label>
                            <div class="input-group-custom">
                                <input type="text" class="form-control" id="user_hp" name="user_hp" 
                                       value="<?php echo $is_new ? '' : htmlspecialchars($user['user_hp'] ?? ''); ?>">
                                <i class="fas fa-phone input-icon"></i>
                            </div>
                        </div>
                        
                        <div class="form-group-wrapper">
                            <label for="user_lv" class="form-label">
                                <i class="fas fa-star me-1"></i>회원 레벨
                            </label>
                            <div class="input-group-custom">
                                <input type="number" class="form-control" id="user_lv" name="user_lv" 
                                       value="<?php echo $is_new ? '1' : htmlspecialchars($user['user_lv']); ?>" required min="1">
                                <i class="fas fa-level-up-alt input-icon"></i>
                            </div>
                        </div>
                        
                        <?php if (!$is_new): ?>
                        <div class="form-group-wrapper">
                            <label class="form-label">
                                <i class="fas fa-coins me-1"></i>보유포인트
                            </label>
                            <div class="point-display">
                                <i class="fas fa-gem me-2"></i><?php echo number_format($user['user_point']); ?> P
                            </div>
                        </div>
                        
                        <div class="form-group-wrapper">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="user_block" name="user_block" value="1" 
                                       <?php echo $user['user_block'] == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="user_block">
                                    <i class="fas fa-ban me-2"></i>회원 차단
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group-wrapper">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="user_leave" name="user_leave" value="1" 
                                       <?php echo $user['user_leave'] == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="user_leave">
                                    <i class="fas fa-user-times me-2"></i>회원 탈퇴
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="btn-group-custom">
                            <button type="submit" name="action" value="<?php echo $is_new ? 'insert' : 'update'; ?>" class="btn btn-custom btn-primary-custom">
                                <i class="fas fa-save me-2"></i><?php echo $is_new ? '회원 등록' : '정보 수정'; ?>
                            </button>
                            <?php if (!$is_new): ?>
                            <button type="submit" name="action" value="delete" class="btn btn-custom btn-danger-custom" 
                                    onclick="return confirm('정말로 이 회원을 삭제하시겠습니까?');">
                                <i class="fas fa-trash me-2"></i>회원 삭제
                            </button>
                            <?php endif; ?>
                            <a href="member_list.php" class="btn btn-custom btn-secondary-custom">
                                <i class="fas fa-list me-2"></i>목록으로
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 비밀번호 보기/숨기기 토글
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('user_password');
    const eyeIcon = passwordInput.parentElement.querySelector('.input-icon');
    
    eyeIcon.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    });
    
    // 폼 유효성 검사
    document.getElementById('memberForm').addEventListener('submit', function(e) {
        const userId = document.getElementById('user_id').value.trim();
        const userName = document.getElementById('user_name').value.trim();
        const userPassword = document.getElementById('user_password').value;
        const userLevel = document.getElementById('user_lv').value;
        
        if (!userId) {
            alert('아이디를 입력해주세요.');
            e.preventDefault();
            return false;
        }
        
        if (!userName) {
            alert('이름을 입력해주세요.');
            e.preventDefault();
            return false;
        }
        
        if (userLevel < 1) {
            alert('회원 레벨은 1 이상이어야 합니다.');
            e.preventDefault();
            return false;
        }
        
        <?php if ($is_new): ?>
        if (!userPassword) {
            alert('비밀번호를 입력해주세요.');
            e.preventDefault();
            return false;
        }
        <?php endif; ?>
    });
});
</script>

<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>