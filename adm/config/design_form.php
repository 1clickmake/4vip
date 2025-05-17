<?php
include_once './_common.php';
$cm_title = "디자인 설정";
include_once CM_ADMIN_PATH.'/admin.head.php';
?>

    <!-- Main Content -->
    <div class="main-content shifted" id="mainContent">
       <div class="container-fluid">
		  <h2 class="mb-4"><?php echo $cm_title;?></h2>
		  <form action="./design_form_update.php" method="post">
			<div class="mb-3">
				<label for="groupId" class="form-label">템플릿 선택</label>
				<select class="form-select" id="templateId" name="template_id" required>
					<?php
					$folderDirectory = CM_PATH.'/template';
					$folders = getSubdirectories($folderDirectory);
					foreach ($folders as $folder) {
					?>
					<option value="<?php echo $folder;?>"><?php echo $folder;?></option>
					<?php } ?>
				</select>
			</div>
			<button type="submit" class="btn btn-primary">저장</button>
		  </form>
		</div>


    </div>

    
<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>