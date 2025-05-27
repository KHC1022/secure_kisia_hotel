<?php
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../action/login_check.php';

$inquiry_id = $_GET['inquiry_id'] ?? 0;
$inquiry_id = (int)$inquiry_id;

$sql = "SELECT * FROM inquiries WHERE inquiry_id = $inquiry_id";
$result = mysqli_query($conn, $sql);
$inquiry = mysqli_fetch_assoc($result);

// 현재 업로드된 파일 목록 가져오기
$files_sql = "SELECT * FROM inquiry_files WHERE inquiry_id = $inquiry_id";
$files_result = mysqli_query($conn, $files_sql);

?>
    <main class="inquiry-board-container">
        <div class="inquiry-form-container">
            <div class="hotel-add-admin-header" style="margin-top: 4rem;">
                <h1 class="hotel-add-admin-title" style="color:black;">문의 수정</h1>
            </div>
            <form class="inquiry-form" method="post" action="../action/inquiry_edit_action.php" enctype="multipart/form-data">
                <input type="hidden" name="inquiry_id" value="<?= $inquiry['inquiry_id'] ?>">

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
                    <input type="text" id="title" name="title" value="<?= $inquiry['title'] ?>" required>
                </div>

                <div class="inquiry-form-group">
                    <label for="content">내용</label>
                    <textarea id="content" name="content" required><?= $inquiry['content'] ?></textarea>
                </div>
                <?php if ($files_result && mysqli_num_rows($files_result) > 0): ?>
                    <div class="inquiry-edit-files">
                        <h3>📎 첨부 파일</h3>
                        <div class="file-list">
                            <?php while ($file = mysqli_fetch_assoc($files_result)): ?>
                                <a href="../<?= $file['file_path'] ?>" class="file-item" download="">
                                    <i class="fas fa-file-alt"></i> <?=$file['file_name'] ?>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="inquiry-form-group">
                    <label for="files">첨부 파일 수정 (선택)</label>
                    <input type="file" id="files" name="files" accept="*/*">
                    <small class="file-help-text" style="margin-left: 1rem;">새 파일을 업로드하면 기존 파일은 삭제됩니다.</small>
                </div>

                <div class="inquiry-form-actions">
                    <a href="inquiry_detail.php?inquiry_id=<?= $inquiry['inquiry_id'] ?>" class="inquiry-write-btn inquiry-cancel-btn">취소</a>
                    <button type="submit" class="inquiry-write-btn">수정 완료</button>
                </div>
            </form>
        </div>
    </main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
