<?php
include_once './_common.php';
$cm_title = "기본 환경설정";
include_once CM_ADMIN_PATH.'/admin.head.php';
?>

<!-- Main Content -->
<div class="main-content shifted" id="mainContent">
	<div class="container-fluid">
		<!-- 헤더 카드 -->
            <div class="card shadow-sm mb-4 border-0 card-move">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="card-title mb-1 text-primary">
                                <i class="bi bi-gear me-2"></i><?php echo $cm_title;?>
                            </h2>
                            <p class="card-text text-muted mb-0">홈페이지 기본 환경설정</p>
                        </div>
                    </div>
                </div>
            </div>
		
		<div class="row justify-content-center">
			<div class="col">
				<div class="card chart-card shadow-lg">
					<div class="card-header">
						<h5 class="card-title mb-0">
							<i class="fas fa-wrench me-2"></i>
							시스템 기본 설정
						</h5>
					</div>
					<div class="card-body p-4">
						<form action="./config_form_update.php" method="post">
							<!-- CSRF 토큰 (예시) -->
							<!-- <input type="hidden" name="csrf_token" value="<?php // echo htmlspecialchars($_SESSION['csrf_token']); ?>"> -->
							<!-- 사이트 정보 섹션 -->
							<div class="mb-5">
								<h6 class="text-muted fw-bold mb-3">
									<i class="fas fa-globe me-2"></i>
									사이트 정보
								</h6>
								<div class="mb-4">
									<label for="siteTitle" class="form-label fw-semibold">
										<i class="fas fa-heading me-2 text-primary"></i>
										홈페이지 제목
									</label>
									<div class="input-group">
										<span class="input-group-text bg-light">
											<i class="fas fa-home"></i>
										</span>
										<input type="text" class="form-control" id="siteTitle" name="site_title" 
											   placeholder="홈페이지 제목을 입력하세요" 
											   value="<?php echo htmlspecialchars($config['site_title'] ?? '');?>" required>
									</div>
								</div>
								
								<div class="mb-4">
									<label for="adminEmail" class="form-label fw-semibold">
										<i class="fas fa-envelope me-2 text-success"></i>
										관리자 이메일
									</label>
									<div class="input-group">
										<span class="input-group-text bg-light">
											<i class="fas fa-at"></i>
										</span>
										<input type="email" class="form-control" id="adminEmail" name="admin_email" 
											   placeholder="관리자 이메일을 입력하세요" 
											   value="<?php echo htmlspecialchars($config['admin_email'] ?? '');?>" required>
									</div>
								</div>
								
								<div class="mb-4">
									<label for="contactNumber" class="form-label fw-semibold">
										<i class="fas fa-phone me-2 text-info"></i>
										연락처
									</label>
									<div class="input-group">
										<span class="input-group-text bg-light">
											<i class="fas fa-mobile-alt"></i>
										</span>
										<input type="tel" class="form-control" id="contactNumber" name="contact_number" 
											   value="<?php echo htmlspecialchars($config['contact_number'] ?? '');?>" 
											   placeholder="연락처를 입력하세요 (예: 010-1234-5678)">
									</div>
								</div>
							</div>

							<!-- 보안 설정 섹션 -->
							<div class="mb-5">
								<h6 class="text-muted fw-bold mb-3">
									<i class="fas fa-shield-alt me-2"></i>
									보안 설정
								</h6>
								<div class="mb-4">
									<label for="ip_access" class="form-label fw-semibold">
										<i class="fas fa-check-circle me-2 text-success"></i>
										접근가능 IP
									</label>
									<div class="alert alert-info py-2 mb-2" role="alert">
										<i class="fas fa-info-circle me-2"></i>
										<small>입력된 IP의 컴퓨터만 접근가능합니다. 123.123.+ 형식도 가능하며, 엔터로 구분해주세요.</small>
									</div>
									<div class="input-group">
										<span class="input-group-text bg-light">
											<i class="fas fa-unlock"></i>
										</span>
										<textarea class="form-control" id="ip_access" name="ip_access" rows="4" 
												  placeholder="예:&#10;192.168.1.1&#10;123.123.+&#10;10.0.0.1"><?php echo htmlspecialchars($config['ip_access'] ?? '');?></textarea>
									</div>
								</div>
								
								<div class="mb-4">
									<label for="ip_block" class="form-label fw-semibold">
										<i class="fas fa-times-circle me-2 text-danger"></i>
										접근차단 IP
									</label>
									<div class="alert alert-warning py-2 mb-2" role="alert">
										<i class="fas fa-exclamation-triangle me-2"></i>
										<small>입력된 IP의 컴퓨터는 접근이 차단됩니다. 123.123.+ 형식도 가능하며, 엔터로 구분해주세요.</small>
									</div>
									<div class="input-group">
										<span class="input-group-text bg-light">
											<i class="fas fa-ban"></i>
										</span>
										<textarea class="form-control" id="ip_block" name="ip_block" rows="4" 
												  placeholder="예:&#10;192.168.1.100&#10;123.123.+&#10;10.0.0.100"><?php echo htmlspecialchars($config['ip_block'] ?? '');?></textarea>
									</div>
								</div>
							</div>

							<!-- reCAPTCHA 설정 섹션 -->
							<div class="mb-5">
								<h6 class="text-muted fw-bold mb-3">
									<i class="fab fa-google me-2"></i>
									Google reCAPTCHA 설정
								</h6>
								<div class="alert alert-secondary py-2 mb-3" role="alert">
									<i class="fas fa-robot me-2"></i>
									<small>Google reCAPTCHA v3을 사용하여 스팸을 방지할 수 있습니다.</small>
								</div>
								
								<div class="mb-4">
									<label for="recaptcha_site_key" class="form-label fw-semibold">
										<i class="fas fa-key me-2 text-warning"></i>
										Site Key
									</label>
									<div class="input-group">
										<span class="input-group-text bg-light">
											<i class="fas fa-globe"></i>
										</span>
										<input type="text" class="form-control" id="recaptcha_site_key" name="recaptcha_site_key" 
											   value="<?php echo htmlspecialchars($config['recaptcha_site_key'] ?? '');?>" 
											   placeholder="Google reCAPTCHA Site Key를 입력하세요">
									</div>
								</div>
								
								<div class="mb-4">
									<label for="recaptcha_secret_key" class="form-label fw-semibold">
										<i class="fas fa-lock me-2 text-danger"></i>
										Secret Key
									</label>
									<div class="input-group">
										<span class="input-group-text bg-light">
											<i class="fas fa-shield-alt"></i>
										</span>
										<input type="password" class="form-control" id="recaptcha_secret_key" name="recaptcha_secret_key" 
											   value="<?php echo htmlspecialchars($config['recaptcha_secret_key'] ?? '');?>" 
											   placeholder="Google reCAPTCHA Secret Key를 입력하세요">
										<button class="btn btn-outline-secondary" type="button" id="togglePassword">
											<i class="fas fa-eye"></i>
										</button>
									</div>
								</div>
							</div>

							<!-- 저장 버튼 -->
							<div class="d-grid gap-2 d-md-flex justify-content-md-end">
								<button type="submit" class="btn btn-primary btn-lg px-5">
									<i class="fas fa-save me-2"></i>
									설정 저장
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
// 비밀번호 보기/숨기기 토글
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordField = document.getElementById('recaptcha_secret_key');
    const toggleIcon = this.querySelector('i');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
});

// 폼 유효성 검사 시각적 피드백
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('필수 입력 항목을 모두 입력해주세요.');
    }
});

// 실시간 유효성 검사
document.querySelectorAll('input[required]').forEach(input => {
    input.addEventListener('blur', function() {
        if (this.value.trim()) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        }
    });
});
</script>

<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>