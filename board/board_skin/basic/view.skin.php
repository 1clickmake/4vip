<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
?>

<div class="container mt-5">
    <h2 class="mb-4"><?php echo $bo_title;?></h2>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><?= htmlspecialchars($view['title']) ?></h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-0">작성자: <?= htmlspecialchars($view['name']) ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">작성일: <?= date('Y-m-d H:i', strtotime($view['reg_date'])) ?></p>
                </div>
            </div>
            
            <div class="content-area border-top pt-3 mb-3">
                <?= $view['content'] ?>
            </div>
            
            <?php if (!empty($files)): ?>
            <div class="file-area border-top pt-3">
                <h6>첨부파일</h6>
                <ul class="list-group">
                    <?php foreach ($files as $file): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><?= htmlspecialchars($file['original_filename']) ?> (<?= number_format($file['file_size'] / 1024, 2) ?> KB)</span>
                        <a href="download.php?board=<?php echo $boardId;?>&file_id=<?= $file['file_id'] ?>" class="btn btn-sm btn-primary">다운로드</a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="d-flex justify-content-between mb-4">
        <div>
            <a href="<?php echo get_board_url('list',$boardId);?>" class="btn btn-secondary">목록</a>
        </div>
        <div>
            <?php
            // 수정 버튼: 회원(본인) 또는 관리자는 모달 없이 바로 이동
            if ($is_member && ($member['user_id'] == $view['user_id'] || $is_admin)) {
                echo '<a href="edit.php?board=' . $boardId . '&id=' . $boardNum . '" class="btn btn-primary">수정</a>';
            } else {
                echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#passwordModal" data-action="edit">수정</button>';
            }
            ?>
            <?php
            // 삭제 버튼: 관리자는 모달 없이 confirm, 나머지는 모달 표시
            if ($is_admin) {
                echo '<button type="button" class="btn btn-danger" onclick="confirmDelete(\'' . $boardId . '\', ' . $boardNum . ')">삭제</button>';
            } else {
                echo '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#passwordModal" data-action="delete">삭제</button>';
            }
            ?>
            <a href="<?php echo get_board_url('write',$boardId);?>" class="btn btn-dark">글쓰기</a>
        </div>
    </div>
</div>

<!-- 비밀번호 확인 모달 -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">비밀번호 확인</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>게시글 접근을 위해 정보를 입력해주세요.</p>
                <?php if ($is_member) { ?>
                    <!-- 회원: 이메일 hidden 처리 -->
                    <input type="hidden" id="checkEmail" value="<?php echo htmlspecialchars($member['user_email'] ?? ''); ?>">
                <?php } else { ?>
                    <!-- 비회원: 이메일 입력창 표시 -->
                    <div class="mb-3">
                        <label for="checkEmail" class="form-label">이메일</label>
                        <input type="email" id="checkEmail" class="form-control" required>
                        <div id="emailError" class="form-text text-danger"></div>
                    </div>
                <?php } ?>
                <div class="mb-3">
                    <label for="checkPassword" class="form-label">비밀번호</label>
                    <input type="password" id="checkPassword" class="form-control" required>
                    <div id="passwordError" class="form-text text-danger"></div>
                </div>
                <input type="hidden" id="actionType" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                <button type="button" class="btn btn-primary" id="confirmPassword">확인</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 모달이 열릴 때 액션 타입 설정
    $('#passwordModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var action = button.data('action');
        $('#actionType').val(action);
        $('#checkPassword').val(''); // 비밀번호 입력창 초기화
        <?php if (!$is_member) { ?>
        $('#checkEmail').val(''); // 비회원: 이메일 입력창 초기화
        <?php } ?>
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
        var board_id = '<?php echo $boardId; ?>';
        var board_num = <?php echo $boardNum; ?>;
        
        // 입력 검증
        <?php if (!$is_member) { ?>
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
        <?php } ?>
        if (!password.trim()) {
            $('#passwordError').text('비밀번호를 입력해주세요.');
            $('#checkPassword').focus();
            return;
        }
        
        // 비밀번호 확인 요청
        $.ajax({
            url: 'check_password.php',
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
                        window.location.href = 'edit.php?board=' + board_id + '&id=' + board_num;
                    } else {
                        if (confirm('정말 삭제하시겠습니까?')) {
                            window.location.href = 'delete.php?board=' + board_id + '&id=' + board_num;
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
});

// 관리자 삭제 확인 함수
function confirmDelete(board_id, board_num) {
    if (confirm('정말 삭제하시겠습니까?')) {
        window.location.href = 'delete.php?board=' + board_id + '&id=' + board_num;
    }
}
</script>