<?php
include_once './_common.php';
$cm_title = "회원 관리";
include_once CM_ADMIN_PATH.'/admin.head.php';

$options = [
    'table' => 'cm_users',
    'page' => $_GET['page'] ?? 1,
    'per_page' => 20,
    'order_by' => 'user_no DESC',
    'conditions' => [
        ['field' => 'user_id', 'operator' => '=', 'value' => $_GET['user_id'] ?? ''],
        ['field' => 'user_name', 'operator' => '=', 'value' => $_GET['user_name'] ?? ''],
        //['field' => 'category', 'operator' => 'IN', 'value' => $_GET['category'] ?? []],
        //['field' => 'created_at', 'operator' => 'BETWEEN', 'value' => [$_GET['from'] ?? '', $_GET['to'] ?? '']],
    ]
];

$result = sql_list($options);
$total_pages = $result['total_pages'];
$page = $result['current_page'];
?>

    <!-- Main Content -->
    <div class="main-content shifted" id="mainContent">
        <div class="container-fluid">
			<h2 class="admin-list-title"><h2><?php echo $cm_title;?></h2></h2>
			<div class="table-responsive">
				<table class="table table-sm table-striped table-bordered align-middle" style="min-width:1200px;">
					<thead class="table-dark  text-center">
						<tr>
							<th scope="col">No</th>
							<th scope="col">아이디</th>
							<th scope="col">이름</th>
							<th scope="col">이메일</th>
							<th scope="col">휴대폰 번호</th>
							<th scope="col">가입일</th>
							<th scope="col">관리</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						foreach ($result['list'] as $list) { 
						?>
						<tr class=" text-center">
							<th scope="row">1</th>
							<td><?php echo $list['user_id'];?></td>
							<td><?php echo $list['user_name'];?></td>
							<td><?php echo $list['user_email'];?></td>
							<td><?php echo $list['user_hp'];?></td>
							<td><?php echo get_formatDate($list['created_at'], 'Y-m-d h:i:s');?></td>
							<td>
								<a href="#" class="btn btn-sm btn-primary me-2">수정</a>
								<button type="button" class="btn btn-sm btn-danger">삭제</button>
							</td>
						</tr>
						<?php } ?>
						
						</tbody>
				</table>
			</div>
			
			<!-- 페이지네이션 -->
			<?php echo render_pagination($page, $total_pages, $_GET);?>
			<!-- 페이지네이션 끝-->
		</div>
    </div>

    
<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>