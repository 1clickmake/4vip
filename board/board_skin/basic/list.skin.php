<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가
?>
    <div class="container mt-5">
        <h2 class="mb-4"><?php echo $cm_title;?></h2>
        
        <!-- 검색 폼 -->

        <!-- 게시글 목록 테이블 -->
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr class="text-center">
                    <th width="70">번호</th>
                    <th>제목</th>
                    <th width="120">작성자</th>
                    <th width="150">작성일</th>
                    <th width="80">조회</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="5" class="text-center">등록된 게시글이 없습니다.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($rows as $list): ?>
                    <tr>
                        <td class="text-center"><?= $list['board_id'] ?></td>
                        <td>
                            <a href="<?php echo get_board_url('view', $boardId, $list['board_num']);?>" class="text-decoration-none">
                                <?= htmlspecialchars($list['title']) ?>
                                <?php if ($list['file_count'] > 0): ?>
                                <i class="fas fa-paperclip text-secondary small ms-1"></i>
                                <?php endif; ?>
                            </a>
                        </td>
                        <td class="text-center"><?= htmlspecialchars($list['name']) ?></td>
                        <td class="text-center"><?= date('Y-m-d', strtotime($list['reg_date'])) ?></td>
                        <td class="text-center"><?= $list['view_count'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        
        
        <!-- 글쓰기 버튼 -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="<?php echo get_board_url('write',$boardId);?>" class="btn btn-primary">글쓰기</a>
        </div>
		
		
		<!-- 페이지네이션 -->
		<?php echo render_pagination($page, $total_pages, $_GET);?>
		<!-- 페이지네이션 끝-->
		
    </div>