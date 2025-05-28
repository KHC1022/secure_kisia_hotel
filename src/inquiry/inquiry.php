<?php
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../action/inquiry_action.php';

// 입력값 유효성 검사
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$sort = $_GET['sort'] ?? 'none';
$page = isset($GLOBALS['page']) && is_numeric($GLOBALS['page']) ? (int)$GLOBALS['page'] : 1;
$limit = 10;

// 화이트리스트 기반 정렬 방식 제한
$allowed_sort = ['none', 'recent', 'old'];
if (!in_array($sort, $allowed_sort)) {
    $sort = 'none';
}

$inquiry_list = $GLOBALS['inquiry_list'] ?? [];
$total_inquiries = $GLOBALS['totalInquiries'] ?? 0;
$total_pages = ceil($total_inquiries / $limit);
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
                    <option value="none" <?= $sort === 'none' ? 'selected' : '' ?>>정렬 순서</option>
                    <option value="recent" <?= $sort === 'recent' ? 'selected' : '' ?>>최신순</option>
                    <option value="old" <?= $sort === 'old' ? 'selected' : '' ?>>오래된순</option>
                </select>
                <?php if (!empty($_GET['keyword'])): ?>
                    <input type="hidden" name="keyword" value="<?= htmlspecialchars($_GET['keyword'], ENT_QUOTES, 'UTF-8') ?>">
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
                $start_num = ($page - 1) * 5 + 1;
                foreach ($inquiry_list as $inquiry): ?>
                    <tr>
                        <td><?= htmlspecialchars($start_num++, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($inquiry['category'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php if ($inquiry['is_secret']): ?>
                                <span class="lock-icon">🔒</span>
                            <?php endif; ?>
                            <a href="inquiry_detail.php?inquiry_id=<?= htmlspecialchars($inquiry['inquiry_id'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($inquiry['title'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($inquiry['username'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($inquiry['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
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
        if ($total_pages > 0 && $page > $total_pages) {
            $page = $total_pages;
        }
        include_once __DIR__ . '/../includes/pagination.php';
        pagination($total_inquiries, $limit);
        ?>
    <?php endif; ?>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>