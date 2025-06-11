<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가

// 메뉴 데이터를 조회하고 계층 구조로 출력하는 함수
function generateMegaMenu($parent_id = 0, $level = 1, $max_depth = 5) {
    global $pdo;

    // 메뉴 조회 쿼리: 비활성화되지 않은 메뉴를 sort_order 기준으로 정렬
    $sql = "SELECT menu_id, menu_name, menu_url, target_blank, menu_level
            FROM cm_menu
            WHERE parent_id = :parent_id AND is_disabled = 0
            ORDER BY sort_order ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['parent_id' => $parent_id]);
    $menus = $stmt->fetchAll();

    // 메뉴가 없으면 종료
    if (empty($menus)) {
        return;
    }

    // 최대 뎁스 제한
    if ($level > $max_depth) {
        return;
    }

    // 메뉴 리스트 시작
    $ul_class = ($level == 1) ? '' : 'mega-menu-level-' . $level;
	$ul_style = ($level < 3) ? '' : 'top:0; left: 100%; margin-top: 0;';
    $output = '<ul class="' . $ul_class . '" style="' . $ul_style . '">';

    foreach ($menus as $row) {
        $menu_id = $row['menu_id'];
        $menu_name = htmlspecialchars($row['menu_name']);
        $menu_url = htmlspecialchars($row['menu_url']);
        $target = ($row['target_blank'] == 1) ? ' target="_blank"' : '';

        // 자기 참조 방지
        if ($menu_id == $parent_id) {
            continue;
        }

        // 서브메뉴 존재 여부 확인
        $sub_sql = "SELECT COUNT(*) as count FROM cm_menu WHERE parent_id = :parent_id AND is_disabled = 0";
        $sub_stmt = $pdo->prepare($sub_sql);
        $sub_stmt->execute(['parent_id' => $menu_id]);
        $sub_count = $sub_stmt->fetch()['count'];

        // 메뉴 아이템 시작
        $li_class = ($sub_count > 0) ? 'nav-item dropdown' : 'nav-item';
        $output .= '<li class="' . $li_class . '">';

        if ($sub_count > 0) {
            // 드롭다운 메뉴
            $output .= '<a class="nav-link dropdown-toggle" href="' . $menu_url . '" id="megaMenuDropdown' . $menu_id . '" role="button" data-bs-toggle="dropdown" aria-expanded="false"' . $target . '>';
            $output .= $menu_name;
            $output .= '</a>';

            // 서브메뉴 재귀 호출
            ob_start();
            generateMegaMenu($menu_id, $level + 1, $max_depth);
            $output .= ob_get_clean();
        } else {
            // 일반 메뉴
            $output .= '<a class="nav-link" href="' . $menu_url . '"' . $target . '>';
            $output .= $menu_name;
            $output .= '</a>';
        }

        $output .= '</li>';
    }

    $output .= '</ul>';
    echo $output;
}
?>