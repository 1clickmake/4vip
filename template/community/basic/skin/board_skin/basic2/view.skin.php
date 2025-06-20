<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
?>
<!-- PHP 변수 주입 -->
<script>
const boardId 	= '<?php echo $boardId; ?>';
const boardNum	= <?php echo $boardNum; ?>;
const comment_page	= <?php echo $comment_page; ?>;
const is_member	= <?php echo $is_member ? 'true' : 'false'; ?>;
</script>
<script src="<?php echo CM_URL?>/js/board.view.js?ver=<?php echo time();?>"></script>

<main class="main-content">
        <!-- Breadcrumb -->
        <div class="mt-5 mb-3 text-end" >
            <a href="<?php echo CM_URL?>">홈</a> <i class="bi bi-chevron-right"></i> <a href="<?php echo get_board_url('list',$boardId);?>"><?php echo $bo['board_name'];?></a> <i class="bi bi-chevron-right"></i> <?php echo $bo_title;?>
        </div>
		
		<!-- Post Container -->
        <div class="post-container">
            <!-- Post Header -->
            <div class="post-header">
                <h1 class="post-title">
                    <?= htmlspecialchars($view['title']) ?>
                    <span class="post-status status-hot">HOT</span>
                </h1>
                <div class="post-meta">
                    <div class="post-info">
                        <span>작성자: <strong><?= htmlspecialchars($view['name'] ?? '알 수 없음') ?></strong></span>
                        <span>작성일: <?= date('Y-m-d H:i', strtotime($view['reg_date'])) ?></span>
                    </div>
                    <div class="post-stats">
                        <span>조회: <?= (int)($view['view_count'] ?? 0) ?></span>
                        <span>댓글: <?= (int)($comment_count ?? 0) ?></span>
                        <span>추천: <span id="likeCountMain"><?= (int)($view['good'] ?? 0) ?></span></span>
                    </div>
                </div>
            </div>
			<!-- Post Content -->
            <div class="post-content">
				<?= $view['content'] ?>
            </div>
    

            
            <?php if (!empty($files)): ?>
            <div class="file-area border-top p-3">
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
			
			<!-- Post Actions -->
            <div class="post-actions">
                <div class="like-section">
                    <button class="like-btn" id="goodBtn">
                        <span>👍</span>
                        <span id="likeCount"><?= (int)($view['good'] ?? 0) ?></span>
                    </button>
                    <button class="like-btn" id="badBtn">
                        <span>👎</span>
                        <span id="dislikeCount"><?= (int)($view['bad'] ?? 0) ?></span>
                    </button>
                </div>
                <div class="post-controls">
                    <a href="<?php echo get_board_url('list',$boardId);?>" class="btn btn-secondary">목록</a>
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
					<?php if($view['notice_chk'] == 0 && $view['reply_chk'] == 1){?>
					<a href="<?php echo get_board_url('write',$boardId, $boardNum);?>&parent=<?php echo $boardNum;?>" class="btn btn-info">답변글</a>
					<?php } ?>
                </div>
            </div>
        </div>


		<!-- 코멘트 섹션 { -->
		<?php 
		if(isset($view['comment_chk']) && $view['comment_chk'] === 1){
			include_once('comment.skin.php');
		} 
		?>
		<!-- } 코멘트 섹션 끝-->
		
</main>

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


