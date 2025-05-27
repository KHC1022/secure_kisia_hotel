<?php 
if (isset($_GET['file'])) {
    include($_GET['file']);
    exit;
}

include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/hotel_detail_info.php';

$room_type = $_GET['room_type'] ?? 'deluxe';
?>
    <main class="hotel-detail-container">
        <div class="hotel-header">
            <div class="hotel-gallery">
                <div class="main-image">
                    <img src="<?php echo $hotel_main_image; ?>" alt="호텔 메인 이미지">
                </div>
                <div class="thumbnail-images">
                    <img src="<?php echo $hotel_detail_image_1; ?>" alt="호텔 이미지 1">
                    <img src="<?php echo $hotel_detail_image_2; ?>" alt="호텔 이미지 2">
                    <img src="<?php echo $hotel_detail_image_3; ?>" alt="호텔 이미지 3">
                    <img src="<?php echo $hotel_detail_image_4; ?>" alt="호텔 이미지 4">
                </div>
            </div>
            <div class="hotel-info">
                <h1 class="hotel-name"><?php echo $hotel_name; ?></h1>
                <div class="hotel-rating">
                    <div class="stars">
                        <?php
                            $fullStars = floor($hotel_rating);
                            $emptyStars = 5 - $fullStars;
                            
                            for ($i = 0; $i < $fullStars; $i++) {
                                echo '<i class="fas fa-star"></i>';
                            }
                            for ($i = 0; $i < $emptyStars; $i++) {
                                echo '<i class="far fa-star"></i>';
                            }
                        ?>
                        <span><?php echo $hotel_rating; ?></span>
                    </div>
                    <span class="review-count">(<?php echo $review_count; ?>개 후기)</span>
                </div>
                <div class="hotel-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <?php echo $hotel_location; ?>
                </div>
                <div class="hotel-features">
                    <?php if ($hotel_facilities['pool']) : ?><span class="feature">수영장</span><?php endif; ?>
                    <?php if ($hotel_facilities['spa']) : ?><span class="feature">스파</span><?php endif; ?>
                    <?php if ($hotel_facilities['fitness']) : ?><span class="feature">피트니스</span><?php endif; ?>
                    <?php if ($hotel_facilities['restaurant']) : ?><span class="feature">레스토랑</span><?php endif; ?>
                    <?php if ($hotel_facilities['parking']) : ?><span class="feature">주차</span><?php endif; ?>
                    <?php if ($hotel_facilities['wifi']) : ?><span class="feature">와이파이</span><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="hotel-content">
            <div class="main-content">
                <section class="description">
                    <h2>호텔 소개</h2>
                    <p><?php echo str_replace('.', '.<br>', $hotel_description); ?></p>
                </section>

                <section class="facilities">
                    <h2>편의시설</h2>
                    <div class="facility-grid">
                        <?php if ($hotel_facilities['pool']) : ?>
                        <div class="facility-item">
                            <i class="fas fa-swimming-pool"></i>
                            <h3>수영장</h3>
                            <p>실내 수영장과 야외 수영장을 모두 이용하실 수 있습니다.</p>
                        </div>
                        <?php endif; ?>
                        <?php if ($hotel_facilities['spa']) : ?>
                        <div class="facility-item">
                            <i class="fas fa-spa"></i>
                            <h3>스파</h3>
                            <p>전문 테라피스트가 제공하는 다양한 마사지와 트리트먼트를 즐기실 수 있습니다.</p>
                        </div>
                        <?php endif; ?>
                        <?php if ($hotel_facilities['fitness']) : ?>
                        <div class="facility-item">
                            <i class="fas fa-dumbbell"></i>
                            <h3>피트니스 센터</h3>
                            <p>최신식 운동기구와 전문 트레이너가 상주하는 피트니스 센터입니다.</p>
                        </div>
                        <?php endif; ?>
                        <?php if ($hotel_facilities['restaurant']) : ?>
                        <div class="facility-item">
                            <i class="fas fa-utensils"></i>
                            <h3>레스토랑</h3>
                            <p>미슐랭 스타 셰프가 운영하는 고급 레스토랑에서 다양한 요리를 즐기실 수 있습니다.</p>
                        </div>
                        <?php endif; ?>
                        <?php if ($hotel_facilities['parking']) : ?>
                        <div class="facility-item">
                            <i class="fas fa-parking"></i>
                            <h3>주차</h3>
                            <p>여유 있는 주차 공간이 준비되어 있습니다.</p>
                        </div>
                        <?php endif; ?>
                        <?php if ($hotel_facilities['wifi']) : ?>
                        <div class="facility-item">
                            <i class="fas fa-wifi"></i>
                            <h3>와이파이</h3>
                            <p>무료 Wi-Fi를 이용하실 수 있습니다.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>
                
                <section class="rooms section-tab-content" style="display: block;">
                    <h2>객실 타입</h2>
                    <?php if ($available_rooms > 0) : ?>
                    <div class="room-grid">
                        <?php foreach ($hotel_rooms_deluxe as $deluxe) : ?>
                        <div class="room-card">
                            <img src="<?php echo $deluxe['rooms_image']; ?>" alt="디럭스 룸">
                            <div class="room-info">
                                <h3>디럭스 룸</h3>
                                <p>최대 <?php echo $deluxe['max_person']; ?>인 / 35㎡</p>
                                <ul class="room-features">
                                    <li><i class="fas fa-bed"></i> 킹 사이즈 베드</li>
                                    <li><i class="fas fa-wifi"></i> 무료 Wi-Fi</li>
                                    <li><i class="fas fa-tv"></i> 55인치 스마트 TV</li>
                                    <li><i class="fas fa-coffee"></i> 커피/티 메이커</li>
                                </ul>
                                <?php if ($event_busan == 1 || $event_japan == 1) : ?>
                                <div class="timedeal-price-info">
                                    <span class="timedeal-original-price">₩<?= $deluxe['price'] ?></span>
                                    <p class="timedeal-discount-price">₩<?= $deluxe_sale_price?>/박</p>
                                </div>
                                <?php else : ?>
                                <div class="room-price">
                                    <span class="price">₩<?php echo $deluxe['price']; ?></span>
                                    <span class="per-night">/ 박</span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php foreach ($hotel_rooms_suite as $suite) : ?>
                        <div class="room-card">
                            <img src="<?php echo $suite['rooms_image']; ?>" alt="스위트 룸">
                            <div class="room-info">
                                <h3>스위트 룸</h3>
                                <p>최대 <?php echo $suite['max_person']; ?>인 / 65㎡</p>
                                <ul class="room-features">
                                    <li><i class="fas fa-bed"></i> 킹 사이즈 베드 + 소파베드</li>
                                    <li><i class="fas fa-wifi"></i> 무료 Wi-Fi</li>
                                    <li><i class="fas fa-tv"></i> 65인치 스마트 TV</li>
                                    <li><i class="fas fa-hot-tub"></i> 스파 욕조</li>
                                </ul>
                                <?php if ($event_busan == 1 || $event_japan == 1) : ?>
                                <div class="timedeal-price-info">
                                    <span class="timedeal-original-price">₩<?= $suite['price'] ?></span>
                                    <p class="timedeal-discount-price">₩<?= $suite_sale_price ?>/박</p>
                                </div>
                                <?php else : ?>
                                <div class="room-price">
                                    <span class="price">₩<?php echo $suite['price']; ?></span>
                                    <span class="per-night">/ 박</span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else : ?>
                    <div class="no-rooms-message">
                        <p>현재 예약 가능한 객실이 없습니다.</p>
                    </div>
                    <?php endif; ?>
                </section>

                <section class="reviews">
                    <h2>후기 작성</h2>
                    <div class="reviews-section">
                        <div class="write-review">
                            <?php if (isset($_SESSION['user_id']) && $user_reservation_id !== null): ?>
                                <form class="review-form" action="../action/review_action.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
                                    <input type="hidden" name="reservation_id" value="<?php echo $user_reservation_id; ?>">
                                    <div class="rating-input">
                                        <label>평점</label>
                                        <div class="star-rating">
                                            <i class="far fa-star" data-value="1"></i>
                                            <i class="far fa-star" data-value="2"></i>
                                            <i class="far fa-star" data-value="3"></i>
                                            <i class="far fa-star" data-value="4"></i>
                                            <i class="far fa-star" data-value="5"></i>
                                            <input type="hidden" name="rating" value="0" />
                                        </div>
                                    </div>
                                    <div class="review-content">
                                        <label for="content">후기 내용</label>
                                        <textarea id="content" name="content" rows="4" required placeholder="호텔 이용 경험을 공유해주세요."></textarea>
                                    </div>
                                    <div class="review-travel-type">
                                        <label for="travel_type">여행 유형</label>
                                        <select name="travel_type" id="travel_type" required>
                                            <option value="">선택하세요</option>
                                            <option value="solo">혼자</option>
                                            <option value="couple">커플</option>
                                            <option value="friend">친구</option>
                                            <option value="family">가족</option>
                                            <option value="business">출장</option>
                                        </select>
                                    </div>
                                    <div class="review-image">
                                        <label for="image">사진 첨부 (선택)</label>
                                        <input type="file" name="review_image" accept="image/*">
                                    </div>
                                    <div class="review-form-actions">
                                        <button type="submit" name="submit_review" class="submit-review">후기 등록</button>
                                    </div>
                                </form>
                            <?php elseif (isset($_SESSION['user_id'])): ?>
                                <div class="no-rooms-message">
                                    <p>해당 호텔 이용 후에만 후기를 작성할 수 있습니다.</p>
                                </div>
                            <?php else: ?>
                                <div class="no-rooms-message">
                                    <p>후기 작성을 위해서는 로그인이 필요합니다.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <h2>후기</h2>
                    <div class="review-list">
                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $review) : ?>
                            <div class="review-card">
                                <div class="reviewer-info">
                                    <img src="<?php echo $review['profile_image'] ?>" alt="Reviewer">
                                    <div class="reviewer-details">
                                        <div class="reviewer-name"><?php echo $review['username']; ?></div>
                                        <div class="review-rating">
                                            <div class="stars">
                                                <?php
                                                    $rating = $review['rating'];
                                                    $fullStars = floor($rating);
                                                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                                    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                                    
                                                    // 완전한 별 출력
                                                    for ($i = 0; $i < $fullStars; $i++) {
                                                        echo '<i class="fas fa-star"></i>';
                                                    }
                                                    
                                                    // 반별 출력
                                                    if ($hasHalfStar) {
                                                        echo '<i class="fas fa-star-half-alt"></i>';
                                                    }
                                                    
                                                    // 빈 별 출력
                                                    for ($i = 0; $i < $emptyStars; $i++) {
                                                        echo '<i class="far fa-star"></i>';
                                                    }
                                                ?>
                                                <span><?php echo number_format($review['rating'], 1); ?></span>
                                            </div>
                                            <div class="review-date"><?php echo $review['created_at']; ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="review-content">
                                    <?php echo $review['content']; ?>
                                </div>

                                <?php if (!empty($review['image_url'])): ?>
                                <div class="review-image">
                                    <img src="/<?php echo ltrim($review['image_url'], '/'); ?>" alt="Review Image">
                                </div>
                                <?php endif; ?>

                                <div class="review-meta">
                                    <div class="review-travel-type">
                                        <?php
                                        $types = [
                                            'solo' => '혼자',
                                            'couple' => '커플',
                                            'friend' => '친구',
                                            'family' => '가족',
                                            'business' => '출장'
                                        ];
                                        echo "여행 유형: <span>" . ($types[$review['travel_type']] ?? '미정') . "</span>";
                                        ?>
                                    </div>
                                    <div class="detail-review-actions">
                                        <a href="../action/review_action.php?review_id=<?php echo $review['review_id']; ?>&action=helpful&hotel_id=<?php echo $hotel_id; ?>" class="action-btn">
                                            <i class="far fa-thumbs-up"></i>도움이 됨<span class="count">(<?php echo $review['count_is_helpful']; ?>)</span>
                                        </a>
                                        <a href="../action/review_action.php?review_id=<?php echo $review['review_id']; ?>&action=not_helpful&hotel_id=<?php echo $hotel_id; ?>" class="action-btn">
                                            <i class="far fa-thumbs-down"></i>도움이 되지 않음<span class="count">(<?php echo $review['count_is_not_helpful']; ?>)</span>
                                        </a>
                                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $review['user_id']) : ?>
                                        <a href="../action/review_action.php?action=delete&review_id=<?php echo $review['review_id']; ?>&hotel_id=<?php echo $hotel_id; ?>"
                                        class="action-btn"
                                        onclick="return confirm('정말 삭제하시겠습니까?');">
                                        🗑 삭제
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-reviews">
                                <p class="no-review-text">아직 작성된 후기가 없습니다.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <div class="sidebar">
                <?php if ($available_rooms > 0) : ?>
                <div class="booking-widget">
                    <h2>객실 예약</h2>
                    <form class="booking-form" action="../user/payment.php" method="get">
                        <input type="hidden" name="id" id="id" value="<?= $hotel_id ?>">
                        <div class="booking-form-group">
                            <label for="check-in">체크인</label>
                            <input type="date" id="check-in" name="checkin" value="<?= isset($_GET['checkin']) ? $_GET['checkin'] : '' ?>" min="<?= $today ?>" required>
                        </div>
                        <div class="booking-form-group">
                            <label for="check-out">체크아웃</label>
                            <input type="date" id="check-out" name="checkout" value="<?= isset($_GET['checkout']) ? $_GET['checkout'] : '' ?>" min="<?= $today ?>" required>
                        </div>
                        <div class="booking-form-group">
                            <label for="guests">인원</label>
                            <input type="number" id="guests" name="guests" value="<?= isset($_GET['guests']) ? (int)$_GET['guests'] : 1 ?>" min="1" max="4" required>
                        </div>
                        <div class="booking-form-group">
                            <label for="room-type">객실 타입</label>
                            <select id="room-type" name="room_type">
                                <?php if ($deluxe_room_id) : ?>
                                <option value="deluxe" <?= ($room_type === 'deluxe') ? 'selected' : '' ?>>디럭스 룸</option>
                                <?php endif; ?>
                                <?php if ($suite_room_id) : ?>
                                <option value="suite" <?= ($room_type === 'suite') ? 'selected' : '' ?>>스위트 룸</option>
                                <?php endif; ?>
                            </select>
                            <?php if ($deluxe_room_id) : ?>
                            <input type="hidden" name="deluxe_room_id" value="<?= $deluxe_room_id ?>">
                            <?php endif; ?>
                            <?php if ($suite_room_id) : ?>
                            <input type="hidden" name="suite_room_id" value="<?= $suite_room_id ?>">
                            <?php endif; ?>
                        </div>
                        <?php if ($event_busan == 1) : ?>
                        <input type="hidden" name="event_busan" value="<?= $event_busan ?>">
                        <?php endif; ?>
                        <?php if ($event_japan == 1) : ?>
                        <input type="hidden" name="event_japan" value="<?= $event_japan ?>">
                        <?php endif; ?>
                        <button type="submit" class="book-now-btn">예약하기</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

<script>
const stars = document.querySelectorAll('.star-rating i');
const ratingInput = document.querySelector('.star-rating input');

stars.forEach(star => {
    star.addEventListener('click', function(e) {
        const rect = this.getBoundingClientRect();
        const offsetX = e.clientX - rect.left;
        const starWidth = rect.width;
        const value = parseInt(this.getAttribute('data-value'));
        let selectedValue = value;

        if (offsetX < starWidth / 2) selectedValue -= 0.5;
        ratingInput.value = selectedValue;

        stars.forEach(s => {
            s.classList.remove('fas', 'far', 'fa-star', 'fa-star-half-alt', 'full', 'half');
            s.classList.add('far', 'fa-star');
        });

        stars.forEach(s => {
            const sValue = parseInt(s.getAttribute('data-value'));
            s.classList.remove('far', 'fas', 'fa-star', 'fa-star-half-alt');

            if (sValue < selectedValue) {
                s.classList.add('fas', 'fa-star', 'full');
            } else if (sValue === Math.ceil(selectedValue)) {
                if (selectedValue % 1 === 0.5) {
                    s.classList.add('fas', 'fa-star-half-alt', 'half');
                } else {
                    s.classList.add('fas', 'fa-star', 'full');
                }
            } else {
                s.classList.add('far', 'fa-star');
            }
        });
    });
});
</script> 

<?php include_once __DIR__ . '/../includes/footer.php'; ?> 