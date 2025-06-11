<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
include_once CM_TEMPLATE_PATH.'/lib/menu.lib.php'; 
?>

<div class="container-fluid p-0">
    <!-- Top Header -->
    <div class="top-header">
        <div class="container text-center">
            <a href="#"><i class="fas fa-home me-1"></i>Home</a>
            <a href="#"><i class="fas fa-user me-1"></i>MyPage</a>
            <a href="#"><i class="fas fa-edit me-1"></i>정보수정</a>
            <a href="#"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
            <a href="#"><i class="fas fa-cog me-1"></i>관리자</a>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg main-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">COMPANY</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#megaMenuNavbar" aria-controls="megaMenuNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="megaMenuNavbar">
                <?php generateMegaMenu(0, 1, 5); ?>
                <span class="navbar-text">Excellence in Digital Solutions</span>
            </div>
        </div>
    </nav>
