//board write 관련 JSON

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
    

	if(recaptcha_site && recaptcha_secret){
		form.addEventListener('submit', function (e) {
			e.preventDefault(); // 폼 제출 막기
			grecaptcha.ready(function() {
				grecaptcha.execute(recaptchaSiteKey, {action: 'submit'}).then(function(token) {
					document.getElementById('g-recaptcha-response').value = token;
					form.submit(); // 토큰 설정 후 폼 제출
				});
			});
		});
	}

});

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
                        url: CM.LIB_URL + '/summernote_upload.php',
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