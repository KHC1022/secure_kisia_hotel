<?php 
if (isset($_GET['file'])) {
    include($_GET['file']);
    exit;
}

include_once __DIR__ . '/../includes/header.php'; 
include_once __DIR__ . '/../action/inquiry_detail_action.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/inquiry-detail.css">
</head>
<body>
<main class="board-container"> 
    <div class="inquiry-detail">
        <?php if ($_SESSION['is_admin'] ?? false): ?>
        <div class="inquiry-section-header">
            <a href="../admin/admin.php?tab=inquiries" class="inquiry-back-btn"><i class="fas fa-arrow-left"></i> 관리자 메뉴로 돌아가기</a>
        </div>
        <?php else: ?>
        <div class="inquiry-section-header">
            <a href="inquiry.php" class="inquiry-back-btn"><i class="fas fa-arrow-left"></i> 목록으로 돌아가기</a>
        </div>
        <?php endif; ?>
        <div class="inquiry-header">
            <h2 class="inquiry-title"><?= $inquiry['title'] ?></h2>
            <div class="inquiry-meta">
                <span class="writer">작성자: <?= $inquiry['username'] ?></span>
                <span class="date">작성일: <?= date('Y-m-d H:i', strtotime($inquiry['created_at'])) ?></span>
            </div>
        </div>

        <div class="inquiry-content">
            <p><?= nl2br($inquiry['content']) ?></p>
        </div>

        <?php if (!empty($files)): ?>
            <div class="inquiry-files">
                <h3>📎 첨부 파일</h3>
                <div class="file-list">
                    <?php foreach ($files as $file): ?>
                        <a href="../action/file_download_action.php?file=<?= urlencode($file['file_path']) ?>" class="file-item">
                            <i class="fas fa-file-alt"></i> <?= $file['file_name'] ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- 작성자 또는 관리자 수정/삭제 -->
        <?php if ((isset($_SESSION['user_id']) && $_SESSION['user_id'] == $inquiry['user_id']) || ($_SESSION['is_admin'] ?? false)): ?>
        <div class="inquiry-actions">
            <a href="inquiry_edit.php?inquiry_id=<?= $inquiry['inquiry_id'] ?>" class="inquiry-edit-btn">수정</a>
            <form action="../action/inquiry_delete_action.php" method="get" onsubmit="return confirm('정말 삭제하시겠습니까?');" style="display:inline;">
                <input type="hidden" name="inquiry_id" value="<?= $inquiry['inquiry_id'] ?>">
                <button type="submit" class="inquiry-delete-btn">삭제</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- 답변 표시 -->
        <?php if ($response): ?>
            <div class="answers-section">
                <div class="inquiry-header">
                    <h2 class="inquiry-title" style="color: #7851A9;">답변</h2>
                    <div class="inquiry-meta">
                        <span class="writer">작성자 : 관리자</span>
                        <span class="date">작성일: <?= date('Y-m-d H:i', strtotime($response['created_at'])) ?></span>
                    </div>
                </div>
                <div class="answer-content">
                    <p><?= nl2br($response['content']) ?></p>
                </div>
                <!-- 관리자일 경우 수정 버튼 -->
                <?php if ($_SESSION['is_admin'] ?? false): ?>
                    <div class="admin-actions">
                    <a href="inquiry_response_edit.php?inquiry_id=<?= $inquiry['inquiry_id'] ?>" class="inquiry-edit-btn">답변 수정</a>
                    </div>
                <?php endif; ?>
            </div>
        
        <?php elseif ($_SESSION['is_admin'] ?? false): ?>
            <!-- 관리자일 경우 답변 작성 폼 -->
            <div class="admin-answer-form">
                <h2>답변 작성</h2>
                <form action="../action/inquiry_response_action.php" method="get">
                    <input type="hidden" name="inquiry_id" value="<?= $inquiry['inquiry_id'] ?>">
                    <textarea name="content" rows="6" style="width:100%;" required></textarea>
                    <div style="margin-top: 0.5rem; text-align: right;">
                        <button type="submit" class="inquiry-edit-btn">등록</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>