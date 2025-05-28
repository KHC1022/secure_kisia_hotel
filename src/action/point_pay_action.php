<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/mypage_action.php';  // VIP 관련 함수

// 세션 확인
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='../user/login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$charge_amount = isset($_POST['charge_amount']) ? floatval($_POST['charge_amount']) : 0;
$room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : null;
$checkin = mysqli_real_escape_string($conn, $_POST['checkin'] ?? '');
$checkout = mysqli_real_escape_string($conn, $_POST['checkout'] ?? '');
$total_price = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;
$guests = isset($_POST['guests']) ? intval($_POST['guests']) : 1;
$selected_coupon = isset($_POST['selected_coupon']) ? intval($_POST['selected_coupon']) : null;

// 유저 포인트 확인
$pay_sql = "SELECT point FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $pay_sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user && $user['point'] >= $charge_amount) {

    // 쿠폰 사용 확인
    if ($selected_coupon) {
        $check_coupon_sql = "SELECT * FROM user_coupons WHERE user_id = ? AND coupon_id = ? AND is_used = 0";
        $stmt_coupon = mysqli_prepare($conn, $check_coupon_sql);
        mysqli_stmt_bind_param($stmt_coupon, 'ii', $user_id, $selected_coupon);
        mysqli_stmt_execute($stmt_coupon);
        $coupon_result = mysqli_stmt_get_result($stmt_coupon);

        if (mysqli_num_rows($coupon_result) === 0) {
            echo "<script>alert('이미 사용했거나 잘못된 쿠폰입니다.'); history.back();</script>";
            exit;
        }
    }

    // 포인트 차감
    $new_point = $user['point'] - $charge_amount;
    $update_sql = "UPDATE users SET point = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, 'di', $new_point, $user_id);
    mysqli_stmt_execute($stmt);

    // 객실 상태 변경
    $room_update_sql = "UPDATE rooms SET status = 'reserved' WHERE room_id = ?";
    $stmt = mysqli_prepare($conn, $room_update_sql);
    mysqli_stmt_bind_param($stmt, 'i', $room_id);
    mysqli_stmt_execute($stmt);

    // 예약 등록
    $reservation_sql = "INSERT INTO reservations 
        (user_id, room_id, check_in, check_out, total_price, guests, created_at, status, coupon_id)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), 'done', ?)";

    $stmt = mysqli_prepare($conn, $reservation_sql);
    if ($selected_coupon) {
        mysqli_stmt_bind_param($stmt, 'iissdii', $user_id, $room_id, $checkin, $checkout, $total_price, $guests, $selected_coupon);
    } else {
        $null = null;
        mysqli_stmt_bind_param($stmt, 'iissdii', $user_id, $room_id, $checkin, $checkout, $total_price, $guests, $null);
    }

    if (!mysqli_stmt_execute($stmt)) {
        echo "<script>alert('예약 등록 중 오류가 발생했습니다.'); history.back();</script>";
        exit;
    }

    // 쿠폰 사용 처리
    if ($selected_coupon) {
        $update_coupon_sql = "UPDATE user_coupons SET is_used = 1 WHERE coupon_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $update_coupon_sql);
        mysqli_stmt_bind_param($stmt, 'ii', $selected_coupon, $user_id);
        mysqli_stmt_execute($stmt);
    }

    // VIP 상태 업데이트
    $vip_score = calculate_vip_score($user_id, $conn);
    update_vip_status($user_id, $vip_score, $conn);

    echo "<script>alert('결제가 완료되었습니다.'); location.href='../user/mypage.php';</script>";
    exit;
} else {
    echo "<script>alert('포인트가 부족합니다.'); history.back();</script>";
    exit;
}
?>
