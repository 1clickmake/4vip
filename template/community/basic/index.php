<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가


?>

<section class="testimonial-section pt-5 mt-5">
        <div class="floating-icons">
            <i class="floating-icon fab fa-php"></i>
            <i class="floating-icon fas fa-download"></i>
            <i class="floating-icon fas fa-code"></i>
        </div>
        
        <div class="container testimonial-container">
            <div id="carouselExampleControls" class="carousel slide testimonial-carousel" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="container-fluid">
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="testimonial-card border-primary text-center">
                                        <div class="testimonial-avatar">
                                            <img src="<?php echo CM_TEMPLATE_URL?>/images/m1.png" alt="개발자 프로필">
                                        </div>
                                        <blockquote class="blockquote">
                                            <p class="testimonial-quote">완전 무료로 제공되는 PHP 기반 게시판 툴! 설치도 간단하고 커스터마이징도 자유자재로 가능해서 새로운 사이트에 딱 맞게 구축할 수 있습니다.</p>
                                            <footer class="blockquote-footer d-none">
                                                <span class="testimonial-author">김개발</span> - 
                                                <cite class="testimonial-role">웹 개발자</cite>
                                            </footer>
                                        </blockquote>
                                        <div class="tech-badges">
                                            <span class="tech-badge"><i class="fab fa-php me-1"></i>PHP 기반</span>
                                            <span class="tech-badge"><i class="fas fa-download me-1"></i>무료 다운로드</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="carousel-item">
                        <div class="container-fluid">
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="testimonial-card border-success text-center">
                                        <div class="testimonial-avatar">
                                            <img src="<?php echo CM_TEMPLATE_URL?>/images/m2.png" alt="개발자 프로필">
                                        </div>
                                        <blockquote class="blockquote">
                                            <p class="testimonial-quote">오픈소스 PHP 게시판 툴의 강력함을 체험해보세요! 복잡한 라이선스 걱정 없이 상업적 용도로도 자유롭게 사용할 수 있습니다.</p>
                                            <footer class="blockquote-footer d-none">
                                                <span class="testimonial-author">박관리</span> - 
                                                <cite class="testimonial-role">시스템 관리자</cite>
                                            </footer>
                                        </blockquote>
                                        <div class="tech-badges">
                                            <span class="tech-badge"><i class="fas fa-code me-1"></i>오픈소스</span>
                                            <span class="tech-badge"><i class="fas fa-rocket me-1"></i>상업적 이용 가능</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="carousel-item">
                        <div class="container-fluid">
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="testimonial-card border-info text-center">
                                        <div class="testimonial-avatar">
                                            <img src="<?php echo CM_TEMPLATE_URL?>/images/m3.png" alt="개발자 프로필">
                                        </div>
                                        <blockquote class="blockquote">
                                            <p class="testimonial-quote">PHP 초보자도 쉽게 설치할 수 있는 게시판 툴이에요! 문서화도 잘 되어 있고, 커뮤니티 지원도 막힐 때마다 도움을 받을 수 있습니다.</p>
                                            <footer class="blockquote-footer d-none">
                                                <span class="testimonial-author">이사용</span> - 
                                                <cite class="testimonial-role">PHP 학습자</cite>
                                            </footer>
                                        </blockquote>
                                        <div class="tech-badges">
                                            <span class="tech-badge"><i class="fas fa-heart me-1"></i>초보자 친화적</span>
                                            <span class="tech-badge"><i class="fas fa-users me-1"></i>커뮤니티 지원</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                    <i class="fas fa-chevron-left" aria-hidden="true"></i>
                    <span class="visually-hidden">이전</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                    <span class="visually-hidden">다음</span>
                </button>
                
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleControls" data-bs-slide-to="0" class="active"></button>
                    <button type="button" data-bs-target="#carouselExampleControls" data-bs-slide-to="1"></button>
                    <button type="button" data-bs-target="#carouselExampleControls" data-bs-slide-to="2"></button>
                </div>
            </div>
        </div>
    </section>


<section class="features-section py-5">
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
        
        <div class="container py-5">
            <div class="row text-center mb-5">
                <div class="col">
                    <h2 class="section-title">무료 PHP 게시판 툴의 강력한 기능</h2>
                    <p class="section-subtitle">현대적이고 안정적인 커뮤니티 플랫폼을 경험해보세요</p>
                </div>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-download"></i>
                        </div>
                        <h4 class="feature-title">무료 다운로드</h4>
                        <p class="feature-description">
                            누구나 부담 없이 무료로 다운로드하여 사용할 수 있는 PHP 기반 게시판 툴입니다. 
                            복잡한 라이선스 없이 자유롭게 사용하세요.
                        </p>
                        <a href="#" class="feature-btn">
                            <i class="fas fa-arrow-right me-2"></i>자세히 보기
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <h4 class="feature-title">PHP 기반 설계</h4>
                        <p class="feature-description">
                            강력한 PHP로 구축된 게시판으로, 안정적이고 확장 가능한 기능을 제공합니다. 
                            모든 웹 호스팅에서 쉽게 설치할 수 있습니다.
                        </p>
                        <a href="#" class="feature-btn">
                            <i class="fas fa-arrow-right me-2"></i>자세히 보기
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4 class="feature-title">커뮤니티 중심</h4>
                        <p class="feature-description">
                            사용자 간 자유로운 자료 공유와 협업을 지원하는 커뮤니티 친화적 플랫폼입니다. 
                            활발한 소통을 위한 다양한 기능을 제공합니다.
                        </p>
                        <a href="#" class="feature-btn">
                            <i class="fas fa-arrow-right me-2"></i>자세히 보기
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
	  
	  
	  
<section class="title-section">
    <div class="title-floating-shapes">
        <div class="title-shape title-shape-1"></div>
        <div class="title-shape title-shape-2"></div>
        <div class="title-shape title-shape-3"></div>
    </div>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col col-md-8 text-center">
                <h1>CM-BOARD</h1>
            </div>
        </div>
    </div>
</section>	  
      



<section class="carousel-section">
    <div class="carousel-floating-elements">
        <div class="floating-element">
            <i class="fab fa-php"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-users"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-download"></i>
        </div>
    </div>
    
    <div class="container">
        <div class="row">
            <div class="col-6">
                <h3>무료 PHP 게시판의 매력</h3>
            </div>
            <div class="col-6 text-end">
                <div class="carousel-controls">
                    <button class="carousel-btn" data-bs-target="#carouselExampleIndicators1" data-bs-slide="prev">
                        <i class="fa fa-arrow-left"></i>
                    </button>
                    <button class="carousel-btn" data-bs-target="#carouselExampleIndicators1" data-bs-slide="next">
                        <i class="fa fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            <div class="col-12">
                <div id="carouselExampleIndicators1" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="Community" src="https://images.unsplash.com/photo-1516321497487-e288fb19713f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">무료로 공유하세요</h4>
                                            <p class="card-text">누구나 무료로 다운로드하고 공유할 수 있는 PHP 게시판으로 커뮤니티를 활성화하세요.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="PHP Code" src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">강력한 PHP 기반</h4>
                                            <p class="card-text">PHP로 설계된 안정적이고 확장 가능한 게시판으로 원활한 사용자 경험을 제공합니다.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="Collaboration" src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">커뮤니티 협업</h4>
                                            <p class="card-text">사용자 간 자유로운 자료 교환으로 지식과 아이디어를 함께 키워나가세요.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="Open Source" src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">오픈소스 정신</h4>
                                            <p class="card-text">오픈소스 기반의 무료 툴로 누구나 기여하고 혜택을 누릴 수 있습니다.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="Easy Setup" src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">쉬운 설치</h4>
                                            <p class="card-text">간단한 설치 과정으로 빠르게 무료 게시판을 시작할 수 있습니다.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="Community Support" src="https://images.unsplash.com/photo-1556761175-b413da4baf72?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">활발한 지원</h4>
                                            <p class="card-text">커뮤니티의 활발한 지원으로 문제를 빠르게 해결하고 성장하세요.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="Free Access" src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">완전 무료</h4>
                                            <p class="card-text">비용 없이 모든 기능을 이용하며 자유로운 게시판을 경험하세요.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="Customizable" src="https://images.unsplash.com/photo-1551650975-87deedd944c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">맞춤 가능</h4>
                                            <p class="card-text">PHP 기반으로 원하는 대로 커스터마이징이 가능한 유연한 툴입니다.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="Community Driven" src="https://images.unsplash.com/photo-1559526324-4b87b5e36e44?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">커뮤니티 주도</h4>
                                            <p class="card-text">사용자들의 피드백으로 끊임없이 발전하는 게시판 툴입니다.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="carousel-section">
    <div class="carousel-floating-elements">
        <div class="floating-element">
            <i class="fab fa-php"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-users"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-download"></i>
        </div>
    </div>
    
    <div class="container">
        <div class="row">
            <div class="col-6">
                <h3>무료 PHP 게시판의 매력</h3>
            </div>
            <div class="col-6 text-end">
                <div class="carousel-controls">
                    <button class="carousel-btn" data-bs-target="#carouselExampleIndicators2" data-bs-slide="prev">
                        <i class="fa fa-arrow-left"></i>
                    </button>
                    <button class="carousel-btn" data-bs-target="#carouselExampleIndicators2" data-bs-slide="next">
                        <i class="fa fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            <div class="col-12">
                <div id="carouselExampleIndicators2" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="Community" src="https://images.unsplash.com/photo-1516321497487-e288fb19713f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">무료로 공유하세요</h4>
                                            <p class="card-text">누구나 무료로 다운로드하고 공유할 수 있는 PHP 게시판으로 커뮤니티를 활성화하세요.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="PHP Code" src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">강력한 PHP 기반</h4>
                                            <p class="card-text">PHP로 설계된 안정적이고 확장 가능한 게시판으로 원활한 사용자 경험을 제공합니다.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="Collaboration" src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">커뮤니티 협업</h4>
                                            <p class="card-text">사용자 간 자유로운 자료 교환으로 지식과 아이디어를 함께 키워나가세요.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="Open Source" src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">오픈소스 정신</h4>
                                            <p class="card-text">오픈소스 기반의 무료 툴로 누구나 기여하고 혜택을 누릴 수 있습니다.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="Easy Setup" src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">쉬운 설치</h4>
                                            <p class="card-text">간단한 설치 과정으로 빠르게 무료 게시판을 시작할 수 있습니다.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="Community Support" src="https://images.unsplash.com/photo-1556761175-b413da4baf72?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">활발한 지원</h4>
                                            <p class="card-text">커뮤니티의 활발한 지원으로 문제를 빠르게 해결하고 성장하세요.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="Free Access" src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">완전 무료</h4>
                                            <p class="card-text">비용 없이 모든 기능을 이용하며 자유로운 게시판을 경험하세요.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="Customizable" src="https://images.unsplash.com/photo-1551650975-87deedd944c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">맞춤 가능</h4>
                                            <p class="card-text">PHP 기반으로 원하는 대로 커스터마이징이 가능한 유연한 툴입니다.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card enhanced-card">
                                        <img class="card-img-top" alt="Community Driven" src="https://images.unsplash.com/photo-1559526324-4b87b5e36e44?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80">
                                        <div class="card-body">
                                            <h4 class="card-title">커뮤니티 주도</h4>
                                            <p class="card-text">사용자들의 피드백으로 끊임없이 발전하는 게시판 툴입니다.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>