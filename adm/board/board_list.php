<?php
include_once './_common.php';
$cm_title = "게시판 관리";
include_once CM_ADMIN_PATH.'/admin.head.php';

$options = [
    'table' => 'cm_board_list',
    'page' => $_GET['page'] ?? 1,
    'per_page' => 20,
    'order_by' => 'created_at DESC',
    'conditions' => [
        ['field' => 'board_id', 'operator' => 'LIKE', 'value' => $_GET['board_id'] ?? ''],
        ['field' => 'board_name', 'operator' => '=', 'value' => $_GET['status'] ?? ''],
        //['field' => 'category', 'operator' => 'IN', 'value' => $_GET['category'] ?? []],
        //['field' => 'created_at', 'operator' => 'BETWEEN', 'value' => [$_GET['from'] ?? '', $_GET['to'] ?? '']],
    ]
];

$result = sql_list($options);
$total_pages = $result['total_pages'];
$page = $result['current_page'];
?>

    <!-- Main Content -->
    <div class="main-content shifted" id="mainContent">
        <div class="container-fluid">
			<h2><?php echo $cm_title;?></h2>

			<!-- 게시판 생성 버튼 -->
			<button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#createBoardModal">
				새 게시판 만들기
			</button>

			<!-- 게시판 목록 (이전과 동일 또는 업데이트된 목록) -->
			<div class="card">
				<div class="card-header">
					게시판 목록 및 삭제
				</div>
				<div class="card-body">
					<?php if (empty($result['list'])) { ?>
					<div class="text-center">
						생성된 게시판이 없습니다
					</div>
					<?php } else { ?>
						
					<ul class="list-group mb-3">
						<?php foreach ($result['list'] as $list) { ?>
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<a href="<?php echo get_board_url('list',  $list['board_id']);?>"><?php echo  $list['board_id']; ?></a>
							<?php echo  $list['board_name']; ?>
							<?php echo  $list['board_skin']; ?>
							
							<span>
							<button type="button" class="btn btn-primary btn-sm">수정</button>
							<button type="button" class="btn btn-danger btn-sm">삭제</button>
							</span>
						</li>
						<?php } //endforeach; ?>
					</ul>
					
					<?php } //endif; ?>
				</div>
			</div>
			
			<!-- 페이지네이션 -->
			<?php echo render_pagination($page, $total_pages, $_GET);?>
			<!-- 페이지네이션 끝-->


			<!-- 게시판 생성 모달 -->
			<div class="modal fade" id="createBoardModal" tabindex="-1" aria-labelledby="createBoardModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg"> <!-- modal-lg 클래스로 모달을 크게 만들어요 -->
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="createBoardModalLabel">새 게시판 만들기</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<form action="./board_form_insert.php" method="POST"> <!-- 모달 안에 폼을 넣어요 -->
							<div class="modal-body">
								<div class="mb-3">
									<label for="groupId" class="form-label">게시판그룹 선택</label>
									<select class="form-select" id="groupId" name="group_id" required>
									<?php
									$grList = sql_all_list("SELECT * FROM `cm_board_group` ORDER BY `group_name` ASC");
									if ($grList !== false && !empty($grList)) { // 함수 실행 자체에 오류가 없었다면
										foreach ($grList as $row) {
									?>
										<option value="<?php echo $row['group_id'];?>"><?php echo $row['group_name'];?></option>
										<?php } ?>
									<?php } ?>
									</select>
								</div>
								<div class="mb-3">
									<label for="boardId" class="form-label">게시판 아이디 (영문, 기호, 숫자만)</label>
									<input type="text" class="form-control" id="boardId" name="board_id" required pattern="^[a-zA-Z0-9!@#$%^&*()_+=\-\[\]{};':\\|,.<>\/?~]*$"> <!-- 영문, 기호, 숫자만 허용하는 pattern 추가 -->
									<small class="form-text text-muted">영문, 숫자, 일부 기호(-,_)만 사용할 수 있습니다.</small>
								</div>
								<div class="mb-3">
									<label for="boardName" class="form-label">게시판 이름</label>
									<input type="text" class="form-control" id="boardName" name="board_name" required>
								</div>
								<div class="mb-3">
									<label for="groupId" class="form-label">게시판스킨 선택</label>
									<select class="form-select" id="groupId" name="board_skin" required>
										<?php
										$folderDirectory = CM_BOARD_PATH.'/board_skin';
										$folders = getSubdirectories($folderDirectory);
										foreach ($folders as $folder) {
										?>
										<option value="<?php echo $folder;?>"><?php echo $folder;?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
								<button type="submit" class="btn btn-primary">게시판 생성</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>


    </div>

    
<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>