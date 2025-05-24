<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
?>

<style>
        * {
            font-family: 'Noto Sans KR', sans-serif;
        }
        
        .footer-section {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            position: relative;
            overflow: hidden;
            border-top: 3px solid #667eea;
        }
        
        .footer-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
        }
        
        .footer-content {
            position: relative;
            z-index: 1;
        }
        
        .company-info {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .company-info:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .company-info p {
            margin-bottom: 0.8rem;
            color: #e2e8f0;
            font-size: 1rem;
            display: flex;
            align-items: center;
            transition: color 0.3s ease;
        }
        
        .company-info p:hover {
            color: #667eea;
        }
        
        .company-info p::before {
            content: '';
            width: 4px;
            height: 4px;
            background: #667eea;
            border-radius: 50%;
            margin-right: 12px;
            transition: all 0.3s ease;
        }
        
        .company-info p:hover::before {
            background: #764ba2;
            transform: scale(1.5);
        }
        
        .site-title-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.8rem;
            transition: all 0.3s ease;
            display: inline-block;
            position: relative;
        }
        
        .site-title-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
        }
        
        .site-title-link:hover::after {
            width: 100%;
        }
        
        .site-title-link:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }
        
        .social-buttons {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .social-buttons:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .social-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0.5rem;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
        }
        
        .social-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .social-btn i {
            position: relative;
            z-index: 1;
            font-size: 1.2rem;
            color: #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .social-btn:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .social-btn:hover::before {
            opacity: 1;
        }
        
        .social-btn:hover i {
            color: white;
            transform: scale(1.1);
        }
        
        .social-btn.x-twitter:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }
        
        .social-btn.facebook:hover {
            box-shadow: 0 10px 25px rgba(24, 119, 242, 0.4);
        }
        
        .social-btn.linkedin:hover {
            box-shadow: 0 10px 25px rgba(0, 119, 181, 0.4);
        }
        
        .social-btn.instagram:hover {
            box-shadow: 0 10px 25px rgba(225, 48, 108, 0.4);
        }
        
        .social-btn.youtube:hover {
            box-shadow: 0 10px 25px rgba(255, 0, 0, 0.4);
        }
        
        .social-btn.kakao:hover {
            box-shadow: 0 10px 25px rgba(255, 235, 0, 0.4);
        }
        
        .social-btn.naver:hover {
            box-shadow: 0 10px 25px rgba(3, 199, 90, 0.4);
        }
        
        .social-btn.discord:hover {
            box-shadow: 0 10px 25px rgba(114, 137, 218, 0.4);
        }
        
        @media (max-width: 768px) {
            .company-info, .social-buttons {
                margin-bottom: 1.5rem;
                text-align: center;
            }
            
            .site-title-link {
                font-size: 1.5rem;
                margin: 1rem 0;
            }
        }
        
        .floating-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        
        .particle {
            position: absolute;
            background: rgba(102, 126, 234, 0.3);
            border-radius: 50%;
            animation: float-particle 8s infinite linear;
        }
        
        .particle:nth-child(1) {
            width: 4px;
            height: 4px;
            left: 10%;
            animation-delay: 0s;
        }
        
        .particle:nth-child(2) {
            width: 6px;
            height: 6px;
            left: 30%;
            animation-delay: 2s;
        }
        
        .particle:nth-child(3) {
            width: 3px;
            height: 3px;
            left: 60%;
            animation-delay: 4s;
        }
        
        .particle:nth-child(4) {
            width: 5px;
            height: 5px;
            left: 80%;
            animation-delay: 6s;
        }
        
        @keyframes float-particle {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) scale(1);
                opacity: 0;
            }
        }
    </style>
 <section class="footer-section pb-4 pt-4 mt-0">
        <div class="floating-particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
        
        <div class="container">
            <div class="row footer-content justify-content-between align-items-center">
                <div class="col-md-4 text-start my-3">
                    <div class="company-info">
                        <p><strong>회사명 :</strong> (주)테크솔루션</p>
                        <p><strong>대표 :</strong> 홍길동</p>
                        <p><strong>주소 :</strong> 서울특별시 강남구 테헤란로 123</p>
                        <p><strong>이메일 :</strong> info@example.com</p>
                    </div>
                </div>
                
                <div class="col-md-4 text-center my-3">
                    <a href="#" class="site-title-link">
                        <?php echo $config['site_title'];?>
                    </a>
                </div>
                
                <div class="col-md-4 text-end my-3">
                    <div class="social-buttons text-center text-md-end">
                        <a href="#" class="social-btn x-twitter" role="button" title="X (Twitter)">
                            <i class="fab fa-x-twitter" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="social-btn facebook" role="button" title="Facebook">
                            <i class="fab fa-facebook" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="social-btn instagram" role="button" title="Instagram">
                            <i class="fab fa-instagram" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="social-btn youtube" role="button" title="YouTube">
                            <i class="fab fa-youtube" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="social-btn linkedin" role="button" title="LinkedIn">
                            <i class="fab fa-linkedin" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="social-btn kakao" role="button" title="KakaoTalk">
                            <i class="fas fa-comment" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="social-btn naver" role="button" title="Naver">
                            <i class="fas fa-n" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="social-btn discord" role="button" title="Discord">
                            <i class="fab fa-discord" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
	  