<?php
// 설정 파일 로드
require_once('./_common.php');

// 사이트 제목 가져오기 (기본값: '홈페이지')
$site_title = isset($config['site_title']) ? $config['site_title'] : '홈페이지';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>오프라인 상태 - <?php echo htmlspecialchars($site_title); ?></title>
    <style>
        body {
            font-family: 'Noto Sans KR', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        .offline-container {
            text-align: center;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
        }
        .offline-icon {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        h1 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: #dc3545;
        }
        p {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="offline-container">
        <div class="offline-icon">
            <i class="fas fa-wifi-slash"></i>
        </div>
        <h1>인터넷 연결 없음</h1>
        <p>현재 오프라인 상태입니다. 인터넷 연결을 확인한 후 다시 시도해 주세요.</p>
        <a href="/" class="btn">새로고침</a>
    </div>
    
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
