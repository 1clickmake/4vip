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
if (!isset($input['title']) || !isset($input['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Title and message are required']);
    exit();
}

// VAPID 키 설정 (실제 프로젝트에서는 환경 변수 등으로 관리)
$vapidKeys = [
    'publicKey' => $pwa_vapid_public_key,  // VAPID 공개키 
    'privateKey' => $pwa_vapid_private_key // VAPID 비공개키 
];

// 구독 정보 로드
$subscriptionsFile = __DIR__ . '/../data/push-subscriptions.json';
if (!file_exists($subscriptionsFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'No subscribers found']);
    exit();
}

$subscriptions = json_decode(file_get_contents($subscriptionsFile), true) ?: [];

// 알림 데이터 준비
$notification = [
    'title' => $input['title'],
    'message' => $input['message'],
    'data' => $input['data'] ?? [],
    'actions' => $input['actions'] ?? [],
    'tag' => $input['tag'] ?? 'quick-delivery-notification',
    'icon' => $input['icon'] ?? '/images/icons/icon-192x192.png',
    'badge' => '/images/icons/icon-96x96.png',
    'vibrate' => [200, 100, 200, 100, 200, 100, 400],
    'timestamp' => time()
];

// 각 구독자에게 알림 전송
$results = [];
$successCount = 0;

foreach ($subscriptions as $subscription) {
    try {
        $result = sendPushNotification($subscription, $notification, $vapidKeys);
        $results[] = [
            'endpoint' => $subscription['endpoint'],
            'success' => $result['success'],
            'message' => $result['message']
        ];
        
        if ($result['success']) {
            $successCount++;
        }
    } catch (Exception $e) {
        $results[] = [
            'endpoint' => $subscription['endpoint'],
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

// 결과 반환
http_response_code(200);
echo json_encode([
    'success' => true,
    'sent' => $successCount,
    'failed' => count($subscriptions) - $successCount,
    'results' => $results
]);

/**
 * 푸시 알림 전송 함수
 */
function sendPushNotification($subscription, $notification, $vapidKeys) {
    $endpoint = $subscription['endpoint'];
    $auth = $subscription['keys']['auth'];
    $p256dh = $subscription['keys']['p256dh'];
    
    // Web Push 라이브러리 로드
    require_once __DIR__ . '/../../vendor/autoload.php';
    
    try {
        $auth = [
            'VAPID' => [
                'subject' => $config['admin_email'], // 관리자 이메일
                'publicKey' => $vapidKeys['publicKey'],
                'privateKey' => $vapidKeys['privateKey']
            ]
        ];
        
        $webPush = new WebPush\WebPush($auth);
        
        $payload = json_encode([
            'title' => $notification['title'],
            'body' => $notification['message'],
            'icon' => $notification['icon'],
            'badge' => $notification['badge'],
            'vibrate' => $notification['vibrate'],
            'data' => $notification['data'],
            'actions' => $notification['actions'],
            'tag' => $notification['tag'],
            'timestamp' => $notification['timestamp']
        ]);
        
        $webPush->sendNotification(
            $endpoint,
            $payload,
            $p256dh,
            $auth,
            true, // flush
            [
                'TTL' => 3600, // 1시간 유효
                'urgency' => 'normal',
                'topic' => 'quick-delivery-updates'
            ]
        );
        
        return ['success' => true, 'message' => 'Notification sent'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
