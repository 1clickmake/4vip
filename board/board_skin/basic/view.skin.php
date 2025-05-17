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
                            <a href="download.php?board=<?php echo $board;?>&file_id=<?= $file['file_id'] ?>" class="btn btn-sm btn-primary">다운로드</a>
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#passwordModal" data-action="edit">수정</button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#passwordModal" data-action="delete">삭제</button>
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
                    <p>게시글 비밀번호를 입력해주세요.</p>
                    <input type="password" id="checkPassword" class="form-control" required>
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
                
                // 모달 제목 변경
                if (action === 'edit') {
                    $('#passwordModalLabel').text('수정을 위한 비밀번호 확인');
                } else {
                    $('#passwordModalLabel').text('삭제를 위한 비밀번호 확인');
                }
            });
            
            // 비밀번호 확인 버튼 클릭
            $('#confirmPassword').click(function() {
                var password = $('#checkPassword').val();
                var action = $('#actionType').val();
				var board_id = '<?php echo $boardId; ?>';
                var board_num = <?php echo $boardNum; ?>;
                
                // 비밀번호 빈 값 체크
                if (!password.trim()) {
                    alert('비밀번호를 입력해주세요.');
                    return;
                }
                
                // 비밀번호 확인 요청
                $.ajax({
                    url: 'check_password.php',
                    type: 'POST',
                    data: {
						board_id: board_id,
                        board_num: board_num,
                        password: password
                    },
                    success: function(response) {
                        if (response === 'success') {
                            if (action === 'edit') {
                                window.location.href = 'edit.php?board=' + board_id + '&id=' + board_num;
                            } else {
                                if (confirm('정말 삭제하시겠습니까?')) {
                                    window.location.href = 'delete.php?board=' + board_id + '&id=' + board_num;
                                }
                            }
                        } else {
                            alert('비밀번호가 일치하지 않습니다.--'+response);
                        }
                    },
                    error: function() {
                        alert('오류가 발생했습니다. 다시 시도해주세요.');
                    }
                });
            });
        });
    </script>

