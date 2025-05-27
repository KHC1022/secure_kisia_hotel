<?php
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../action/index_search_action.php';
include_once __DIR__ . '/../includes/hotels_info.php';
?>

    <main class="hotels-container">
        <div class="hotels-header">
            <h1 class="hotels-title">호텔 목록</h1>
        </div>

        <div class="hotels-search-sort-container">
            <div class="hotels-search-box">
                <form id="searchForm" method="GET">
                    <div class="hotels-search-row">
                        <div class="hotels-search-input">
                            <i class="fas fa-search"></i>
                            <input class="hotels-search-input-input" type="text" name="search" placeholder="호텔 이름 또는 위치를 입력하세요" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                        </div>
                        <button type="submit" class="style-search-btn">검색</button>
                    </div>
                </form>
            </div>
            <div class="hotels-controls-row">
                <div class="hotels-sort-controls">
                    <span class="hotels-sort-label">정렬:</span>
                    <form method="GET" action="" id="sortForm">
                        <select class="hotels-sort-select" name="sort" onchange="this.form.submit()">
                            <option value="hotel-id" <?= isset($_GET['sort']) && $_GET['sort'] === 'hotel-id' ? 'selected' : '' ?>>정렬 순서</option>
                            <option value="price-low" <?= isset($_GET['sort']) && $_GET['sort'] === 'price-low' ? 'selected' : '' ?>>가격 낮은순</option>
                            <option value="price-high" <?= isset($_GET['sort']) && $_GET['sort'] === 'price-high' ? 'selected' : '' ?>>가격 높은순</option>
                            <option value="rating" <?= isset($_GET['sort']) && $_GET['sort'] === 'rating' ? 'selected' : '' ?>>평점순</option>
                        </select>
                        <?php if (isset($_GET['search'])): ?>
                            <input type="hidden" name="search" value="<?= $_GET['search'] ?>">
                        <?php endif; ?>
                        <?php if (isset($_GET['price'])): ?>
                            <input type="hidden" name="price" value="<?= $_GET['price'] ?>">
                        <?php endif; ?>
                        <?php if (isset($_GET['facilities'])): ?>
                            <input type="hidden" name="facilities" value="<?= $_GET['facilities'] ?>">
                        <?php endif; ?>
                    </form>
                </div>
                <div class="hotels-filter-controls">
                    <div class="hotels-filter-group">
                        <button class="hotels-filter-toggle">
                            <span class="hotels-filter-label">필터</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="hotels-filter-dropdown">
                            <form method="GET" action="" id="filterForm">
                                <div class="hotels-filter-section">
                                    <h4 class="hotels-filter-section-title">가격대</h4>
                                    <div class="hotels-filter-options">
                                        <label class="hotels-filter-option">
                                            <input type="radio" name="price" value="price-0-100000" <?= isset($_GET['price']) && $_GET['price'] === 'price-0-100000' ? 'checked' : '' ?>>
                                            <span>10만원 이하</span>
                                        </label>
                                        <label class="hotels-filter-option">
                                            <input type="radio" name="price" value="price-100000-200000" <?= isset($_GET['price']) && $_GET['price'] === 'price-100000-200000' ? 'checked' : '' ?>>
                                            <span>10-20만원</span>
                                        </label>
                                        <label class="hotels-filter-option">
                                            <input type="radio" name="price" value="price-200000-300000" <?= isset($_GET['price']) && $_GET['price'] === 'price-200000-300000' ? 'checked' : '' ?>>
                                            <span>20-30만원</span>
                                        </label>
                                        <label class="hotels-filter-option">
                                            <input type="radio" name="price" value="price-300000-" <?= isset($_GET['price']) && $_GET['price'] === 'price-300000-' ? 'checked' : '' ?>>
                                            <span>30만원 이상</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="hotels-filter-section">
                                    <h4 class="hotels-filter-section-title">편의시설</h4>
                                    <div class="hotels-filter-options">
                                        <label class="hotels-filter-option">
                                            <input type="checkbox" name="facilities[]" value="pool" <?= isset($_GET['facilities']) && is_array($_GET['facilities']) && in_array('pool', $_GET['facilities']) ? 'checked' : '' ?>>
                                            <span>수영장</span>
                                        </label>
                                        <label class="hotels-filter-option">
                                            <input type="checkbox" name="facilities[]" value="spa" <?= isset($_GET['facilities']) && is_array($_GET['facilities']) && in_array('spa', $_GET['facilities']) ? 'checked' : '' ?>>
                                            <span>스파</span>
                                        </label>
                                        <label class="hotels-filter-option">
                                            <input type="checkbox" name="facilities[]" value="fitness" <?= isset($_GET['facilities']) && is_array($_GET['facilities']) && in_array('fitness', $_GET['facilities']) ? 'checked' : '' ?>>
                                            <span>피트니스</span>
                                        </label>
                                        <label class="hotels-filter-option">
                                            <input type="checkbox" name="facilities[]" value="restaurant" <?= isset($_GET['facilities']) && is_array($_GET['facilities']) && in_array('restaurant', $_GET['facilities']) ? 'checked' : '' ?>>
                                            <span>레스토랑</span>
                                        </label>
                                        <label class="hotels-filter-option">
                                            <input type="checkbox" name="facilities[]" value="parking" <?= isset($_GET['facilities']) && is_array($_GET['facilities']) && in_array('parking', $_GET['facilities']) ? 'checked' : '' ?>>
                                            <span>주차</span>
                                        </label>
                                        <label class="hotels-filter-option">
                                            <input type="checkbox" name="facilities[]" value="wifi" <?= isset($_GET['facilities']) && is_array($_GET['facilities']) && in_array('wifi', $_GET['facilities']) ? 'checked' : '' ?>>
                                            <span>와이파이</span>
                                        </label>
                                    </div>
                                </div>
                                <?php if (isset($_GET['search'])): ?>
                                    <input type="hidden" name="search" value="<?= $_GET['search'] ?>">
                                <?php endif; ?>
                                <?php if (isset($_GET['sort'])): ?>
                                    <input type="hidden" name="sort" value="<?= $_GET['sort'] ?>">
                                <?php endif; ?>
                                <div class="hotels-filter-actions">
                                    <button type="reset" class="hotels-reset-button">초기화</button>
                                    <button type="submit" class="hotels-apply-button">적용</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="style-hotel-grid">
            <?php if (empty($current_hotels)): ?>
                <p class="no-results">검색 결과가 없습니다.</p>
            <?php else: ?>
                <?php foreach ($current_hotels as $hotel): ?>
                    <div class="style-hotel-card">
                        <img src="<?= $hotel['main_image'] ?>" alt="<?= $hotel['name'] ?>" class="hotel-image">
                        <div class="style-hotel-info">
                            <h3 class="hotels-name"><?= $hotel['name'] ?></h3>
                            <p class="style-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= $hotel['location'] ?>
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
                                <span><?= $hotel['rating'] ?></span>
                            </div>
                            <div class="hotel-features">
                                    <?php if ($hotel['pool']) : ?><span class="feature">수영장</span><?php endif; ?>
                                    <?php if ($hotel['spa']) : ?><span class="feature">스파</span><?php endif; ?>
                                    <?php if ($hotel['fitness']) : ?><span class="feature">피트니스</span><?php endif; ?>
                                    <?php if ($hotel['restaurant']) : ?><span class="feature">레스토랑</span><?php endif; ?>
                                    <?php if ($hotel['parking']) : ?><span class="feature">주차</span><?php endif; ?>
                                    <?php if ($hotel['wifi']) : ?><span class="feature">와이파이</span><?php endif; ?>
                            </div>
                            <div class="style-price">
                                ₩<?= number_format($hotel['price_per_night']) ?> <span class="price-per-night">/ 박</span>
                            </div>
                            <div class="hotel-actions">
                                <a href="hotel-detail.php?id=<?= $hotel['hotel_id'] ?><?= isset($_GET['checkin']) ? '&checkin=' . urlencode($_GET['checkin']) : '' ?><?= isset($_GET['checkout']) ? '&checkout=' . urlencode($_GET['checkout']) : '' ?><?= isset($_GET['guests']) ? '&guests=' . (int)$_GET['guests'] : '' ?>" class="style-detail-btn">상세보기</a>
                                <form method="get" action="../action/wishlist_action.php" style="display:inline;">
                                    <input type="hidden" name="hotel_id" value="<?= $hotel['hotel_id'] ?>">
                                    <button type="submit" class="style-wishlist-btn">
                                    <i class="fas fa-heart"></i>찜</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php 
            include_once __DIR__ . '/../includes/pagination.php';
            pagination($total_hotels, 9);
        ?>
    </main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterToggle = document.querySelector('.hotels-filter-toggle');
            const filterDropdown = document.querySelector('.hotels-filter-dropdown');

            filterToggle.addEventListener('click', function() {
                this.classList.toggle('active');
                filterDropdown.classList.toggle('active');
            });

            document.addEventListener('click', function(event) {
                if (!filterToggle.contains(event.target) && !filterDropdown.contains(event.target)) {
                    filterToggle.classList.remove('active');
                    filterDropdown.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html> 