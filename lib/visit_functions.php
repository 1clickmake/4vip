<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가

function get_visit() {
    global $pdo;
    
    try {
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $visit_time = date('Y-m-d H:i:s');
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        
        // 참조 URL 처리
        if ($referer) {
            $url_parts = parse_url($referer);
            $path = $url_parts['path'];
            $query = isset($url_parts['query']) ? $url_parts['query'] : '';
            
            // board 파라미터가 있는지 확인
            parse_str($query, $query_params);
            if (isset($query_params['board'])) {
                // board 값이 있으면 board=1111 까지 포함
                $referer = $url_parts['scheme'] . '://' . $url_parts['host'] . $path . '?board=' . $query_params['board'];
            } else {
                // board 값이 없으면 파일명까지만
                $referer = $url_parts['scheme'] . '://' . $url_parts['host'] . $path;
            }
        }
        
        // 동일 IP와 참조 URL이 있는지 확인
        $stmt = $pdo->prepare("SELECT id, visit_count FROM cm_visit WHERE ip_address = ? AND referer = ?");
        $stmt->execute([$ip_address, $referer]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // 기존 레코드가 있으면 방문 횟수 증가
            $stmt = $pdo->prepare("UPDATE cm_visit SET visit_count = visit_count + 1, visit_time = ? WHERE id = ?");
            $stmt->execute([$visit_time, $existing['id']]);
        } else {
            // 새 레코드 추가
            $stmt = $pdo->prepare("INSERT INTO cm_visit (ip_address, visit_time, user_agent, referer, visit_count) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$ip_address, $visit_time, $user_agent, $referer]);
        }
    } catch (PDOException $e) {
        error_log("방문자 기록 실패: " . $e->getMessage());
    }
}
?>