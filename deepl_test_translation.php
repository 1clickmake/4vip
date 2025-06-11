<?php
include_once './common.php'; // 경로 및 설정 로드
if (!defined('CM_LIB_PATH')) {
    // CM_LIB_PATH가 정의되지 않은 경우를 위한 기본값 설정 또는 오류 처리
    // 예시: define('CM_LIB_PATH', __DIR__ . '/lib');
    // common.php 또는 config.php에서 CM_LIB_PATH가 정의되어 있어야 합니다.
    die("CM_LIB_PATH is not defined. Check your common.php or config.php.");
}
include_once CM_LIB_PATH . '/deepl_translate_helper.php';

$target_lang_param = $_GET['target_lang'] ?? 'EN-US'; // 기본값 영어 (DeepL은 EN-US, EN-GB 등을 구분)
$target_lang_display = $target_lang_param; // 표시용

$sample_text_ko = "안녕하세요, 세계! 이것은 DeepL 번역 기능 테스트입니다. 이 문장이 성공적으로 번역되기를 바랍니다.";
$sample_text_en = "Hello, world! This is a DeepL translation feature test. I hope this sentence gets translated successfully.";

// 대상 언어에 따라 원본 텍스트와 원본 언어 표시 선택
if (strtoupper($target_lang_param) === 'KO') {
    $text_to_translate = $sample_text_en;
    $source_lang_for_display = '영어 (English)';
} else {
    $text_to_translate = $sample_text_ko;
    $source_lang_for_display = '한국어 (Korean)';
}

$translated_text = deepl_translate($text_to_translate, $target_lang_param);

include_once CM_PATH.'/head.php';
?>
<div class="container mt-4 mb-4">
    <h1 class="mb-4">DeepL API 번역 테스트</h1>

    <div class="card">
        <div class="card-header"><strong>원본 텍스트 (<?php echo htmlspecialchars($source_lang_for_display); ?>)</strong></div>
        <div class="card-body">
            <p><?php echo htmlspecialchars($text_to_translate); ?></p>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header"><strong>번역된 텍스트 (대상 언어: <?php echo htmlspecialchars($target_lang_display); ?>)</strong></div>
        <div class="card-body">
            <?php if ($translated_text !== null): ?>
                <p><?php echo htmlspecialchars($translated_text); ?></p>
            <?php else: ?>
                <p class="text-danger">번역에 실패했습니다. <code>lib/deepl_translate_helper.php</code> 파일에서 API 키 설정을 확인하거나 서버 오류 로그를 확인해주세요.</p>
            <?php endif; ?>
        </div>
    </div>

    <a href="<?php echo CM_URL; ?>" class="btn btn-primary mt-3">홈으로 돌아가기</a>
    <hr class="mt-4">
    <p><small>참고: 이 페이지는 DeepL API 연동을 테스트하기 위한 것입니다. 실제 웹사이트 전체의 실시간 번역은 JavaScript를 이용한 DOM 조작 또는 서버 측 출력 버퍼링과 같은 추가적인 고급 구현이 필요합니다.</small></p>
</div>
<?php
include_once CM_PATH.'/tail.php';
?>