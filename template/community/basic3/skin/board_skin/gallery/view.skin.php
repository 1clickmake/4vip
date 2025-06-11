<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가

// Font Awesome 추가
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">';

// 코멘트 목록 가져오기
$comment_page = isset($_GET['comment_page']) ? (int)$_GET['comment_page'] : 1;
$comment_limit = 10;
$comment_offset = ($comment_page - 1) * $comment_limit;

// 전체 댓글 수 가져오기
$comment_count_sql = "SELECT COUNT(*) as cnt FROM cm_board_comment 
                     WHERE board_id = :board_id AND board_num = :board_num";
$comment_count_params = [
    ':board_id' => $boardId,
    ':board_num' => $boardNum
];
$comment_count = sql_fetch($comment_count_sql, $comment_count_params)['cnt'];

// 페이지네이션 계산
$total_pages = ceil($comment_count / $comment_limit);
$start_page = max(1, $comment_page - 2);
$end_page = min($total_pages, $comment_page + 2);

// 댓글 목록 가져오기
$comment_sql = "SELECT * FROM cm_board_comment 
                WHERE board_id = :board_id AND board_num = :board_num 
                ORDER BY comment_id DESC
                LIMIT :limit OFFSET :offset";
$comment_params = [
    ':board_id' => $boardId,
    ':board_num' => $boardNum,
    ':limit' => (int)$comment_limit,
    ':offset' => (int)$comment_offset
];

// 디버깅을 위한 로그 추가
error_log("Comment Query: " . $comment_sql);
error_log("Comment Params: " . print_r($comment_params, true));

$comments = sql_all_list($comment_sql, $comment_params);

// 디버깅을 위한 로그 추가
error_log("Comments Result: " . print_r($comments, true));

// 댓글 목록이 비어있는지 확인
if ($comments === false) {
    error_log("Error fetching comments: " . print_r(error_get_last(), true));
    $comments = [];
}
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
                    <?php foreach ($files as $file): 
                        $is_image = is_image_file($file['original_filename']);
                        $icon_class = get_file_icon_class($file['original_filename']);
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <?php if ($is_image): ?>
                                <img src="download.php?board=<?php echo $boardId;?>&file_id=<?= $file['file_id'] ?>" 
                                     class="me-2" style="width: 30px; height: 30px; object-fit: cover;">
                            <?php else: ?>
                                <i class="fas <?= $icon_class ?> me-2" style="font-size: 1.2rem;"></i>
                            <?php endif; ?>
                            <span><?= htmlspecialchars($file['original_filename']) ?> (<?= number_format($file['file_size'] / 1024, 2) ?> KB)</span>
                        </div>
                        <a href="download.php?board=<?php echo $boardId;?>&file_id=<?= $file['file_id'] ?>" class="btn btn-sm btn-primary">다운로드</a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 코멘트 섹션 -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">댓글 (<?php echo $comment_count; ?>)</h5>
        </div>
        <div class="card-body">
            <!-- 코멘트 목록 -->
            <div id="commentList">
                <?php if (!empty($comments)): ?>
                    <?php foreach ($comments as $comment): ?>
                    <div class="comment-item border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong><?= htmlspecialchars($comment['name']) ?></strong>
                                <small class="text-muted ms-2"><?= date('Y-m-d H:i', strtotime($comment['reg_date'])) ?></small>
                            </div>
                            <?php if ($is_member && ($member['user_id'] == $comment['user_id'] || $is_admin)): ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary edit-comment" 
                                        data-comment-id="<?= $comment['comment_id'] ?>">수정</button>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-comment" 
                                        data-comment-id="<?= $comment['comment_id'] ?>">삭제</button>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="comment-content" id="comment-content-<?= $comment['comment_id'] ?>">
                            <?= $comment['content'] ?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <!-- 페이지네이션 -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="댓글 페이지네이션" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($comment_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?board=<?php echo $boardId; ?>&id=<?php echo $boardNum; ?>&comment_page=<?php echo ($comment_page - 1); ?>" aria-label="이전">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?php echo ($i == $comment_page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?board=<?php echo $boardId; ?>&id=<?php echo $boardNum; ?>&comment_page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>

                            <?php if ($comment_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?board=<?php echo $boardId; ?>&id=<?php echo $boardNum; ?>&comment_page=<?php echo ($comment_page + 1); ?>" aria-label="다음">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-center text-muted">등록된 댓글이 없습니다.</p>
                <?php endif; ?>
            </div>

            <!-- 코멘트 입력 폼 -->
            <div class="comment-form mt-4">
                <form id="commentForm" action="comment_update.php" method="post">
                    <input type="hidden" name="board_id" value="<?php echo $boardId;?>">
                    <input type="hidden" name="board_num" value="<?php echo $boardNum;?>">
                    <input type="hidden" name="action" value="write">
                    <input type="hidden" name="comment_id" value="">
                    <?php if ($is_member): ?>
                        <input type="hidden" name="user_id" value="<?php echo $member['user_id'];?>">
                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($member['user_name']);?>">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($member['user_email']);?>">
                        <input type="hidden" name="password" value="<?php echo $member['user_password'];?>">
                    <?php else: ?>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="name" placeholder="이름" required>
                            </div>
                            <div class="col-md-4">
                                <input type="email" class="form-control" name="email" placeholder="이메일" required>
                            </div>
                            <div class="col-md-4">
                                <input type="password" class="form-control" name="password" placeholder="비밀번호" required>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <textarea id="commentContent" name="content" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">댓글 작성</button>
                    </div>
                </form>
            </div>
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
                        url: '<?php echo CM_URL;?>/summernote/summernote_upload.php',
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
                    var currentPage = <?php echo $comment_page; ?>;
                    // 댓글 등록 후 첫 페이지로 이동
                    window.location.href = '?board=<?php echo $boardId; ?>&id=<?php echo $boardNum; ?>&comment_page=1';
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
                url: 'comment_update.php',
                type: 'POST',
                data: {
                    action: 'delete',
                    comment_id: commentId,
                    board_id: '<?php echo $boardId;?>',
                    board_num: <?php echo $boardNum;?>
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // 현재 페이지 번호 유지
                        var currentPage = <?php echo $comment_page; ?>;
                        window.location.href = '?board=<?php echo $boardId; ?>&id=<?php echo $boardNum; ?>&comment_page=' + currentPage;
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
});

// 관리자 삭제 확인 함수
function confirmDelete(board_id, board_num) {
    if (confirm('정말 삭제하시겠습니까?')) {
        window.location.href = 'delete.php?board=' + board_id + '&id=' + board_num;
    }
}
</script>