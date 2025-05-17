<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
?>

<div class="container mt-5">
    <h2 class="mb-4"><?php echo $bo_title;?></h2>
    
    <form action="<?php echo $formAction;?>" method="post" enctype="multipart/form-data" id="writeForm">
        <input type="hidden" name="board_id" value="<?php echo $boardId;?>">
        <input type="hidden" name="board_num" value="<?php echo $boardNum ?? '';?>">
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
        
        <div class="mb-3">
            <label for="email" class="form-label">이메일</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($write['email'] ?? ''); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="name" class="form-label">이름</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($write['name'] ?? ''); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label">비밀번호 (변경시에만 입력)</label>
            <input type="password" class="form-control" id="password" name="password">
            <small class="text-muted">비밀번호를 변경하지 않으려면 비워두세요.</small>
        </div>
        
        <div class="mb-3">
            <label for="title" class="form-label">제목</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($write['title'] ?? ''); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="content" class="form-label">내용</label>
            <textarea id="summernote" name="content"><?php echo $write['content'] ?? ''; ?></textarea>
        </div>
        
        <!-- 기존 첨부파일 목록 -->
        <?php if ($currentFilename == "edit" && !empty($files)): ?>
        <div class="mb-4">
            <label class="form-label">📎 기존 첨부파일</label>
            <div class="row">
                <?php foreach ($files as $file): ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-sm border">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div class="me-2">
                                <i class="bi bi-file-earmark-text-fill me-1 text-primary"></i>
                                <span class="text-muted"><?php echo htmlspecialchars($file['original_filename']); ?></span>
                                <small class="d-block text-muted"><?php echo number_format($file['file_size'] / 1024, 2); ?> KB</small>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="delete_<?php echo $file['file_id']; ?>" name="delete_files[]" value="<?php echo $file['file_id']; ?>">
                                <label class="form-check-label text-danger small" for="delete_<?php echo $file['file_id']; ?>">삭제</label>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- 새 첨부파일 -->
        <div class="mb-4">
            <div class="clearfix mb-2">
                <div class="float-start"><label class="form-label">📤 파일 첨부</label></div>
                <div class="float-end">
                    <button class="btn btn-outline-primary btn-sm" id="add-file" type="button">
                        <i class="bi bi-plus-circle"></i> 파일 추가
                    </button>
                </div>
            </div>
            <div id="file-container">
                <div class="input-group mb-2 file-input-group">
                    <input type="file" class="form-control" name="files[]">
                    <button class="btn btn-outline-danger remove-file" type="button">
                        <i class="bi bi-dash-circle"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-success"><?php echo $writeBtn;?></button>
            <a href="<?php echo get_board_url('list',$boardId);?>" class="btn btn-secondary">취소</a>
        </div>
    </form>
</div>

<script>
const recaptchaSiteKey = '<?php echo $recaptcha_site; ?>';
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('writeForm');
    form.addEventListener('submit', function (e) {
        e.preventDefault(); // 폼 제출 막기
        grecaptcha.ready(function() {
            grecaptcha.execute(recaptchaSiteKey, {action: 'submit'}).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
                form.submit(); // 토큰 설정 후 폼 제출
            });
        });
    });

    const container = document.getElementById('file-container');
    const addButton = document.getElementById('add-file');

    addButton.addEventListener('click', function () {
        const group = document.createElement('div');
        group.className = 'input-group mb-2 file-input-group';
        group.innerHTML = `
            <input type="file" class="form-control" name="files[]">
            <button class="btn btn-outline-danger remove-file" type="button">
                <i class="bi bi-dash-circle"></i>
            </button>`;
        container.appendChild(group);
    });

    container.addEventListener('click', function (e) {
        if (e.target.closest('.remove-file')) {
            const allGroups = container.querySelectorAll('.file-input-group');
            if (allGroups.length > 1) {
                const group = e.target.closest('.file-input-group');
                group.remove();
            }
        }
    });
});
</script>

<script>
$(document).ready(function() {
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
                    data.append('board_id', $('input[name="board_id"]').val());

                    $.ajax({
                        url: '<?php echo CM_BOARD_URL;?>/summernote_upload.php',
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
            }
        }
    });
});
</script>

<?php
// reCAPTCHA 검증 코드는 write_update.php 파일로 이동
?>