<?php
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../action/inquiry_action.php';

$inquiry_list = $GLOBALS['inquiry_list'] ?? [];
$total_inquiries = $GLOBALS['totalInquiries'] ?? 0;
$page = $GLOBALS['page'] ?? 1;
$limit = 10; // 페이지당 표시할 문의 수
$GLOBALS['total_pages'] = ceil($total_inquiries / $limit);
?>

<main class="inquiry-board-container">
    <div class="inquiry-board-header">
        <h1 class="inquiry-board-title">문의 게시판</h1>
    </div>

    <div class="hotels-search-sort-container">
        <form class="hotels-search-box" method="get" action="inquiry.php">
            <div class="hotels-search-row">
                <div class="hotels-search-input">
                    <i class="fas fa-search"></i>
                    <input class="hotels-search-input-input" type="text" name="keyword" placeholder="제목을 입력하세요" value="<?= $_GET['keyword'] ?? '' ?>">
                </div>
                <button class="style-search-btn" type="submit">검색</button>
            </div>
        </form>
        <div class="controls-row">
            <form method="get" action="inquiry.php" class="sort-form">
                <span class="sort-label">정렬:</span>
                <select name="sort" class="sort-select" onchange="this.form.submit()">
                    <option value="none" <?= isset($_GET['sort']) && $_GET['sort'] === 'none' ? 'selected' : '' ?>>정렬 순서</option>
                    <option value="recent" <?= isset($_GET['sort']) && $_GET['sort'] === 'recent' ? 'selected' : '' ?>>최신순</option>
                    <option value="old" <?= isset($_GET['sort']) && $_GET['sort'] === 'old' ? 'selected' : '' ?>>오래된순</option>
                </select>
                <?php if (!empty($_GET['keyword'])): ?>
                    <input type="hidden" name="keyword" value="<?= $_GET['keyword'] ?>">
                <?php endif; ?>
            </form>
            <a href="inquiry-write.php" class="write-btn">문의하기</a>
        </div>
    </div>

    <?php if (empty($inquiry_list)): ?>
        <p class="no-results">검색 결과가 없습니다.</p>
    <?php else: ?>
        <table class="inquiry-board-table">
            <thead>
                <tr>
                    <th style="width: 10%">번호</th>
                    <th style="width: 10%">분류</th>
                    <th style="width: 35%">제목</th>
                    <th style="width: 15%">작성자</th>
                    <th style="width: 15%">작성일</th>
                    <th style="width: 15%">답변여부</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $start_num = ($page - 1) * $limit + 1;
                foreach ($inquiry_list as $inquiry): ?>
                    <tr>
                        <td><?= $start_num++ ?></td>
                        <td><?= $inquiry['category'] ?></td>
                        <td>
                            <?php if ($inquiry['is_secret']): ?>
                                <span class="lock-icon">🔒</span>
                            <?php endif; ?>
                            <a href="inquiry_detail.php?inquiry_id=<?= $inquiry['inquiry_id'] ?>">
                                <?= $inquiry['title'] ?>
                            </a>
                        </td>
                        <td><?= $inquiry['username'] ?></td>
                        <td><?= $inquiry['created_at'] ?></td>
                        <td>
                            <?php if ($inquiry['response']): ?>
                                <span class="status-complete">답변완료</span>
                            <?php else: ?>
                                <span class="status-pending">미답변</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php 
        include_once __DIR__ . '/../includes/pagination.php';
        pagination($total_inquiries, $limit);
        ?>
    <?php endif; ?>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>