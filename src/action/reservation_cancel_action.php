<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/mypage_action.php';  // VIP 관련 함수

$reservation_id = (int)($_POST['reservation_id'] ?? 0);
$room_id = (int)($_POST['room_id'] ?? 0);
$user_id = $_SESSION['user_id'] ?? 0;

if (!$reservation_id || !$room_id || !$user_id) {
    exit('<script>alert("잘못된 접근입니다."); location.href="../user/mypage.php";</script>');
}

// 예약 정보 조회 및 본인 확인
$stmt = $conn->prepare("SELECT total_price, coupon_id, user_id FROM reservations WHERE reservation_id = ?");
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$res = $stmt->get_result();
$reservation = $res->fetch_assoc();

if (!$reservation || $reservation['user_id'] != $user_id) {
    exit('<script>alert("예약 정보가 없거나 권한이 없습니다."); location.href="../user/mypage.php";</script>');
}

$total_price = $reservation['total_price'];
$coupon_id = $reservation['coupon_id'];
$refund_amount = $total_price;

// 쿠폰 정보 조회
if ($coupon_id !== null) {
    $stmt = $conn->prepare("SELECT discount_type, discount_value FROM coupons WHERE coupon_id = ?");
    $stmt->bind_param("i", $coupon_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $coupon = $res->fetch_assoc();

    if ($coupon) {
        if ($coupon['discount_type'] === 'percentage') {
            $refund_amount = floor($total_price * (1 - $coupon['discount_value'] / 100));
        } elseif ($coupon['discount_type'] === 'fixed') {
            $refund_amount = max(0, $total_price - $coupon['discount_value']);
        }
    }
}

// 객실 상태 available로 변경
$stmt = $conn->prepare("UPDATE rooms SET status = 'available' WHERE room_id = ?");
$stmt->bind_param("i", $room_id);
$result2 = $stmt->execute();

// 예약 취소 처리
$stmt = $conn->prepare("UPDATE reservations SET status = 'cancel' WHERE reservation_id = ?");
$stmt->bind_param("i", $reservation_id);
$result = $stmt->execute();

// 포인트 환불
$stmt = $conn->prepare("UPDATE users SET point = point + ? WHERE user_id = ?");
$stmt->bind_param("ii", $refund_amount, $user_id);
$stmt->execute();

// 쿠폰 복구
if ($coupon_id !== null) {
    $stmt = $conn->prepare("UPDATE user_coupons SET is_used = 0 WHERE coupon_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $coupon_id, $user_id);
    $stmt->execute();
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
