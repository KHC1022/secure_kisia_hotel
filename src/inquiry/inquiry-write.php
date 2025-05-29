<?php 
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../action/login_check.php';
include_once __DIR__ . '/../includes/session.php';
?>

<main class="inquiry-board-container">
    <div class="inquiry-form-container">
        <div class="hotel-add-admin-header" style="margin-top: 4rem;">
            <h1 class="hotel-add-admin-title" style="color:black;">문의 작성</h1>
        </div>

        <form class="inquiry-form" method="post" action="../action/inquiry_write_action.php" enctype="multipart/form-data">
            <!-- CSRF 토큰 포함 -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <div class="inquiry-form-group">
                <label for="category">분류</label>
                <select id="category" name="category" required>
                    <option value="">분류 선택</option>
                    <option value="reservation">예약</option>
                    <option value="payment">결제 및 환불</option>
                    <option value="room">객실</option>
                    <option value="other">기타</option>
                </select>
            </div>

            <div class="inquiry-form-group">
                <label for="title">제목</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="inquiry-form-group">
                <label for="content">내용</label>
                <textarea id="content" name="content" rows="8" required></textarea>
            </div>

            <div class="inquiry-form-group">
                <label for="files">파일 첨부</label>
                <input type="file" id="files" name="files[]" multiple accept=".jpg,.jpeg,.png,.pdf,.txt,.doc,.docx">
                <!-- 서버단에서 확장자 검증 필수 -->
            </div>

            <div class="inquiry-form-group">
                <label>
                    <input type="checkbox" id="is_secret" name="is_secret" value="1">
                    비밀글로 등록합니다
                </label>
            </div>

            <div class="inquiry-form-actions">
                <a href="inquiry.php" class="inquiry-write-btn inquiry-cancel-btn">취소</a>
                <button type="submit" class="inquiry-write-btn">등록</button>
            </div>
        </form>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
