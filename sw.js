// 최소한의 서비스 워커 (캐시 사용 안함)

// 설치 이벤트: 즉시 활성화
self.addEventListener('install', event => {
  // 기존 서비스 워커를 즉시 활성화
  self.skipWaiting();
  console.log('서비스 워커 설치됨');
});

// 활성화 이벤트: 모든 클라이언트 제어
self.addEventListener('activate', event => {
  event.waitUntil(
    // 기존 캐시 삭제
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => caches.delete(cacheName))
      );
    })
    // 모든 클라이언트 제어 권한 얻기
    .then(() => self.clients.claim())
  );
  console.log('서비스 워커 활성화됨');
});

// 오프라인 페이지 경로
const OFFLINE_PAGE = '/offline.php';

// fetch 이벤트: 네트워크 우선, 오프라인 시 오프라인 페이지 표시
self.addEventListener('fetch', event => {
  // 네트워크 요청 시도
  event.respondWith(
    fetch(event.request).catch(async error => {
      // 네트워크 오류 발생 시 오프라인 페이지 반환
      console.log('네트워크 오류로 오프라인 페이지 표시:', error);
      return caches.match(OFFLINE_PAGE);
    })
  );
});

// PWA 설치 이벤트
self.addEventListener('appinstalled', event => {
  console.log('앱이 설치되었습니다.');
});
