<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
?>
<script>
//사이드바
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const toggleButton = document.getElementById('toggleSidebar');
const closeButton = document.getElementById('closeSidebar');

function toggleSidebar() {
    sidebar.classList.toggle('hidden');
    mainContent.classList.toggle('shifted');
}

toggleButton.addEventListener('click', toggleSidebar);
closeButton.addEventListener('click', toggleSidebar);

//사이드바 토글메뉴
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');

    // 모든 collapse 토글 아이콘 처리
    document.querySelectorAll('.toggle-icon').forEach(function (icon) {
        const targetId = icon.getAttribute('data-target');
        const submenu = document.getElementById(targetId);

        if (submenu) {
            submenu.addEventListener('show.bs.collapse', function () {
                // 1. 다른 열린 collapse 요소들을 닫는다
                const openMenus = sidebar.querySelectorAll('.collapse.show');
                openMenus.forEach(function (menu) {
                    if (menu !== submenu) {
                        const collapseInstance = bootstrap.Collapse.getInstance(menu);
                        if (collapseInstance) {
                            collapseInstance.hide();
                        } else {
                            new bootstrap.Collapse(menu, { toggle: false }).hide();
                        }
                    }
                });

                // 2. 아이콘 변경: 열림
                icon.classList.remove('bi-chevron-down');
                icon.classList.add('bi-chevron-up');
            });

            submenu.addEventListener('hide.bs.collapse', function () {
                // 아이콘 변경: 닫힘
                icon.classList.remove('bi-chevron-up');
                icon.classList.add('bi-chevron-down');
            });
        }
    });
});
</script>