
<div class="offcanvas offcanvas-start" tabindex="-1" id="site-menu" aria-labelledby="site-menuLabel">
	<div class="offcanvas-header">
		<h5 class="offcanvas-title" id="offcanvasExampleLabel"><?php echo $config['site_title'];?></h5>
		<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
	</div>
	<div class="offcanvas-body">
	
			
			<div class="list-group list-group-flush mt-2">
				<a href="<?php echo CM_URL?>/myoffice/my_bank.php" class="list-group-item d-flex justify-content-between align-items-center py-3 py-lg-2 text-start">
					<span class="float-start"><i class="bi bi-circle-fill  me-2 text-danger"></i> menu1</span>
					<span class="float-end text-primary"><i class="bi bi-arrow-right-short"></i></span>
				</a>
				<a href="<?php echo CM_URL?>/myoffice/my_add.php" class="list-group-item d-flex justify-content-between align-items-center py-3 py-lg-2">
					<span class="float-start"><i class="bi bi-circle-fill  me-2 text-warning"></i>  menu2</span>
					<span class="float-end  text-primary"><i class="bi bi-arrow-right-short"></i></span>
				</a>
				<a href="<?php echo CM_URL?>/myoffice/my_addlist.php" class="list-group-item d-flex justify-content-between align-items-center py-3 py-lg-2">
					<span class="float-start"><i class="bi bi-circle-fill  me-2 text-info"></i>  menu3</span>
					<span class="float-end  text-primary"><i class="bi bi-arrow-right-short"></i></span>
				</a>
				<a href="<?php echo CM_URL?>/myoffice/my_calendar.php" class="list-group-item d-flex justify-content-between align-items-center py-3 py-lg-2">
					<span class="float-start"><i class="bi bi-circle-fill  me-2 text-primary"></i>  menu4</span>
					<span class="float-end  text-primary"><i class="bi bi-arrow-right-short"></i></span>
				</a>
			</div>
		</div>
	</div>
</div>