<?php
$host = '49.247.44.118';
$dbname = 'sql_4vip_work';
$username = 'root';
$password = '01055403957w';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    echo "데이터베이스 연결 성공!\n";
    
    // 테이블 목록 조회
    $stmt = $pdo->query("SHOW TABLES");
    echo "\n테이블 목록:\n";
    while ($row = $stmt->fetch()) {
        echo "- " . $row[0] . "\n";
    }
} catch (PDOException $e) {
    echo "연결 실패: " . $e->getMessage();
}
?> 