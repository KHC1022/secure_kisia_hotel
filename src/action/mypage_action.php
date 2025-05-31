<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/login_check.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: /user/login.php");
    exit;
}

// VIP 점수 계산 함수
function calculate_vip_score($user_id, $conn) {
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) AS done_count,
            SUM(CASE WHEN status = 'cancel' THEN 1 ELSE 0 END) AS cancel_count
        FROM reservations 
        WHERE user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $counts = $result->fetch_assoc();
    $stmt->close();

    return (int)$counts['done_count'] - (int)$counts['cancel_count'];
}

//  VIP 상태 갱신 함수
function update_vip_status($user_id, $vip_score, $conn) {
    $stmt = $conn->prepare("SELECT vip_status FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user['vip_status'] === 'manual') {
        return true; // 수동 VIP는 변경하지 않음
    }

    $is_vip = $vip_score >= 5 ? 1 : 0;
    $vip_status = 'auto';

    $stmt = $conn->prepare("UPDATE users SET vip = ?, vip_status = ? WHERE user_id = ?");
    $stmt->bind_param("isi", $is_vip, $vip_status, $user_id);
    return $stmt->execute();
}

// VIP 상태 갱신
$vip_score = calculate_vip_score($user_id, $conn);
update_vip_status($user_id, $vip_score, $conn);

// 유저 정보
$user_stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$users = $user_result->fetch_assoc();
$user_stmt->close();

// 페이징 설정
$reservation_items_per_page = 3;
$wishlist_items_per_page = 5;
$page = max(1, (int)($_GET['page'] ?? 1));

// 찜 목록 개수
$wishlist_count_stmt = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM wishlist w
    JOIN hotels h ON w.hotel_id = h.hotel_id
    WHERE w.user_id = ?
");
$wishlist_count_stmt->bind_param("i", $user_id);
$wishlist_count_stmt->execute();
$total_wishlist_items = $wishlist_count_stmt->get_result()->fetch_assoc()['total'];
$wishlist_count_stmt->close();

$total_wishlist_pages = ceil($total_wishlist_items / $wishlist_items_per_page);
$wishlist_offset = ($page - 1) * $wishlist_items_per_page;

// 찜 목록 조회
$wishlist_stmt = $conn->prepare("
    SELECT w.hotel_id, h.name
    FROM wishlist w
    JOIN hotels h ON w.hotel_id = h.hotel_id
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
    LIMIT ?, ?
");
$wishlist_stmt->bind_param("iii", $user_id, $wishlist_offset, $wishlist_items_per_page);
$wishlist_stmt->execute();
$wishlist_items = $wishlist_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$wishlist_stmt->close();

// 예약 내역 개수
$reservation_count_stmt = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM reservations r
    JOIN rooms rm ON r.room_id = rm.room_id
    JOIN hotels h ON rm.hotel_id = h.hotel_id
    WHERE r.user_id = ?
");
$reservation_count_stmt->bind_param("i", $user_id);
$reservation_count_stmt->execute();
$total_reservation_items = $reservation_count_stmt->get_result()->fetch_assoc()['total'];
$reservation_count_stmt->close();

$total_reservation_pages = ceil($total_reservation_items / $reservation_items_per_page);
$reservation_offset = ($page - 1) * $reservation_items_per_page;

// 예약 내역 조회
$reservation_stmt = $conn->prepare("
    SELECT r.*, h.name AS hotel_name, rm.room_type, rm.hotel_id
    FROM reservations r
    JOIN rooms rm ON r.room_id = rm.room_id
    JOIN hotels h ON rm.hotel_id = h.hotel_id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
    LIMIT ?, ?
");
$reservation_stmt->bind_param("iii", $user_id, $reservation_offset, $reservation_items_per_page);
$reservation_stmt->execute();
$reservations = $reservation_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$reservation_stmt->close();

// 전역 변수 저장
$GLOBALS['vip_score'] = $vip_score;
$GLOBALS['users'] = $users;
$GLOBALS['page'] = $page;
$GLOBALS['wishlist_items'] = $wishlist_items;
$GLOBALS['reservations'] = $reservations;
$GLOBALS['total_wishlist_pages'] = $total_wishlist_pages;
$GLOBALS['total_reservation_pages'] = $total_reservation_pages;
$GLOBALS['reservation_items_per_page'] = $reservation_items_per_page;
$GLOBALS['wishlist_items_per_page'] = $wishlist_items_per_page;
?>
