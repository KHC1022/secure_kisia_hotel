<?php 
include_once __DIR__ . '/../includes/header.php'; 
include_once __DIR__ . '/../includes/notice_detail_info.php';
?>
<link rel="stylesheet" href="../style/inquiry-detail.css">
<main class="board-container"> 
    <div class="inquiry-detail">
    <?php if ($_SESSION['is_admin'] ?? false): ?>
        <div class="inquiry-section-header">
            <a href="../admin/admin.php?tab=notices" class="inquiry-back-btn"><i class="fas fa-arrow-left"></i> 관리자 메뉴로 돌아가기</a>
        </div>
        <?php else: ?>
        <div class="inquiry-section-header">
            <a href="notice.php" class="inquiry-back-btn"><i class="fas fa-arrow-left"></i> 목록으로 돌아가기</a>
        </div>
        <?php endif; ?>
            <div class="inquiry-header">
                <h2 class="inquiry-title"><?= $notice['title'] ?></h2>
            <div class="inquiry-meta">
                <span class="writer">작성자: <?= $notice['username'] ?></span>
                <span class="date">작성일: <?= date('Y-m-d H:i', strtotime($notice['created_at'])) ?></span>
            </div>
        </div>
        <div class="inquiry-content">
            <p><?= nl2br($notice['content']) ?></p>
        </div>
    </div>
</main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>