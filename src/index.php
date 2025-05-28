<?php 
// 보안 헤더 적용 (XSS 방지 등)
header("Content-Security-Policy: default-src 'self'; img-src *; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com; script-src 'self'");

// 데이터 포함
include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/hotels_info.php';

// 오늘 날짜 설정 (예: checkin 최소값용)
$today = date("Y-m-d");
?>

<main>
    <section class="style-hero">
        <div class="style-search-container">
            <h1>완벽한 숙소를 찾아보세요</h1>
            <div class="style-search-box"> 
                <form action="hotel/hotels.php" method="GET" class="style-search-form">
                    <div class="style-search-input">
                        <i class="fas fa-map-marker-alt"></i>
                        <input type="text" name="search" placeholder="어디로 가시나요?" required>
                    </div>
                    <div class="style-search-input">
                        <i class="fas fa-calendar"></i>
                        <input type="date" name="checkin" min="<?= $today ?>">
                    </div>
                    <div class="style-search-input">
                        <i class="fas fa-calendar"></i>
                        <input type="date" name="checkout" min="<?= $today ?>">
                    </div>
                    <div class="style-search-input">
                        <i class="fas fa-user"></i>
                        <input type="number" name="guests" placeholder="인원 수" min="1" max="4" value="">
                    </div>
                    <button type="submit" class="style-search-btn">검색</button>
                </form>
            </div>
            <div id="searchResults" class="search-results"></div>
        </div>
    </section>

    <section class="style-featured-hotels">
        <h2>추천 호텔</h2>
        <div class="style-hotel-grid">
            <?php foreach ($featured_hotels as $hotel): ?>
            <div class="style-hotel-card">
                <img src="<?= htmlspecialchars($hotel['main_image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($hotel['name'], ENT_QUOTES, 'UTF-8') ?>" class="hotel-image">
                <div class="style-hotel-info">
                    <h3 class="hotels-name"><?= htmlspecialchars($hotel['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="style-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <?= htmlspecialchars($hotel['location'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <div class="style-rating">
                        <?php
                        $fullStars = floor($hotel['rating']);
                        $emptyStars = 5 - $fullStars;

                        for ($i = 0; $i < $fullStars; $i++) {
                            echo '<i class="fas fa-star"></i>';
                        }
                        for ($i = 0; $i < $emptyStars; $i++) {
                            echo '<i class="far fa-star"></i>';
                        }
                        ?>
                        <span><?= htmlspecialchars($hotel['rating'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="style-price">
                        ₩<?= htmlspecialchars(number_format($hotel['price_per_night']), ENT_QUOTES, 'UTF-8') ?> <span class="price-per-night">/ 박</span>
                    </div>
                    <div class="hotel-actions">
                        <a href="hotel/hotel-detail.php?id=<?= htmlspecialchars(urlencode($hotel['hotel_id']), ENT_QUOTES, 'UTF-8') ?>" class="style-detail-btn">상세보기</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="style-benefits">
        <h2>이벤트 & 프로모션</h2>
        <div class="style-benefits-grid">
            <a href="event/event-review.php" class="style-benefit-card">
                <i class="fas fa-gift"></i>
                <h3>의견 남기고 선물 받자!</h3>
                <p>KISIA HOTEL 이용 소감을 댓글로 남기면 추첨을 통해 특별 선물 증정</p>
                <span class="style-event-date">2025년 5월 1일 ~ 5월 30일</span>
                <span class="style-benefit-link">자세히 보기 <i class="fas fa-arrow-right"></i></span>
            </a>
            <a href="event/event-timedeal.php" class="style-benefit-card">
                <i class="fas fa-percent"></i>
                <h3>부산 호텔 타임딜</h3>
                <p>오늘만 부산 호텔 40% 할인</p>
                <span class="style-event-date">오늘 자정까지</span>
                <span class="style-benefit-link">자세히 보기 <i class="fas fa-arrow-right"></i></span>
            </a>
            <a href="event/event-japan.php" class="style-benefit-card">
                <i class="fas fa-hotel"></i>
                <h3>일본 호텔 단독 특가</h3>
                <p>도쿄, 오사카, 후쿠오카 등 일본 전역 호텔 20% 할인</p>
                <span class="style-event-date">2025년 4월 1일 ~ 6월 30일</span>
                <span class="style-benefit-link">자세히 보기 <i class="fas fa-arrow-right"></i></span>
            </a>
        </div>
    </section>
</main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
