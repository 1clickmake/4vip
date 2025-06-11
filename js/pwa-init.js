// PWA 초기화 스크립트
class PWAInstaller {
    constructor() {
        this.deferredPrompt = null;
        this.installButton = null;
        this.init();
    }

    init() {
        this.registerServiceWorker();
        this.setupInstallPrompt();
        this.setupBeforeInstallPrompt();
        this.checkDisplayMode(); 
    }
    
    // 쿠키 관련 메서드 제거됨

    // 서비스 워커 등록
    registerServiceWorker() {
        // HTTPS에서만 서비스 워커 등록 시도
        if ('serviceWorker' in navigator && window.location.protocol === 'https:') {
            window.addEventListener('load', () => {
                // 서비스 워커 등록 시도
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('ServiceWorker 등록 성공:', registration.scope);
                        // 서비스 워커가 활성화될 때까지 기다린 후 periodicSync 설정
                        return navigator.serviceWorker.ready;
                    })
                    .catch(error => {
                        // 오류 로그만 기록하고 계속 진행
                        console.warn('ServiceWorker 등록 중 경고:', error);
                    });
            });
        } else {
            console.log('ServiceWorker는 HTTPS 환경에서만 사용 가능합니다.');
        }
    }


    // 설치 프롬프트 설정
    setupInstallPrompt() {
        // 설치 버튼 이벤트 리스너
        document.addEventListener('click', (e) => {
            if (e.target.matches('#install-app-btn, #install-app-btn *')) {
                this.promptInstall();
            }
            // 나중에 버튼 클릭 시
            if (e.target.matches('#later-install-btn, #later-install-btn *')) {
                console.log('"나중에" 버튼 클릭됨');
                this.hideInstallPromotion();
            }
        });
    }

        // 설치 가능 시점 감지
    setupBeforeInstallPrompt() {
        console.log('setupBeforeInstallPrompt 함수 호출됨');
        
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('beforeinstallprompt 이벤트 발생!', e);
            // 기본 프롬프트 방지
            e.preventDefault();
            // 이벤트 저장
            this.deferredPrompt = e;
            console.log('deferredPrompt 설정됨:', this.deferredPrompt);
            // 설치 버튼 표시
            this.showInstallPromotion();
        });

        // 앱 설치 후 이벤트
        window.addEventListener('appinstalled', () => {
            console.log('앱이 설치되었습니다.');
            this.hideInstallPromotion();
            // 설치 완료 이벤트 추적 (GA 등)
            if (window.gtag) {
                gtag('event', 'app_installed');
            }
        });
    }

    // 설치 버튼 클릭 시 호출
    async promptInstall() {
        if (!this.deferredPrompt) return;
        
        this.deferredPrompt.prompt();
        const { outcome } = await this.deferredPrompt.userChoice;
        console.log(`사용자 응답: ${outcome}`);
        
        // 사용 후 초기화
        this.deferredPrompt = null;
        this.hideInstallPromotion();
    }

    // 설치 프롬프트 표시 (UI)
    showInstallPromotion() {
        console.log('showInstallPromotion 호출됨');
        
        // 이미 설치된 경우 표시하지 않음
        if (window.matchMedia('(display-mode: standalone)').matches) {
            console.log('이미 설치된 앱으로 감지됨');
            return;
        }
        
        // 설치 버튼이 있다면 표시
        const installButton = document.getElementById('pwa-install-btn');
        if (installButton) {
            console.log('설치 버튼 표시');
            installButton.style.display = 'block';
        } else {
            console.log('설치 버튼을 찾을 수 없음');
        }
        
        // 설치 안내 배너 표시 (예시)
        let banner = document.getElementById('install-banner');
        if (!banner) {
            console.log('새로운 설치 배너 생성');
            banner = document.createElement('div');
            banner.id = 'install-banner';
            banner.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: white;
                padding: 15px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                z-index: 1000;
                max-width: 300px;
            `;
            banner.innerHTML = `
                <p>홈 화면에 추가하여 더 나은 경험을 즐기세요</p>
                <div style="display: flex; justify-content: flex-end; margin-top: 10px; gap: 10px;">
                    <button id="later-install-btn" style="padding: 5px 10px; background: #f0f0f0; border: none; border-radius: 4px; cursor: pointer;">
                        나중에
                    </button>
                    <button id="install-app-btn" style="padding: 5px 10px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        설치하기
                    </button>
                </div>
            `;
            document.body.appendChild(banner);
        } else {
            console.log('기존 배너 표시');
            banner.style.display = 'block';
        }
    }

    // 설치 프롬프트 숨기기
    hideInstallPromotion() {
        const banner = document.getElementById('install-banner');
        if (banner) {
            banner.style.display = 'none';
        }
        const installButton = document.getElementById('install-app-btn');
        if (installButton) {
            installButton.style.display = 'none';
        }
    }

    // 디스플레이 모드 확인 (브라우저/스탠드얼론)
    checkDisplayMode() {
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches;
        if (isStandalone) {
            console.log('앱 모드로 실행 중');
            document.documentElement.setAttribute('data-display-mode', 'standalone');
        } else {
            console.log('브라우저에서 실행 중');
            document.documentElement.setAttribute('data-display-mode', 'browser');
        }
    }
}

// PWA 설치 관리자 초기화
document.addEventListener('DOMContentLoaded', () => {
    window.pwaInstaller = new PWAInstaller();
    
    // 오프라인 상태 감지
    window.addEventListener('online', () => {
        console.log('온라인 상태입니다.');
        document.documentElement.classList.remove('offline');
    });
    
    window.addEventListener('offline', () => {
        console.log('오프라인 상태입니다.');
        document.documentElement.classList.add('offline');
        
        // 오프라인 상태 알림
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({
                type: 'OFFLINE_STATUS',
                isOffline: true
            });
        }
    });
    
    // 초기 상태 설정
    if (!navigator.onLine) {
        document.documentElement.classList.add('offline');
    }
});
