<?php
include_once './_common.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';

try {
    if ($action === 'add') {
        $parent_id = (int)$_POST['parent_id'];
        $menu_name = $_POST['menu_name'];
        $menu_url = $_POST['menu_url'];
        $target_blank = isset($_POST['target_blank']) && $_POST['target_blank'] == '1' ? 1 : 0;
        $is_disabled = isset($_POST['is_disabled']) && $_POST['is_disabled'] == '1' ? 1 : 0;
        
        // 메뉴 레벨 계산 (무제한 레벨 지원)
        $menu_level = 1;
        if ($parent_id > 0) {
            $stmt = $pdo->prepare("SELECT menu_level FROM cm_menu WHERE menu_id = ?");
            $stmt->execute([$parent_id]);
            $parent = $stmt->fetch();
            if ($parent) {
                $menu_level = $parent['menu_level'] + 1;
            } else {
                throw new PDOException("상위 메뉴를 찾을 수 없습니다.");
            }
        }
        
        // sort_order 계산
        $stmt = $pdo->prepare("SELECT MAX(sort_order) as max_sort FROM cm_menu WHERE parent_id = ?");
        $stmt->execute([$parent_id]);
        $sort_order = ($stmt->fetch()['max_sort'] ?? 0) + 1;
        
        $stmt = $pdo->prepare("INSERT INTO cm_menu (parent_id, menu_name, menu_url, target_blank, is_disabled, menu_level, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$parent_id, $menu_name, $menu_url, $target_blank, $is_disabled, $menu_level, $sort_order]);
        
        echo json_encode(['status' => 'success']);
    }
    elseif ($action === 'get') {
        $menu_id = (int)$_POST['menu_id'];
        $stmt = $pdo->prepare("SELECT * FROM cm_menu WHERE menu_id = ?");
        $stmt->execute([$menu_id]);
        $menu = $stmt->fetch();
        if ($menu) {
            echo json_encode($menu);
        } else {
            echo json_encode(['status' => 'error', 'message' => '메뉴를 찾을 수 없습니다.']);
        }
    }
    elseif ($action === 'edit') {
        $menu_id = (int)$_POST['menu_id'];
        $parent_id = (int)$_POST['parent_id'];
        $menu_name = $_POST['menu_name'];
        $menu_url = $_POST['menu_url'] ?? '';
        $target_blank = isset($_POST['target_blank']) && $_POST['target_blank'] == '1' ? 1 : 0;
        $is_disabled = isset($_POST['is_disabled']) && $_POST['is_disabled'] == '1' ? 1 : 0;
        
        // 메뉴 레벨 계산 (무제한 레벨 지원)
        $menu_level = 1;
        if ($parent_id > 0) {
            $stmt = $pdo->prepare("SELECT menu_level FROM cm_menu WHERE menu_id = ?");
            $stmt->execute([$parent_id]);
            $parent = $stmt->fetch();
            if ($parent) {
                $menu_level = $parent['menu_level'] + 1;
            } else {
                throw new PDOException("상위 메뉴를 찾을 수 없습니다.");
            }
        }
        
        $stmt = $pdo->prepare("UPDATE cm_menu SET parent_id = ?, menu_name = ?, menu_url = ?, target_blank = ?, is_disabled = ?, menu_level = ? WHERE menu_id = ?");
        $stmt->execute([$parent_id, $menu_name, $menu_url, $target_blank, $is_disabled, $menu_level, $menu_id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => '변경된 내용이 없거나 메뉴를 찾을 수 없습니다.']);
        }
    }
    elseif ($action === 'delete') {
        $menu_id = (int)$_POST['menu_id'];
        
        // 하위 메뉴 확인
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cm_menu WHERE parent_id = ?");
        $stmt->execute([$menu_id]);
        if ($stmt->fetch()['count'] > 0) {
            echo json_encode(['status' => 'error', 'message' => '하위 메뉴가 있어 삭제할 수 없습니다.']);
            exit;
        }
        
        $stmt = $pdo->prepare("DELETE FROM cm_menu WHERE menu_id = ?");
        $stmt->execute([$menu_id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => '메뉴를 찾을 수 없습니다.']);
        }
    }
    elseif ($action === 'update_order') {
        $parent_id = (int)$_POST['parent_id'];
        $menu_ids = $_POST['menu_ids'];
        
        if (is_array($menu_ids)) {
            $stmt = $pdo->prepare("UPDATE cm_menu SET sort_order = ? WHERE menu_id = ? AND parent_id = ?");
            foreach ($menu_ids as $index => $menu_id) {
                $stmt->execute([$index + 1, (int)$menu_id, $parent_id]);
            }
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => '잘못된 메뉴 ID 배열']);
        }
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => '데이터베이스 오류: ' . $e->getMessage()]);
}
?>