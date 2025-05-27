<?php
include_once __DIR__ . '/../includes/header.php';
?>

<main class="admin-hotel-add-container">
    <form action="../action/coupon_add_action.php" method="GET" class="hotel-add-admin-form">
        <div class="hotel-add-admin-header">
            <a href="admin.php?tab=coupons" class="hotel-add-admin-back-btn"><i class="fas fa-arrow-left"></i> 목록으로 돌아가기</a>
            <h1 class="hotel-add-admin-title">쿠폰 추가</h1>
        </div>

        <!-- 쿠폰 정보 -->
        <div class="hotel-add-admin-form-group image-upload-section">
            <div class="room-info-grid">
                <div class="room-info-item">
                    <label for="code">쿠폰 코드</label>
                    <input type="text" id="code" name="code" required>
                </div>
                <div class="room-info-item">
                    <label for="name">쿠폰명</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="room-info-item">
                    <label for="discount_type">할인 유형</label>
                    <select id="discount_type" name="discount_type" required>
                        <option value="percentage">퍼센트</option>
                        <option value="fixed">정액</option>
                    </select>
                </div>
                <div class="room-info-item">
                    <label for="discount_value">할인 값</label>
                    <input type="number" id="discount_value" name="discount_value" min="0" required>
                    <small id="discount_hint">퍼센트 선택 시: 0-100, 정액 선택 시: 원 단위</small>
                </div>
                <div class="room-info-item">
                    <label for="start_date">시작일</label>
                    <input type="date" id="start_date" name="start_date" required>
                </div>
                <div class="room-info-item">
                    <label for="end_date">종료일</label>
                    <input type="date" id="end_date" name="end_date" required>
                </div>
                <div class="room-info-item">
                    <label for="minimum_purchase">최소 구매액</label>
                    <input type="number" id="minimum_purchase" name="minimum_purchase" min="0" required>
                </div>
                <div class="room-info-item">
                    <label for="maximum_discount">최대 할인액</label>
                    <input type="number" id="maximum_discount" name="maximum_discount" min="0">
                    <small>퍼센트 할인 시에만 적용됩니다. 비워두면 제한 없음</small>
                </div>
                <div class="room-info-item">
                    <label for="usage_limit">사용 제한 횟수</label>
                    <input type="number" id="usage_limit" name="usage_limit" min="0">
                    <small>비워두면 무제한</small>
                </div>
                <div class="room-info-item">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" checked>
                        활성화 여부 (체크 시 활성화)
                    </label>
                </div>
            </div>
        </div>

        <div class="hotel-add-admin-form-actions">
            <a href="admin.php?tab=coupons" class="hotel-add-admin-cancel-btn">취소</a>
            <button type="submit" class="hotel-add-admin-submit-btn">쿠폰 추가</button>
        </div>
    </form>
</main>

<script>
document.getElementById('discount_type').addEventListener('change', function() {
    const discountValue = document.getElementById('discount_value');
    const discountHint = document.getElementById('discount_hint');
    const maximumDiscount = document.getElementById('maximum_discount').parentElement;
    
    if (this.value === 'percentage') {
        discountValue.max = 100;
        discountHint.textContent = '퍼센트 선택 시: 0-100';
        maximumDiscount.style.display = 'block';
    } else {
        discountValue.removeAttribute('max');
        discountHint.textContent = '정액 선택 시: 원 단위';
        maximumDiscount.style.display = 'none';
    }
});
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?> 