<?php
// inquiry-edit.php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../action/login_check.php';

$inquiry_id = isset($_GET['inquiry_id']) ? (int)$_GET['inquiry_id'] : 0;

if ($inquiry_id < 1) {
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM inquiries WHERE inquiry_id = ?");
$stmt->bind_param("i", $inquiry_id);
$stmt->execute();
$result = $stmt->get_result();
$inquiry = $result->fetch_assoc();

if (!$inquiry) {
    echo "<script>alert('존재하지 않는 문의입니다.'); history.back();</script>";
    exit;
}

//작성자 본인 확인
if (!isset($_SESSION['user_id']) || $inquiry['user_id'] !== $_SESSION['user_id']) {
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}

$file_stmt = $conn->prepare("SELECT * FROM inquiry_files WHERE inquiry_id = ?");
$file_stmt->bind_param("i", $inquiry_id);
$file_stmt->execute();
$files_result = $file_stmt->get_result();
?>

<main class="inquiry-board-container">
    <div class="inquiry-form-container">
        <div class="hotel-add-admin-header" style="margin-top: 4rem;">
            <h1 class="hotel-add-admin-title" style="color:black;">문의 수정</h1>
        </div>
        <form class="inquiry-form" method="post" action="../action/inquiry_edit_action.php" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="inquiry_id" value="<?= htmlspecialchars($inquiry['inquiry_id'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="inquiry-form-group">
                <label for="category">분류</label>
                <select id="category" name="category" required>
                    <option value="">분류 선택</option>
                    <option value="reservation" <?= $inquiry['category'] === 'reservation' ? 'selected' : '' ?>>예약</option>
                    <option value="payment" <?= $inquiry['category'] === 'payment' ? 'selected' : '' ?>>결제 및 환불</option>
                    <option value="room" <?= $inquiry['category'] === 'room' ? 'selected' : '' ?>>객실</option>
                    <option value="other" <?= $inquiry['category'] === 'other' ? 'selected' : '' ?>>기타</option>
                </select>
            </div>

            <div class="inquiry-form-group">
                <label for="title">제목</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($inquiry['title'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="inquiry-form-group">
                <label for="content">내용</label>
                <textarea id="content" name="content" required><?= htmlspecialchars($inquiry['content'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <?php if ($files_result && $files_result->num_rows > 0): ?>
                <div class="inquiry-edit-files">
                    <h3>\ud83d\udcce 첨부 파일</h3>
                    <div class="file-list">
                        <?php while ($file = $files_result->fetch_assoc()): ?>
                            <a href="../action/file_download_action.php?file=<?= urlencode($file['file_path']) ?>" class="file-item" download>
                                <i class="fas fa-file-alt"></i> <?= htmlspecialchars($file['file_name'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="inquiry-form-group">
                <label for="files">첨부 파일 수정 (선택)</label>
                <input type="file" id="files" name="files[]" multiple>
                <small class="file-help-text" style="margin-left: 1rem;">새 파일을 업로드하면 기존 파일은 삭제됩니다.</small>
            </div>

            <div class="inquiry-form-actions">
                <a href="inquiry_detail.php?inquiry_id=<?= htmlspecialchars($inquiry['inquiry_id'], ENT_QUOTES, 'UTF-8') ?>" class="inquiry-write-btn inquiry-cancel-btn">취소</a>
                <button type="submit" class="inquiry-write-btn">수정 완료</button>
            </div>
        </form>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>