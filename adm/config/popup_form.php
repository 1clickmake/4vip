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
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h1 class="page-title mb-0">
                    <i class="fas fa-window-restore me-3"></i>
                    <?php echo $cm_title;?> <?php echo ($mode == 'insert') ? '생성' : '수정'; ?>
                </h1>
                <a href="popup_list.php" class="btn btn-outline-secondary">
                    <i class="fas fa-list me-2"></i>
                    목록으로
                </a>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card chart-card shadow-lg">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-cog me-2"></i>
                                팝업 설정
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form id="popupForm" method="post" action="popup_form_update.php">
                                <input type="hidden" name="mode" value="<?php echo $mode; ?>">
                                <?php if ($mode == 'update') : ?>
                                    <input type="hidden" name="po_id" value="<?php echo htmlspecialchars($popup['po_id']); ?>">
                                <?php endif; ?>
                                
                                
                                <!-- 기본 정보 섹션 -->
                                <div class="mb-5">
                                    <h6 class="text-muted fw-bold mb-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        기본 정보
                                    </h6>
                                    <div class="mb-4">
                                        <label for="po_title" class="form-label fw-semibold">
                                            <i class="fas fa-heading me-2 text-primary"></i>
                                            팝업 제목 <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-tag"></i>
                                            </span>
                                            <input type="text" class="form-control" id="po_title" name="po_title" 
                                                   value="<?php echo htmlspecialchars($popup['po_title']); ?>" 
                                                   placeholder="팝업 제목을 입력하세요" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- 위치 및 크기 설정 -->
                                <div class="mb-5">
                                    <h6 class="text-muted fw-bold mb-3">
                                        <i class="fas fa-expand-arrows-alt me-2"></i>
                                        위치 및 크기 설정
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-3 mb-4">
                                            <label for="po_top" class="form-label fw-semibold">
                                                <i class="fas fa-arrow-up me-2 text-info"></i>
                                                상단 위치 (px)
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-ruler-vertical"></i>
                                                </span>
                                                <input type="number" class="form-control" id="po_top" name="po_top" 
                                                       value="<?php echo $popup['po_top']; ?>" min="0" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-4">
                                            <label for="po_left" class="form-label fw-semibold">
                                                <i class="fas fa-arrow-left me-2 text-info"></i>
                                                좌측 위치 (px)
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-ruler-horizontal"></i>
                                                </span>
                                                <input type="number" class="form-control" id="po_left" name="po_left" 
                                                       value="<?php echo $popup['po_left']; ?>" min="0" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-4">
                                            <label for="po_width" class="form-label fw-semibold">
                                                <i class="fas fa-arrows-alt-h me-2 text-success"></i>
                                                가로 크기 (px)
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-expand-arrows-alt"></i>
                                                </span>
                                                <input type="number" class="form-control" id="po_width" name="po_width" 
                                                       value="<?php echo $popup['po_width']; ?>" min="100" placeholder="400">
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-4">
                                            <label for="po_height" class="form-label fw-semibold">
                                                <i class="fas fa-arrows-alt-v me-2 text-success"></i>
                                                세로 크기 (px)
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-expand-arrows-alt"></i>
                                                </span>
                                                <input type="number" class="form-control" id="po_height" name="po_height" 
                                                       value="<?php echo $popup['po_height']; ?>" min="100" placeholder="400">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 표시 기간 설정 -->
                                <div class="mb-5">
                                    <h6 class="text-muted fw-bold mb-3">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        표시 기간 설정
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-4 mb-4">
                                            <label for="po_start_date" class="form-label fw-semibold">
                                                <i class="fas fa-play me-2 text-success"></i>
                                                시작일
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-calendar"></i>
                                                </span>
                                                <input type="date" class="form-control" id="po_start_date" name="po_start_date" 
                                                       value="<?php echo $popup['po_start_date']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-4">
                                            <label for="po_end_date" class="form-label fw-semibold">
                                                <i class="fas fa-stop me-2 text-danger"></i>
                                                종료일
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-calendar"></i>
                                                </span>
                                                <input type="date" class="form-control" id="po_end_date" name="po_end_date" 
                                                       value="<?php echo $popup['po_end_date']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-4">
                                            <label for="po_cookie_time" class="form-label fw-semibold">
                                                <i class="fas fa-cookie me-2 text-warning"></i>
                                                쿠키 유지시간 (시간)
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-clock"></i>
                                                </span>
                                                <input type="number" class="form-control" id="po_cookie_time" name="po_cookie_time" 
                                                       value="<?php echo $popup['po_cookie_time']; ?>" min="1" placeholder="24">
                                            </div>
                                            <div class="form-text">
                                                <i class="fas fa-info-circle me-1"></i>
                                                "오늘 하루 보지 않음" 클릭 시 설정된 시간동안 팝업이 표시되지 않습니다.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 팝업 내용 -->
                                <div class="mb-5">
                                    <h6 class="text-muted fw-bold mb-3">
                                        <i class="fas fa-edit me-2"></i>
                                        팝업 내용
                                    </h6>
                                    <div class="mb-4">
                                        <label for="summernote" class="form-label fw-semibold">
                                            <i class="fas fa-file-alt me-2 text-primary"></i>
                                            내용 작성
                                        </label>
                                        <div class="border rounded p-2">
                                            <textarea class="form-control border-0" id="summernote" name="po_content" rows="10"><?php echo htmlspecialchars($popup['po_content']); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- 링크 설정 -->
                                <div class="mb-5">
                                    <h6 class="text-muted fw-bold mb-3">
                                        <i class="fas fa-link me-2"></i>
                                        링크 설정
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-8 mb-4">
                                            <label for="po_url" class="form-label fw-semibold">
                                                <i class="fas fa-external-link-alt me-2 text-info"></i>
                                                URL 링크
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-globe"></i>
                                                </span>
                                                <input type="text" class="form-control" id="po_url" name="po_url" 
                                                       value="<?php echo htmlspecialchars($popup['po_url']); ?>" 
                                                       placeholder="https://example.com">
                                            </div>
                                            <div class="form-text">
                                                <i class="fas fa-info-circle me-1"></i>
                                                팝업 클릭 시 이동할 URL을 입력하세요. (선택사항)
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-4">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-window-restore me-2 text-secondary"></i>
                                                URL 타겟
                                            </label>
                                            <div class="mt-2">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" name="po_target" id="po_target_blank" 
                                                           value="_blank" <?php echo ($popup['po_target'] == '_blank') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label fw-medium" for="po_target_blank">
                                                        <i class="fas fa-external-link-alt me-2 text-primary"></i>
                                                        새 창에서 열기
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="po_target" id="po_target_self" 
                                                           value="_self" <?php echo ($popup['po_target'] == '_self') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label fw-medium" for="po_target_self">
                                                        <i class="fas fa-window-maximize me-2 text-secondary"></i>
                                                        현재 창에서 열기
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 사용 설정 -->
                                <div class="mb-5">
                                    <h6 class="text-muted fw-bold mb-3">
                                        <i class="fas fa-toggle-on me-2"></i>
                                        사용 설정
                                    </h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="po_use" name="po_use" 
                                               value="1" <?php echo ($popup['po_use'] == 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-medium" for="po_use">
                                            <i class="fas fa-power-off me-2 text-success"></i>
                                            팝업 사용
                                        </label>
                                    </div>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        체크 해제 시 팝업이 표시되지 않습니다.
                                    </div>
                                </div>
                                
                                <!-- 버튼 그룹 -->
                                <div class="d-flex justify-content-center gap-3 mt-5">
                                    <button type="button" class="btn btn-outline-secondary btn-lg px-4" onclick="location.href='popup_list.php'">
                                        <i class="fas fa-times me-2"></i>
                                        취소
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-save me-2"></i>
                                        <?php echo ($mode == 'insert') ? '등록' : '수정'; ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- 미리보기 카드 -->
                    <div class="card table-card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-eye me-2"></i>
                                팝업 미리보기
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <button type="button" class="btn btn-outline-primary" id="previewBtn">
                                    <i class="fas fa-search me-2"></i>
                                    미리보기 보기
                                </button>
                                <p class="text-muted mt-2 mb-0">
                                    <i class="fas fa-info-circle me-1"></i>
                                    현재 설정으로 팝업이 어떻게 보이는지 확인할 수 있습니다.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
$(document).ready(function() {
    // Summernote 에디터 초기화
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
                        url: CM.LIB_URL + '/summernote_upload.php',
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

    // 실시간 유효성 검사
    $('#po_title').on('input', function() {
        if ($(this).val().trim()) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
    });

    $('#po_start_date, #po_end_date').change(function() {
        const startDate = new Date($('#po_start_date').val());
        const endDate = new Date($('#po_end_date').val());
        
        if (startDate && endDate) {
            if (startDate <= endDate) {
                $('#po_start_date, #po_end_date').removeClass('is-invalid').addClass('is-valid');
            } else {
                $('#po_start_date, #po_end_date').removeClass('is-valid').addClass('is-invalid');
            }
        }
    });

    // 미리보기 기능
    $('#previewBtn').click(function() {
        const title = $('#po_title').val() || '팝업 제목';
        const content = $('#summernote').summernote('code') || '팝업 내용이 없습니다.';
        const width = $('#po_width').val() || 400;
        const height = $('#po_height').val() || 400;
        const top = $('#po_top').val() || 0;
        const left = $('#po_left').val() || 0;
        
        const previewWindow = window.open('', 'popup_preview', 
            `width=${parseInt(width)},height=${parseInt(height)},top=${parseInt(top)},left=${parseInt(left)},scrollbars=yes,resizable=yes`);
        
        const safeTitle = $('<textarea />').html(title).text(); // jQuery를 사용한  HTML 이스케이프
        previewWindow.document.write(`
            <html>
                <head>
                    <title>${safeTitle}</title>
                    <style>
                        body { margin: 0; overflow: hidden; }
						p {margin:0 !important; padding:0 !important;}
                        .popup-header { background: #f8f9fa; padding: 10px; border-bottom: 1px solid #dee2e6; }
                        .popup-content { padding:10px; height: calc(100vh - 50px); overflow: auto; }
                        .popup-close { float: right; cursor: pointer; color: #6c757d; }
                        .popup-content img { max-width: 100% !important; height: auto !important; }
                        .popup-content * { max-width: 100%; }
                    </style>
                </head>
                <body>
                    <div class="popup-header">
                        <span class="popup-close" onclick="window.close()">✕</span>
                        <strong>${safeTitle}</strong>
                    </div>
                    <div class="popup-content">
                        ${content}
                    </div>
                </body>
            </html>
        `);
        previewWindow.document.close();
    });
});
</script>

<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>