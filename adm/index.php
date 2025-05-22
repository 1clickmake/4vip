<?php
include_once './_common.php';
define('_ADMIN_INDEX_', true);
include_once CM_ADMIN_PATH.'/admin.head.php';
?>

    <!-- Main Content -->
    <div class="main-content shifted" id="mainContent">
        <div class="container-fluid">
            <h5 class="fw-bold">DASABOARD</h5>
            
            <!-- 통계 카드 -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">총 방문자</h5>
                            <h2 class="card-text"><?php echo number_format($total_visits);?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">오늘 방문자</h5>
                            <h2 class="card-text"><?php echo number_format($today_visits);?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">어제 방문자</h5>
                            <h2 class="card-text"><?php echo number_format($yesterday_visits);?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">어제 게시물</h5>
                            <h2 class="card-text"><?php echo number_format($yesterday_posts);?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- 좌측 섹션 -->
                <div class="col-md-6">
                    <!-- 최근 7일간 방문자 통계 그래프 -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">최근 7일간 방문자 통계</h5>
                            <canvas id="visitChart" style="height: 300px;"></canvas>
                        </div>
                    </div>

                    <!-- 최근 가입 회원 -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">최근 가입 회원</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>아이디</th>
                                            <th>이름</th>
                                            <th>이메일</th>
                                            <th>가입일</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // 최근 가입 회원 10명 조회
                                        $sql = "SELECT user_id, user_name, user_email, created_at 
                                               FROM cm_users 
                                               ORDER BY created_at DESC 
                                               LIMIT 10";
                                        $latest_members = sql_all_list($sql);

                                        if ($latest_members) {
                                            foreach ($latest_members as $member) {
                                                ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($member['user_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($member['user_name']); ?></td>
                                                    <td class="text-truncate" style="max-width: 200px;">
                                                        <?php echo htmlspecialchars($member['user_email']); ?>
                                                    </td>
                                                    <td><?php echo date('Y-m-d H:i', strtotime($member['created_at'])); ?></td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            echo '<tr><td colspan="4" class="text-center">가입된 회원이 없습니다.</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 우측 섹션 -->
                <div class="col-md-6">
                    <!-- 최근 7일간 시간대별 방문자 통계 -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">최근 7일간 시간대별 방문자 통계</h5>
                            <canvas id="hourlyChart" style="height: 300px;"></canvas>
                        </div>
                    </div>

                    <!-- 최신 게시글 -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">최신 게시글</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>게시판</th>
                                            <th>작성자</th>
                                            <th>제목</th>
                                            <th>작성일</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // 최신 게시글 10개 조회
                                        $sql = "SELECT board_num, board_id, name, title, reg_date 
                                               FROM cm_board 
                                               ORDER BY reg_date DESC 
                                               LIMIT 10";
                                        $latest_posts = sql_all_list($sql);

                                        if ($latest_posts) {
                                            foreach ($latest_posts as $post) {
                                                ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($post['board_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($post['name']); ?></td>
                                                    <td class="text-truncate" style="max-width: 200px;">
                                                        <a href="<?php echo get_board_url('view', $post['board_id'], $post['board_num']); ?>">
                                                            <?php echo htmlspecialchars($post['title']); ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo date('Y-m-d H:i', strtotime($post['reg_date'])); ?></td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            echo '<tr><td colspan="4" class="text-center">등록된 게시글이 없습니다.</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    
<?php
include_once CM_ADMIN_PATH.'/admin.tail.php';
?>