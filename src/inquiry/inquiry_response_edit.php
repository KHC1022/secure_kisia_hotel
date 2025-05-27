<?php 
include_once __DIR__ . '/../includes/header.php'; 
include_once __DIR__ . '/../action/inquiry_detail_action.php'; // $inquiry, $response, $files 포함
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>답변 수정 - KISIA HOTEL</title>
    <link rel="stylesheet" href="../style/inquiry-detail.css">
</head>
<body>
<main class="board-container">
    <div class="inquiry-detail">
        <div class="inquiry-section-header">
            <a href="inquiry.php" class="inquiry-back-btn"><i class="fas fa-arrow-left"></i> 목록으로 돌아가기</a>
        </div>
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
                        <a href="../<?= $file['file_path'] ?>" class="file-item" download>
                            <i class="fas fa-file-alt"></i> <?= $file['file_name'] ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- 답변 수정 폼 -->
        <div class="admin-answer-form">
            <h2>답변 수정</h2>
            <form action="../action/inquiry_response_edit_action.php" method="get">
                <input type="hidden" name="inquiry_id" value="<?= $inquiry['inquiry_id'] ?>">
                <textarea name="content" rows="6" style="width:100%;" required><?= $response['content'] ?></textarea>
                <div style="margin-top: 0.5rem; text-align: right;">
                    <button type="submit" class="edit-btn">수정 완료</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
