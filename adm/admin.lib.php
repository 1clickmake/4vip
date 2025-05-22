<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가

if(!$is_admin){
	alert('접근하실 수 없습니다.');
}

// 최근 7일간의 방문자 통계 조회
$sql = "SELECT 
            DATE(visit_time) as visit_date,
            COUNT(*) as visit_count
        FROM cm_visit
        WHERE visit_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(visit_time)
        ORDER BY visit_date ASC";
$result = sql_all_list($sql);

// 최근 7일간 시간대별 방문자 통계 조회
$hourly_sql = "SELECT 
                DATE(visit_time) as visit_date,
                HOUR(visit_time) as visit_hour,
                COUNT(*) as visit_count
               FROM cm_visit
               WHERE visit_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
               GROUP BY DATE(visit_time), HOUR(visit_time)
               ORDER BY visit_date ASC, visit_hour ASC";
$hourly_result = sql_all_list($hourly_sql);

$visit_dates = [];
$visit_counts = [];
$total_visits = 0;

// 시간대별 데이터 초기화 (7일 x 24시간)
$hourly_data = [];
$dates = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dates[] = date('m/d', strtotime($date));
    $hourly_data[$date] = array_fill(0, 24, 0);
}

if ($result) {
    foreach ($result as $row) {
        $visit_dates[] = date('m/d', strtotime($row['visit_date']));
        $visit_counts[] = (int)$row['visit_count'];
        $total_visits += $row['visit_count'];
    }
}

// 시간대별 데이터 채우기
if ($hourly_result) {
    foreach ($hourly_result as $row) {
        $date = date('Y-m-d', strtotime($row['visit_date']));
        if (isset($hourly_data[$date])) {
            $hourly_data[$date][$row['visit_hour']] = (int)$row['visit_count'];
        }
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

// 어제 작성된 게시물 수
$yesterday_posts_sql = "SELECT COUNT(*) as cnt FROM cm_board WHERE DATE(reg_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
$yesterday_posts_result = sql_fetch($yesterday_posts_sql);
$yesterday_posts = $yesterday_posts_result ? $yesterday_posts_result['cnt'] : 0;