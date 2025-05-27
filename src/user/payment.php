<?php 
include_once __DIR__ . '/../action/payment_action.php';
include_once __DIR__ . '/../action/coupon_view_action.php'; 
include_once __DIR__ . '/../includes/header.php';
?>
<main class="payment-container">
    <div class="payment-content">

        <!-- 예약 정보 -->
        <div class="booking-summary">
            <h2>예약 정보</h2>
            <div class="summary-card">
                <div class="payment-hotel-info">
                    <h3><?= $hotel['name'] ?></h3>
                    <p class="payment-location"><i class="fas fa-map-marker-alt"></i> <?= $hotel['location'] ?></p>
                </div>
                <div class="booking-details">
                    <div class="detail-item"><span class="label">체크인</span><span class="value"><?= $checkin ?></span></div>
                    <div class="detail-item"><span class="label">체크아웃</span><span class="value"><?= $checkout ?></span></div>
                    <div class="detail-item"><span class="label">객실</span><span class="value"><?= $room_type === 'deluxe' ? '디럭스 룸' : '스위트 룸' ?></span></div>
                    <div class="detail-item"><span class="label">인원</span><span class="value"><?= $guests ?>명</span></div>
                </div>
                <div class="price-summary">
                    <div class="price-item"><span class="label">객실 요금 (<?= $days ?>박)</span><span class="value">₩<?= number_format($room_fee) ?></span></div>
                    <div class="price-item"><span class="label">세금 및 수수료</span><span class="value">₩<?= number_format($tax) ?></span></div>
                    <div class="price-item total"><span class="label">총 결제 금액</span><span class="value">₩<?= number_format($total_price) ?></span></div>
                </div>
            </div>
        </div>

        <!-- 포인트 결제 폼 -->
        <div class="payment-form">
            <h2>포인트 결제</h2>
            <form action="../action/point_pay_action.php" method="get">
                <div class="payment-form-group">
                    <label>보유 포인트</label>
                    <p><?= number_format($users['point']) ?> P</p>
                </div>

                <!-- 쿠폰 선택 -->
                <div class="payment-form-group">
                    <label style="cursor:pointer;" onclick="toggleCoupons()">쿠폰 선택 (선택 시 자동 적용) <span id="toggle-icon">▼</span></label>
                    <div id="coupon-list" style="display: none;">
                        <ul class="coupon-list">
                        <?php foreach ($available_coupons as $coupon): ?>
                            <?php 
                                $is_used = $coupon['is_used'] == 1;
                            ?>
                            <li style="<?= $is_used ? 'opacity:0.5; pointer-events:none;' : '' ?>">
                                <input type="radio" name="selected_coupon" class="coupon-radio"
                                    value="<?= $coupon['coupon_id'] ?>"
                                    data-discount-type="<?= $coupon['discount_type'] ?>"
                                    data-discount-value="<?= $coupon['discount_value'] ?>"
                                    <?= $is_used ? 'disabled' : '' ?>
                                    onclick="toggleCouponSelection(this)">
                                <div>
                                    <strong><?= $coupon['name'] ?> (<?= $coupon['code'] ?>)</strong><br>
                                    <span>
                                        <?= $coupon['discount_type'] === 'percentage' 
                                                ? intval($coupon['discount_value']).'% 할인'
                                                : number_format($coupon['discount_value']).'원 할인' ?>
                                        · <?= date('Y.m.d', strtotime($coupon['start_date'])) ?> ~ <?= date('Y.m.d', strtotime($coupon['end_date'])) ?>
                                        <?php if ($is_used): ?>
                                            <span style="color:red; font-weight:bold;">[사용 완료]</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>

                        </ul>
                    </div>
                </div>

                <!-- 총 결제 금액 -->
                <div class="payment-form-group">
                    <label>총 결제 금액</label>
                    <p id="finalPrice" data-original-price="<?= $total_price ?>"><?= number_format($total_price) ?> P</p>
                    <input type="hidden" name="charge_amount" id="chargeAmount" value="<?= $total_price ?>">
                </div>

                <div class="terms-agreement">
                    <input type="checkbox" id="terms" required>
                    <label for="terms">포인트 차감 및 예약에 동의합니다.</label>
                </div>

                <button type="submit" class="payment-btn">포인트로 결제하기</button>

                <!-- 필수 값 -->
                <input type="hidden" name="room_id" value="<?= $room_id ?>">
                <input type="hidden" name="checkin" value="<?= $checkin ?>">
                <input type="hidden" name="checkout" value="<?= $checkout ?>">
                <input type="hidden" name="total_price" value="<?= $total_price ?>">
                <input type="hidden" name="guests" value="<?= $guests ?>">
            </form>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>

<script>
// 쿠폰 할인 적용
document.querySelectorAll('input[name="selected_coupon"]').forEach(input => {
    input.addEventListener('change', function() {
        const originalPrice = parseFloat(document.getElementById('finalPrice').dataset.originalPrice);
        const type = this.dataset.discountType;
        const value = parseFloat(this.dataset.discountValue);
        let discountedPrice = originalPrice;

        if (type === 'percentage') {
            discountedPrice = Math.floor(originalPrice * (1 - value / 100));
        } else if (type === 'fixed') {
            discountedPrice = Math.max(0, originalPrice - value);
        }

        document.getElementById('finalPrice').innerText = discountedPrice.toLocaleString() + ' P';
        document.getElementById('chargeAmount').value = discountedPrice;
    });
});

// 쿠폰 접기/펼치기
function toggleCoupons() {
    const list = document.getElementById('coupon-list');
    const icon = document.getElementById('toggle-icon');
    list.style.display = list.style.display === "none" ? "block" : "none";
    icon.textContent = list.style.display === "block" ? "▲" : "▼";
}

// 쿠폰 선택 토글 함수
function toggleCouponSelection(radio) {
    if (radio.checked && radio.dataset.wasChecked === 'true') {
        radio.checked = false;
        radio.dataset.wasChecked = 'false';
        // 선택 해제 시 원래 가격으로 복원
        const originalPrice = parseFloat(document.getElementById('finalPrice').dataset.originalPrice);
        document.getElementById('finalPrice').innerText = originalPrice.toLocaleString() + ' P';
        document.getElementById('chargeAmount').value = originalPrice;
    } else {
        // 다른 라디오 버튼들의 wasChecked 상태 초기화
        document.querySelectorAll('.coupon-radio').forEach(r => {
            r.dataset.wasChecked = 'false';
        });
        radio.dataset.wasChecked = 'true';
    }
}
</script>
