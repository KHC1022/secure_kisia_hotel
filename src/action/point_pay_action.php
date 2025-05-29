<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/mypage_action.php';
include_once __DIR__ . '/../action/login_check.php';

$user_id = $_SESSION['user_id'] ?? null;
$room_id = (int)($_POST['room_id'] ?? 0);
$checkin = $_POST['checkin'] ?? '';
$checkout = $_POST['checkout'] ?? '';
$guests = (int)($_POST['guests'] ?? 1);
$selected_coupon = isset($_POST['selected_coupon']) ? (int)$_POST['selected_coupon'] : null;
$event_busan = (int)($_POST['event_busan'] ?? 0);
$event_japan = (int)($_POST['event_japan'] ?? 0);

// 날짜 유효성 확인
if (!$checkin || !$checkout || $room_id <= 0) {
    echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
    exit;
}

$start = new DateTime($checkin);
$end = new DateTime($checkout);
$days = $start->diff($end)->days;
if ($days <= 0) $days = 1;

// 객실 요금 확인 및 계산
$price_stmt = $conn->prepare("SELECT price FROM rooms WHERE room_id = ?");
$price_stmt->bind_param("i", $room_id);
$price_stmt->execute();
$price_result = $price_stmt->get_result();
if ($price_result->num_rows === 0) {
    echo "<script>alert('존재하지 않는 객실입니다.'); history.back();</script>";
    exit;
}
$room_price = $price_result->fetch_assoc()['price'];

// 할인 적용
if ($event_busan === 1) {
    $room_price *= 0.6;
} elseif ($event_japan === 1) {
    $room_price *= 0.8;
}

$room_fee = $room_price * $days;
$tax = round($room_fee * 0.1);
$total_price = $room_fee + $tax;

// 포인트 확인
$user_stmt = $conn->prepare("SELECT point FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user || $user['point'] < $total_price) {
    echo "<script>alert('포인트가 부족합니다.'); location.href='../user/mypage.php';</script>";
    exit;
}

// 쿠폰 검증
if ($selected_coupon) {
    $coupon_stmt = $conn->prepare("SELECT * FROM user_coupons WHERE user_id = ? AND coupon_id = ? AND is_used = 0");
    $coupon_stmt->bind_param("ii", $user_id, $selected_coupon);
    $coupon_stmt->execute();
    $coupon_result = $coupon_stmt->get_result();
    if ($coupon_result->num_rows === 0) {
        echo "<script>alert('유효하지 않은 쿠폰입니다.'); history.back();</script>";
        exit;
    }
}

// 포인트 차감
$new_point = $user['point'] - $total_price;
$update_stmt = $conn->prepare("UPDATE users SET point = ? WHERE user_id = ?");
$update_stmt->bind_param("di", $new_point, $user_id);
$update_stmt->execute();

// 객실 상태 변경
$status_stmt = $conn->prepare("UPDATE rooms SET status = 'reserved' WHERE room_id = ?");
$status_stmt->bind_param("i", $room_id);
$status_stmt->execute();

// 예약 등록
$reserve_stmt = $conn->prepare("INSERT INTO reservations (user_id, room_id, check_in, check_out, total_price, guests, created_at, status, coupon_id)
VALUES (?, ?, ?, ?, ?, ?, NOW(), 'done', ?)");
$reserve_stmt->bind_param("iissdii", $user_id, $room_id, $checkin, $checkout, $total_price, $guests, $selected_coupon);
$reserve_stmt->execute();

// 쿠폰 사용 처리
if ($selected_coupon) {
    $use_coupon_stmt = $conn->prepare("UPDATE user_coupons SET is_used = 1 WHERE user_id = ? AND coupon_id = ?");
    $use_coupon_stmt->bind_param("ii", $user_id, $selected_coupon);
    $use_coupon_stmt->execute();
}

// VIP 상태 업데이트
$vip_score = calculate_vip_score($user_id, $conn);
update_vip_status($user_id, $vip_score, $conn);

echo "<script>alert('결제가 완료되었습니다.'); location.href='../user/mypage.php';</script>";
exit;

?>
