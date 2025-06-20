<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가

// 데이터베이스 연결 정보
$host = 'localhost';
$dbname = 'sql_php7_cmake';
$username = 'sql_php7_cmake';
$password = '01055403957w';

// PDO를 사용한 데이터베이스 연결
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	// 디버깅을 위해 오류 메시지 기록 (운영 환경에서는 주석 처리)
	$errorMessage = $e->getMessage();
	$encodedMessage = mb_convert_encoding($errorMessage, 'UTF-8', 'auto');
    error_log('Database connection failed: ' . $encodedMessage);
    header('Location: ../install/index.php');
    exit();
}