<?php
// 오류 표시 (개발 중에만 사용)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// JSON 헤더 설정
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');

// 사이트 제목 가져오기 (기본값: '홈페이지')
$site_title = '홈페이지';

// 설정 파일이 있는 경우에만 로드
if (file_exists('./_common.php')) {
    require_once('./_common.php');
    if (isset($config['site_title'])) {
        $site_title = $config['site_title'];
    }
}

// 매니페스트 데이터 생성
$manifest = array(
    'name' => $site_title,
    'short_name' => $site_title,
    'id' => '/',
    'start_url' => '/',
    'display' => 'standalone',
    'background_color' => '#ffffff',
    'theme_color' => '#3498db',
    'description' => $site_title . ' 서비스',
    'orientation' => 'portrait',
    'icons' => [
        // 필수 아이콘 (192x192 또는 512x512 권장)
        [
            'src' => '/images/icons/iconss-192x192.png',
            'sizes' => '192x192',
            'type' => 'image/png',
            'purpose' => 'any maskable'
        ],
        // 고해상도 디스플레이를 위한 큰 아이콘
        [
            'src' => '/images/icons/iconss-512x512.png',
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'any maskable'
        ]
    ]
);

// JSON으로 출력 후 종료
$json = json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

// JSON 유효성 검사
if (json_last_error() !== JSON_ERROR_NONE) {
    $json = json_encode(['error' => 'JSON 생성 오류: ' . json_last_error_msg()]);
}

// 콘텐츠 길이 설정
header('Content-Length: ' . strlen($json));

// JSON 출력
echo $json;

// 스크립트 종료
exit();
