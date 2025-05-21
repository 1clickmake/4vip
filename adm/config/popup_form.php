<?php
include_once './_common.php';
$cm_title = "팝업레이어";
include_once CM_ADMIN_PATH.'/admin.head.php';

// 초기화
$mode = 'insert';
$popup = [
    'po_id' => '',
    'po_title' => '',
    'po_content' => '',
    'po_top' => 0,
    'po_left' => 0,
    'po_width' => 400,
    'po_height' => 400,
    'po_start_date' => date('Y-m-d'),
    'po_end_date' => date('Y-m-d', strtotime('+7 days')),
    'po_cookie_time' => 24,
    'po_url' => '',
    'po_target' => '_blank',
    'po_use' => 1
];

// 수정 모드인 경우 데이터 가져오기
if (isset($_GET['po_id']) && !empty($_GET['po_id'])) {
    $po_id = intval($_GET['po_id']);
    $mode = 'update';
    
    try {
        $sql = "SELECT * FROM cm_popup WHERE po_id = :po_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':po_id', $po_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($row = $stmt->fetch()) {
            $popup = $row;
        } else {
            echo '<script>alert("존재하지 않는 팝업입니다."); location.href="popup_list.php";</script>';
            exit;
        }
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger">데이터 조회 중 오류가 발생했습니다: ' . $e->getMessage() . '</div>';
    }
}
?>

    <!-- Main Content -->
    <div class="main-content shifted" id="mainContent">
        <div class="container-fluid">
			<div class="d-flex justify-content-between align-items-center mb-4">
				<h2><?php echo $cm_title;?> <?php echo ($mode == 'insert') ? ' 생성' : ' 수정'; ?></h2>
				<a href="popup_list.php" class="btn btn-secondary">목록으로</a>
			</div>

			<div class="card">
				<div class="card-body">
					<form id="popupForm" method="post" action="popup_form_update.php">
						<input type="hidden" name="mode" value="<?php echo $mode; ?>">
						<?php if ($mode == 'update') : ?>
							<input type="hidden" name="po_id" value="<?php echo $popup['po_id']; ?>">
						<?php endif; ?>
						
						<div class="row mb-3">
							<div class="col-md-12">
								<label for="po_title" class="form-label">팝업 제목 <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="po_title" name="po_title" value="<?php echo htmlspecialchars($popup['po_title']); ?>" required>
							</div>
						</div>
						
						<div class="row mb-3">
							<div class="col-md-3">
								<label for="po_top" class="form-label">상단 위치 (px)</label>
								<input type="number" class="form-control" id="po_top" name="po_top" value="<?php echo $popup['po_top']; ?>" min="0">
							</div>
							<div class="col-md-3">
								<label for="po_left" class="form-label">좌측 위치 (px)</label>
								<input type="number" class="form-control" id="po_left" name="po_left" value="<?php echo $popup['po_left']; ?>" min="0">
							</div>
							<div class="col-md-3">
								<label for="po_width" class="form-label">가로 크기 (px)</label>
								<input type="number" class="form-control" id="po_width" name="po_width" value="<?php echo $popup['po_width']; ?>" min="0">
							</div>
							<div class="col-md-3">
								<label for="po_height" class="form-label">세로 크기 (px)</label>
								<input type="number" class="form-control" id="po_height" name="po_height" value="<?php echo $popup['po_height']; ?>" min="0">
							</div>
						</div>
						
						<div class="row mb-3">
							<div class="col-md-4">
								<label for="po_start_date" class="form-label">시작일</label>
								<input type="date" class="form-control" id="po_start_date" name="po_start_date" value="<?php echo $popup['po_start_date']; ?>">
							</div>
							<div class="col-md-4">
								<label for="po_end_date" class="form-label">종료일</label>
								<input type="date" class="form-control" id="po_end_date" name="po_end_date" value="<?php echo $popup['po_end_date']; ?>">
							</div>
							<div class="col-md-4">
								<label for="po_cookie_time" class="form-label">쿠키 유지시간 (시간)</label>
								<input type="number" class="form-control" id="po_cookie_time" name="po_cookie_time" value="<?php echo $popup['po_cookie_time']; ?>" min="1">
								<small class="text-muted">팝업 하단의 "오늘 하루 보지 않음" 클릭 시 설정된 시간동안 팝업이 표시되지 않습니다.</small>
							</div>
						</div>
						
						<div class="row mb-3">
							<div class="col-md-12">
								<label for="po_content" class="form-label">팝업 내용</label>
								<textarea class="form-control" id="summernote" name="po_content" rows="5"><?php echo htmlspecialchars($popup['po_content']); ?></textarea>
							</div>
						</div>
						
						<div class="row mb-3">
							<div class="col-md-8">
								<label for="po_url" class="form-label">URL 링크</label>
								<input type="text" class="form-control" id="po_url" name="po_url" value="<?php echo htmlspecialchars($popup['po_url']); ?>" placeholder="http://">
							</div>
							<div class="col-md-4">
								<label class="form-label">URL 타겟</label>
								<div class="mt-2">
									<div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="po_target" id="po_target_blank" value="_blank" <?php echo ($popup['po_target'] == '_blank') ? 'checked' : ''; ?>>
										<label class="form-check-label" for="po_target_blank">새 창</label>
									</div>
									<div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="po_target" id="po_target_self" value="_self" <?php echo ($popup['po_target'] == '_self') ? 'checked' : ''; ?>>
										<label class="form-check-label" for="po_target_self">현재 창</label>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row mb-3">
							<div class="col-md-12">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" id="po_use" name="po_use" value="1" <?php echo ($popup['po_use'] == 1) ? 'checked' : ''; ?>>
									<label class="form-check-label" for="po_use">
										팝업 사용
									</label>
								</div>
							</div>
						</div>
						
						<div class="d-flex justify-content-center mt-4">
							<button type="button" class="btn btn-secondary me-2" onclick="location.href='popup_list.php'">취소</button>
							<button type="submit" class="btn btn-primary"><?php echo ($mode == 'insert') ? '등록' : '수정'; ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
    </div>

<script>
$(document).ready(function() {
    // 폼 유효성 검사
    $('#popupForm').submit(function(e) {
        if ($('#po_title').val().trim() === '') {
            alert('팝업 제목을 입력해주세요.');
            $('#po_title').focus();
            e.preventDefault();
            return false;
        }
        
        const startDate = new Date($('#po_start_date').val());
        const endDate = new Date($('#po_end_date').val());
        
        if (startDate > endDate) {
            alert('종료일은 시작일보다 이후여야 합니다.');
            $('#po_end_date').focus();
            e.preventDefault();
            return false;
        }
        
        return true;
    });
});
</script>

<script>
$(document).ready(function() {
    $('#summernote').summernote({
        height: 300,
        minHeight: null,
        maxHeight: null,
        callbacks: {
            onImageUpload: function(files) {
                if (files.length > 0) {
                    var file = files[0];
                    var data = new FormData();
                    data.append('image', file);
                    data.append('board_id', 'popup');

                    $.ajax({
                        url: '<?php echo CM_URL;?>/summernote/summernote_upload.php',
                        method: 'POST',
                        data: data,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.url) {
                                $('#summernote').summernote('insertImage', response.url);
                            } else {
                                alert('이미지 업로드 실패: ' + (response.error || '알 수 없는 오류'));
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            alert('이미지 업로드 실패: ' + textStatus + ' (' + errorThrown + ')');
                        }
                    });
                }
            }
        }
    });
});
</script>
<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>