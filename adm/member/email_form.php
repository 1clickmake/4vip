<?php
include_once './_common.php';
include_once CM_ADMIN_PATH.'/admin.head.php';
include_once CM_LIB_PATH.'/send_gmail_function.php';
/* 
$result = send_gmail(
	'하데스'                 // 수신자 이름 
    'hades708@naver.com',    // 수신자 이메일
    '메일 보냅니다123',          // 제목
    '<h1>HTML 형식 본문입니다.</h1><p>안녕하세요!</p>', // 본문
    
);

if ($result['success']) {
    echo $result['message']; // 성공 메시지 출력
} else {
    echo "오류 발생: " . $result['message']; // 오류 메시지 출력
} */
?>

    <!-- Main Content -->
    <div class="main-content shifted" id="mainContent">
        <h1>email form</h1>
    </div>

    
<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>