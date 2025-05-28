<?php
include_once __DIR__ . '/../action/admin_access.php';
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/hotel_edit_info.php';
?>

<main class="admin-hotel-add-container">
    <form action="../action/hotel_edit_action.php" method="POST" enctype="multipart/form-data" class="hotel-add-admin-form">
        <input type="hidden" name="hotel_id" value="<?php echo htmlspecialchars($hotel_id, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="hotel-add-admin-header">
            <a href="admin.php?tab=hotels" class="hotel-add-admin-back-btn"><i class="fas fa-arrow-left"></i> 목록으로 돌아가기</a>
            <h1 class="hotel-add-admin-title">호텔 수정</h1>
        </div>

        <!-- 호텔 기본 정보 -->
        <div class="hotel-add-admin-form-group image-upload-section">
            <h3>호텔 정보</h3>
            <div class="image-upload-grid">
                <div class="detail-image-section">
                    <h4>기본 정보</h4>
                    <div class="room-info-grid">
                        <div class="room-info-item">
                            <label for="name">호텔 이름</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($hotel_name, ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="room-info-item">
                            <label for="location">위치</label>
                            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($hotel_location, ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="room-info-item">
                            <label for="description">설명</label>
                            <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($hotel_description, ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                        <div class="room-info-item">
                            <label for="price_per_night">1박 가격</label>
                            <input type="number" id="price_per_night" name="price_per_night" min="0" value="<?php echo htmlspecialchars($hotel_price_per_night, ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="detail-image-section">
                    <h4>부대시설</h4>
                    <div class="room-info-grid">
                        <div class="hotel-add-admin-checkbox-group">
                            <label>
                                <input type="checkbox" name="facilities[]" value="pool" <?php echo htmlspecialchars($hotel_facilities['pool'], ENT_QUOTES, 'UTF-8') && $hotel_facilities['pool'] ? 'checked' : ''; ?>> 수영장
                            </label>
                            <label>
                                <input type="checkbox" name="facilities[]" value="spa" <?php echo htmlspecialchars($hotel_facilities['spa'], ENT_QUOTES, 'UTF-8') && $hotel_facilities['spa'] ? 'checked' : ''; ?>> 스파
                            </label>
                            <label>
                                <input type="checkbox" name="facilities[]" value="fitness" <?php echo htmlspecialchars($hotel_facilities['fitness'], ENT_QUOTES, 'UTF-8') ? 'checked' : ''; ?>> 피트니스
                            </label>
                            <label>
                                <input type="checkbox" name="facilities[]" value="restaurant" <?php echo htmlspecialchars($hotel_facilities['restaurant'], ENT_QUOTES, 'UTF-8') ? 'checked' : ''; ?>> 레스토랑
                            </label>
                            <label>
                                <input type="checkbox" name="facilities[]" value="parking" <?php echo htmlspecialchars($hotel_facilities['parking'], ENT_QUOTES, 'UTF-8') ? 'checked' : ''; ?>> 주차장
                            </label>
                            <label>
                                <input type="checkbox" name="facilities[]" value="wifi" <?php echo htmlspecialchars($hotel_facilities['wifi'], ENT_QUOTES, 'UTF-8') ? 'checked' : ''; ?>> 와이파이
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 호텔 이미지 -->
        <div class="hotel-add-admin-form-group image-upload-section">
            <h3>호텔 이미지</h3>
            <div class="image-upload-grid">
                <div class="main-image-section">
                    <label for="main_image">메인 이미지</label>
                    <?php if ($hotel_main_image): ?>
                        <img src="<?php echo $hotel_main_image; ?>" alt="현재 메인 이미지" style="max-width: 200px;">
                    <?php endif; ?>
                    <input type="file" id="main_image" name="main_image" accept="image/*">
                </div>

                <div class="detail-image-section">
                    <label for="detail_image_1">상세 이미지 1</label>
                    <?php if ($hotel_detail_image_1): ?>
                        <img src="<?php echo $hotel_detail_image_1; ?>" alt="현재 상세 이미지 1" style="max-width: 200px;">
                    <?php endif; ?>
                    <input type="file" id="detail_image_1" name="detail_image_1" accept="image/*">
                </div>

                <div class="detail-image-section">
                    <label for="detail_image_2">상세 이미지 2</label>
                    <?php if ($hotel_detail_image_2): ?>
                        <img src="<?php echo $hotel_detail_image_2; ?>" alt="현재 상세 이미지 2" style="max-width: 200px;">
                    <?php endif; ?>
                    <input type="file" id="detail_image_2" name="detail_image_2" accept="image/*">
                </div>

                <div class="detail-image-section">
                    <label for="detail_image_3">상세 이미지 3</label>
                    <?php if ($hotel_detail_image_3): ?>
                        <img src="<?php echo $hotel_detail_image_3; ?>" alt="현재 상세 이미지 3" style="max-width: 200px;">
                    <?php endif; ?>
                    <input type="file" id="detail_image_3" name="detail_image_3" accept="image/*">
                </div>

                <div class="detail-image-section">
                    <label for="detail_image_4">상세 이미지 4</label>
                    <?php if ($hotel_detail_image_4): ?>
                        <img src="<?php echo $hotel_detail_image_4; ?>" alt="현재 상세 이미지 4" style="max-width: 200px;">
                    <?php endif; ?>
                    <input type="file" id="detail_image_4" name="detail_image_4" accept="image/*">
                </div>
            </div>
        </div>

        <!-- 객실 정보 -->
        <div class="hotel-add-admin-form-group image-upload-section">
            <h3>객실 정보</h3>
            <div class="image-upload-grid">
                <!-- 디럭스 룸 정보 -->
                <div class="detail-image-section">
                    <h4>디럭스 룸</h4>
                    <div class="room-info-grid">
                        <div class="room-info-item">
                            <label for="deluxe_max_person">최대 인원</label>
                            <input type="number" id="deluxe_max_person" name="deluxe_max_person" min="1" max="2" value="<?php echo $deluxe_room['max_person'] ?? '1'; ?>" required>
                        </div>
                        <div class="room-info-item">
                            <label for="deluxe_price">가격</label>
                            <input type="number" id="deluxe_price" name="deluxe_price" min="0" value="<?php echo $deluxe_room['price'] ?? ''; ?>" required>
                        </div>
                        <div class="room-info-item">
                            <label for="deluxe_image">객실 이미지</label>
                            <?php if (isset($deluxe_room['rooms_image'])): ?>
                                <img src="<?php echo $deluxe_room['rooms_image']; ?>" alt="현재 디럭스 룸 이미지" style="max-width: 200px;">
                            <?php endif; ?>
                            <input type="file" id="deluxe_image" name="deluxe_image" accept="image/*">
                        </div>
                    </div>
                </div>

                <!-- 스위트 룸 정보 -->
                <div class="detail-image-section">
                    <h4>스위트 룸</h4>
                    <div class="room-info-grid">
                        <div class="room-info-item">
                            <label for="suite_max_person">최대 인원</label>
                            <input type="number" id="suite_max_person" name="suite_max_person" min="1" max="4" value="<?php echo $suite_room['max_person'] ?? '1'; ?>" required>
                        </div>
                        <div class="room-info-item">
                            <label for="suite_price">가격</label>
                            <input type="number" id="suite_price" name="suite_price" min="0" value="<?php echo $suite_room['price'] ?? ''; ?>" required>
                        </div>
                        <div class="room-info-item">
                            <label for="suite_image">객실 이미지</label>
                            <?php if (isset($suite_room['rooms_image'])): ?>
                                <img src="<?php echo $suite_room['rooms_image']; ?>" alt="현재 스위트 룸 이미지" style="max-width: 200px;">
                            <?php endif; ?>
                            <input type="file" id="suite_image" name="suite_image" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hotel-add-admin-form-actions">
            <a href="admin.php?tab=hotels" class="hotel-add-admin-cancel-btn">취소</a>
            <button type="submit" class="hotel-add-admin-submit-btn">수정 하기</button>
        </div>
    </form>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
