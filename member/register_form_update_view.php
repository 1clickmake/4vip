<?php
include_once('./_common.php');


if (!$is_member) {
    alert('회원만 이용하실 수 있습니다.');
    exit;
}

$cm_title = '회원정보 수정';
include_once(CM_PATH.'/head.php');

// 현재 로그인한 회원의 정보를 가져옵니다
$sql = " select * from cm_users where user_id = '{$member['user_id']}' and user_leave = 0 ";
$mb = sql_fetch($sql);
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- 비밀번호 확인 모달 -->
            <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="passwordModalLabel">비밀번호 확인</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="passwordCheckForm">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">현재 비밀번호</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">확인</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 회원정보 수정 폼 -->
            <div id="updateForm" style="display: none;">
                <h2 class="text-center mb-4">회원정보 수정</h2>
                <form id="memberUpdateForm" method="post" action="./register_form_update.php">
                    <input type="hidden" name="w" value="update">
                    <input type="hidden" name="user_id" value="<?php echo $member['user_id'] ?>">
                    
                    <div class="mb-3">
                        <label for="user_name" class="form-label">이름</label>
                        <input type="text" class="form-control" id="user_name" name="user_name" value="<?php echo $mb['user_name'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="user_password" class="form-label">새 비밀번호</label>
                        <input type="password" class="form-control" id="user_password" name="user_password">
                        <small class="text-muted">변경하지 않으려면 비워두세요</small>
                    </div>

                    <div class="mb-3">
                        <label for="user_password_re" class="form-label">새 비밀번호 확인</label>
                        <input type="password" class="form-control" id="user_password_re" name="user_password_re">
                    </div>

                    <div class="mb-3">
                        <label for="user_email" class="form-label">이메일</label>
                        <input type="email" class="form-control" id="user_email" name="user_email" value="<?php echo $mb['user_email'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="user_hp" class="form-label">휴대폰번호</label>
                        <input type="tel" class="form-control" id="user_hp" name="user_hp" value="<?php echo $mb['user_hp'] ?>">
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">수정하기</button>
                        <a href="<?php echo CM_URL ?>" class="btn btn-secondary">취소</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 페이지 로드시 비밀번호 확인 모달 표시
    var passwordModal = new bootstrap.Modal(document.getElementById('passwordModal'));
    passwordModal.show();

    // 비밀번호 확인 폼 제출
    $('#passwordCheckForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: './password_check.php',
            type: 'POST',
            data: {
                user_id: '<?php echo $member['user_id'] ?>',
                user_password: $('#current_password').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    passwordModal.hide();
                    $('#updateForm').show();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('서버 오류가 발생했습니다.');
            }
        });
    });

    // 회원정보 수정 폼 제출
    $('#memberUpdateForm').on('submit', function(e) {
        e.preventDefault();
        
        if ($('#user_password').val() !== $('#user_password_re').val()) {
            alert('비밀번호가 일치하지 않습니다.');
            return;
        }

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert('회원정보가 수정되었습니다.');
                    location.href = '<?php echo CM_URL ?>';
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('서버 오류가 발생했습니다.');
            }
        });
    });
});
</script>

<?php
include_once(CM_PATH.'/tail.php');
?> 