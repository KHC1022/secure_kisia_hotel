<?php 
include_once __DIR__ . '/../includes/header.php'; 
include_once __DIR__ . '/../includes/notice_detail_info.php';
?>
<link rel="stylesheet" href="../style/inquiry-detail.css">

<main class="board-container"> 
    <div class="inquiry-detail">

        <div class="inquiry-section-header">
            <?php if ($_SESSION['is_admin'] ?? false): ?>
                <a href="../admin/admin.php?tab=notices" class="inquiry-back-btn">
                    <i class="fas fa-arrow-left"></i> 관리자 메뉴로 돌아가기
                </a>
            <?php else: ?>
                <a href="notice.php" class="inquiry-back-btn">
                    <i class="fas fa-arrow-left"></i> 목록으로 돌아가기
                </a>
            <?php endif; ?>
        </div>

        <div class="inquiry-header">
            <h2 class="inquiry-title"><?= htmlspecialchars($notice['title'], ENT_QUOTES, 'UTF-8') ?></h2>
            <div class="inquiry-meta">
                <span class="writer">작성자: <?= htmlspecialchars($notice['username'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="date">작성일: <?= htmlspecialchars(date('Y-m-d H:i', strtotime($notice['created_at'])), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>

        <div class="inquiry-content">
            <p><?= nl2br(htmlspecialchars($notice['content'], ENT_QUOTES, 'UTF-8')) ?></p>
        </div>

    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
