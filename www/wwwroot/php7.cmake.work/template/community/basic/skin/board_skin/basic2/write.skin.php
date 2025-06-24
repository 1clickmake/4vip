<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가  
?>
<script>
const recaptchaSiteKey = '<?php echo $recaptcha_site; ?>';
const recaptcha_secret = '<?php echo $recaptcha_secret; ?>';
</script>
<script src="<?php echo CM_URL?>/js/board.write.js?ver=<?php echo time();?>"></script>

<!-- Main Content -->
<main class="main-content">
    <!-- Breadcrumb -->
    <div class="mt-5 mb-3 text-end" >
        <a href="<?php echo CM_URL?>">홈</a> <i class="bi bi-chevron-right"></i> <a href="<?php echo get_board_url('list',$boardId);?>"><?php echo $bo['board_name'];?></a> <i class="bi bi-chevron-right"></i> <?php echo $bo_title;?>
    </div>
		
	<div class="write-container">
		<form action="<?php echo $formAction;?>" method="post" enctype="multipart/form-data" id="writeForm">
			<input type="hidden" name="board_id" value="<?php echo $boardId;?>">
			<input type="hidden" name="board_num" value="<?php echo $boardNum ?? '';?>">
			<input type="hidden" name="parent_num" value="<?php echo $_GET['parent'] ?? '';?>">
			<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
			<input type="hidden" name="user_id" value="<?php echo htmlspecialchars($member['user_id'] ?? ''); ?>">
			<?php if ($is_member): // 회원인 경우 이메일, 이름, 비밀번호를 hidden으로 처리 ?>
				<input type="hidden" name="email" value="<?php echo htmlspecialchars($member['user_email'] ?? ''); ?>">
				<input type="hidden" name="name" value="<?php echo htmlspecialchars($member['user_name'] ?? ''); ?>">
				<input type="hidden" name="password" value="<?php echo isset($member['user_password']) ? $member['user_password'] : ''; ?>">
			<?php else: // 비회원인 경우 입력 폼 표시 ?>
			
				 
					
				<div class="form-group">
					<label for="email" class="form-label">이메일</label>
					<input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($write['email'] ?? ''); ?>" required>
				</div>
				
				<div class="form-group">
					<label for="name" class="form-label">이름</label>
					<input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($write['name'] ?? ''); ?>" required>
				</div>
				
				<div class="form-group">
					<label for="password" class="form-label"><?php echo isset($write) ? '비밀번호 (변경시에만 입력)' : '비밀번호'; ?></label>
					<input type="password" class="form-control" id="password" name="password" <?php echo isset($write) ? '' : 'required'; ?>>
					<?php if (isset($write)): ?>
					<small class="text-muted">비밀번호를 변경하지 않으려면 비워두세요.</small>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			
			<!-- Category -->
			<?php if(isset($bo['board_category']) && $bo['board_category']){?>
					<div class="form-group">
						<label class="form-label">카테고리 <span class="required">*</span></label>
						<select name="category" class="form-select" required>
							<option value="">카테고리 선택</option>
							<?php if($is_admin){?><option value="공지">공지</option><?php } ?>
							<?php
							$category_lines = explode("\n", str_replace("\r\n", "\n", $bo['board_category']));
							foreach ($category_lines as $line) {
								$trimmed_line = trim($line);
								if (!empty($trimmed_line)) {
							?>
								<option value="<?php echo htmlspecialchars($trimmed_line);?>" <?php if(isset($write['category']) && $write['category'] === $trimmed_line) echo 'selected';?> ><?php echo htmlspecialchars($trimmed_line);?></option>
							<?php }
							} ?>

						</select>
					</div>
			<?php } ?>
					
			<div class="form-group">
				<label for="title" class="form-label">제목</label>
				<input type="text" class="form-input" id="title" name="title" value="<?php echo htmlspecialchars($write['title'] ?? ''); ?>" required>
			</div>
			
			<div class="form-group">
				<label for="content" class="form-label">내용</label>
				<textarea id="summernote" name="content"><?php echo $write['content'] ?? ''; ?></textarea>
			</div>
			
			<!-- 기존 첨부파일 목록 -->
			<?php if ($currentFilename == "edit" && !empty($files)): ?>
			<div class="mb-4">
				<label class="form-label">📎 기존 첨부파일</label>
				<div class="row">
					<?php foreach ($files as $file): 
						$is_image = is_image_file($file['original_filename']);
						$icon_class = get_file_icon_class($file['original_filename']);
					?>
					<div class="col-md-6 col-lg-4 mb-3">
						<div class="card shadow-sm border">
							<div class="card-body d-flex justify-content-between align-items-center">
								<div class="d-flex align-items-center">
									<?php if ($is_image): ?>
										<img src="download.php?board=<?php echo $boardId;?>&file_id=<?= $file['file_id'] ?>" 
											 class="me-2" style="width: 30px; height: 30px; object-fit: cover;">
									<?php else: ?>
										<i class="fas <?= $icon_class ?> me-2" style="font-size: 1.2rem;"></i>
									<?php endif; ?>
									<div>
										<span class="text-muted"><?php echo htmlspecialchars($file['original_filename']); ?></span>
										<small class="d-block text-muted"><?php echo number_format($file['file_size'] / 1024, 2); ?> KB</small>
									</div>
								</div>
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="delete_<?php echo $file['file_id']; ?>" name="delete_files[]" value="<?php echo $file['file_id']; ?>">
									<label class="form-check-label text-danger small" for="delete_<?php echo $file['file_id']; ?>">삭제</label>
								</div>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>

			<!-- 새 첨부파일 -->
			
			
			
			<div class="form-group">
						<label class="form-label">첨부파일</label>
						<div class="file-upload" onclick="document.getElementById('fileInput').click()">
							<div class="file-upload-icon">📁</div>
							<div class="file-upload-text">파일을 선택하거나 여기로 드래그하세요</div>
							<div class="file-upload-hint">최대 10MB, jpg, png, gif, pdf, doc, hwp 파일만 업로드 가능</div>
							<small class="text-muted">여러 파일을 선택하려면 Ctrl(Windows) 또는 Command(Mac) 키를 누른 상태에서 파일을 선택하세요.</small>
						</div>
						<input type="file" id="fileInput" class="file-input" name="files[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.hwp">
						<div id="filePreview" class="row g-2">
					<!-- 파일 미리보기가 여기에 표시됩니다 -->
				</div>
			</div>
					
			<!-- Post Options -->
					<div class="post-options">
						<div class="options-title">게시글 옵션</div>
						<div class="option-group">
							<?php if($is_admin){ ?>
							<div class="option-item">
								<input type="checkbox" name='notice_chk' id="noticePost" class="checkbox" <?php if(isset($write['notice_chk']) && $write['notice_chk'] == 1) echo "checked";?>>
								<label for="noticePost">공지글</label>
							</div>
							<?php } ?>
							<div class="option-item">
								<input type="checkbox" name='secret_chk' id="secretPost" class="checkbox" <?php if(isset($write['secret_chk']) && $write['secret_chk'] == 1) echo "checked";?>>
								<label for="secretPost">비밀글</label>
							</div>
							<div class="option-item">
								<input type="checkbox" name='reply_chk' id="replyPost" class="checkbox" <?php if(isset($write['reply_chk']) && $write['reply_chk'] == 1) echo "checked";?>>
								<label for="replyPost">답변글 허용</label>
							</div>
							<div class="option-item">
								<input type="checkbox" name='comment_chk' id="commentPost" class="checkbox" <?php if(isset($write['comment_chk']) && $write['comment_chk'] == 1) echo "checked";?>>
								<label for="commentPost">댓글(코멘트) 허용</label>
							</div>
						</div>
					</div>		
					
			
			<div class="d-grid gap-2 d-md-flex justify-content-md-end">
				<button type="submit" class="btn btn-success"><?php echo $writeBtn;?></button>
				<a href="<?php echo get_board_url('list',$boardId);?>" class="btn btn-secondary">취소</a>
			</div>
		</form>
	</div>
</main>