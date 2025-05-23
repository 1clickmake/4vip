<?php
include_once './_common.php';
$cm_title = "메뉴관리";
include_once CM_ADMIN_PATH.'/admin.head.php';
?>

<style>
.menu-scroll{min-height:1000px; max-height:1000px; overflow-y:auto;}
.nested-menu { margin-left: 0; }
.sortable {padding-left: 1.0rem;}
.sortable .btn {font-size:12px !important; padding:4px 8px;}
.menu-item { 
    border-bottom: 1px solid #e9ecef; 
    padding: 10px 10px; 
    font-size:14px; 
    cursor: move; 
    transition: all 0.3s ease;
    border-radius: 8px;
    margin-bottom: 5px;
    background: #fff;
}
.nested-menu-deep { margin-left: 0; }
.menu-item:hover { 
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.sortable-ghost { 
    opacity: 0.4; 
    background: linear-gradient(135deg, #e9ecef, #dee2e6);
    transform: rotate(3deg);
}
.menu-level-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 8px;
}
.level-0 { background: #007bff; }
.level-1 { background: #28a745; }
.level-2 { background: #ffc107; }
.level-3 { background: #dc3545; }
.menu-status-badge {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 12px;
}
</style>

    <!-- Main Content -->
    <div class="main-content shifted" id="mainContent">
        <div class="container-fluid">
            <h1 class="page-title mb-5 fs-4">
                <i class="fas fa-sitemap me-3"></i>
                <?php echo $cm_title;?>
            </h1>
            
            <div class="row">
                <!-- 메뉴 추가 폼 -->
                <div class="col-12 col-lg-5">
                    <div class="card chart-card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-plus-circle me-2"></i>
                                새 메뉴 추가
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form id="menuForm">
                                <div class="mb-4">
                                    <label for="parent_id" class="form-label fw-semibold">
                                        <i class="fas fa-layer-group me-2 text-primary"></i>
                                        상위 메뉴
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-sitemap"></i>
                                        </span>
                                        <select class="form-select" id="parent_id" name="parent_id">
                                            <option value="0">최상위 메뉴</option>
                                            <?php
                                            function display_menu_options($pdo, $parent_id = 0, $level = 0, $prefix = '') {
                                                $stmt = $pdo->prepare("SELECT * FROM cm_menu WHERE parent_id = ? ORDER BY sort_order");
                                                $stmt->execute([$parent_id]);
                                                $menus = $stmt->fetchAll();
                                                
                                                foreach ($menus as $menu) {
                                                    echo '<option value="' . $menu['menu_id'] . '">' . $prefix . $menu['menu_name'] . ' (레벨 ' . $menu['menu_level'] . ')</option>';
                                                    display_menu_options($pdo, $menu['menu_id'], $level + 1, $prefix . '— ');
                                                }
                                            }
                                            display_menu_options($pdo);
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="menu_name" class="form-label fw-semibold">
                                        <i class="fas fa-tag me-2 text-success"></i>
                                        메뉴 이름
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-font"></i>
                                        </span>
                                        <input type="text" class="form-control" id="menu_name" name="menu_name" 
                                               placeholder="메뉴 이름을 입력하세요" required>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="menu_url" class="form-label fw-semibold">
                                        <i class="fas fa-link me-2 text-info"></i>
                                        이동 URL
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-external-link-alt"></i>
                                        </span>
                                        <input type="text" class="form-control" id="menu_url" name="menu_url"
                                               placeholder="https://example.com">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="text-muted fw-bold mb-3">
                                        <i class="fas fa-cog me-2"></i>
                                        메뉴 옵션
                                    </h6>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="target_blank" name="target_blank" value="1">
                                        <label class="form-check-label fw-medium" for="target_blank">
                                            <i class="fas fa-external-link-alt me-2 text-primary"></i>
                                            새 창에서 열기
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="is_disabled" name="is_disabled" value="1">
                                        <label class="form-check-label fw-medium" for="is_disabled">
                                            <i class="fas fa-eye-slash me-2 text-warning"></i>
                                            메뉴 비활성화
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-plus me-2"></i>
                                        메뉴 추가
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            
                <div class="col-12 col-lg-7">
                    <div class="menu-scroll">
                        <!-- 메뉴 목록 -->
                        <div class="card table-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-list me-2"></i>
                                    메뉴 목록
                                    <small class="text-muted ms-2">드래그하여 순서 변경 가능</small>
                                </h5>
                            </div>
                            <div class="card-body p-3" id="menuList">
                                <?php
                                function display_menu($pdo, $parent_id = 0, $level = 0) {
                                    $stmt = $pdo->prepare("SELECT * FROM cm_menu WHERE parent_id = ? ORDER BY sort_order");
                                    $stmt->execute([$parent_id]);
                                    $menus = $stmt->fetchAll();
                                    
                                    if ($menus) {
                                        echo '<ul class="sortable list-unstyled" data-parent-id="' . $parent_id . '">';
                                        foreach ($menus as $menu) {
                                            $class = $level > 2 ? 'nested-menu-deep' : 'nested-menu';
                                            $levelClass = 'level-' . min($menu['menu_level'], 3);
                                            echo '<li class="menu-item ' . $class . '" data-id="' . $menu['menu_id'] . '" style="margin-left: ' . ($level * 0) . 'px">';
                                            echo '<div class="d-flex justify-content-between align-items-center">';
                                            echo '<div class="d-flex align-items-center">';
                                            echo '<i class="fas fa-grip-vertical text-muted me-2"></i>';
                                            echo '<span class="menu-level-indicator ' . $levelClass . '"></span>';
                                            echo '<span class="fw-medium">' . htmlspecialchars($menu['menu_name']) . '</span>';
                                            
                                            // 상태 배지들
                                            if ($menu['target_blank']) {
                                                echo '<span class="badge bg-info menu-status-badge ms-2">새창</span>';
                                            }
                                            if ($menu['is_disabled']) {
                                                echo '<span class="badge bg-warning menu-status-badge ms-1">비활성</span>';
                                            }
                                            echo '<span class="badge bg-secondary menu-status-badge ms-1">Lv.' . $menu['menu_level'] . '</span>';
                                            echo '</div>';
                                            
                                            echo '<div class="btn-group" role="group">';
                                            echo '<button class="btn btn-outline-primary btn-sm edit-menu" data-id="' . $menu['menu_id'] . '" title="수정">';
                                            echo '<i class="fas fa-edit"></i>';
                                            echo '</button>';
                                            echo '<button class="btn btn-outline-danger btn-sm delete-menu" data-id="' . $menu['menu_id'] . '" title="삭제">';
                                            echo '<i class="fas fa-trash"></i>';
                                            echo '</button>';
                                            echo '</div></div>';
                                            display_menu($pdo, $menu['menu_id'], $level + 1);
                                            echo '</li>';
                                        }
                                        echo '</ul>';
                                    }
                                }
                                display_menu($pdo);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--//row-->
        </div>

        <!-- 수정 모달 -->
        <div class="modal fade" id="editModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-edit me-2"></i>
                            메뉴 수정
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="editForm">
                            <input type="hidden" id="edit_menu_id" name="menu_id">
                            <div class="mb-4">
                                <label for="edit_parent_id" class="form-label fw-semibold">
                                    <i class="fas fa-layer-group me-2 text-primary"></i>
                                    상위 메뉴
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-sitemap"></i>
                                    </span>
                                    <select class="form-select" id="edit_parent_id" name="parent_id">
                                        <option value="0">최상위 메뉴</option>
                                        <?php display_menu_options($pdo); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="edit_menu_name" class="form-label fw-semibold">
                                    <i class="fas fa-tag me-2 text-success"></i>
                                    메뉴 이름
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-font"></i>
                                    </span>
                                    <input type="text" class="form-control" id="edit_menu_name" name="menu_name" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="edit_menu_url" class="form-label fw-semibold">
                                    <i class="fas fa-link me-2 text-info"></i>
                                    이동 URL
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-external-link-alt"></i>
                                    </span>
                                    <input type="text" class="form-control" id="edit_menu_url" name="menu_url">
                                </div>
                            </div>
                            <div class="mb-4">
                                <h6 class="text-muted fw-bold mb-3">
                                    <i class="fas fa-cog me-2"></i>
                                    메뉴 옵션
                                </h6>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="edit_target_blank" name="target_blank" value="1">
                                    <label class="form-check-label fw-medium" for="edit_target_blank">
                                        <i class="fas fa-external-link-alt me-2 text-primary"></i>
                                        새 창에서 열기
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="edit_is_disabled" name="is_disabled" value="1">
                                    <label class="form-check-label fw-medium" for="edit_is_disabled">
                                        <i class="fas fa-eye-slash me-2 text-warning"></i>
                                        메뉴 비활성화
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>
                            취소
                        </button>
                        <button type="button" class="btn btn-primary" id="saveEdit">
                            <i class="fas fa-save me-2"></i>
                            저장
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                // 메뉴 추가
                $('#menuForm').submit(function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: 'menu_form_update.php',
                        type: 'POST',
                        data: $(this).serialize() + '&action=add',
                        success: function(response) {
                            alert('메뉴가 추가되었습니다.');
                            location.reload();
                        },
                        error: function(xhr) {
                            console.error('메뉴 추가 실패:', xhr.responseText);
                            alert('메뉴 추가에 실패했습니다.');
                        }
                    });
                });

                // 메뉴 수정 폼 열기
                $('.edit-menu').click(function() {
                    const menuId = $(this).data('id');
                    $.ajax({
                        url: 'menu_form_update.php',
                        type: 'POST',
                        data: { action: 'get', menu_id: menuId },
                        success: function(response) {
                            try {
                                const data = JSON.parse(response);
                                $('#edit_menu_id').val(data.menu_id);
                                $('#edit_parent_id').val(data.parent_id);
                                $('#edit_menu_name').val(data.menu_name);
                                $('#edit_menu_url').val(data.menu_url);
                                $('#edit_target_blank').prop('checked', data.target_blank == 1);
                                $('#edit_is_disabled').prop('checked', data.is_disabled == 1);
                                $('#editModal').modal('show');
                            } catch (e) {
                                console.error('JSON 파싱 오류:', response);
                                alert('메뉴 데이터를 불러오지 못했습니다.');
                            }
                        },
                        error: function(xhr) {
                            console.error('메뉴 조회 실패:', xhr.responseText);
                            alert('메뉴 데이터를 불러오지 못했습니다.');
                        }
                    });
                });

                // 메뉴 수정 저장
                $('#saveEdit').click(function() {
                    const formData = $('#editForm').serializeArray();
                    formData.push({ name: 'action', value: 'edit' });
                    // 체크박스 값이 누락되지 않도록 명시적 처리
                    if (!$('#edit_target_blank').is(':checked')) {
                        formData.push({ name: 'target_blank', value: '0' });
                    }
                    if (!$('#edit_is_disabled').is(':checked')) {
                        formData.push({ name: 'is_disabled', value: '0' });
                    }
                    $.ajax({
                        url: 'menu_form_update.php',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            try {
                                const result = JSON.parse(response);
                                if (result.status === 'success') {
                                    alert('메뉴가 수정되었습니다.');
                                    location.reload();
                                } else {
                                    console.error('수정 실패:', result.message);
                                    alert('메뉴 수정에 실패했습니다: ' + result.message);
                                }
                            } catch (e) {
                                console.error('JSON 파싱 오류:', response);
                                alert('메뉴 수정에 실패했습니다.');
                            }
                        },
                        error: function(xhr) {
                            console.error('메뉴 수정 실패:', xhr.responseText);
                            alert('메뉴 수정에 실패했습니다.');
                        }
                    });
                });

                // 메뉴 삭제
                $('.delete-menu').click(function() {
                    if (confirm('정말 삭제하시겠습니까?')) {
                        const menuId = $(this).data('id');
                        $.ajax({
                            url: 'menu_form_update.php',
                            type: 'POST',
                            data: { action: 'delete', menu_id: menuId },
                            success: function(response) {
                                try {
                                    const result = JSON.parse(response);
                                    if (result.status === 'success') {
                                        alert('메뉴가 삭제되었습니다.');
                                        location.reload();
                                    } else {
                                        alert('메뉴 삭제에 실패했습니다: ' + result.message);
                                    }
                                } catch (e) {
                                    console.error('JSON 파싱 오류:', response);
                                    alert('메뉴 삭제에 실패했습니다.');
                                }
                            },
                            error: function(xhr) {
                                console.error('메뉴 삭제 실패:', xhr.responseText);
                                alert('메뉴 삭제에 실패했습니다.');
                            }
                        });
                    }
                });

                // 드래그앤드롭 정렬
                $('.sortable').sortable({
                    items: '> li',
                    placeholder: 'sortable-ghost',
                    update: function(event, ui) {
                        const parentId = $(this).data('parent-id');
                        const menuIds = $(this).find('li').map(function() {
                            return $(this).data('id');
                        }).get();
                        
                        $.ajax({
                            url: 'menu_form_update.php',
                            type: 'POST',
                            data: {
                                action: 'update_order',
                                parent_id: parentId,
                                menu_ids: menuIds
                            },
                            success: function(response) {
                                alert('순서가 업데이트되었습니다.');
                            },
                            error: function(xhr) {
                                console.error('순서 업데이트 실패:', xhr.responseText);
                                alert('순서 업데이트에 실패했습니다.');
                                location.reload();
                            }
                        });
                    }
                }).disableSelection();
            });
        </script>
    </div>

<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>