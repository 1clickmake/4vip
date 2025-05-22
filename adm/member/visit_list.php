<?php
include_once './_common.php';
$cm_title = "방문자 관리";
include_once CM_ADMIN_PATH.'/admin.head.php';

// 날짜 구간 및 IP로 검색 및 삭제 처리 
// 기본값: 시작날짜 = 1주일 전, 종료날짜 = 오늘
$default_start_date = date('Y-m-d', strtotime('-1 week'));
$default_end_date = date('Y-m-d');

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : $default_start_date;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : $default_end_date;
$ip_address = !empty($_GET['ip_address']) ? $_GET['ip_address'] : null;

// 삭제 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_by_date'])) {
    try {
        global $pdo;
        
        // 기본 WHERE 조건 구성
        $whereClause = "visit_time BETWEEN :start_date AND :end_date";
        $params = [
            ':start_date' => $start_date,
            ':end_date' => $end_date . ' 23:59:59'
        ];

        // IP 주소가 입력된 경우 조건 추가
        if ($ip_address) {
            $whereClause .= " AND ip_address = :ip_address";
            $params[':ip_address'] = $ip_address;
        }

        // DELETE 쿼리 실행
        $sql = "DELETE FROM cm_visit WHERE " . $whereClause;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        if ($stmt->rowCount() > 0) {
            $delete_message = "선택한 조건의 방문자 기록이 삭제되었습니다.";
        } else {
            $delete_message = "삭제할 데이터가 없습니다.";
        }
    } catch (Exception $e) {
        $delete_message = "삭제 실패: " . $e->getMessage();
    }
}

// 방문자 통계
$stats = [];
try {
    // 총 방문자 수
    $stmt = $pdo->query("SELECT SUM(visit_count) as total FROM cm_visit");
    $stats['total'] = $stmt->fetch()['total'];
    
    // 오늘 방문자 수
    $stmt = $pdo->query("SELECT SUM(visit_count) as today FROM cm_visit WHERE DATE(visit_time) = CURDATE()");
    $stats['today'] = $stmt->fetch()['today'];
    
    // IP별 방문 횟수
    $stmt = $pdo->query("SELECT ip_address, SUM(visit_count) as count FROM cm_visit GROUP BY ip_address ORDER BY count DESC LIMIT 5");
    $stats['top_ips'] = $stmt->fetchAll();
} catch (PDOException $e) {
    $stats_error = "통계 조회 실패: " . $e->getMessage();
}

// 목록 조회 옵션 설정
$options = [
    'table' => 'cm_visit',
    'page' => $_GET['page'] ?? 1,
    'per_page' => 20,
    'order_by' => 'visit_time DESC',
    'conditions' => []
];

// 정렬 처리
$sort_field = $_GET['sort'] ?? 'visit_time';
$sort_order = $_GET['order'] ?? 'DESC';

// 정렬 가능한 필드 목록
$sortable_fields = ['ip_address', 'visit_time', 'user_agent', 'referer', 'visit_count'];

// 정렬 필드가 유효한지 확인
if (in_array($sort_field, $sortable_fields)) {
    $options['order_by'] = $sort_field . ' ' . $sort_order;
}

// 검색 조건이 있는 경우
if (!empty($start_date) && !empty($end_date)) {
    $options['conditions'][] = [
        'field' => 'visit_time',
        'operator' => 'BETWEEN',
        'value' => [$start_date . ' 00:00:00', $end_date . ' 23:59:59']
    ];
}

// IP 주소 검색 조건
if (!empty($ip_address)) {
    $options['conditions'][] = [
        'field' => 'ip_address',
        'operator' => 'LIKE',
        'value' => $ip_address . '%'
    ];
}

$result = sql_list($options);
$total_pages = $result['total_pages'];
$page = $result['current_page'];

// 검색 결과 메시지
if (!empty($start_date) || !empty($end_date) || !empty($ip_address)) {
    if (empty($result['list'])) {
        $search_message = "검색 결과가 없습니다.";
    } else {
        $search_message = "총 " . $result['total_rows'] . "개의 결과가 검색되었습니다.";
    }
}
?>

    <!-- Main Content -->
    <div class="main-content shifted" id="mainContent">
        <div class="container-fluid">
			<h2 class="admin-list-title"><?php echo $cm_title;?></h2> 
			
			<!-- 방문자 통계 -->
			<div class="card mb-4">
				<div class="card-header">방문자 통계</div>
				<div class="card-body">
					<?php if (isset($stats_error)): ?>
						<div class="alert alert-danger"><?php echo $stats_error; ?></div>
					<?php else: ?>
						<p>총 방문자 수: <?php echo $stats['total']; ?></p>
						<p>오늘 방문자 수: <?php echo $stats['today']; ?></p>
						<h5>상위 5개 IP</h5>
						<ul>
							<?php foreach ($stats['top_ips'] as $ip): ?>
								<li><?php echo htmlspecialchars($ip['ip_address']); ?>: <?php echo $ip['count']; ?>회</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>
			
			<!-- 날짜 구간 검색 및 삭제 -->
			<div class="card mb-4">
				<div class="card-header">날짜 구간으로 검색 및 삭제</div>
				<div class="card-body">
					<?php if (isset($delete_message)): ?>
						<div class="alert <?php echo strpos($delete_message, '실패') ? 'alert-danger' : 'alert-success'; ?>">
							<?php echo $delete_message; ?>
						</div>
					<?php endif; ?>
					<?php if (isset($search_message)): ?>
						<div class="alert alert-info">
							<?php echo $search_message; ?>
						</div>
					<?php endif; ?>
					<form method="GET" id="searchForm">
						<div class="row mb-3">
							<div class="col-md-4">
								<label for="start_date" class="form-label">시작 날짜</label>
								<input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>" required>
							</div>
							<div class="col-md-4">
								<label for="end_date" class="form-label">종료 날짜</label>
								<input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>" required>
							</div>
							<div class="col-md-4">
								<label for="ip_address" class="form-label">IP 주소 (선택사항)</label>
								<input type="text" class="form-control" id="ip_address" name="ip_address" value="<?php echo $ip_address; ?>" placeholder="예: 192.168.1.1">
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 text-end">
								<button type="submit" class="btn btn-primary me-2">검색</button>
								<button type="button" class="btn btn-danger me-2" onclick="confirmDelete()">삭제</button>
								<?php if (!empty($start_date) || !empty($end_date) || !empty($ip_address)): ?>
								<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">전체보기</a>
								<?php endif; ?>
							</div>
						</div>
					</form>
				</div>
			</div>
			
			<!-- 방문자 목록 -->
			<div class="table-responsive">
				<table class="table table-sm table-striped table-bordered align-middle" style="min-width:1200px;">
					<thead class="table-dark text-center">
						<tr>
							<th scope="col">ID</th>
							<th scope="col" class="sortable" data-field="ip_address">
								IP 주소 <i class="bi bi-arrow-down-up"></i>
								<?php echo get_sort_icon($sort_field, $sort_order, 'ip_address'); ?>
							</th>
							<th scope="col" class="sortable" data-field="visit_time">
								방문 시간 <i class="bi bi-arrow-down-up"></i>
								<?php echo get_sort_icon($sort_field, $sort_order, 'visit_time'); ?>
							</th>
							<th scope="col" class="sortable" data-field="user_agent">
								사용자 에이전트 <i class="bi bi-arrow-down-up"></i>
								<?php echo get_sort_icon($sort_field, $sort_order, 'user_agent'); ?>
							</th>
							<th scope="col" class="sortable" data-field="referer">
								참조 URL <i class="bi bi-arrow-down-up"></i>
								<?php echo get_sort_icon($sort_field, $sort_order, 'referer'); ?>
							</th>
							<th scope="col" class="sortable" data-field="visit_count">
								방문 횟수 <i class="bi bi-arrow-down-up"></i>
								<?php echo get_sort_icon($sort_field, $sort_order, 'visit_count'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($result['list'])){ ?>
							<tr>
								<td colspan="6" class="text-center">데이터가 없습니다.</td>
							</tr>
						<?php } else { ?>
							<?php 
							$start_number = $result['total_rows'] - ($page - 1) * $options['per_page'];
							foreach ($result['list'] as $index => $list) {
								$list_no = $start_number - $index;
							?>
							<tr class="text-center">
								<td><?php echo $list_no;?></td>
								<td><?php echo htmlspecialchars($list['ip_address']);?></td>
								<td><?php echo $list['visit_time'];?></td>
								<td><?php echo htmlspecialchars($list['user_agent']);?></td>
								<td><?php echo htmlspecialchars($list['referer'] ?? '');?></td>
								<td><?php echo $list['visit_count'];?></td>
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

<script>
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
            
            // 기존 검색 조건 유지
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const ipAddress = document.getElementById('ip_address').value;
            
            if (startDate) urlParams.set('start_date', startDate);
            if (endDate) urlParams.set('end_date', endDate);
            if (ipAddress) urlParams.set('ip_address', ipAddress);
            
            // 정렬 파라미터 설정
            urlParams.set('sort', field);
            urlParams.set('order', newOrder);
            
            // 페이지 이동
            window.location.href = window.location.pathname + '?' + urlParams.toString();
        });
    });
});

// 삭제 확인 함수
function confirmDelete() {
    if (confirm('정말로 삭제하시겠습니까?')) {
        const form = document.getElementById('searchForm');
        const deleteForm = document.createElement('form');
        deleteForm.method = 'POST';
        deleteForm.action = window.location.pathname;
        
        // 검색 폼의 모든 입력값을 복사
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = input.name;
            hiddenInput.value = input.value;
            deleteForm.appendChild(hiddenInput);
        });
        
        // 삭제 액션 추가
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'delete_by_date';
        actionInput.value = '1';
        deleteForm.appendChild(actionInput);
        
        document.body.appendChild(deleteForm);
        deleteForm.submit();
    }
}
</script>

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

<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>