<?php
include_once './_common.php';
$cm_title = "내용관리";
include_once CM_ADMIN_PATH.'/admin.head.php';

// 초기화
$mode = 'insert';
$content = [
    'id' => '',
    'co_id' => '',
    'co_subject' => '',
    'co_content' => '',
    'co_editor' => 0,
    'co_width' => 1
];

// 수정 모드인 경우 데이터 가져오기
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $mode = 'update';
    
    try {
        $sql = "SELECT * FROM cm_content WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($row = $stmt->fetch()) {
            $content = $row;
        } else {
            echo '<script>alert("존재하지 않는 내용입니다."); location.href="content_list.php";</script>';
            exit;
        }
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger">데이터 조회 중 오류가 발생했습니다: ' . $e->getMessage() . '</div>';
    }
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.56.0/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.56.0/theme/eclipse.css">

<!-- Main Content -->
<div class="main-content shifted" id="mainContent">
    <div class="container-fluid">

		<!-- 헤더 카드 -->
            <div class="card shadow-sm mb-4 border-0 card-move">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="card-title mb-1 text-primary">
                                <i class="fas fa-window-restore me-2"></i><?php echo $cm_title;?>
                            </h2>
                            <p class="card-text text-muted mb-0">페이지를 추가 / 관리 / 설정할 수 있습니다.</p>
                        </div>
                        <div>
                            <a href="content_list.php" class="btn btn-primary btn-lg shadow-sm">
                                <i class="fas fa-list me-2"></i>목록으로
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog me-2"></i>
                            내용 설정
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form name="frmcontentform" action="./content_form_update.php" onsubmit="return frmcontentform_check(this);" method="post">
                            <input type="hidden" name="w" value="<?php echo $mode; ?>">
                            <input type="hidden" name="id" value="<?php echo $content['id']; ?>">
                            
                            <div class="mb-3">
                                <label for="co_id" class="form-label">ID</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="co_id" name="co_id" value="<?php echo htmlspecialchars($content['co_id'], ENT_QUOTES, 'UTF-8'); ?>" required readonly>
                                    <?php if (!empty($content['co_id'])) { ?>
                                    <a href="<?php echo CM_URL?>/content/content.php?co_id=<?php echo urlencode($content['co_id']); ?>" class="btn btn-outline-secondary" id="checkIdBtn" target="_blank">내용확인</a>
                                    <?php } ?>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    ID는 영문 또는 숫자만 입력 가능하며, 10단위로 자동 생성됩니다.
                                </div>
                                <div id="idCheckResult" class="form-text mt-1"></div>
                            </div>

                            <div class="mb-3">
                                <label for="co_subject" class="form-label">제목</label>
                                <input type="text" class="form-control" id="co_subject" name="co_subject" value="<?php echo $content['co_subject']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">에디터 선택</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="co_editor" id="co_editor0" value="0" <?php echo ($content['co_editor'] == 0) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="co_editor0">기본에디터</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="co_editor" id="co_editor1" value="1" <?php echo ($content['co_editor'] == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="co_editor1">개발자용에디터</label>
                                </div>
                            </div>

                            <div class="mb-3 co_content_html">
                                <label for="co_content" class="form-label">내용</label>
                                <div class="border rounded p-2">
                                    <textarea class="form-control border-0" id="summernote" rows="10"><?php echo $content['co_content']; ?></textarea>
                                    <input type="hidden" name="co_content" value="<?php echo htmlspecialchars($content['co_content']); ?>">
                                </div>
                            </div>

                            <div class="mb-3 co_add_html" style="display: none;">
                                <label for="co_add_html" class="form-label">개발자용 에디터</label>
                                <div class="alert alert-warning mb-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    기본에디터로 원하는 기능을 구현하지 못할 경우 개발자용 에디터에서 <span class="text-primary fw-bold">HTML+PHP+JS+CSS</span> 등 언어로 페이지 개발을 할수 있습니다.
                                    <br>
                                    <span class="text-danger">작성한 소스의 오류시 사이트에 심각한 에러가 발생할 수 있으니 주의해서 작성해 주시길 바랍니다.</span>
                                </div>
                                <textarea id="co_add_html" rows="10" class="form-control"><?php echo $content['co_content']; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">레이아웃 설정</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="co_width" id="co_width" value="1" <?php echo ($content['co_width'] == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="co_width">
                                        전체 너비 사용 (width: 100%)
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="content_list.php" class="btn btn-secondary">
                                    <i class="fas fa-list me-2"></i>목록
                                </a>
                                <?php if ($mode == 'update') { ?>
                                <button type="button" class="btn btn-danger" onclick="deleteContent(<?php echo $content['id'];?>, '<?php echo htmlspecialchars($content['co_id']);?>')">
                                    <i class="fas fa-trash-alt me-2"></i>삭제
                                </button>
                                <?php } ?>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>저장
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.56.0/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.56.0/addon/edit/matchbrackets.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.56.0/mode/htmlmixed/htmlmixed.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.56.0/mode/xml/xml.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.56.0/mode/javascript/javascript.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.56.0/mode/css/css.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.56.0/mode/clike/clike.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.56.0/mode/php/php.js"></script>

<script>
$(document).ready(function() {
    // 수정 모드 여부 확인
    const isEditMode = <?php echo isset($_GET['id']) ? 'true' : 'false'; ?>;
    
    // ID 중복 체크 함수
    function checkIdDuplicate(co_id) {
        if (!co_id || isEditMode) return; // 수정 모드일 경우 중복 체크 하지 않음
        
        $.ajax({
            url: '<?php echo CM_ADMIN_URL;?>/ajax/check_content_id.php',
            method: 'POST',
            data: { co_id: co_id },
            success: function(response) {
                const resultDiv = $('#idCheckResult');
                if (response.exists) {
                    resultDiv.html('<span class="text-danger"><i class="fas fa-times-circle me-1"></i>이미 사용 중인 ID입니다.</span>');
                    $('#co_id').addClass('is-invalid');
                } else {
                    resultDiv.html('<span class="text-success"><i class="fas fa-check-circle me-1"></i>사용 가능한 ID입니다.</span>');
                    $('#co_id').removeClass('is-invalid').addClass('is-valid');
                }
            },
            error: function() {
                $('#idCheckResult').html('<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>ID 확인 중 오류가 발생했습니다.</span>');
            }
        });
    }

    // ID 자동 생성 함수
    function generateNextId() {
        if (isEditMode) return; // 수정 모드일 경우 자동 생성 하지 않음
        
        $.ajax({
            url: '<?php echo CM_ADMIN_URL;?>/ajax/get_next_content_id.php',
            method: 'GET',
            success: function(response) {
                if(response.success) {
                    $('#co_id').val(response.next_id);
                    checkIdDuplicate(response.next_id);
                } else {
                    alert('ID 생성 중 오류가 발생했습니다.');
                }
            },
            error: function() {
                alert('서버 통신 중 오류가 발생했습니다.');
            }
        });
    }

    // 페이지 로드 시 ID가 비어있으면 자동 생성
    if($('#co_id').val() === '' && !isEditMode) {
        generateNextId();
    } else if (!isEditMode) {
        // 신규 등록 모드일 경우에만 중복 체크
        checkIdDuplicate($('#co_id').val());
    }

   
    // Summernote 에디터 초기화
    $('#summernote').summernote({
        height: 300,
        minHeight: null,
        maxHeight: null,
        callbacks: {
            onImageUpload: function(files) {
                if (files.length > 0) {
                    var file = files[0];
                    var data = new FormData();
                    data.append('image', file);
                    data.append('board_id', 'content');

                    $.ajax({
                        url: '<?php echo CM_LIB_URL;?>/summernote_upload.php',
                        method: 'POST',
                        data: data,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.url) {
                                $('#summernote').summernote('insertImage', response.url);
                            } else {
                                alert('이미지 업로드 실패: ' + (response.error || '알 수 없는 오류'));
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            alert('이미지 업로드 실패: ' + textStatus + ' (' + errorThrown + ')');
                        }
                    });
                }
            },
            onChange: function(contents) {
                $('input[name="co_content"]').val(contents);
            }
        }
    });

    // CodeMirror 에디터 초기화
    var editor = CodeMirror.fromTextArea(document.getElementById("co_add_html"), {
        theme: "eclipse",
        lineNumbers: true,
        matchBrackets: true,
        mode: "application/x-httpd-php",
        indentUnit: 4,
        indentWithTabs: true,
        lineWrapping: true,
        extraKeys: {"Ctrl-Space": "autocomplete"},
        autoCloseBrackets: true,
        matchBrackets: true,
        indentWithTabs: true,
        smartIndent: true,
        tabSize: 4,
        lineNumbers: true,
        foldGutter: true,
        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
    });

    // 에디터 초기 상태 설정
    <?php if($content['co_editor'] == 1) { ?>
        $('.co_add_html').show();
        $('.co_content_html').hide();
        // 개발자용 에디터에 기존 내용 설정
        setTimeout(function() {
            editor.setValue(<?php echo json_encode($content['co_content']); ?>);
            editor.refresh();
            // 초기 내용을 hidden input에 설정
            $('input[name="co_content"]').val(<?php echo json_encode($content['co_content']); ?>);
        }, 100);
    <?php } else { ?>
        $('.co_content_html').show();
        $('.co_add_html').hide();
        // Summernote에 기존 내용 설정
        $('#summernote').summernote('code', <?php echo json_encode($content['co_content']); ?>);
        // 초기 내용을 hidden input에 설정
        $('input[name="co_content"]').val(<?php echo json_encode($content['co_content']); ?>);
    <?php } ?>

    // 라디오 버튼 변경 이벤트
    $('input[name="co_editor"]').change(function() {
        if($(this).val() == '0') {
            $('.co_content_html').show();
            $('.co_add_html').hide();
            // 개발자용 에디터의 내용을 Summernote로 복사
            var editorContent = editor.getValue();
            $('#summernote').summernote('code', editorContent);
            $('input[name="co_content"]').val(editorContent);
        } else {
            $('.co_content_html').hide();
            $('.co_add_html').show();
            // Summernote의 내용을 개발자용 에디터로 복사
            var summernoteContent = $('#summernote').summernote('code');
            setTimeout(function() {
                editor.setValue(summernoteContent);
                editor.refresh();
                $('input[name="co_content"]').val(summernoteContent);
            }, 100);
        }
    });

    // CodeMirror 에디터 내용 변경 이벤트
    editor.on('change', function() {
        var content = editor.getValue();
        $('input[name="co_content"]').val(content);
    });

    // 폼 제출 전 내용 확인
    $('form[name="frmcontentform"]').on('submit', function() {
        if ($('input[name="co_editor"]:checked').val() == '0') {
            var content = $('#summernote').summernote('code').trim();
            if (!content) {
                alert("내용을 입력하세요.");
                $('#summernote').summernote('focus');
                return false;
            }
            $('input[name="co_content"]').val(content);
        } else {
            var content = editor.getValue().trim();
            if (!content) {
                alert("내용을 입력하세요.");
                editor.focus();
                return false;
            }
            $('input[name="co_content"]').val(content);
        }
        return true;
    });

    // 창 크기 변경 시 에디터 리프레시
    $(window).resize(function() {
        editor.refresh();
    });
});

function frmcontentform_check(f) {
    if (!f.co_id.value) {
        alert("ID를 입력하세요.");
        f.co_id.focus();
        return false;
    }

    // 신규 등록 모드일 경우에만 ID 중복 체크
    if (!isEditMode) {
        const idCheckResult = $('#idCheckResult').text();
        if (idCheckResult.includes('이미 사용 중인 ID입니다')) {
            alert("이미 사용 중인 ID입니다. 다른 ID를 사용해주세요.");
            f.co_id.focus();
            return false;
        }
    }

    if (!f.co_subject.value) {
        alert("제목을 입력하세요.");
        f.co_subject.focus();
        return false;
    }
    
    // 에디터 타입에 따른 내용 검증
    if (f.co_editor[0].checked) { // 기본 에디터
        if ($('#summernote').summernote('isEmpty')) {
            alert("내용을 입력하세요.");
            $('#summernote').summernote('focus');
            return false;
        }
        // Summernote 내용을 hidden input에 복사
        f.co_content.value = $('#summernote').summernote('code');
    } else { // 개발자용 에디터
        var editorContent = editor.getValue().trim();
        if (!editorContent) {
            alert("내용을 입력하세요.");
            editor.focus();
            return false;
        }
        // CodeMirror 내용을 hidden input에 복사
        f.co_content.value = editorContent;
    }
    return true;
}

function deleteContent(id, co_id) {
    if (confirm(`정말로 "${co_id}" 내용을 삭제하시겠습니까?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'content_form_update.php';
        
        const wInput = document.createElement('input');
        wInput.type = 'hidden';
        wInput.name = 'w';
        wInput.value = 'delete';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        
        form.appendChild(wInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>