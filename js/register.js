//회원가입/수정
$(document).ready(function() {
	
	if(registerUpdate){
		// 페이지 로드시 비밀번호 확인 모달 표시
		var passwordModal = new bootstrap.Modal(document.getElementById('passwordModal'), {
			backdrop: 'static', // Prevent closing by clicking outside
			keyboard: false // Prevent closing with ESC key
		});
		passwordModal.show();

		// 모달 닫힐 때 이벤트 처리
		$('#passwordModal').on('hidden.bs.modal', function (e) {
			// 비밀번호가 확인되지 않은 상태에서 모달이 닫히면 이전 페이지로 이동
			if ($('#updateForm').is(':hidden')) {
				window.location.href = CM.URL;
			}
		});

		// 비밀번호 확인 폼 제출
		$('#passwordCheckForm').on('submit', function(e) {
			e.preventDefault();
			
			$.ajax({
				url: CM.MB_URL + '/password_check.php',
				type: 'POST',
				data: {
					user_id: user_id,
					user_password: $('#current_password').val()
				},
				dataType: 'json',
				success: function(response) {
					if (response.status === 'success') {
						passwordModal.hide();
						$('#updateForm').show();
					} else {
						alert(response.message);
						$('#current_password').val('').focus();
					}
				},
				error: function(xhr, status, error) {
					console.error('비밀번호 확인 AJAX 오류:', error, xhr.responseText);
					alert('서버 오류가 발생했습니다.');
					$('#current_password').val('').focus();
				}
			});
		});

		// 회원정보 수정 폼 제출
		$('#registerForm').on('submit', function(e) {
			e.preventDefault();
			
			if ($('#user_password').val() !== $('#password_confirm').val()) {
				$('#password_confirmError').text('비밀번호가 일치하지 않습니다.');
				return;
			}

			const performUpdateSubmit = function() {
				$.ajax({
					url: $('#registerForm').attr('action'),
					type: 'POST',
					data: $('#registerForm').serialize(),
					dataType: 'json',
					success: function(response) {
						if (response.status === 'success') {
							alert('회원정보가 수정되었습니다.');
							location.href = CM.URL;
						} else {
							// 오류 메시지 표시
							if (response.field && $('#' + response.field + 'Error').length) {
								$('#' + response.field + 'Error').text(response.message);
							} else {
								alert(response.message || '회원정보 수정에 실패했습니다.');
							}
						}
					},
					error: function(xhr, status, error) {
						console.error('회원정보 수정 AJAX 오류:', error, xhr.responseText);
						alert('서버 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
					}
				});
			};

			if (recaptchaSiteKey && typeof grecaptcha !== 'undefined' && grecaptcha.ready) {
				// reCAPTCHA 사용
				grecaptcha.ready(function() {
					grecaptcha.execute(recaptchaSiteKey, {action: 'submit'}).then(function(token) {
						document.getElementById('g-recaptcha-response').value = token;
						performUpdateSubmit();
					}).catch(function(error) {
						console.error('reCAPTCHA 실행 오류 (정보 수정):', error);
						alert('reCAPTCHA 인증 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
					});
				});
			} else {
				// reCAPTCHA 키가 없거나 grecaptcha가 로드되지 않은 경우 바로 제출
				performUpdateSubmit();
			}
		});
	} else { 
		// 폼 제출 이벤트 처리
		$('#registerForm').on('submit', function(e) {
			e.preventDefault();
			
			// 오류 메시지 초기화
			$('#user_idError, #user_nameError, #user_passwordError, #password_confirmError, #user_emailError, #user_hpError').text('');
			
			// 비밀번호 일치 여부 확인
			const password = $('#user_password').val();
			const passwordConfirm = $('#password_confirm').val();
			
			if (password !== passwordConfirm) {
				$('#password_confirmError').text('비밀번호가 일치하지 않습니다');
				return;
			}

			const performRegisterSubmit = function() {
				$.ajax({
					url: $('#registerForm').attr('action'),
					type: 'POST',
					data: $('#registerForm').serialize(),
					dataType: 'json',
					success: function(response) {
						if (response.status === 'success') {
							alert('회원가입이 완료되었습니다.');
							window.location.href = CM.URL;
						} else {
							// 오류 메시지 표시
							if (response.field && $('#' + response.field + 'Error').length) {
								$('#' + response.field + 'Error').text(response.message);
							} else {
								alert(response.message || '회원가입에 실패했습니다.');
							}
						}
					},
					error: function(xhr, status, error) {
						console.error('회원가입 AJAX 오류:', error, xhr.responseText);
						alert('서버 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
					}
				});
			};

			if (recaptchaSiteKey && typeof grecaptcha !== 'undefined' && grecaptcha.ready) {
				// reCAPTCHA 사용
				grecaptcha.ready(function() {
					grecaptcha.execute(recaptchaSiteKey, {action: 'submit'}).then(function(token) {
						document.getElementById('g-recaptcha-response').value = token;
						performRegisterSubmit();
					}).catch(function(error) {
						console.error('reCAPTCHA 실행 오류 (회원가입):', error);
						alert('reCAPTCHA 인증 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
					});
				});
			} else {
				// reCAPTCHA 키가 없거나 grecaptcha가 로드되지 않은 경우 바로 제출
				performRegisterSubmit();
			}
		});
		
		// 실시간 입력 검증
		$('#user_id').on('blur', function() {
			const userId = $(this).val();
			if (userId.length < 4) {
				$('#user_idError').text('아이디는 최소 4자 이상이어야 합니다');
			} else {
				$('#user_idError').text('');
			}
		});
		
		$('#user_password').on('blur', function() {
			const password = $(this).val();
			if (password.length < 8) {
				$('#user_passwordError').text('비밀번호는 최소 8자 이상이어야 합니다');
			} else {
				$('#user_passwordError').text('');
			}
		});
		
		$('#password_confirm').on('blur', function() {
			const password = $('#user_password').val();
			const passwordConfirm = $(this).val();
			
			if (password !== passwordConfirm) {
				$('#password_confirmError').text('비밀번호가 일치하지 않습니다');
			} else {
				$('#password_confirmError').text('');
			}
		});
		
		$('#user_email').on('blur', function() {
			const email = $(this).val();
			const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			
			if (!emailRegex.test(email)) {
				$('#user_emailError').text('유효한 이메일 주소를 입력해주세요');
			} else {
				$('#user_emailError').text('');
			}
		});
	}
});