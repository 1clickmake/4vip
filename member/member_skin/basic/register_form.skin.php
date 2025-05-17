<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
?>
<div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg p-4" style="max-width: 500px; width: 100%; border-radius: 15px;">
        <div class="card-body">
            <h3 class="text-center mb-4" style="color: #333; font-weight: 600;">회원가입</h3>
            <form id="registerForm"  method="post" enctype="multipart/form-data" autocomplete="off">
				<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                <div class="mb-3">
                    <label for="user_id" class="form-label" style="color: #555;">아이디</label>
                    <input type="text" class="form-control" id="user_id" name="user_id" placeholder="아이디를 입력하세요" required autofocus>
                    <div id="useridError" class="form-text text-danger"></div>
                </div>
				<div class="mb-3">
                    <label for="user_name" class="form-label" style="color: #555;">이름</label>
                    <input type="text" class="form-control" id="user_name" name="user_name" placeholder="이름을 입력하세요" required autofocus>
                    <div id="usernameError" class="form-text text-danger"></div>
                </div>
                <div class="mb-3">
                    <label for="user_password" class="form-label" style="color: #555;">비밀번호</label>
                    <input type="password" class="form-control" id="user_password" name="user_password" placeholder="비밀번호를 입력하세요" required>
                    <div id="passwordError" class="form-text text-danger"></div>
                </div>
                <div class="mb-3">
                    <label for="passwordConfirm" class="form-label" style="color: #555;">비밀번호 확인</label>
                    <input type="password" class="form-control" id="passwordConfirm" name="password_confirm" placeholder="비밀번호를 다시 입력하세요" required>
                    <div id="passwordConfirmError" class="form-text text-danger"></div>
                </div>
                <div class="mb-3">
                    <label for="user_email" class="form-label" style="color: #555;">이메일</label>
                    <input type="email" class="form-control" id="user_email" name="user_email" placeholder="이메일을 입력하세요" required>
                    <div id="emailError" class="form-text text-danger"></div>
                </div>
                <div class="mb-3">
                    <label for="user_hp" class="form-label" style="color: #555;">연락처</label>
                    <input type="tel" class="form-control" id="user_hp" name="user_hp" placeholder="연락처를 입력하세요 (예: 01012345678 (숫자만))">
                    <div id="phoneError" class="form-text text-danger"></div>
                </div>
				<div class="text-danger" id="generalError"></div>
                <button type="submit" class="btn btn-primary w-100" style="border-radius: 10px; padding: 10px; font-weight: 500;">가입하기</button>
            </form>
            <div class="text-center mt-3">
                <p class="mb-0" style="color: #777; font-size: 0.9rem;">이미 계정이 있으신가요? <a href="/login" class="text-decoration-none" style="color: #007bff;">로그인</a></p>
            </div>
        </div>
    </div>
</div>
<script>
const recaptchaSiteKey = '<?php echo $recaptcha_site; ?>';

$(document).ready(function() {
    // 폼 제출 이벤트 처리
    $('#registerForm').on('submit', function(e) {
        e.preventDefault(); // 기본 폼 제출 동작 방지
        
        // reCAPTCHA 토큰 가져오기
        grecaptcha.ready(function() {
            grecaptcha.execute(recaptchaSiteKey, {action: 'submit'}).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
                
                // 오류 메시지 초기화
                $('#useridError, #usernameError, #passwordError, #passwordConfirmError, #emailError, #phoneError, #generalError').text('');
                
                // 폼 데이터 수집
                const formData = $('#registerForm').serialize();
                
                // 비밀번호 일치 여부 확인
                const password = $('#user_password').val();
                const passwordConfirm = $('#passwordConfirm').val();
                
                if (password !== passwordConfirm) {
                    $('#passwordConfirmError').text('비밀번호가 일치하지 않습니다'); 
                    return;
                }
                
                // Ajax 요청 보내기
                $.ajax({
                    url: 'register_form_update.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // 회원가입 성공
                            alert(response.message);
                            window.location.href = '<?php echo CM_URL?>'; // 로그인 페이지로 리다이렉트
                        } else {
                            // 회원가입 실패 시 해당 필드에 오류 메시지 표시
                            switch (response.field) {
                                case 'user_id':
                                    $('#useridError').text(response.message);
                                    break;
                                case 'user_name':
                                    $('#usernameError').text(response.message);
                                    break;
                                case 'user_password':
                                    $('#passwordError').text(response.message);
                                    break;
                                case 'password_confirm':
                                    $('#passwordConfirmError').text(response.message);
                                    break;
                                case 'user_email':
                                    $('#emailError').text(response.message);
                                    break;
                                case 'user_hp':
                                    $('#phoneError').text(response.message);
                                    break;
                                default:
                                    $('#generalError').text(response.message);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#generalError').text('서버 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
                        console.error('Ajax 요청 오류:', error);
                    }
                });
            });
        });
    });
    
    // 실시간 입력 검증 (선택 사항)
    $('#user_id').on('blur', function() {
        const userId = $(this).val();
        if (userId.length < 4) {
            $('#useridError').text('아이디는 최소 4자 이상이어야 합니다');
        } else {
            $('#useridError').text('');
        }
    });
    
    $('#user_password').on('blur', function() {
        const password = $(this).val();
        if (password.length < 3) {
            $('#passwordError').text('비밀번호는 최소 3자 이상이어야 합니다');
        } else {
            $('#passwordError').text('');
        }
    });
    
    $('#passwordConfirm').on('blur', function() {
        const password = $('#user_password').val();
        const passwordConfirm = $(this).val();
        
        if (password !== passwordConfirm) {
            $('#passwordConfirmError').text('비밀번호가 일치하지 않습니다');
        } else {
            $('#passwordConfirmError').text('');
        }
    });
    
    $('#user_email').on('blur', function() {
        const email = $(this).val();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailRegex.test(email)) {
            $('#emailError').text('유효한 이메일 주소를 입력해주세요');
        } else {
            $('#emailError').text('');
        }
    });
});
</script>
