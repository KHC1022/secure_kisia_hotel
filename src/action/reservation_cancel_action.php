<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/mypage_action.php';  // VIP 관련 함수 포함

$reservation_id = $_GET['reservation_id'];
$room_id = $_GET['room_id'];
$user_id = $_SESSION['user_id'];

// 객실 상태 available로 변경
$sql2 = "UPDATE rooms SET status = 'available' WHERE room_id = $room_id";
$result2 = mysqli_query($conn, $sql2);

// 예약 정보 + 쿠폰 정보 조회
$reservation_sql = "SELECT total_price, coupon_id FROM reservations WHERE reservation_id = $reservation_id";
$reservation_result = mysqli_query($conn, $reservation_sql);
$reservation = mysqli_fetch_assoc($reservation_result);

$total_price = $reservation['total_price'];
$coupon_id = $reservation['coupon_id'];

// 환불 금액 계산 : 쿠폰 적용 금액으로만 환불
$refund_amount = $total_price;

// 쿠폰 사용한 경우 쿠폰 정보로 할인 계산
if ($coupon_id !== null) {
    $coupon_sql = "SELECT discount_type, discount_value FROM coupons WHERE coupon_id = $coupon_id";
    $coupon_result = mysqli_query($conn, $coupon_sql);
    $coupon = mysqli_fetch_assoc($coupon_result);

    if ($coupon) {
        $type = $coupon['discount_type'];
        $value = $coupon['discount_value'];

        if ($type === 'percentage') {
            $refund_amount = floor($total_price * (1 - $value / 100));
        } elseif ($type === 'fixed') {
            $refund_amount = max(0, $total_price - $value);
        }
    }
}

// 예약 취소 처리
$sql = "UPDATE reservations SET status = 'cancel' WHERE reservation_id = $reservation_id";
$result = mysqli_query($conn, $sql);

// 포인트 환불
mysqli_query($conn, "UPDATE users SET point = point + $refund_amount WHERE user_id = $user_id");

// 쿠폰 복구 (사용 상태 해제)
if ($coupon_id !== null) {
    mysqli_query($conn, "
        UPDATE user_coupons 
        SET is_used = 0 
        WHERE coupon_id = $coupon_id 
          AND user_id = $user_id
    ");
}

// VIP 상태 업데이트
if ($result && $result2) {
    $vip_score = calculate_vip_score($user_id, $conn);
    update_vip_status($user_id, $vip_score, $conn);

    echo "<script>
        alert('예약이 취소되고 {$refund_amount} P가 환불되었습니다.');
        location.href = '../user/mypage.php';
    </script>";
} else {
    echo "<script>alert('예약 취소에 실패했습니다.'); history.back();</script>";
}
?>
