<?php 
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/hotels_info.php';
?>

    <main class="event-container">
        <div class="event-header">
            <h1>부산 호텔 타임딜</h1>
            <div class="event-period">오늘 자정까지</div>
        </div>

        <div class="event-content">
            <div class="event-image">
                <img src="https://images.unsplash.com/photo-1596178065887-1198b6148b2b?auto=format&fit=crop&w=1600&q=80" alt="타임딜 이벤트">
            </div>

            <div class="event-details">
                <h2>이벤트 내용</h2>
                <p class="event-description">부산의 럭셔리 호텔을 특별한 가격으로 만나보세요! 오늘 자정까지만 진행되는 특별 할인 이벤트입니다.</p>

                <div class="event-section">
                    <h3>할인 호텔</h3>
                    <div class="timedeal-deals">
                        <?php foreach ($busan_hotels as $hotel): ?>
                            <div class="timedeal-deal-item">
                                <img src="<?= htmlspecialchars($hotel['main_image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($hotel['name'], ENT_QUOTES, 'UTF-8') ?>" class="hotel-image">
                                <span class="timedeal-discount-badge">40% 할인</span>
                                <div class="timedeal-hotel-info">
                                    <h3 class="hotels-name"><?= htmlspecialchars($hotel['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                                    <p class="style-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= htmlspecialchars($hotel['location'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <div class="timedeal-price-info">
                                        <span class="timedeal-original-price">₩<?= number_format(htmlspecialchars($hotel['price_per_night'], ENT_QUOTES, 'UTF-8')) ?></span>
                                        <p class="timedeal-discount-price">₩<?= number_format(htmlspecialchars($hotel['price_per_night']*0.6, ENT_QUOTES, 'UTF-8')) ?>/박</p>
                                    </div>
                                    <form action="../hotel/hotel-detail.php" method="get">
                                        <input type="hidden" name="event_busan" id="event_busan" value="1">
                                        <input type="hidden" name="id" id="id" value="<?= htmlspecialchars($hotel['hotel_id'], ENT_QUOTES, 'UTF-8') ?>">
                                        <button type="submit" class="style-detail-btn" style="border: none;">상세보기</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="event-section">
                    <h3>예약 기간</h3>
                    <p>오늘 자정까지</p>
                </div>

                <div class="event-section">
                    <h3>유의사항</h3>
                    <ul class="notice-list">
                        <li>본 이벤트는 오늘 자정까지만 진행됩니다.</li>
                        <li>예약 변경 및 취소는 호텔 정책에 따릅니다.</li>
                        <li>할인율은 객실 타입 및 날짜에 따라 상이할 수 있습니다.</li>
                        <li>성수기 및 공휴일에는 추가 요금이 발생할 수 있습니다.</li>
                        <li>문의사항은 고객센터(1588-1234)로 연락 부탁드립니다.</li>
                    </ul>
                </div>

                <div class="event-buttons">
                    <button class="share-btn" onclick="showShareBox()">
                        <i class="fas fa-share-alt"></i>
                        공유하기
                    </button>
                </div>
            </div>
        </div>
    </main>

<?php include_once __DIR__ . '/../action/share_action.php'; ?>
<?php include_once __DIR__ . '/../includes/footer.php'; ?> 