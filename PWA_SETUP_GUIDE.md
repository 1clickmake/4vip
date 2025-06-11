# 홈페이지 PWA 설치 및 설정 가이드

## 1. VAPID 키 생성

PWA 푸시 알림을 사용하기 위해 VAPID 키를 생성해야 합니다. 다음 단계를 따르세요:

1. Node.js가 설치되어 있는지 확인하세요.
2. 터미널을 열고 다음 명령어를 실행하세요:
   ```bash
   npx web-push generate-vapid-keys
   ```
3. 생성된 공개 키와 비공개 키를 복사해두세요.

## 2. VAPID 키 설정

1. `api/send-notification.php` 파일을 엽니다.
2. 다음 부분을 찾아서 생성한 VAPID 키로 변경하세요:
   ```php
   $vapidKeys = [
       'publicKey' => 'YOUR_VAPID_PUBLIC_KEY',  // 여기에 VAPID 공개키 입력
       'privateKey' => 'YOUR_VAPID_PRIVATE_KEY' // 여기에 VAPID 비공개키 입력
   ];
   ```
3. `sendPushNotification` 함수 내의 관리자 이메일도 변경하세요:
   ```php
   'subject' => 'mailto:your-email@example.com', // 관리자 이메일로 변경
   ```

## 3. Composer 의존성 설치

1. 터미널을 열고 프로젝트 루트 디렉토리로 이동하세요.
2. 다음 명령어를 실행하여 의존성을 설치하세요:
   ```bash
   composer install
   ```

## 4. PWA 설치 테스트

1. 웹사이트에 접속합니다.
2. 모바일 기기나 데스크톱 브라우저에서 다음을 확인하세요:
   - Chrome/Edge: 주소 표시줄 오른쪽에 설치 아이콘이 표시됩니다.
   - iOS: Safari에서 "공유" > "홈 화면에 추가"를 선택합니다.
   - Android: Chrome에서 "메뉴" > "홈 화면에 추가"를 선택합니다.

## 5. 푸시 알림 테스트

1. 브라우저에서 알림 권한을 허용했는지 확인하세요.
2. 다음 API를 호출하여 테스트 알림을 전송할 수 있습니다:
   ```bash
   curl -X POST https://your-domain.com/api/send-notification.php \
   -H "Content-Type: application/json" \
   -d '{"title":"테스트 알림", "message":"PWA 푸시 알림이 정상적으로 작동합니다!"}'
   ```

## 6. 문제 해결

- **서비스 워커가 등록되지 않는 경우**:
  - HTTPS를 사용하고 있는지 확인하세요. (로컬 개발 환경에서는 localhost가 허용됨)
  - 서비스 워커 파일(`sw.js`)이 루트 디렉토리에 있는지 확인하세요.

- **푸시 알림이 작동하지 않는 경우**:
  - VAPID 키가 올바르게 설정되었는지 확인하세요.
  - 브라우저의 알림 권한이 허용되어 있는지 확인하세요.
  - 브라우저 콘솔에서 오류 메시지를 확인하세요.

## 7. 추가 설정

### 아이콘 및 스플래시 이미지
- `images/icons/` 디렉토리에 다양한 크기의 아이콘을 추가하세요.
- `images/splash/` 디렉토리에 다양한 기기 해상도에 맞는 스플래시 이미지를 추가하세요.

### 오프라인 지원
- `sw.js` 파일에서 `urlsToCache` 배열에 오프라인에서 사용할 정적 자원을 추가하세요.

## 8. 배포 시 주의사항

- 반드시 HTTPS를 사용하세요. (Let's Encrypt 등 무료 인증서 사용 가능)
- `manifest.json`의 `start_url`이 올바른지 확인하세요.
- 서비스 워커의 캐시 전략을 프로덕션 환경에 맞게 최적화하세요.

## 9. 참고 자료

- [Web Push Protocol](https://developers.google.com/web/fundamentals/push-notifications/web-push-protocol)
- [Service Worker API](https://developer.mozilla.org/ko/docs/Web/API/Service_Worker_API)
- [Web App Manifest](https://developer.mozilla.org/ko/docs/Web/Manifest)
