<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
?>
		<?php
		if (defined('CM_IS_ADMIN')) {
			include_once CM_ADMIN_PATH.'/add/add.script.tail.php';
		}else{
			include_once CM_TEMPLATE_PATH.'/add/add.script.tail.php';
		}
		?>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
	</body>
</html>