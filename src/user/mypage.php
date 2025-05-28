<?php 
if (isset($_GET['file'])) {
    include($_GET['file']);
    exit;
}

include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../action/mypage_action.php';
$users = $GLOBALS['users'];
?>

    <main class="mypage-container">
        <div class="mypage-sidebar">
            <div class="profile-section">
                <form method="post" action="../action/upload_profile_image.php" enctype="multipart/form-data">
                    <?php
                        $has_image = !empty($users['profile_image']) ? 'has-image' : '';
                        $profile_img = !empty($users['profile_image']) 
                        ? $users['profile_image']
                        : '/image/default_profile.jpg';
                    ?>
                    <div class="profile-image-container <?= $has_image ?>">
                        <img src="<?= $profile_img ?>" alt="프로필 사진" id="profileImage" class="profile-image">
                        <label for="profileUpload" class="change-profile-btn">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="profileUpload" name="profile_image" accept="image/*" style="display: none;" onchange="this.form.submit()">
                    </div>
                </form>
                <h2 id="username"><?=htmlspecialchars($users['username'], ENT_QUOTES, 'UTF-8')?></h2>
                <p id="email"><?=htmlspecialchars($users['email'], ENT_QUOTES, 'UTF-8')?></p>
                <div class="member-grade">
                    <?php if($users['vip']): ?>
                        <span class="vip-badge">VIP 회원</span>
                    <?php else: ?>
                        <span class="normal-badge">일반 회원</span>
                    <?php endif; ?>
                </div>
            </div>
            <nav class="mypage-nav">
                <ul>
                    <li><a href="#" data-tab="reservations" class="active">예약 관리</a></li>
                    <li><a href="#" data-tab="profile">여행객 정보</a></li>
                    <li><a href="#" data-tab="wishlist">찜 목록</a></li>
                    <li><a href="#" data-tab="point">포인트</a></li>
                </ul>
            </nav>
        </div>

        <div class="mypage-content">
            <!-- 예약 관리 섹션 -->
            <section id="reservations" class="content-section active">
                <h2>예약 관리</h2>
                <?php if (empty($reservations)): ?>
                    <div class="mypage-no-results">예약 내역이 없습니다.</div>
                <?php else: ?>
                    <div class="reservation-list">
                        <?php foreach ($reservations as $reservation): ?>
                        <div class="reservation-card">
                            <div class="reservation-header">
                                <h3><?=htmlspecialchars($reservation['hotel_name'], ENT_QUOTES, 'UTF-8')?></h3>
                                <?php if($reservation['status'] == 'done'): ?>
                                    <span class="status-complete">예약확정</span>
                                <?php elseif($reservation['status'] == 'cancel'): ?>
                                    <span class="status-pending">예약취소</span>
                                <?php endif; ?>
                            </div>
                            <div class="reservation-details">
                                <div class="mypage-detail-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>체크인:  <?=htmlspecialchars($reservation['check_in'], ENT_QUOTES, 'UTF-8')?></span>
                                </div>
                                <div class="mypage-detail-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>체크아웃:  <?=htmlspecialchars($reservation['check_out'], ENT_QUOTES, 'UTF-8')?></span>
                                </div>
                                <div class="mypage-detail-item">
                                    <i class="fas fa-bed"></i>
                                    <?php if($reservation['room_type'] == 'deluxe'): ?>
                                        <span>디럭스 룸</span>
                                    <?php elseif($reservation['room_type'] == 'suite'): ?>
                                        <span>스위트 룸</span>
                                    <?php endif; ?>
                                </div>
                                <div class="mypage-detail-item">
                                    <i class="fas fa-user"></i>
                                    <span><?=$reservation['guests']?>명</span>
                                </div>
                            </div>
                            <?php if($reservation['status'] == 'done'): ?>
                            <form method="get" action="../action/reservation_cancel_action.php">
                                <input type="hidden" name="reservation_id" value="<?=htmlspecialchars($reservation['reservation_id'], ENT_QUOTES, 'UTF-8')?>">
                                <input type="hidden" name="room_id" value="<?=htmlspecialchars($reservation['room_id'], ENT_QUOTES, 'UTF-8')?>">
                                <div class="reservation-actions">
                                    <button class="mypage-cancel-btn">예약 취소</button>
                                </div>
                            </form>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php pagination($total_reservation_items, $reservation_items_per_page); ?>
                <?php endif; ?>
            </section>

            <!-- 여행객 정보 섹션 -->
            <section id="profile" class="content-section">
                <h2>여행객 정보</h2>
                <div class="profile-form-container">
                    <form class="profile-form" method="get" action="../action/mypage_change_action.php">
                        <div class="mypage-form-group">
                            <label for="username">이름</label>
                            <input type="text" id="username" name="username" value="<?= $users['username']?>" readonly class="readonly-input">
                        </div>
                        <div class="mypage-form-group">
                            <label for="email">이메일</label>
                            <input type="email" id="email" value="<?=htmlspecialchars($users['email'], ENT_QUOTES, 'UTF-8')?>" readonly class="readonly-input">
                        </div>
                        <div class="mypage-form-group">
                            <label for="phone">전화번호</label>
                            <input type="tel" id="phone" value="<?=htmlspecialchars($users['phone'], ENT_QUOTES, 'UTF-8')?>" readonly class="readonly-input">
                        </div>
                        <div class="mypage-form-group">
                            <label for="password">기존 비밀번호</label>
                            <input type="password" id="password" name="password" placeholder="기존 비밀번호">
                        </div>
                        <div class="mypage-form-group">
                            <label for="new_password">비밀번호 변경</label>
                            <input type="password" id="new_password" name="new_password"  placeholder="새 비밀번호">
                        </div>
                        <div class="mypage-form-group">
                            <label for="new_password_check">비밀번호 확인</label>
                            <input type="password" id="new_password_check" name="new_password_check" placeholder="새 비밀번호 확인">
                        </div>
                        <button type="submit" class="save-btn">저장</button>
                    </form>
                </div>
            </section>

            <!-- 찜 목록 섹션 -->
            <section id="wishlist" class="content-section">
                <h2>찜 목록</h2>
                <?php if (empty($wishlist_items)): ?>
                    <div class="mypage-no-results">찜한 호텔이 없습니다.</div>
                <?php else: ?>
                    <table class="wishlist-table">
                        <thead>
                            <tr>
                                <th width="90%">호텔 이름</th>
                                <th width="10%" style="padding-left: 25px;">관리</th>
                            </tr>
                        </thead>
                            <tbody>
                            <?php foreach ($wishlist_items as $hotel): ?>
                                <tr>
                                    <td>
                                        <div class="hotel-row">
                                            <h3 class="hotel-name"><?=$hotel['name'] ?></h3>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="button-group">
                                            <a href="../hotel/hotel-detail.php?id=<?=htmlspecialchars($hotel['hotel_id'], ENT_QUOTES, 'UTF-8')?>" class="detail-btn">상세보기</a>
                                            <form method="get" action="../action/wishlist_delete.php" style="display:inline;">
                                                <input type="hidden" name="hotel_id" value="<?= $hotel['hotel_id'] ?>">
                                                <button type="submit" class="delete-btn"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                    </table>
                    
                    <?php pagination($total_wishlist_items, $wishlist_items_per_page); ?>
                <?php endif; ?>
            </section>
            <section id="point" class="content-section">
                <h2>포인트 관리</h2>
                <div class="profile-form-container">
                    <p><strong>현재 보유 포인트 :</strong> <?=htmlspecialchars(number_format($users['point']), ENT_QUOTES, 'UTF-8')?> P</p>
                    <form action="../action/charge_point_action.php" method="get">
                        <div class="mypage-form-group">
                            <label for="charge_amount">충전할 포인트</label>
                            <input type="number" name="point" id="point" required>
                        </div>
                        <button type="submit" class="save-btn">충전</button>
                    </form>
                </div>
            </section>

        </div>
    </main>

    <script src="js/mypage.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.mypage-nav a');
            const contentSections = document.querySelectorAll('.content-section');

            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    navLinks.forEach(l => l.classList.remove('active'));
                    contentSections.forEach(section => section.classList.remove('active'));
                    
                    this.classList.add('active');
                    
                    const targetTab = this.getAttribute('data-tab');
                    document.getElementById(targetTab).classList.add('active');
                });
            });
        });
    </script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?> 