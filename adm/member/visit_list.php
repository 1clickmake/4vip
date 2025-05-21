<?php
include_once './_common.php';
include_once CM_ADMIN_PATH.'/admin.head.php';

// 날짜 구간 및 IP로 검색 및 삭제 처리
// 기본값: 시작날짜 = 1주일 전, 종료날짜 = 오늘
$default_start_date = date('Y-m-d', strtotime('-1 week'));
$default_end_date = date('Y-m-d');

$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : $default_start_date;
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : $default_end_date;
$ip_address = !empty($_POST['ip_address']) ? $_POST['ip_address'] : null;
$search_results = [];
$is_searching = false;

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

// 검색 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_by_date'])) {
    $is_searching = true;
    try {
        $sql = "SELECT * FROM cm_visit WHERE visit_time BETWEEN ? AND ?";
        $params = [$start_date, $end_date . ' 23:59:59'];
        
        if ($ip_address) {
            $sql .= " AND ip_address = ?";
            $params[] = $ip_address;
        }
        
        $sql .= " ORDER BY visit_time DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $search_results = $stmt->fetchAll();
        
        if (count($search_results) === 0) {
            $search_message = "검색 결과가 없습니다.";
        } else {
            $search_message = "총 " . count($search_results) . "개의 결과가 검색되었습니다.";
        }
    } catch (PDOException $e) {
        $search_message = "검색 실패: " . $e->getMessage();
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

// 방문자 목록 조회 (검색 중이 아닐 때만)
if (!$is_searching) {
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;

    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM cm_visit");
        $total_records = $stmt->fetch()['total'];
        $total_pages = ceil($total_records / $per_page);
        
        $stmt = $pdo->prepare("SELECT * FROM cm_visit ORDER BY visit_time DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $per_page, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $visits = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error_message = "목록 조회 실패: " . $e->getMessage();
    }
}
?>

    <!-- Main Content -->
    <div class="main-content shifted" id="mainContent">
        <div class="container-fluid">
			<h2>방문자 관리</h2>
			
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
						<div class="alert <?php echo strpos($search_message, '실패') ? 'alert-danger' : 'alert-info'; ?>">
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
			
			<!-- 검색 결과 또는 방문자 목록 -->
			<div class="card">
				<div class="card-header"><?php echo $is_searching ? '검색 결과' : '방문자 목록'; ?></div>
				<div class="card-body">
					<?php if ($is_searching && isset($search_message) && strpos($search_message, '실패')): ?>
						<div class="alert alert-danger"><?php echo $search_message; ?></div>
					<?php elseif (isset($error_message) && !$is_searching): ?>
						<div class="alert alert-danger"><?php echo $error_message; ?></div>
					<?php else: ?>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>ID</th>
									<th>IP 주소</th>
									<th>방문 시간</th>
									<th>사용자 에이전트</th>
									<th>참조 URL</th>
									<th>방문 횟수</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								// 검색 중이면 검색 결과를, 아니면 기본 방문자 목록을 표시
								$display_data = $is_searching ? $search_results : $visits;
								?>
								<?php if (count($display_data) > 0): ?>
									<?php foreach ($display_data as $item): ?>
										<tr>
											<td><?php echo $item['id']; ?></td>
											<td><?php echo htmlspecialchars($item['ip_address']); ?></td>
											<td><?php echo $item['visit_time']; ?></td>
											<td><?php echo htmlspecialchars($item['user_agent']); ?></td>
											<td><?php echo htmlspecialchars($item['referer'] ?? ''); ?></td>
											<td><?php echo $item['visit_count']; ?></td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="6" class="text-center">데이터가 없습니다.</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
						
						<!-- 페이지네이션 (검색 중이 아닐 때만 표시) -->
						<?php if (!$is_searching && isset($total_pages)): ?>
							<nav aria-label="Page navigation">
								<ul class="pagination justify-content-center">
									<?php if ($page > 1): ?>
										<li class="page-item">
											<a class="page-link" href="?page=<?php echo $page - 1; ?>">이전</a>
										</li>
									<?php endif; ?>
									<?php for ($i = 1; $i <= $total_pages; $i++): ?>
										<li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
											<a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
										</li>
									<?php endfor; ?>
									<?php if ($page < $total_pages): ?>
										<li class="page-item">
											<a class="page-link" href="?page=<?php echo $page + 1; ?>">다음</a>
										</li>
									<?php endif; ?>
								</ul>
							</nav>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
    </div>

    
<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>