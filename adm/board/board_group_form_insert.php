<?php
include_once './_common.php';

// POST 방식으로 데이터가 넘어왔는지 확인
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 폼에서 넘어온 데이터를 받습니다.
    // $_POST 변수는 폼의 name 속성과 일치하는 키를 가집니다.
    $groupId = $_POST['group_id'] ?? '';
    $groupName = $_POST['group_name'] ?? '';

    // 간단한 데이터 유효성 검사 (여기서는 기본적인 것만 하고, 더 상세히 할 수 있습니다)
    if (empty($groupId) || empty($groupName)) {
        // 필수 값이 비어있으면 오류 처리
        // 실제로는 사용자에게 오류 메시지를 보여주는 페이지로 리다이렉트하거나 메시지를 표시합니다.
        echo "<script>alert('필수 입력 값이 누락되었습니다.'); history.back();</script>";
        exit; // 코드 실행 중지
    }

    // 그룹 아이디 형식 유효성 검사 (영문, 기호, 숫자만 허용)
    // 모달 폼의 pattern 속성으로 1차 검증했지만, 서버에서 다시 검증하는 것이 안전합니다.
    if (!preg_match('/^[a-zA-Z0-9!@#$%^&*()_+=\-\[\]{};\':\\"\\|,.<>\/?~]*$/', $groupId)) {
         echo "<script>alert('그룹 아이디 형식이 올바르지 않습니다. 영문, 숫자, 기호만 사용 가능합니다.'); history.back();</script>";
         exit;
    }



    // process_data_insert 함수에 전달할 데이터 배열을 만듭니다.
    // 배열의 키는 데이터베이스 테이블의 컬럼 이름과 같아야 합니다.
    $groupData = [
        'group_id' => $groupId,
        'group_name' => $groupName
        // created_at 컬럼은 DB에서 CURRENT_TIMESTAMP로 자동 입력되므로 여기에 포함시키지 않아도 됩니다.
    ];

    // 두목님이 만드신 process_data_insert 함수를 호출하여 데이터 삽입 시도
    $insertResult = process_data_insert('cm_board_group', $groupData);

    // 삽입 결과 확인
    if ($insertResult !== false) {
        // 삽입 성공
        // 보통 그룹 목록 페이지 등으로 리다이렉트합니다.
        echo "<script>alert('그룹 [{$groupName}] 생성이 완료되었습니다.'); window.location.href = './board_group_list.php';</script>"; // 'admin_board_list.php'는 예시입니다.
        exit;
    } else {
        // 삽입 실패 (함수 내부에서 오류 로그는 남겼을 겁니다)
        echo "<script>alert('그룹 생성 중 오류가 발생했습니다.'); history.back();</script>";
        exit;
    }

} else {
    // POST 방식이 아닌 접근은 허용하지 않습니다.
    echo "<script>alert('잘못된 접근 방식입니다.'); window.location.href = './board_group_list.php';</script>"; // 'index.php'는 예시입니다.
    exit;
}
?>