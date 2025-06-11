<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
?>
<script>
const user_id = '<?php echo $member['user_id'] ?>';
</script>
<script src="<?php echo CM_URL?>/js/mypage.js?ver=<?php echo time();?>"></script>
<!-- Main Content -->
    <main class="main-content container">
		<div class="row">
			<div class="col-12 col-md-3 mb-3">
				<!-- Sidebar -->
				<aside class="sidebar">
					<div class="user-profile">
						<div class="profile-image">👤</div>
						<div class="user-name"><?php echo $member['user_name'];?></div>
						<div class="user-grade">LV.<?php echo $member['user_lv'];?></div>
					</div>
					<ul class="p-0">
						<li class="menu-item">
							<a href="./mypage.php" class="menu-link">개인정보</a>
						</li>
						<!--
						<li class="menu-item">
							<a href="product_order_list.html" class="menu-link">주문 내역</a>
						</li>
						<li class="menu-item">
							<a href="point.html" class="menu-link">포인트 내역</a>
						</li>
						<li class="menu-item">
							<a href="#" class="menu-link">찜한 상품</a>
						</li>
						<li class="menu-item">
							<a href="#" class="menu-link">리뷰 관리</a>
						</li>
						<li class="menu-item">
							<a href="#" class="menu-link">1:1 문의</a>
						</li>
						<li class="menu-item">
							<a href="#" class="menu-link">배송지 관리</a>
						</li>
						-->
					</ul>
				</aside>
			</div>
			
			<div class="col-12 col-md-9">
				<!-- Content Area -->
				<div class="content-area">
					<h1 class="content-title">개인정보</h1>
					
					<div class="d-flex text-body-secondary pt-3"> 
						<div class="pb-3 mb-0 small lh-sm border-bottom w-100"> 
							<div class="d-flex justify-content-between"> 
								<strong class="text-gray-dark">ID</strong> 
								<a href="#"></a> 
							</div> 
							<span class="d-block"><?php echo $member['user_id'];?></span> 
						</div> 
					</div>
					
					<div class="d-flex text-body-secondary pt-3"> 
						<div class="pb-3 mb-0 small lh-sm border-bottom w-100"> 
							<div class="d-flex justify-content-between"> 
								<strong class="text-gray-dark">Level</strong> 
								<a href="#"></a> 
							</div> 
							<span class="d-block">Lv.<?php echo $member['user_lv'];?></span> 
						</div> 
					</div>
					
					<div class="d-flex text-body-secondary pt-3"> 
						<div class="pb-3 mb-0 small lh-sm border-bottom w-100"> 
							<div class="d-flex justify-content-between"> 
								<strong class="text-gray-dark">Name</strong> 
								<a href="#"></a> 
							</div> 
							<span class="d-block"><?php echo $member['user_name'];?></span> 
						</div> 
					</div>
					
					<div class="d-flex text-body-secondary pt-3"> 
						<div class="pb-3 mb-0 small lh-sm border-bottom w-100"> 
							<div class="d-flex justify-content-between"> 
								<strong class="text-gray-dark">Email</strong> 
								<a href="#"></a> 
							</div> 
							<span class="d-block"><?php echo $member['user_email'];?></span> 
						</div> 
					</div>
					
					<div class="d-flex text-body-secondary pt-3"> 
						<div class="pb-3 mb-0 small lh-sm border-bottom w-100"> 
							<div class="d-flex justify-content-between"> 
								<strong class="text-gray-dark">HP</strong> 
								<a href="#"></a> 
							</div> 
							<span class="d-block"><?php echo $member['user_hp'];?></span> 
						</div> 
					</div>
					
					<!-- 정보 수정 -->
					<div class="danger-zone mt-5">
						<h1 class="content-title">정보 수정</h1>
						<div class="alert alert-info" role="alert">
							이름, 이메일, 연락처, 비빌번호를<br>
							변경하실 수 있습니다.<br><br>
							<button type="button" class="btn btn-info text-white" onclick="location.href='<?php echo CM_MB_URL?>/register_form.php?w=update'">정보 수정</button>
						</div>
					</div>
					

					<!-- 회원 탈퇴 -->
					<div class="danger-zone mt-5">
						<h1 class="content-title">회원 탈퇴</h1>
						<div class="alert alert-danger" role="alert">
							회원탈퇴 시 모든 개인정보내역이 삭제되며, 복구할 수 없습니다.<br>
							탈퇴 후 동일한 아이디로 재가입이 불가능합니다.<br><br>
							<button type="button" class="btn btn-danger" id="showLeaveModalBtn">회원 탈퇴</button>
						</div>
					</div>
				</div>
			</div>
		</div>
    </main>
	
	<!-- 회원 탈퇴 비밀번호 확인 모달 -->
<div class="modal fade" id="leavePasswordModal" tabindex="-1" aria-labelledby="leavePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leavePasswordModalLabel">비밀번호 확인</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="leavePasswordCheckForm">
                    <div class="mb-3">
                        <label for="leave_current_password" class="form-label">현재 비밀번호</label>
                        <input type="password" class="form-control" id="leave_current_password" name="current_password" required>
                        <div id="leavePasswordError" class="form-text text-danger"></div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-danger">회원 탈퇴 진행</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
