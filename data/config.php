<?php
// 데이터베이스 연결 정보
$host = 'localhost';
$dbname = '';		//your_database_name
$username = '';	//your_username
$password = '';		//your_password

// PDO를 사용한 데이터베이스 연결
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // PDO 오류 모드를 예외 모드로 설정
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // 기본 페치 모드를 연관 배열로 설정
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("데이터베이스 연결 실패: " . $e->getMessage());
}

?>