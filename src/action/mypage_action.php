<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';

// VIP 점수 계산 함수
function calculate_vip_score($user_id, $conn) {
    $vip_check_sql = "
        SELECT COUNT(*) as done_count, 
               SUM(CASE WHEN status = 'cancel' THEN 1 ELSE 0 END) as cancel_count
        FROM reservations 
        WHERE user_id = '$user_id'";
    $vip_check_result = mysqli_query($conn, $vip_check_sql);
    $vip_counts = mysqli_fetch_assoc($vip_check_result);
    
    return $vip_counts['done_count'] - $vip_counts['cancel_count'];
}

// VIP 상태 업데이트 함수
function update_vip_status($user_id, $vip_score, $conn) {
    // 현재 VIP 상태 확인
    $check_sql = "SELECT vip_status FROM users WHERE user_id = '$user_id'";
    $check_result = mysqli_query($conn, $check_sql);
    $user = mysqli_fetch_assoc($check_result);
    
    // 관리자가 수동으로 지정한 VIP는 계산에서 제외
    if ($user['vip_status'] === 'manual') {
        return true;
    }
    
    if ($vip_score >= 5) {
        $update_vip_sql = "UPDATE users SET vip = TRUE, vip_status = 'auto' WHERE user_id = '$user_id'";
    } else {
        $update_vip_sql = "UPDATE users SET vip = FALSE, vip_status = 'auto' WHERE user_id = '$user_id'";
    }
    return mysqli_query($conn, $update_vip_sql);
}

$user_id = $_SESSION['user_id'];

// VIP 점수 계산 및 전역 변수로 저장
$GLOBALS['vip_score'] = calculate_vip_score($user_id, $conn);

$user_sql="select * from users where user_id='$user_id'";
$user_result = mysqli_query($conn, $user_sql);
$users = mysqli_fetch_assoc($user_result);

$GLOBALS['users'] = $users;

// 페이지네이션 설정
$reservation_items_per_page = 3;  // 예약은 페이지당 3개
$wishlist_items_per_page = 5;     // 찜 목록은 페이지당 5개
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);

// 찜 목록 조회
$wishlist_count_query = "
    SELECT COUNT(*) as total 
    FROM wishlist w
    JOIN hotels h ON w.hotel_id = h.hotel_id
    WHERE w.user_id = '$user_id'";
$wishlist_count_result = mysqli_query($conn, $wishlist_count_query);
$total_wishlist_items = mysqli_fetch_assoc($wishlist_count_result)['total'];
$total_wishlist_pages = ceil($total_wishlist_items / $wishlist_items_per_page);

$wishlist_offset = ($page - 1) * $wishlist_items_per_page;
$wishlist_query = "
    SELECT w.hotel_id, h.name
    FROM wishlist w
    JOIN hotels h ON w.hotel_id = h.hotel_id
    WHERE w.user_id = '$user_id'
    ORDER BY w.created_at DESC
    LIMIT $wishlist_offset, $wishlist_items_per_page";
$wishlist_result = mysqli_query($conn, $wishlist_query);
$wishlist_items = [];
while ($row = mysqli_fetch_assoc($wishlist_result)) {
    $wishlist_items[] = $row;
}

// 예약 내역 조회
$reservation_count_query = "
    SELECT COUNT(*) as total 
    FROM reservations r
    JOIN rooms rm ON r.room_id = rm.room_id
    JOIN hotels h ON rm.hotel_id = h.hotel_id
    WHERE r.user_id = '$user_id'";
$reservation_count_result = mysqli_query($conn, $reservation_count_query);
$total_reservation_items = mysqli_fetch_assoc($reservation_count_result)['total'];
$total_reservation_pages = ceil($total_reservation_items / $reservation_items_per_page);

$reservation_offset = ($page - 1) * $reservation_items_per_page;
$reservation_query = "
    SELECT r.*, h.name AS hotel_name, rm.room_type, rm.hotel_id
    FROM reservations r
    JOIN rooms rm ON r.room_id = rm.room_id
    JOIN hotels h ON rm.hotel_id = h.hotel_id
    WHERE r.user_id = '$user_id'
    ORDER BY r.created_at DESC
    LIMIT $reservation_offset, $reservation_items_per_page";

$reservation_result = mysqli_query($conn, $reservation_query);
$reservations = [];
while ($row = mysqli_fetch_assoc($reservation_result)) {
    $reservations[] = $row;
}

$GLOBALS['reservations'] = $reservations;
$GLOBALS['wishlist_items'] = $wishlist_items;
$GLOBALS['page'] = $page;
$GLOBALS['total_wishlist_pages'] = $total_wishlist_pages;
$GLOBALS['total_reservation_pages'] = $total_reservation_pages;
$GLOBALS['reservation_items_per_page'] = $reservation_items_per_page;
$GLOBALS['wishlist_items_per_page'] = $wishlist_items_per_page;
?>