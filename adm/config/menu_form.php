<?php
include_once './_common.php';
$cm_title = "메뉴관리";
include_once CM_ADMIN_PATH.'/admin.head.php';
?>

<style>
.menu-scroll{min-height:600px; max-height:600px; overflow-y:auto;}
.nested-menu { margin-left: 0; }
.sortable {padding-left: 1.5rem;}
.sortable .btn {font-size:12px !important; padding:2px 7px 2px 7px;}
.menu-item { border-bottom: 0; padding: 1px 0; font-size:13px !important; cursor: move; }
.nested-menu-deep { margin-left: 0; }
.menu-item:hover { background-color: #f8f9fa; }
.sortable-ghost { opacity: 0.4; background-color: #e9ecef; }
</style>
    <!-- Main Content -->
    <div class="main-content shifted" id="mainContent">

    <div class="container-fluid">
        <h2 class="mb-4"><?php echo $cm_title;?></h2>
        <div class="row">
			<!-- 메뉴 추가 폼 -->
			<div class="col-12 col-md-5">
				<div class="card mb-4">
					<div class="card-header">새 메뉴 추가</div>
					<div class="card-body">
						<form id="menuForm">
							<div class="mb-3">
								<label for="parent_id" class="form-label">상위 메뉴</label>
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
							<div class="mb-3">
								<label for="menu_name" class="form-label">메뉴 이름</label>
								<input type="text" class="form-control" id="menu_name" name="menu_name" required>
							</div>
							<div class="mb-3">
								<label for="menu_url" class="form-label">이동 URL</label>
								<input type="text" class="form-control" id="menu_url" name="menu_url">
							</div>
							<div class="form-check mb-3">
								<input class="form-check-input" type="checkbox" id="target_blank" name="target_blank" value="1">
								<label class="form-check-label" for="target_blank">새 창에서 열기</label>
							</div>
							<div class="form-check mb-3">
								<input class="form-check-input" type="checkbox" id="is_disabled" name="is_disabled" value="1">
								<label class="form-check-label" for="is_disabled">메뉴 비활성화</label>
							</div>
							<button type="submit" class="btn btn-primary">메뉴 추가</button>
						</form>
					</div>
				</div>
			</div>
		
			<div class="col-12 col-md-7">
				<div class="menu-scroll">
					<!-- 메뉴 목록 -->
					<div class="card">
						<div class="card-header">메뉴 목록 (드래그하여 순서 변경 가능)</div>
						<div class="card-body" id="menuList">
							<?php
							function display_menu($pdo, $parent_id = 0, $level = 0) {
								$stmt = $pdo->prepare("SELECT * FROM cm_menu WHERE parent_id = ? ORDER BY sort_order");
								$stmt->execute([$parent_id]);
								$menus = $stmt->fetchAll();
								
								if ($menus) {
									echo '<ul class="sortable" data-parent-id="' . $parent_id . '">';
									foreach ($menus as $menu) {
										$class = $level > 2 ? 'nested-menu-deep' : 'nested-menu';
										echo '<li class="menu-item ' . $class . '" data-id="' . $menu['menu_id'] . '" style="margin-left: ' . ($level * 1) . 'px">';
										echo '<div class="d-flex justify-content-between align-items-center">';
										echo '<span>' . htmlspecialchars($menu['menu_name']) . 
											 ($menu['target_blank'] ? ' (새창)' : '') . ($menu['is_disabled'] ? ' (비활성화)' : '') . ' (레벨 ' . $menu['menu_level'] . ')</span>';
										echo '<div>';
										echo '<button class="btn btn-sm btn-warning edit-menu me-1" data-id="' . $menu['menu_id'] . '">수정</button>';
										echo '<button class="btn btn-sm btn-danger delete-menu" data-id="' . $menu['menu_id'] . '">삭제</button>';
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">메뉴 수정</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="edit_menu_id" name="menu_id">
                        <div class="mb-3">
                            <label for="edit_parent_id" class="form-label">상위 메뉴</label>
                            <select class="form-select" id="edit_parent_id" name="parent_id">
                                <option value="0">최상위 메뉴</option>
                                <?php display_menu_options($pdo); ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_menu_name" class="form-label">메뉴 이름</label>
                            <input type="text" class="form-control" id="edit_menu_name" name="menu_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_menu_url" class="form-label">이동 URL</label>
                            <input type="text" class="form-control" id="edit_menu_url" name="menu_url">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="edit_target_blank" name="target_blank" value="1">
                            <label class="form-check-label" for="edit_target_blank">새 창에서 열기</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="edit_is_disabled" name="is_disabled" value="1">
                            <label class="form-check-label" for="edit_is_disabled">메뉴 비활성화</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                    <button type="button" class="btn btn-primary" id="saveEdit">저장</button>
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