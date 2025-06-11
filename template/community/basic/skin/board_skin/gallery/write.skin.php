<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가

// Font Awesome 추가
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">';
?>

<div class="container mt-5">
    <h2 class="mb-4"><?php echo $bo_title;?></h2>
    
    <form action="<?php echo $formAction;?>" method="post" enctype="multipart/form-data" id="writeForm">
        <input type="hidden" name="board_id" value="<?php echo $boardId;?>">
        <input type="hidden" name="board_num" value="<?php echo $boardNum ?? '';?>">
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($member['user_id'] ?? ''); ?>">
        <?php if ($is_member): // 회원인 경우 이메일, 이름, 비밀번호를 hidden으로 처리 ?>
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($member['user_email'] ?? ''); ?>">
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($member['user_name'] ?? ''); ?>">
            <input type="hidden" name="password" value="<?php echo isset($member['user_password']) ? $member['user_password'] : ''; ?>">
        <?php else: // 비회원인 경우 입력 폼 표시 ?>
            <div class="mb-3">
                <label for="email" class="form-label">이메일</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($write['email'] ?? ''); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="name" class="form-label">이름</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($write['name'] ?? ''); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label"><?php echo isset($write) ? '비밀번호 (변경시에만 입력)' : '비밀번호'; ?></label>
                <input type="password" class="form-control" id="password" name="password" <?php echo isset($write) ? '' : 'required'; ?>>
                <?php if (isset($write)): ?>
                <small class="text-muted">비밀번호를 변경하지 않으려면 비워두세요.</small>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
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
                <?php foreach ($files as $file): 
                    $is_image = is_image_file($file['original_filename']);
                    $icon_class = get_file_icon_class($file['original_filename']);
                ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-sm border">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <?php if ($is_image): ?>
                                    <img src="download.php?board=<?php echo $boardId;?>&file_id=<?= $file['file_id'] ?>" 
                                         class="me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                <?php else: ?>
                                    <i class="fas <?= $icon_class ?> me-2" style="font-size: 1.2rem;"></i>
                                <?php endif; ?>
                                <div>
                                    <span class="text-muted"><?php echo htmlspecialchars($file['original_filename']); ?></span>
                                    <small class="d-block text-muted"><?php echo number_format($file['file_size'] / 1024, 2); ?> KB</small>
                                </div>
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
            </div>
            <div class="mb-3">
                <input type="file" class="form-control" id="fileInput" name="files[]" multiple>
                <small class="text-muted">여러 파일을 선택하려면 Ctrl(Windows) 또는 Command(Mac) 키를 누른 상태에서 파일을 선택하세요.</small>
            </div>
            <div id="filePreview" class="row g-2">
                <!-- 파일 미리보기가 여기에 표시됩니다 -->
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
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    
    // 파일 미리보기 기능
    fileInput.addEventListener('change', function(e) {
        filePreview.innerHTML = ''; // 기존 미리보기 초기화
        
        Array.from(e.target.files).forEach((file, index) => {
            const reader = new FileReader();
            const col = document.createElement('div');
            col.className = 'col-md-4 col-lg-3';
            
            reader.onload = function(e) {
                let previewContent = '';
                
                if (file.type.startsWith('image/')) {
                    // 이미지 파일인 경우
                    previewContent = `
                        <div class="card h-100">
                            <div class="position-relative">
                                <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 delete-file" data-index="${index}">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title text-truncate">${file.name}</h6>
                                <p class="card-text small text-muted">${(file.size / 1024).toFixed(2)} KB</p>
                            </div>
                        </div>`;
                } else {
                    // 이미지가 아닌 파일인 경우
                    const fileIcon = getFileIcon(file.type);
                    previewContent = `
                        <div class="card h-100">
                            <div class="position-relative">
                                <div class="card-body text-center">
                                    <i class="bi ${fileIcon} display-4"></i>
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 delete-file" data-index="${index}">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <div class="card-body pt-0">
                                    <h6 class="card-title text-truncate">${file.name}</h6>
                                    <p class="card-text small text-muted">${(file.size / 1024).toFixed(2)} KB</p>
                                </div>
                            </div>
                        </div>`;
                }
                
                col.innerHTML = previewContent;
                filePreview.appendChild(col);
            };
            
            reader.readAsDataURL(file);
        });
    });
    
    // 파일 타입에 따른 아이콘 반환 함수
    function getFileIcon(fileType) {
        if (fileType.includes('pdf')) return 'bi-file-pdf';
        if (fileType.includes('word') || fileType.includes('document')) return 'bi-file-word';
        if (fileType.includes('excel') || fileType.includes('sheet')) return 'bi-file-excel';
        if (fileType.includes('powerpoint') || fileType.includes('presentation')) return 'bi-file-ppt';
        if (fileType.includes('zip') || fileType.includes('compressed')) return 'bi-file-zip';
        return 'bi-file-earmark';
    }
    
    // 파일 삭제 이벤트 처리
    filePreview.addEventListener('click', function(e) {
        if (e.target.closest('.delete-file')) {
            const deleteButton = e.target.closest('.delete-file');
            const index = parseInt(deleteButton.dataset.index);
            
            // DataTransfer 객체를 사용하여 파일 목록 업데이트
            const dt = new DataTransfer();
            const files = fileInput.files;
            
            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    dt.items.add(files[i]);
                }
            }
            
            // 파일 input 업데이트
            fileInput.files = dt.files;
            
            // 미리보기 업데이트를 위해 change 이벤트 발생
            const event = new Event('change');
            fileInput.dispatchEvent(event);
        }
    });
    
    <?php if($recaptcha_site && $recaptcha_secret){?>
    form.addEventListener('submit', function (e) {
        e.preventDefault(); // 폼 제출 막기
        grecaptcha.ready(function() {
            grecaptcha.execute(recaptchaSiteKey, {action: 'submit'}).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
                form.submit(); // 토큰 설정 후 폼 제출
            });
        });
    });
    <?php } ?>
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
                        url: '<?php echo CM_URL;?>/summernote/summernote_upload.php',
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