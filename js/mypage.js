//마이페이지 관련 js
$(document).ready(function() {
    var leavePasswordModal = new bootstrap.Modal(document.getElementById('leavePasswordModal'));

    $('#showLeaveModalBtn').on('click', function() {
        $('#leave_current_password').val(''); // 모달 열 때 입력 필드 초기화
        $('#leavePasswordError').text('');    // 모달 열 때 오류 메시지 초기화
        leavePasswordModal.show();
    });

    $('#leavePasswordCheckForm').on('submit', function(e) {
        e.preventDefault();
        $('#leavePasswordError').text(''); // 이전 오류 메시지 초기화

        const currentPassword = $('#leave_current_password').val();
        if (!currentPassword) {
            $('#leavePasswordError').text('비밀번호를 입력해주세요.');
            return;
        }

        $.ajax({
            url: CM.MB_URL + '/password_check.php', // password_check.php 경로
            type: 'POST',
            data: {
                user_id: user_id,
                user_password: currentPassword
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // 비밀번호 확인 성공, 회원 탈퇴 진행
                    if (confirm('정말로 회원 탈퇴를 진행하시겠습니까?\n\n이 작업은 되돌릴 수 없습니다.')) {
                        $.ajax({
                            url: CM.MB_URL + '/member_leave.php', // leave.php 경로
                            type: 'POST',
                            dataType: 'json',
                            success: function(leaveResponse) {
                                if (leaveResponse.status === 'success') {
                                    alert(leaveResponse.message);
                                    window.location.href = CM.URL; // 메인 페이지로 리다이렉션
                                } else {
                                    alert(leaveResponse.message || '회원 탈퇴 중 오류가 발생했습니다.');
                                    leavePasswordModal.hide();
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('회원 탈퇴 AJAX 오류:', error, xhr.responseText);
                                alert('회원 탈퇴 처리 중 서버 오류가 발생했습니다.');
                                leavePasswordModal.hide();
                            }
                        });
                    } else {
                        // 사용자가 탈퇴 취소
                        leavePasswordModal.hide();
                    }
                } else {
                    $('#leavePasswordError').text(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('비밀번호 확인 AJAX 오류 (회원 탈퇴):', error, xhr.responseText);
                $('#leavePasswordError').text('비밀번호 확인 중 서버 오류가 발생했습니다.');
            }
        });
    });
});