<?php
function get_visit() {
    global $pdo;
    
    try {
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $visit_time = date('Y-m-d H:i:s');
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        
        $stmt = $pdo->prepare("INSERT INTO cm_visit (ip_address, visit_time, user_agent, referer) VALUES (?, ?, ?, ?)");
        $stmt->execute([$ip_address, $visit_time, $user_agent, $referer]);
    } catch (PDOException $e) {
        error_log("방문자 기록 실패: " . $e->getMessage());
    }
}
?>