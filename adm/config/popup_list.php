<?php
include_once './_common.php';
$cm_title = "팝업레이어 관리";
include_once CM_ADMIN_PATH.'/admin.head.php';

// 목록 조회 옵션 설정
$options = [
    'table' => 'cm_popup',
    'page' => $_GET['page'] ?? 1,
    'per_page' => 20,
    'order_by' => 'po_id DESC',
    'conditions' => []
];

$result = sql_list($options);
$total_pages = $result['total_pages'];
$page = $result['current_page'];
?>

    <!-- Main Content -->
    <div class="main-content shifted" id="mainContent">
        <div class="container-fluid">
			<div class="d-flex justify-content-between align-items-center mb-4">
				<h2 class="admin-list-title"><?php echo $cm_title;?></h2>
				<a href="popup_form.php" class="btn btn-primary">팝업 생성</a>
			</div>

			<div class="table-responsive">
				<table class="table table-sm table-striped table-bordered align-middle" style="min-width:1200px;">
					<thead class="table-dark text-center">
						<tr>
							<th scope="col">No</th>
							<th scope="col">팝업 제목</th>
							<th scope="col">노출 기간</th>
							<th scope="col">크기</th>
							<th scope="col">상태</th>
							<th scope="col">관리</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($result['list'])){ ?>
							<tr>
								<td colspan="6" class="text-center">등록된 팝업이 없습니다.</td>
							</tr>
						<?php } else { ?>
							<?php 
							$start_number = $result['total_rows'] - ($page - 1) * $options['per_page'];
							foreach ($result['list'] as $index => $list) {
								$list_no = $start_number - $index;
							?>
							<tr class="text-center">
								<td><?php echo $list_no;?></td>
								<td><?php echo htmlspecialchars($list['po_title']);?></td>
								<td><?php echo $list['po_start_date'] . ' ~ ' . $list['po_end_date'];?></td>
								<td><?php echo $list['po_width'] . 'x' . $list['po_height'];?></td>
								<td>
									<?php
									$today = date('Y-m-d');
									$start_date = $list['po_start_date'];
									$end_date = $list['po_end_date'];
									$status = '';
									
									if ($list['po_use'] == 0) {
										$status = '<span class="badge bg-secondary">미사용</span>';
									} elseif ($today < $start_date) {
										$status = '<span class="badge bg-warning text-dark">대기</span>';
									} elseif ($today > $end_date) {
										$status = '<span class="badge bg-danger">종료</span>';
									} else {
										$status = '<span class="badge bg-success">활성</span>';
									}
									
									echo $status;
									?>
								</td>
								<td>
									<a href="popup_form.php?po_id=<?php echo $list['po_id'];?>" class="btn btn-sm btn-primary me-2">수정</a>
									<button type="button" class="btn btn-sm btn-danger delete-popup" data-po-id="<?php echo $list['po_id'];?>" data-po-title="<?php echo htmlspecialchars($list['po_title']);?>">삭제</button>
								</td>
							</tr>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
			
			<!-- 페이지네이션 -->
			<?php echo render_pagination($page, $total_pages, $_GET);?>
			<!-- 페이지네이션 끝-->
		</div>

		<!-- 삭제 확인 모달 -->
		<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="deleteModalLabel">팝업 삭제 확인</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<p id="deleteModalMessage"></p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
						<form id="deleteForm" method="post" action="popup_form_update.php">
							<input type="hidden" name="po_id" id="deletePoId" value="">
							<input type="hidden" name="mode" value="delete">
							<button type="submit" class="btn btn-danger">삭제</button>
						</form>
					</div>
				</div>
			</div>
		</div>
    </div>

<script>
    $(document).ready(function() {
        // 팝업 삭제 버튼 클릭 이벤트
        $('.delete-popup').click(function() {
            const poId = $(this).data('po-id');
            const poTitle = $(this).data('po-title');
            
            $('#deletePoId').val(poId);
            $('#deleteModalMessage').text(`"${poTitle}" 팝업을 삭제하시겠습니까?`);
            
            $('#deleteModal').modal('show');
        });
    });
</script> 
   
<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>