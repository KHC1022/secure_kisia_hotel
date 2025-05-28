<?php
include_once __DIR__ . '/../action/admin_access.php';
include_once __DIR__ . '/../includes/header.php';
?>

<main class="admin-hotel-add-container">

    <form action="../action/notice_write_action.php" method="post" class="hotel-add-admin-form">
    <div class="hotel-add-admin-header">
        <a href="admin.php?tab=notices" class="hotel-add-admin-back-btn"><i class="fas fa-arrow-left"></i> 목록으로 돌아가기</a>
        <h1 class="hotel-add-admin-title">공지사항 작성</h1>
    </div>
        <!-- 공지사항 작성 -->
        <div class="hotel-add-admin-form-group image-upload-section">
                <div class="room-info-grid">
                    <div class="room-info-item">
                            <label for="title">제목</label>
                            <input type="text" id="title" name="title" required>
                        </div>
                    <div class="room-info-item">
                        <label for="content">내용</label>
                        <textarea style="height: 300px;" id="content" name="content" rows="5" required></textarea>
                    </div>
                    <div class="room-info-item">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_released" value="1" checked>
                            공개 여부 (체크 시 공개)
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="hotel-add-admin-form-actions">
            <a href="admin.php?tab=notice" class="hotel-add-admin-cancel-btn">취소</a>
            <button type="submit" class="hotel-add-admin-submit-btn">공지사항 작성</button>
        </div>
    </form>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?> 