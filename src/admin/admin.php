<?php
include_once __DIR__ . '/../includes/session.php';

// 관리자만 접근 허용
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    http_response_code(404);
    include_once __DIR__ . '/../error/404.php'; // 또는 직접 에러 출력 없이 종료
    exit;
}

include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/info_for_admin.php';
?>

    <main class="admin-container">
        <h2 class="admin-title">관리자 메뉴</h2>
        <div class="admin-menu">
            <ul>
                <li class="active" data-tab="users">
                    <i class="fas fa-users"></i>
                    회원 관리
                </li>
                <li data-tab="hotels">
                    <i class="fas fa-hotel"></i>
                    호텔 관리
                </li>
                <li data-tab="reservations">
                    <i class="fas fa-calendar-check"></i>
                    예약 관리
                </li>
                <li data-tab="reviews">
                    <i class="fas fa-star"></i>
                    후기 관리
                </li>
                <li data-tab="inquiries">
                    <i class="fas fa-question-circle"></i>
                    문의 관리
                </li>
                <li data-tab="notices">
                    <i class="fas fa-bell"></i>
                    공지사항 관리
                </li>
                <li data-tab="coupons">
                    <i class="fas fa-tag"></i>
                    쿠폰 관리
                </li>
            </ul>
        </div>

        <div class="admin-content">
            <!-- 회원 관리 섹션 -->
            <section id="users" class="content-section active">
                <div class="section-header">
                    <h2>회원 관리</h2>
                    <div class="search-form-container">
                        <form method="get" action="../includes/info_for_admin.php">
                            <input type="hidden" name="search" value="user_name_search">
                            <div class="admin-search-box">
                                <input type="text" name="user_name_search" placeholder="회원 이름 검색...">
                                <button><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php if (empty($users)): ?>
                    <div class="admin-no-results">검색 결과가 없습니다.</div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>번호</th>
                                    <th>이름</th>
                                    <th>아이디</th>
                                    <th>비밀번호</th>
                                    <th>이메일</th>
                                    <th>전화번호</th>
                                    <th>가입일</th>
                                    <th>등급</th>
                                    <th>관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= $user['user_id'] ?></td>
                                        <td><?= $user['username'] ?></td>
                                        <td><?= $user['real_id'] ?></td>
                                        <td><?= $user['password'] ?></td>
                                        <td><?= $user['email'] ?></td>
                                        <td><?= $user['phone'] ?></td>
                                        <td><?= $user['created_at'] ?></td>
                                        <td>
                                            <?php if ($user['is_admin'] == 1): ?>
                                                <span class="grade-label admin">
                                                    <i class="fas fa-user-shield"></i> 관리자
                                                </span>
                                            <?php elseif($user['vip'] == 1): ?>
                                                <span class="grade-label vip">
                                                    <i class="fas fa-crown"></i> VIP
                                                </span>
                                            <?php else: ?>
                                                <span class="grade-label">
                                                    일반 회원
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user['is_admin'] != 1): ?>
                                                <form method="get" action="../action/admin_vip_toggle_action.php" style="display:inline;">
                                                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                                    <?php if($user['vip'] == 1): ?>
                                                        <!-- VIP → 일반 회원 -->
                                                        <input type="hidden" name="vip_status" value="0">
                                                        <button type="submit" class="action-btn vip-toggle" title="일반 사용자로 변경">
                                                            <i class="fas fa-user"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <!-- 일반 회원 → VIP -->
                                                        <input type="hidden" name="vip_status" value="1">
                                                        <button type="submit" class="action-btn vip-toggle" title="VIP로 변경">
                                                            <i class="fas fa-crown"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </form>
                                            <?php endif; ?>

                                            <!-- 회원 삭제 -->
                                             <?php if($user['is_admin'] != 1): ?>
                                            <form method="get" action="../action/admin_delete_action.php" style="display:inline;">
                                                <button name="user_delete" class="action-btn delete" value="<?= $user['user_id'] ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    if (isset($_GET['user_name_search'])) {
                        searchPagination($page, $total_pages, 'users', $_GET['user_name_search']);
                    } else {
                        Adminpagination($page, $total_pages, 'users');
                    }
                    ?>
                <?php endif; ?>
            </section>

            <!-- 호텔 관리 섹션 -->
            <section id="hotels" class="content-section">
                <div class="section-header">
                    <h2>호텔 관리</h2>
                    <div class="search-form-container">
                        <div class="section-actions">
                            <a href="hotel-add.php" class="add-btn"><i class="fas fa-plus"></i> 호텔 추가</a>
                        </div>
                        <form method="get" action="../includes/info_for_admin.php">
                            <input type="hidden" name="search" value="hotel_name_search">
                            <div class="admin-search-box">
                                <input type="text" name="hotel_name_search" placeholder="호텔 이름 검색...">
                                <button><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php if (empty($hotels)): ?>
                    <div class="admin-no-results">검색 결과가 없습니다.</div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>호텔명</th>
                                    <th>위치</th>
                                    <th>객실 수</th>
                                    <th>예약 가능 객실 수</th>
                                    <th style="padding-left: 35px;">관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($hotels as $hotel): ?>
                                    <tr>
                                        <td><?= $hotel['hotel_id'] ?></td>
                                        <td><?= $hotel['name'] ?></td>
                                        <td><?= $hotel['location'] ?></td>
                                        <td style="padding-left: 40px;"><?= $hotel['room_count'] ?></td>
                                        <td style="padding-left: 40px;"><?= $hotel['available_room_count'] ?></td>
                                        <td>
                                            <form method="get" action="../admin/hotel-edit.php">
                                                <button name="hotel_edit" class="action-btn edit" value="<?= $hotel['hotel_id'] ?>"><i class="fas fa-edit"></i></button>
                                            </form>
                                            <form method="get" action="../action/admin_delete_action.php">
                                                <button name="hotel_delete" class="action-btn delete" value="<?= $hotel['hotel_id'] ?>"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php 
                    if (isset($_GET['hotel_name_search'])) {
                        searchPagination($page, $total_pages, 'hotels', $_GET['hotel_name_search']);
                    } else {
                        Adminpagination($page, $total_pages, 'hotels');
                    }
                    ?>
                <?php endif; ?>
            </section>

            <!-- 예약 관리 섹션 -->
            <section id="reservations" class="content-section">
                <div class="section-header">
                    <h2>예약 관리</h2>
                    <div class="search-form-container">
                        <form method="get" action="../includes/info_for_admin.php">
                            <input type="hidden" name="search" value="reservation_number_search">
                            <div class="admin-search-box">
                                <input type="text" name="reservation_number_search" placeholder="예약 번호 검색...">
                                <button><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php if (empty($reservations)): ?>
                    <div class="admin-no-results">검색 결과가 없습니다.</div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>예약번호</th>
                                    <th>호텔명</th>
                                    <th>고객명</th>
                                    <th>체크인</th>
                                    <th>체크아웃</th>
                                    <th>상태</th>
                                    <th style="padding-left: 30px;">관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $reservation): ?>
                                    <tr>
                                        <td><?= $reservation['reservation_id'] ?></td>
                                        <td><?= $reservation['name'] ?></td>
                                        <td><?= $reservation['username'] ?></td>
                                        <td><?= $reservation['check_in'] ?></td>
                                        <td><?= $reservation['check_out'] ?></td>
                                        <td>
                                            <span class="status <?php 
                                                if ($reservation['status'] == 'done'): 
                                                    echo 'status-complete';
                                                elseif ($reservation['status'] == 'cancel'): 
                                                    echo 'status-pending';
                                                endif; 
                                            ?>">
                                            <?php 
                                                if ($reservation['status'] == 'done'): 
                                                    echo '예약확정';
                                                elseif ($reservation['status'] == 'cancel'): 
                                                    echo '취소완료';
                                                endif; 
                                            ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="get" action="../action/admin_delete_action.php">
                                                <input type="hidden" name="room_id" value="<?= $reservation['room_id'] ?>">
                                                <button name="reservation_delete" class="action-btn delete" value="<?= $reservation['reservation_id'] ?>"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php 
                    if (isset($_GET['reservation_number_search'])) {
                        searchPagination($page, $total_pages, 'reservations', $_GET['reservation_number_search']);
                    } else {
                        Adminpagination($page, $total_pages, 'reservations');
                    }
                    ?>
                <?php endif; ?>
            </section>

            <!-- 후기 관리 섹션 -->
            <section id="reviews" class="content-section">
                <div class="section-header">
                    <h2>후기 관리</h2>
                    <div class="search-form-container">
                        <form method="get" action="../includes/info_for_admin.php">
                            <div class="admin-search-box">
                                <input type="hidden" name="search" value="review_hotel_search">
                                <input type="text" id="review_hotel_search" name="review_hotel_search" placeholder="호텔 이름 검색...">
                                <button><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php if (empty($reviews)): ?>
                    <div class="admin-no-results">검색 결과가 없습니다.</div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="admin-table">
                            <thead>
                                <tr style="text-align: center;">
                                    <th style="width: 10%">번호</th>
                                    <th style="width: 20%">호텔</th>
                                    <th style="width: 15%">작성자</th>
                                    <th style="width: 10%">평점</th>
                                    <th style="width: 25%">내용</th>
                                    <th style="width: 15%">작성일</th>
                                    <th style="padding-left: 30px;">관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review): ?>
                                    <tr>
                                        <td><?= $review['review_id'] ?></td>
                                        <td><?= $review['hotel_name'] ?></td>
                                        <td><?= $review['username'] ?></td>
                                        <td><?= $review['rating'] ?></td>
                                        <td><?= mb_substr($review['content'], 0, 30) . (mb_strlen($review['content']) > 30 ? '...' : '') ?></td>
                                        <td><?= $review['created_at'] ?></td>
                                        <td>
                                            <form method="get" action="../action/admin_delete_action.php">
                                                <a href="../hotel/hotel-detail.php?id=<?= $review['hotel_id'] ?>" class="action-btn view"><i class="fas fa-eye"></i></a>
                                                <button name="review_delete" class="action-btn delete" value="<?= $review['review_id'] ?>"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php 
                    if (isset($_GET['review_number_search'])) {
                        searchPagination($page, $total_pages, 'reviews', $_GET['review_number_search']);
                    } else {
                        Adminpagination($page, $total_pages, 'reviews');
                    }
                    ?>
                <?php endif; ?>
            </section>

            <!-- 문의 관리 섹션 -->
            <section id="inquiries" class="content-section">
                <div class="section-header">
                    <h2>문의 관리</h2>
                    <div class="search-form-container">
                        <form method="get" action="../includes/info_for_admin.php">
                            <input type="hidden" name="search" value="inquiry_number_search">
                            <div class="admin-search-box">
                                <input type="text" name="inquiry_number_search" placeholder="문의 번호 검색...">
                                <button><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php if (empty($inquiries)): ?>
                    <div class="admin-no-results">검색 결과가 없습니다.</div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="admin-table">
                            <thead>
                                <tr style="text-align: center;">
                                    <th style="width: 10%">번호</th>
                                    <th style="width: 10%">분류</th>
                                    <th style="width: 30%">제목</th>
                                    <th style="width: 15%">작성자</th>
                                    <th style="width: 20%">작성일</th>
                                    <th style="width: 10%">답변상태</th>
                                    <th style="width: 20%">관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inquiries as $inquiry): ?>
                                    <tr>
                                        <td><?= $inquiry['inquiry_id'] ?></td>
                                        <td>
                                            <?php
                                            switch($inquiry['category']) {
                                                case 'reservation':
                                                    echo '예약';
                                                    break;
                                                case 'payment':
                                                    echo '결제';
                                                    break;
                                                case 'room':
                                                    echo '객실';
                                                    break;
                                                case 'other':
                                                    echo '기타';
                                                    break;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($inquiry['is_secret']): ?>
                                                <span class="lock-icon">🔒</span>
                                            <?php endif; ?>
                                            <?= $inquiry['title'] ?>
                                        </td>
                                        <td><?= $inquiry['username'] ?></td>
                                        <td><?= $inquiry['created_at'] ?></td>
                                        <td>
                                            <?php if ($inquiry['is_answered']): ?>
                                                <span class="status-complete">답변완료</span>
                                            <?php else: ?>
                                                <span class="status-pending">미답변</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="get" action="../action/admin_delete_action.php">
                                                <a href="../inquiry/inquiry_detail.php?inquiry_id=<?= $inquiry['inquiry_id'] ?>" class="action-btn view"><i class="fas fa-eye"></i></a>
                                                <button name="inquiry_delete" class="action-btn delete" value="<?= $inquiry['inquiry_id'] ?>"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php 
                    if (isset($_GET['inquiry_number_search'])) {
                        searchPagination($page, $total_pages, 'inquiries', $_GET['inquiry_number_search']);
                    } else {
                        Adminpagination($page, $total_pages, 'inquiries');
                    }
                    ?>
                <?php endif; ?>
            </section>

            <!-- 공지사항 관리 섹션 -->
            <section id="notices" class="content-section">
                <div class="section-header">
                    <h2>공지사항 관리</h2>
                    <div class="search-form-container">
                        <div class="section-actions">
                            <a href="notice-write.php" class="add-btn"><i class="fas fa-plus"></i> 공지사항 작성</a>
                        </div>
                        <form method="get" action="../includes/info_for_admin.php">
                            <input type="hidden" name="search" value="notice_title_search">
                            <div class="admin-search-box">
                                <input type="text" name="notice_title_search" placeholder="공지사항 제목 검색...">
                                <button><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php if (empty($notices)): ?>
                    <div class="admin-no-results">검색 결과가 없습니다.</div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>번호</th>
                                    <th>제목</th>
                                    <th>작성일</th>
                                    <th>공개여부</th>
                                    <th>관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($notices as $notice): ?>
                                    <tr>
                                        <td><?= $notice['notice_id'] ?></td>
                                        <td><?= $notice['title'] ?></td>
                                        <td><?= $notice['created_at'] ?></td>
                                        <td>
                                            <?php if ($notice['is_released']): ?>
                                                <span class="status-complete">공개</span>
                                            <?php else: ?>
                                                <span class="status-pending">비공개</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="get" action="../admin/notice-edit.php">
                                                <button name="notice_edit" class="action-btn edit" value="<?= $notice['notice_id'] ?>"><i class="fas fa-edit"></i></button>
                                            </form>
                                            <form method="get" action="../action/admin_delete_action.php">
                                                <button name="notice_delete" class="action-btn delete" value="<?= $notice['notice_id'] ?>"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php 
                    if (isset($_GET['notice_title_search'])) {
                        searchPagination($page, $total_pages, 'notices', $_GET['notice_title_search']);
                    } else {
                        Adminpagination($page, $total_pages, 'notices');
                    }
                    ?>
                <?php endif; ?>
            </section>

            <!-- 쿠폰 관리 섹션 -->
            <section id="coupons" class="content-section">
                <div class="section-header">
                    <h2>쿠폰 관리</h2>
                    <div class="search-form-container">
                        <div class="section-actions">
                            <a href="coupon-add.php" class="add-btn"><i class="fas fa-plus"></i> 쿠폰 추가</a>
                        </div>
                        <form method="get" action="../includes/info_for_admin.php">
                            <input type="hidden" name="search" value="coupon_code_search">
                            <div class="admin-search-box">
                                <input type="text" name="coupon_code_search" placeholder="쿠폰 코드 검색...">
                                <button><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php if (empty($coupons)): ?>
                    <div class="admin-no-results">검색 결과가 없습니다.</div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="admin-table">
                            <thead>
                                <tr style="text-align: center;">
                                    <th style="width: 10%">쿠폰 코드</th>
                                    <th style="width: 14%">쿠폰명</th>
                                    <th style="width: 10%">할인 유형</th>
                                    <th style="width: 8%">할인 값</th>
                                    <th style="width: 10%">시작일</th>
                                    <th style="width: 10%">종료일</th>
                                    <th style="width: 10%">최소 구매액</th>
                                    <th style="width: 10%">최대 할인액</th>
                                    <th style="width: 8%">사용 제한</th>
                                    <th style="width: 6%">상태</th>
                                    <th style="width: 5%">관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($coupons as $coupon): ?>
                                    <tr>
                                        <td style="text-align: center;"><?= $coupon['code'] ?></td>
                                        <td style="text-align: center;"><?= $coupon['name'] ?></td>
                                        <td style="text-align: center;">
                                            <?php
                                            if ($coupon['discount_type'] == 'percentage') {
                                                echo '퍼센트';
                                            } else {
                                                echo '정액';
                                            }
                                            ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php
                                            if ($coupon['discount_type'] == 'percentage') {
                                                echo $coupon['discount_value'] . '%';
                                            } else {
                                                echo number_format($coupon['discount_value']) . '원';
                                            }
                                            ?>
                                        </td>
                                        <td style="text-align: center;"><?= $coupon['start_date'] ?></td>
                                        <td style="text-align: center;"><?= $coupon['end_date'] ?></td>
                                        <td style="text-align: center;"><?= number_format($coupon['minimum_purchase']) ?>원</td>
                                        <td style="text-align: center;">
                                            <?php
                                            if ($coupon['maximum_discount'] === null) {
                                                echo '-';
                                            } else {
                                                echo number_format($coupon['maximum_discount']) . '원';
                                            }
                                            ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php
                                            if ($coupon['usage_limit'] === null) {
                                                echo '무제한';
                                            } else {
                                                echo $coupon['usage_limit'] . '회';
                                            }
                                            ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php if ($coupon['is_active']): ?>
                                                <span class="status-complete">활성</span>
                                            <?php else: ?>
                                                <span class="status-pending">비활성</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <form method="get" action="../admin/coupon-edit.php">
                                                <button name="coupon_edit" class="action-btn edit" value="<?= $coupon['code'] ?>"><i class="fas fa-edit"></i></button>
                                            </form>
                                            <form method="get" action="../action/admin_delete_action.php">
                                                <button name="coupon_delete" class="action-btn delete" value="<?= $coupon['code'] ?>"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php 
                    if (isset($_GET['coupon_code_search'])) {
                        searchPagination($page, $total_pages, 'coupons', $_GET['coupon_code_search']);
                    } else {
                        Adminpagination($page, $total_pages, 'coupons');
                    }
                    ?>
                <?php endif; ?>
            </section>
        </div>
    </main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>

    <script>
        // URL 파라미터에서 tab 값 가져오기
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'users';

        // 해당 탭 활성화
        document.querySelectorAll('.admin-menu li').forEach(menuItem => {
            if (menuItem.getAttribute('data-tab') === activeTab) {
                menuItem.classList.add('active');
                document.getElementById(activeTab).classList.add('active');
            } else {
                menuItem.classList.remove('active');
                document.getElementById(menuItem.getAttribute('data-tab')).classList.remove('active');
            }
        });

        // 탭 전환 기능
        document.querySelectorAll('.admin-menu li').forEach(menuItem => {
            menuItem.addEventListener('click', () => {
                const tabId = menuItem.getAttribute('data-tab');
                window.location.href = `admin.php?tab=${tabId}`;
            });
        });
    </script>
</body>
</html> 