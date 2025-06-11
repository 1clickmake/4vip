//login 관련 js
$(document).ready(function() {
    // 로그인 폼 제출
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const $errorDiv = $('#loginError');
        
        $.ajax({
            url: CM.MB_URL + '/login_check.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    window.location.href = response.redirect || CM.URL;
                } else {
                    $errorDiv.removeClass('d-none').text(response.message);
                }
            },
            error: function() {
                $errorDiv.removeClass('d-none').text('서버 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
            }
        });
    });

    // 비밀번호 찾기 폼 제출
    $('#forgotPasswordSubmit').on('click', function() {
		//$('#forgotPasswordModal').modal('hide');
        const formData = $('#forgotPasswordForm').serialize();
        const $errorDiv = $('#forgotPasswordError');
        const $tempPasswordDisplay = $('#tempPasswordDisplay');

        $.ajax({
            url: CM.MB_URL + '/lost_password.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $errorDiv.addClass('d-none');
                    $tempPasswordDisplay.text('임시 비밀번호: ' + response.temp_password);
                    //$('#forgotPasswordModal').modal('hide');
                } else {
                    $errorDiv.removeClass('d-none').text(response.message);
                }
            },
            error: function() {
                $errorDiv.removeClass('d-none').text('서버 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
            }
        });
    });
});