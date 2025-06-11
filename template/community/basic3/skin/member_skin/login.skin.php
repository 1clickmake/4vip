<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
?>
<script src="<?php echo CM_URL?>/js/login.js?ver=<?php echo time();?>"></script>

<div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: 100vh; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
  <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%; border-radius: 15px;">
    <div class="card-body">
      <h3 class="text-center mb-4" style="color: #333; font-weight: 600;">로그인</h3>
      <form id="loginForm" method="post">
        <div class="mb-3">
          <label for="user_id" class="form-label" style="color: #555;">아이디</label>
          <input type="text" class="form-control" id="user_id" name="user_id" placeholder="아이디를 입력하세요" required autofocus>
        </div>
        <div class="mb-3">
          <label for="user_password" class="form-label" style="color: #555;">비밀번호</label>
          <input type="password" class="form-control" id="user_password" name="user_password" placeholder="비밀번호를 입력하세요" required>
        </div>
        <div id="loginError" class="alert alert-danger d-none mb-3"></div>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <a href="#" class="text-decoration-none" style="color: #007bff; font-size: 0.9rem;" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">비밀번호를 잊으셨나요?</a>
        </div>
		<div id="tempPasswordDisplay" class="d-block mb-3 text-danger" style="font-size: 0.9rem;"></div>
        <button type="submit" class="btn btn-primary w-100" style="border-radius: 10px; padding: 10px; font-weight: 500;">로그인</button>
      </form>
      <div class="text-center mt-3">
        <p class="mb-0" style="color: #777; font-size: 0.9rem;">계정이 없으신가요? <a href="<?php echo CM_MB_URL?>/register_form.php" class="text-decoration-none" style="color: #007bff;">회원가입</a></p>
      </div>
    </div>
  </div>

</div>

<!-- 비밀번호 찾기 모달 -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="forgotPasswordModalLabel">비밀번호 찾기</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="forgotPasswordForm">
          <div class="mb-3">
            <label for="forgot_user_id" class="form-label">아이디</label>
            <input type="text" class="form-control" id="forgot_user_id" name="user_id" placeholder="아이디를 입력하세요" required>
          </div>
          <div class="mb-3">
            <label for="forgot_user_email" class="form-label">이메일</label>
            <input type="email" class="form-control" id="forgot_user_email" name="user_email" placeholder="이메일을 입력하세요" required>
          </div>
          <div id="forgotPasswordError" class="alert alert-danger d-none"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
        <button type="button" class="btn btn-primary" id="forgotPasswordSubmit">확인</button>
      </div>
    </div>
  </div>
</div>

