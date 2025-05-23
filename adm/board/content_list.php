<?php
include_once './_common.php';
$cm_title = "내용관리";
include_once CM_ADMIN_PATH.'/admin.head.php';

$options = [
    'table' => 'cm_content',
    'page' => $_GET['page'] ?? 1,
    'per_page' => 20,
    'order_by' => 'id DESC',
    'conditions' => []
];

// 정렬 처리
$sort_field = $_GET['sort'] ?? 'id';
$sort_order = $_GET['order'] ?? 'DESC';

// 정렬 가능한 필드 목록
$sortable_fields = ['co_id', 'co_subject', 'co_editor', 'co_width'];

// 정렬 필드가 유효한지 확인
if (in_array($sort_field, $sortable_fields)) {
    $options['order_by'] = $sort_field . ' ' . $sort_order;
}

// 검색 조건이 있는 경우에만 conditions에 추가
if (!empty($_GET['search_type']) && !empty($_GET['search_keyword'])) {
    $search_type = $_GET['search_type'];
    $search_keyword = $_GET['search_keyword'];
    
    switch($search_type) {
        case 'co_id':
            $options['conditions'][] = ['field' => 'co_id', 'operator' => 'LIKE', 'value' => "%{$search_keyword}%"];
            break;
        case 'co_subject':
            $options['conditions'][] = ['field' => 'co_subject', 'operator' => 'LIKE', 'value' => "%{$search_keyword}%"];
            break;
    }
}

$result = sql_list($options);
$total_pages = $result['total_pages'];
$page = $result['current_page'];
?>

    <!-- Main Content -->
    <div class="main-content shifted" id="mainContent">
        <div class="container-fluid">
			<div class="d-flex justify-content-between align-items-center mb-4">
				<h2 class="admin-list-title"><?php echo $cm_title;?></h2>
				<a href="content_form.php" class="btn btn-primary">내용추가</a>
			</div>

			<!-- 검색 폼 -->
			<div class="card mb-4">
				<div class="card-body">
					<form method="get" class="row g-3" id="searchForm" onsubmit="return validateSearch()">
						<div class="col-md-3">
							<label for="search_type" class="form-label">검색 구분 <span class="text-danger">*</span></label>
							<select class="form-select" id="search_type" name="search_type" required>
								<option value="">선택하세요</option>
								<option value="co_id" <?php echo (isset($_GET['search_type']) && $_GET['search_type'] === 'co_id') ? 'selected' : ''; ?>>ID</option>
								<option value="co_subject" <?php echo (isset($_GET['search_type']) && $_GET['search_type'] === 'co_subject') ? 'selected' : ''; ?>>제목</option>
							</select>
						</div>
						<div class="col-md-6">
							<label for="search_keyword" class="form-label">검색어 <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="search_keyword" name="search_keyword" value="<?php echo isset($_GET['search_keyword']) ? htmlspecialchars($_GET['search_keyword']) : ''; ?>" required>
						</div>
						<div class="col-md-3 d-flex align-items-end">
							<button type="submit" class="btn btn-primary me-2">검색</button>
							<a href="content_list.php" class="btn btn-secondary">초기화</a>
						</div>
					</form>
				</div>
			</div>

			<div class="table-responsive">
				<table class="table table-sm table-striped table-bordered align-middle" style="min-width:1200px;">
					<thead class="table-dark text-center">
						<tr>
							<th scope="col">
								No
							</th>
							<th scope="col" class="sortable" data-field="co_id">
								ID <i class="bi bi-arrow-down-up"></i>
								<?php echo get_sort_icon($sort_field, $sort_order, 'co_id'); ?>
							</th>
							<th scope="col" class="sortable" data-field="co_subject">
								제목 <i class="bi bi-arrow-down-up"></i>
								<?php echo get_sort_icon($sort_field, $sort_order, 'co_subject'); ?>
							</th>
							<th scope="col" class="sortable" data-field="co_editor">
								에디터 <i class="bi bi-arrow-down-up"></i>
								<?php echo get_sort_icon($sort_field, $sort_order, 'co_editor'); ?>
							</th>
							<th scope="col" class="sortable" data-field="co_width">
								레이아웃 <i class="bi bi-arrow-down-up"></i>
								<?php echo get_sort_icon($sort_field, $sort_order, 'co_width'); ?>
							</th>
							<th scope="col">관리</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($result['list'])){ ?>
							<tr>
								<td colspan="6" class="text-center">등록된 내용이 없습니다.</td>
							</tr>
						<?php } else { ?>
							<?php 
							$start_number = $result['total_rows'] - ($page - 1) * $options['per_page'];
							foreach ($result['list'] as $index => $list) {
								$list_no = $start_number - $index;
								if($list['co_editor'] == 1){
									$edt = "개발";
								}else{
									$edt = "기본";
								}
								$width = $list['co_width'] == 1 ? "전체" : "기본";
							?>
							<tr class="text-center">
								<td><?php echo $list_no;?></td>
								<td><a href="<?php echo CM_URL?>/content/content.php?co_id=<?php echo urlencode($list['co_id']); ?>"><?php echo htmlspecialchars($list['co_id']);?></a></td>
								<td><?php echo htmlspecialchars($list['co_subject']);?></td>
								<td><?php echo $edt; ?></td>
								<td><?php echo $width; ?></td>
								<td>
									<a href="content_form.php?id=<?php echo $list['id'];?>" class="btn btn-sm btn-primary">수정</a>
									<button type="button" class="btn btn-sm btn-danger" onclick="deleteContent(<?php echo $list['id'];?>, '<?php echo htmlspecialchars($list['co_id']);?>')">삭제</button>
									<a href="<?php echo CM_URL?>/content/content.php?co_id=<?php echo urlencode($list['co_id']); ?>" class="btn btn-sm btn-secondary">확인</a>
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
    </div>

<style>
.sortable {
    position: relative;
    padding-right: 20px !important;
}
.sortable i {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
}
</style>

<script>
// 검색 유효성 검사 함수
function validateSearch() {
    const searchType = document.getElementById('search_type').value;
    const searchKeyword = document.getElementById('search_keyword').value.trim();
    
    if (!searchType) {
        alert('검색 구분을 선택해주세요.');
        document.getElementById('search_type').focus();
        return false;
    }
    
    if (!searchKeyword) {
        alert('검색어를 입력해주세요.');
        document.getElementById('search_keyword').focus();
        return false;
    }
    
    return true;
}

// 정렬 처리 함수
document.addEventListener('DOMContentLoaded', function() {
    const sortableHeaders = document.querySelectorAll('.sortable');
    
    sortableHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            const field = this.dataset.field;
            const currentSort = '<?php echo $sort_field; ?>';
            const currentOrder = '<?php echo $sort_order; ?>';
            
            let newOrder = 'ASC';
            if (field === currentSort && currentOrder === 'ASC') {
                newOrder = 'DESC';
            }
            
            // 현재 URL 파라미터 가져오기
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort', field);
            urlParams.set('order', newOrder);
            
            // 페이지 이동
            window.location.href = window.location.pathname + '?' + urlParams.toString();
        });
    });
});

function deleteContent(id, co_id) {
    if (confirm(`정말로 "${co_id}" 내용을 삭제하시겠습니까?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'content_form_update.php';
        
        const wInput = document.createElement('input');
        wInput.type = 'hidden';
        wInput.name = 'w';
        wInput.value = 'delete';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        
        form.appendChild(wInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>