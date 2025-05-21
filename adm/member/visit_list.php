<?php
include_once './_common.php';
$cm_title = "방문자 관리";
include_once CM_ADMIN_PATH.'/admin.head.php';

// 날짜 구간 및 IP로 검색 및 삭제 처리 
// 기본값: 시작날짜 = 1주일 전, 종료날짜 = 오늘
$default_start_date = date('Y-m-d', strtotime('-1 week'));
$default_end_date = date('Y-m-d');

$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : $default_start_date;
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : $default_end_date;
$ip_address = !empty($_POST['ip_address']) ? $_POST['ip_address'] : null;

// 삭제 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_by_date'])) {
    try {
        if ($ip_address) {
            // IP 주소가 입력된 경우
            $stmt = $pdo->prepare("DELETE FROM cm_visit WHERE visit_time BETWEEN ? AND ? AND ip_address = ?");
            $stmt->execute([$start_date, $end_date . ' 23:59:59', $ip_address]);
        } else {
            // IP 주소가 입력되지 않은 경우
            $stmt = $pdo->prepare("DELETE FROM cm_visit WHERE visit_time BETWEEN ? AND ?");
            $stmt->execute([$start_date, $end_date . ' 23:59:59']);
        }
        $delete_message = "선택한 조건의 방문자 기록이 삭제되었습니다.";
    } catch (PDOException $e) {
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

// 검색 조건이 있는 경우
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_by_date'])) {
    // 날짜 검색 조건
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
            'value' => $ip_address
        ];
    }
}

$result = sql_list($options);
$total_pages = $result['total_pages'];
$page = $result['current_page'];

// 검색 결과 메시지
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_by_date'])) {
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
					<form method="POST">
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
								<button type="submit" name="search_by_date" class="btn btn-primary me-2">검색</button>
								<button type="submit" name="delete_by_date" class="btn btn-danger" onclick="return confirm('정말로 삭제하시겠습니까?');">삭제</button>
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
							<th scope="col">IP 주소</th>
							<th scope="col">방문 시간</th>
							<th scope="col">사용자 에이전트</th>
							<th scope="col">참조 URL</th>
							<th scope="col">방문 횟수</th>
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

    
<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>