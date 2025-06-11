<?php
if (!defined('_CMBOARD_')) exit; // 개별 페이지 접근 불가

// 검색 조건이 있는지 확인
$has_search = !empty($_GET['search_keyword']);
?>
    <div class="container mt-5">
        <h2 class="mb-4"><?php echo $cm_title;?></h2>
        
        <!-- 검색 폼 -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="get" class="row g-3">
                    <input type="hidden" name="board" value="<?php echo $boardId;?>">
                    <div class="col-md-3">
                        <select name="search_field" class="form-select">
                            <option value="title" <?php echo ($_GET['search_field'] ?? '') === 'title' ? 'selected' : '';?>>제목</option>
                            <option value="content" <?php echo ($_GET['search_field'] ?? '') === 'content' ? 'selected' : '';?>>내용</option>
                            <option value="name" <?php echo ($_GET['search_field'] ?? '') === 'name' ? 'selected' : '';?>>작성자</option>
                            <option value="title_content" <?php echo ($_GET['search_field'] ?? '') === 'title_content' ? 'selected' : '';?>>제목+내용</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input type="text" name="search_keyword" class="form-control" value="<?php echo htmlspecialchars($_GET['search_keyword'] ?? '');?>" placeholder="검색어를 입력하세요">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">검색</button>
                        <?php if ($has_search): ?>
                        <a href="?board=<?php echo $boardId;?>" class="btn btn-secondary">전체</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- 게시글 목록 테이블 -->
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th scope="col" width="70">번호</th>
                        <th scope="col">제목</th>
                        <th scope="col" width="120">작성자</th>
                        <th scope="col" width="150">작성일</th>
                        <th scope="col" width="80">조회</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="5" class="text-center">등록된 게시글이 없습니다.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $index => $list): 
                            $list_no = $start_number - $index;
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $list_no;?></td>
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
        </div>
        
        <!-- 글쓰기 버튼 -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
            <a href="<?php echo get_board_url('write',$boardId);?>" class="btn btn-primary">글쓰기</a>
        </div>
		
        <!-- 페이지네이션 -->
        <?php echo render_pagination($page, $total_pages, $_GET);?>
        <!-- 페이지네이션 끝-->
    </div>