<?php
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/notice_info.php';
?>

<main class="inquiry-board-container">
    <div class="inquiry-board-header">
        <h1 class="inquiry-board-title">공지사항</h1>
    </div>

    <div class="hotels-search-sort-container">
        <form class="hotels-search-box" method="get">
            <div class="hotels-search-row">
                <div class="hotels-search-input">
                    <i class="fas fa-search"></i>
                    <input class="hotels-search-input-input" type="text" name="search" placeholder="제목을 입력하세요"
                        value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <button class="style-search-btn" type="submit">검색</button>
            </div>
        </form>

        <div class="controls-row">
            <form method="get" action="notice.php" class="sort-form">
                <span class="sort-label">정렬:</span>
                <select name="sort" class="sort-select" onchange="this.form.submit()">
                    <option value="none" <?= isset($_GET['sort']) && $_GET['sort'] === 'none' ? 'selected' : '' ?>>정렬 순서</option>
                    <option value="recent" <?= isset($_GET['sort']) && $_GET['sort'] === 'recent' ? 'selected' : '' ?>>최신순</option>
                    <option value="old" <?= isset($_GET['sort']) && $_GET['sort'] === 'old' ? 'selected' : '' ?>>오래된순</option>
                </select>
                <?php if (!empty($search)): ?>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if (empty($notice_list)): ?>
        <p class="no-results">검색 결과가 없습니다.</p>
    <?php else: ?>
        <table class="inquiry-board-table">
            <thead>
                <tr>
                    <th style="width: 10%">번호</th>
                    <th style="width: 35%">제목</th>
                    <th style="width: 15%">작성자</th>
                    <th style="width: 15%">작성일</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $start_num = ($page - 1) * 5 + 1;
                foreach ($notice_list as $notice): 
                ?>
                    <tr>
                        <td><?= htmlspecialchars($start_num++, ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a href="../notice/notice-detail.php?notice_id=<?= urlencode($notice['notice_id']) ?>">
                                <?= htmlspecialchars($notice['title'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($notice['username'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($notice['created_at'])), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php 
        include_once __DIR__ . '/../includes/pagination.php';
        pagination($total_notice, 5);
        ?>
    <?php endif; ?>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
