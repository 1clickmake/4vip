<?php
include_once './_common.php'; // 기본 설정 및 라이브러리 로드

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// CORS 프리플라이트 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 실제 요청 처리
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// JSON 본문 파싱
$input = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit();
}

// 필수 필드 검증
if (!isset($input['subscription']['endpoint']) || 
    !isset($input['subscription']['keys']['p256dh']) || 
    !isset($input['subscription']['keys']['auth'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid subscription data']);
    exit();
}

// 여기서는 간단히 파일에 저장하는 예시를 보여드립니다.
// 실제 프로젝트에서는 데이터베이스에 저장하세요.
$subscriptionData = [
    'endpoint' => $input['subscription']['endpoint'],
    'keys' => [
        'p256dh' => $input['subscription']['keys']['p256dh'],
        'auth' => $input['subscription']['keys']['auth']
    ],
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
    'created_at' => date('Y-m-d H:i:s')
];

// 구독 정보를 파일에 저장 (실제로는 DB에 저장해야 함)
$subscriptions = [];
$filename = __DIR__ . '/../data/push-subscriptions.json';

// data 디렉토리가 없으면 생성
if (!file_exists(dirname($filename))) {
    mkdir(dirname($filename), 0755, true);
}

if (file_exists($filename)) {
    $subscriptions = json_decode(file_get_contents($filename), true) ?: [];
}

// 중복 체크
$subscriptionExists = false;
foreach ($subscriptions as $sub) {
    if ($sub['endpoint'] === $subscriptionData['endpoint']) {
        $subscriptionExists = true;
        break;
    }
}

if (!$subscriptionExists) {
    $subscriptions[] = $subscriptionData;
    file_put_contents($filename, json_encode($subscriptions, JSON_PRETTY_PRINT));
}

// 성공 응답
http_response_code(200);
echo json_encode(['success' => true]);
