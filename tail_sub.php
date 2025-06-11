<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
?>
		<!-- PWA 설치 버튼 -->
		<?php if($pwaSet){?>
		<button type="button" id="pwa-install-btn" class="pwa-btn" style="display: none;">
			<i class="fa fa-download" aria-hidden="true"></i>
		</button>
		<?php } ?>
		
		
		<!-- 맨 위로 가기 버튼 -->
		<button type="button" id="top_btn" class="pwa-btn">
			<i class="fa fa-arrow-up" aria-hidden="true" title="top"></i>
		</button>

		<?php
		if (defined('CM_IS_ADMIN')) {
			include_once CM_ADMIN_PATH.'/add/add.script.tail.php';
		}else{
			include_once CM_TEMPLATE_PATH.'/add/add.script.tail.php';
		}
		?>
		<script src="<?php echo CM_URL?>/js/app.js?ver=<?php echo time();?>"></script>

	</body>
</html>