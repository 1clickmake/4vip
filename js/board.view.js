//board view 관련 js
$(document).ready(function() {
    // 모달이 열릴 때 액션 타입 설정
    $('#passwordModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var action = button.data('action');
        $('#actionType').val(action);
        $('#checkPassword').val(''); // 비밀번호 입력창 초기화
        if (!is_member) { 
			$('#checkEmail').val(''); // 비회원: 이메일 입력창 초기화
        }
        $('#emailError, #passwordError').text(''); // 오류 메시지 초기화
        
        // 모달 제목 변경
        if (action === 'edit') {
            $('#passwordModalLabel').text('수정을 위한 정보 확인');
        } else {
            $('#passwordModalLabel').text('삭제를 위한 정보 확인');
        }
    });
    
    // 비밀번호 확인 버튼 클릭
    $('#confirmPassword').on('click', function() {
        var email = $('#checkEmail').val();
        var password = $('#checkPassword').val();
        var action = $('#actionType').val();
        var board_id = boardId;
        var board_num = boardNum;
        
        // 입력 검증
        if (!is_member) { 
			if (!email.trim()) {
				$('#emailError').text('이메일을 입력해주세요.');
				$('#checkEmail').focus();
				return;
			}
			const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			if (!emailRegex.test(email)) {
				$('#emailError').text('유효한 이메일 주소를 입력해주세요.');
				$('#checkEmail').focus();
				return;
			}
        }
        if (!password.trim()) {
            $('#passwordError').text('비밀번호를 입력해주세요.');
            $('#checkPassword').focus();
            return;
        }
        
        // 비밀번호 확인 요청
        $.ajax({
            url: CM.BOARD_URL + '/check_password.php',
            type: 'POST',
            data: {
                board_id: board_id,
                board_num: board_num,
                email: email,
                password: password
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#passwordModal').modal('hide');
                    if (action === 'edit') {
                        window.location.href = CM.BOARD_URL + '/edit.php?board=' + board_id + '&id=' + board_num;
                    } else {
                        if (confirm('정말 삭제하시겠습니까?')) {
                            window.location.href = CM.BOARD_URL + '/delete.php?board=' + board_id + '&id=' + board_num;
                        }
                    }
                } else {
                    if (response.field === 'email') {
                        $('#emailError').text(response.message);
                        $('#checkEmail').focus();
                    } else {
                        $('#passwordError').text(response.message || '비밀번호가 일치하지 않습니다.');
                        $('#checkPassword').focus();
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('비밀번호 확인 오류:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                $('#passwordError').text('서버 오류가 발생했습니다. 다시 시도해주세요.');
            }
        });
    });

    // Summernote 초기화
    $('#commentContent').summernote({
        height: 150,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onImageUpload: function(files) {
                if (files.length > 0) {
                    var file = files[0];
                    var data = new FormData();
                    data.append('image', file);
                    data.append('board_id', $('input[name="board_id"]').val());

                    $.ajax({
                        url: CM.LIB_URL + '/summernote_upload.php',
                        method: 'POST',
                        data: data,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.url) {
                                $('#commentContent').summernote('insertImage', response.url);
                            } else {
                                alert('이미지 업로드 실패: ' + (response.error || '알 수 없는 오류'));
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            alert('이미지 업로드 실패: ' + textStatus + ' (' + errorThrown + ')');
                        }
                    });
                }
            }
        }
    });

    // 코멘트 수정
    $('.edit-comment').on('click', function() {
        var commentId = $(this).data('comment-id');
        var content = $('#comment-content-' + commentId).html();
        $('#commentContent').summernote('code', content);
        $('input[name="comment_id"]').val(commentId);
        $('input[name="action"]').val('edit');
        $('button[type="submit"]').text('댓글 수정');
        $('html, body').animate({ scrollTop: $('#commentForm').offset().top }, 500);
    });

    // 코멘트 폼 제출
    $('#commentForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('content', $('#commentContent').summernote('code'));

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
					
                    // 현재 페이지 번호 가져오기
                    //var currentPage = comment_page;
                    // 댓글 등록 후 첫 페이지로 이동
					var timestamp = new Date().getTime();
					var targetUrl = CM.BOARD_URL + '/view.php?board=' + boardId + '&id=' + boardNum + '&comment_page=1&_t=' + timestamp + '#cmtId' + response.comment_id;
					//console.log('페이지 이동 시도:', targetUrl);
					window.location.href = targetUrl;

                } else {
                    alert(response.message || '댓글 등록에 실패했습니다.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('서버 오류가 발생했습니다.');
            }
        });
    });

    // 폼 초기화 함수 추가
    function resetCommentForm() {
        $('#commentContent').summernote('code', '');
        $('input[name="comment_id"]').val('');
        $('input[name="action"]').val('write');
        $('button[type="submit"]').text('댓글 작성');
    }

    // 새 댓글 작성 시 폼 초기화
    $('#commentContent').on('focus', function() {
        if ($('input[name="action"]').val() === 'edit') {
            resetCommentForm();
        }
    });

    // 코멘트 삭제
    $('.delete-comment').on('click', function() {
        if (confirm('댓글을 삭제하시겠습니까?')) {
            var commentId = $(this).data('comment-id');
            $.ajax({
                url: CM.BOARD_URL + '/comment_update.php',
                type: 'POST',
                data: {
                    action: 'delete',
                    comment_id: commentId,
                    board_id: boardId,
                    board_num: boardNum
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // 현재 페이지 번호 유지
                        var currentPage = comment_page;
                        window.location.href = CM.BOARD_URL + '/view.php?board=' + boardId + '&id=' + boardNum + '&comment_page=' + currentPage;
                    } else {
                        alert(response.message || '댓글 삭제에 실패했습니다.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('서버 오류가 발생했습니다.');
                }
            });
        }
    });

    // 좋아요/싫어요 버튼 이벤트
    function handleVote(action) {
        $.ajax({
            url: CM.BOARD_URL + '/good_bad.php', // good_bad.php 경로 확인
            type: 'POST',
            data: {
                board_id: boardId,
                board_num: boardNum,
                action: action
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#likeCount').text(response.good);
                    $('#likeCountMain').text(response.good); // 상단 추천수도 업데이트
                    $('#dislikeCount').text(response.bad);
                    // 버튼 비활성화 또는 시각적 피드백 추가 가능
                    // 예: $('#goodBtn, #badBtn').prop('disabled', true);
                } else {
                    alert(response.message || '오류가 발생했습니다.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Vote Error:', { status: status, error: error, responseText: xhr.responseText });
                alert('요청 처리 중 오류가 발생했습니다.');
            }
        });
    }

    $('#goodBtn').on('click', function() {
        handleVote('good');
    });

    $('#badBtn').on('click', function() {
        handleVote('bad');
    });


});

// 관리자 삭제 확인 함수
function confirmDelete(board_id, board_num) {
    if (confirm('정말 삭제하시겠습니까?')) {
        window.location.href = CM.BOARD_URL + '/delete.php?board=' + board_id + '&id=' + board_num;
    }
}