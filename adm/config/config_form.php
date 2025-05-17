<?php
include_once './_common.php';
$cm_title = "기본 환경설정";
include_once CM_ADMIN_PATH.'/admin.head.php';
?>

    <!-- Main Content -->
    <div class="main-content shifted" id="mainContent">
       <div class="container-fluid">
		  <h2 class="mb-4"><?php echo $cm_title;?></h2>
		  <form action="./config_form_update.php" method="post">
			<div class="mb-3">
			  <label for="siteTitle" class="form-label">홈페이지 제목</label>
			  <input type="text" class="form-control" id="siteTitle" name="site_title" placeholder="홈페이지 제목을 입력하세요" value="<?php echo $config['site_title'];?>" required>
			</div>
			<div class="mb-3">
			  <label for="adminEmail" class="form-label">관리자 이메일</label>
			  <input type="email" class="form-control" id="adminEmail" name="admin_email" placeholder="관리자 이메일을 입력하세요" value="<?php echo $config['admin_email'];?>" required>
			</div>
			<div class="mb-3">
			  <label for="contactNumber" class="form-label">연락처</label>
			  <input type="tel" class="form-control" id="contactNumber" name="contact_number" value="<?php echo $config['contact_number'];?>" placeholder="연락처를 입력하세요 (예: 010-1234-5678)">
			</div>
			<div class="mb-3">
			  <label for="recaptcha_site_key" class="form-label">구글 reCAPTCHA Site key(reCAPTCHA V3)</label>
			  <input type="tel" class="form-control" id="recaptcha_site_key" name="cf_recaptcha_site_key" value="<?php echo $config['recaptcha_site_key'] ?? '';?>" placeholder="">
			</div>
			<div class="mb-3">
			  <label for="recaptcha_secret_key" class="form-label">구글 reCAPTCHA Secret key(reCAPTCHA V3)</label>
			  <input type="tel" class="form-control" id="recaptcha_secret_key" name="recaptcha_secret_key" value="<?php echo $config['recaptcha_secret_key'] ?? '';?>" placeholder="">
			</div>
			<button type="submit" class="btn btn-primary">저장</button>
		  </form>
		</div>


    </div>

    
<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>