<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/mypage_action.php';  // VIP 관련 함수

$user_id = $_SESSION['user_id'] ?? null;
$charge_amount = $_GET['charge_amount'] ?? 0;
$room_id = $_GET['room_id'] ?? null;
$checkin = $_GET['checkin'] ?? null;
$checkout = $_GET['checkout'] ?? null;
$total_price = $_GET['total_price'] ?? 0;
$guests = $_GET['guests'] ?? 1;
$selected_coupon = $_GET['selected_coupon'] ?? '';

$pay_sql = "SELECT point FROM users WHERE user_id = '$user_id'";
$pay_result = mysqli_query($conn, $pay_sql);
$user = mysqli_fetch_assoc($pay_result);

if ($user && $user['point'] >= $charge_amount) {

    // 쿠폰 사용 가능 여부 체크
    if ($selected_coupon != '') {
        $check_coupon = mysqli_query($conn, "
            SELECT * FROM user_coupons 
            WHERE user_id = '$user_id' 
              AND coupon_id = '$selected_coupon' 
              AND is_used = 0
        ");
        if (mysqli_num_rows($check_coupon) == 0) {
            echo "<script>alert('이미 사용했거나 잘못된 쿠폰입니다.'); history.back();</script>";
            exit;
        }
    }

    // 포인트 차감
    $new_point = $user['point'] - $charge_amount;
    $update_sql = "UPDATE users SET point = $new_point WHERE user_id = '$user_id'";
    mysqli_query($conn, $update_sql);

    // 객실 예약
    $rooms_update_sql = "UPDATE rooms SET status = 'reserved' WHERE room_id = '$room_id'";
    mysqli_query($conn, $rooms_update_sql);

    // 예약 등록
    $reservation_sql = "INSERT INTO reservations 
        (user_id, room_id, check_in, check_out, total_price, guests, created_at, status, coupon_id) 
        VALUES 
        ('$user_id', '$room_id', '$checkin', '$checkout', '$total_price', '$guests', NOW(), 'done', " . 
        ($selected_coupon != '' ? "'$selected_coupon'" : "NULL") . ")";

    $reservation_result = mysqli_query($conn, $reservation_sql);


    // 쿠폰 사용 처리
    if ($selected_coupon != '') {
        mysqli_query($conn, 
            "UPDATE user_coupons 
             SET is_used = 1 
             WHERE coupon_id = '$selected_coupon' 
             AND user_id = '$user_id'"
        );
    }

    if (!$reservation_result) {
        echo "<script>alert('예약 등록 중 오류가 발생했습니다: " . mysqli_error($conn) . "');</script>";
        exit;
    }

    // VIP 상태 즉시 업데이트
    $vip_score = calculate_vip_score($user_id, $conn);
    update_vip_status($user_id, $vip_score, $conn);

    echo "<script>alert('결제가 완료되었습니다.'); location.href='../user/mypage.php';</script>";
    exit;
} else {
    echo "<script>alert('잔액이 부족합니다.'); history.back();</script>";
}
?>
