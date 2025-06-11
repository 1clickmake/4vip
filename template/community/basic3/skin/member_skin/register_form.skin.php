<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
?>
<script>
const recaptchaSiteKey = '<?php echo $recaptcha_site; ?>';
const registerUpdate = '<?php echo $update; ?>';
const user_id = '<?php echo $member['user_id'] ?? '';?>';
</script>
<script src="<?php echo CM_URL?>/js/register.js?ver=<?php echo time();?>"></script>

<main class="main-content">
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
</main>


