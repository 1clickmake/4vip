<?php
include_once './_common.php';
$cm_title = "포인트 관리";
include_once CM_ADMIN_PATH.'/admin.head.php';

$options = [
    'table' => 'cm_point',
    'page' => $_GET['page'] ?? 1,
    'per_page' => 20,
    'order_by' => 'id DESC',
    'conditions' => [
        ['field' => 'user_id', 'operator' => '=', 'value' => $_GET['user_id'] ?? ''],
        ['field' => 'description', 'operator' => 'LIKE', 'value' => $_GET['description'] ?? ''],
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
			<h2 class="admin-list-title"><?php echo $cm_title;?></h2>
			
			<div class="table-responsive">
				<!-- 포인트 리스트 -->
				<table class="table table-sm table-striped table-bordered align-middle" style="min-width:1200px;">
					<thead class="table-dark  text-center">
						<tr>
							<th>No</th>
							<th>회원 아이디</th>
							<th>포인트 내용</th>
							<th>지급포인트</th>
							<th>등록일</th>
							<th>삭제</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($result)){ ?>
							<tr>
								<td colspan="6" class="text-center">포인트 내역이 없습니다.</td>
							</tr>
						<?php } else { ?>
							<?php 
							$start_number = $result['total_rows'] - ($page - 1) * $options['per_page'];
							foreach ($result['list'] as $index => $list) {
								$list_no = $start_number - $index;
							?>
								<tr class=" text-center">
									<td><?php echo $list_no; ?></td>
									<td><?php echo htmlspecialchars($list['user_id']); ?></td>
									<td><?php echo htmlspecialchars($list['description']); ?></td>
									<td><?php echo number_format($list['point']); ?></td>
									<td><?php echo $list['created_at']; ?></td>
									<td>
										<form action="point_update.php" method="POST" onsubmit="return confirm('정말로 삭제하시겠습니까?');">
											<input type="hidden" name="id" value="<?php echo $list['id']; ?>">
											<input type="hidden" name="action" value="delete">
											<button type="submit" class="btn btn-danger btn-sm">삭제</button>
										</form>
									</td>
								</tr>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
			
			<div>
				<!-- 페이지네이션 -->
				<?php echo render_pagination($page, $total_pages, $_GET);?>
				<!-- 페이지네이션 끝-->
			</div>

			<!-- 포인트 지급 폼 -->
			<h3 class="mt-5">포인트 지급</h3>
			<form action="point_update.php" method="POST" class="row g-3">
				<input type="hidden" name="action" value="add">
				<div class="col-md-4">
					<label for="user_id" class="form-label">회원 아이디</label>
					<input type="text" class="form-control" id="user_id" name="user_id" required>
				</div>
				<div class="col-md-4">
					<label for="description" class="form-label">포인트 내용</label>
					<input type="text" class="form-control" id="description" name="description" required>
				</div>
				<div class="col-md-4">
					<label for="point" class="form-label">지급 포인트</label>
					<input type="number" class="form-control" id="point" name="point" required min="1">
				</div>
				
				<div class="col-12">
					<button type="submit" class="btn btn-primary">포인트 지급</button>
				</div>
			</form>
		</div>
    </div>

    
<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>