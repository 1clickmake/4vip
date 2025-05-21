<?php
include_once './_common.php';
$cm_title = "팝업레이어 관리";
include_once CM_ADMIN_PATH.'/admin.head.php';
?>

    <!-- Main Content -->
    <div class="main-content shifted" id="mainContent">
        <div class="container-fluid">
			<div class="d-flex justify-content-between align-items-center mb-4">
				<h2 class="mb-4"><?php echo $cm_title;?></h2>
				<a href="popup_form.php" class="btn btn-primary">팝업 생성</a>
			</div>

			<?php
			try {
				// 팝업 목록 조회
				$sql = "SELECT * FROM cm_popup ORDER BY po_id DESC";
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				$popups = $stmt->fetchAll();
			} catch (PDOException $e) {
				echo '<div class="alert alert-danger">데이터 조회 중 오류가 발생했습니다: ' . $e->getMessage() . '</div>';
			}
			?>

			<div class="card">
				<div class="card-body">
					<table class="table table-striped table-hover text-center">
						<thead>
							<tr>
								<th>번호</th>
								<th>팝업 제목</th>
								<th>노출 기간</th>
								<th>크기</th>
								<th>상태</th>
								<th>관리</th>
							</tr>
						</thead>
						<tbody>
							<?php if (isset($popups) && count($popups) > 0) : ?>
								<?php foreach ($popups as $popup) : ?>
									<tr>
										<td><?php echo $popup['po_id']; ?></td>
										<td><?php echo htmlspecialchars($popup['po_title']); ?></td>
										<td><?php echo $popup['po_start_date'] . ' ~ ' . $popup['po_end_date']; ?></td>
										<td><?php echo $popup['po_width'] . 'x' . $popup['po_height']; ?></td>
										<td>
											<?php
											$today = date('Y-m-d');
											$start_date = $popup['po_start_date'];
											$end_date = $popup['po_end_date'];
											$status = '';
											
											if ($popup['po_use'] == 0) {
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
											<a href="popup_form.php?po_id=<?php echo $popup['po_id']; ?>" class="btn btn-sm btn-outline-primary">수정</a>
											<button type="button" class="btn btn-sm btn-outline-danger delete-popup" data-po-id="<?php echo $popup['po_id']; ?>" data-po-title="<?php echo htmlspecialchars($popup['po_title']); ?>">삭제</button>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else : ?>
								<tr>
									<td colspan="6" class="text-center">등록된 팝업이 없습니다.</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
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