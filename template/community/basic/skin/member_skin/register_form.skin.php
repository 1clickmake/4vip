<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
			
			<?php if($update){?>
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
			<?php } ?>

            <!-- 회원정보 입력/수정 폼 -->
            <div id="updateForm" <?php if($update){?> style="display:none;"<?php } ?>>
                <h2 class="text-center mb-4"><?php echo $cm_title;?></h2>
                <form id="registerForm" method="post" action="./register_form_update.php">
					<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                    <input type="hidden" name="w" value="<?php echo $w;?>">
                    
                    <div class="mb-3">
						<label for="user_id" class="form-label" style="color: #555;">아이디</label>
						<input type="text" class="form-control" id="user_id" name="user_id" value="<?php echo $member['user_id'] ?? '';?>" placeholder="아이디를 입력하세요" required autofocus <?php if($update){?>readonly<?php } ?>>
						<div id="user_idError" class="form-text text-danger"></div>
					</div>
                    <div class="mb-3">
                        <label for="user_name" class="form-label">이름</label>
                        <input type="text" class="form-control" id="user_name" name="user_name" value="<?php echo $member['user_name'] ?? ''; ?>" required>
                        <div id="user_nameError" class="form-text text-danger"></div>
                    </div>

                    <div class="mb-3">
                        <label for="user_password" class="form-label">새 비밀번호</label>
                        <input type="password" class="form-control" id="user_password" name="user_password">
                        <?php if($update){?><small class="text-muted">변경하지 않으려면 비워두세요</small><?php } ?>
                        <div id="user_passwordError" class="form-text text-danger"></div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">새 비밀번호 확인</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                        <div id="password_confirmError" class="form-text text-danger"></div>
                    </div>

                    <div class="mb-3">
                        <label for="user_email" class="form-label">이메일</label>
                        <input type="email" class="form-control" id="user_email" name="user_email" value="<?php echo $member['user_email'] ?? ''; ?>" required>
                        <div id="user_emailError" class="form-text text-danger"></div>
                    </div>

                    <div class="mb-3">
                        <label for="user_hp" class="form-label">휴대폰번호</label>
                        <input type="tel" class="form-control" id="user_hp" name="user_hp" value="<?php echo $member['user_hp'] ?? ''; ?>">
                        <div id="user_hpError" class="form-text text-danger"></div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary"><?php if($update) echo '수정'; else echo '가입';?></button>
                        <a href="<?php echo CM_URL ?>" class="btn btn-secondary">취소</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const recaptchaSiteKey = '<?php echo $recaptcha_site; ?>';

$(document).ready(function() {
	
<?php if($update){?>
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
            error: function(xhr, status, error) {
                console.error('비밀번호 확인 AJAX 오류:', error, xhr.responseText);
                alert('서버 오류가 발생했습니다.');
            }
        });
    });

    // 회원정보 수정 폼 제출
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();
        
        if ($('#user_password').val() !== $('#password_confirm').val()) {
            $('#password_confirmError').text('비밀번호가 일치하지 않습니다.');
            return;
        }

        // reCAPTCHA 토큰 가져오기
        grecaptcha.ready(function() {
            grecaptcha.execute(recaptchaSiteKey, {action: 'submit'}).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
                
                $.ajax({
                    url: $('#registerForm').attr('action'),
                    type: 'POST',
                    data: $('#registerForm').serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('회원정보가 수정되었습니다.');
                            location.href = '<?php echo CM_URL ?>';
                        } else {
                            // 오류 메시지 표시
                            if (response.field && $('#' + response.field + 'Error').length) {
                                $('#' + response.field + 'Error').text(response.message);
                            } else {
                                alert(response.message || '회원정보 수정에 실패했습니다.');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('회원정보 수정 AJAX 오류:', error, xhr.responseText);
                        alert('서버 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
                    }
                });
            });
        });
    });
<?php } else { ?>

    // 폼 제출 이벤트 처리
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();
        
        // 오류 메시지 초기화
        $('#user_idError, #user_nameError, #user_passwordError, #password_confirmError, #user_emailError, #user_hpError').text('');
        
        // 비밀번호 일치 여부 확인
        const password = $('#user_password').val();
        const passwordConfirm = $('#password_confirm').val();
        
        if (password !== passwordConfirm) {
            $('#password_confirmError').text('비밀번호가 일치하지 않습니다');
            return;
        }

        // reCAPTCHA 토큰 가져오기
        grecaptcha.ready(function() {
            grecaptcha.execute(recaptchaSiteKey, {action: 'submit'}).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
                
                // Ajax 요청 보내기
                $.ajax({
                    url: $('#registerForm').attr('action'),
                    type: 'POST',
                    data: $('#registerForm').serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('회원가입이 완료되었습니다.');
                            window.location.href = '<?php echo CM_URL?>';
                        } else {
                            // 오류 메시지 표시
                            if (response.field && $('#' + response.field + 'Error').length) {
                                $('#' + response.field + 'Error').text(response.message);
                            } else {
                                alert(response.message || '회원가입에 실패했습니다.');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('회원가입 AJAX 오류:', error, xhr.responseText);
                        alert('서버 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
                    }
                });
            });
        });
    });
    
    // 실시간 입력 검증
    $('#user_id').on('blur', function() {
        const userId = $(this).val();
        if (userId.length < 4) {
            $('#user_idError').text('아이디는 최소 4자 이상이어야 합니다');
        } else {
            $('#user_idError').text('');
        }
    });
    
    $('#user_password').on('blur', function() {
        const password = $(this).val();
        if (password.length < 8) { // 서버와 동일하게 8자로 수정
            $('#user_passwordError').text('비밀번호는 최소 8자 이상이어야 합니다');
        } else {
            $('#user_passwordError').text('');
        }
    });
    
    $('#password_confirm').on('blur', function() {
        const password = $('#user_password').val();
        const passwordConfirm = $(this).val();
        
        if (password !== passwordConfirm) {
            $('#password_confirmError').text('비밀번호가 일치하지 않습니다');
        } else {
            $('#password_confirmError').text('');
        }
    });
    
    $('#user_email').on('blur', function() {
        const email = $(this).val();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailRegex.test(email)) {
            $('#user_emailError').text('유효한 이메일 주소를 입력해주세요');
        } else {
            $('#user_emailError').text('');
        }
    });
	<?php } ?>
});
</script>