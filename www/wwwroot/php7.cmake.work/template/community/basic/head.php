<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
include_once CM_TEMPLATE_PATH.'/lib/menu.lib.php';
include_once(CM_TEMPLATE_PATH.'/sidemenu.php'); 
?>
<header>
    <div id="navbar" class="container-fluid bg-dark fixed-top">
		<div class="container clearfix">
			<div class="d-flex align-items-center float-start">
                <h1 class="m-0"><a href="<?php echo CM_URL?>" class="text-white"><?php echo $config['site_title'];?></a></h1>
            </div>
			<nav class="custom-navbar float-end">
				<?php generateMegaMenu(); ?>
			</nav>

			<script>
			document.addEventListener('DOMContentLoaded', function() {
				// .dropdown 클래스를 가진 li 요소들에 hover 이벤트 바인딩
				const dropdownItems = document.querySelectorAll('.custom-navbar ul li.dropdown');
				
				dropdownItems.forEach(item => {
					item.addEventListener('mouseenter', function() {
						const submenu = this.querySelector('ul');
						if (submenu) {
							submenu.style.display = 'block';
							submenu.style.opacity = '0';
							requestAnimationFrame(() => {
								submenu.style.transition = 'opacity 200ms ease-in-out';
								submenu.style.opacity = '1';
							});
						}
					});

					item.addEventListener('mouseleave', function() {
						const submenu = this.querySelector('ul');
						if (submenu) {
							submenu.style.opacity = '0';
							setTimeout(() => {
								submenu.style.display = 'none';
							}, 200);
						}
					});
				});
			});
			</script>
		</div>
	</div>
</header>	
	