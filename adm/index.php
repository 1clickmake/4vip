<?php
include_once './_common.php';
include_once CM_ADMIN_PATH.'/admin.head.php';

// 최근 7일간의 방문자 통계 조회
$sql = "SELECT 
            DATE(visit_time) as visit_date,
            COUNT(*) as visit_count
        FROM cm_visit
        WHERE visit_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(visit_time)
        ORDER BY visit_date ASC";
$result = sql_all_list($sql);

$visit_dates = [];
$visit_counts = [];
$total_visits = 0;

if ($result) {
    foreach ($result as $row) {
        $visit_dates[] = date('m/d', strtotime($row['visit_date']));
        $visit_counts[] = (int)$row['visit_count'];
        $total_visits += $row['visit_count'];
    }
}

// 오늘 방문자 수
$today_sql = "SELECT COUNT(*) as cnt FROM cm_visit WHERE DATE(visit_time) = CURDATE()";
$today_result = sql_fetch($today_sql);
$today_visits = $today_result ? $today_result['cnt'] : 0;

// 어제 방문자 수
$yesterday_sql = "SELECT COUNT(*) as cnt FROM cm_visit WHERE DATE(visit_time) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
$yesterday_result = sql_fetch($yesterday_sql);
$yesterday_visits = $yesterday_result ? $yesterday_result['cnt'] : 0;
?>

    <!-- Main Content -->
    <div class="main-content shifted" id="mainContent">
        <div class="container-fluid">
            <h2 class="mb-4">대시보드</h2>
            
            <!-- 통계 카드 -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">총 방문자</h5>
                            <h2 class="card-text"><?php echo number_format($total_visits);?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">오늘 방문자</h5>
                            <h2 class="card-text"><?php echo number_format($today_visits);?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">어제 방문자</h5>
                            <h2 class="card-text"><?php echo number_format($yesterday_visits);?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 방문자 통계 그래프 -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">최근 7일간 방문자 통계</h5>
                    <canvas id="visitChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('visitChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($visit_dates);?>,
                datasets: [{
                    label: '방문자 수',
                    data: <?php echo json_encode($visit_counts);?>,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1,
                    fill: true,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: '일별 방문자 통계'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
    </script>
    
<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>