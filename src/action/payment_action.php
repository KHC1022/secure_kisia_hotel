<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../action/login_check.php';

$user_id = $_SESSION['user_id'] ?? null;
$hotel_id = $_GET['id'] ?? 0;
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$guests = $_GET['guests'] ?? '';
$room_type = $_GET['room_type'] ?? '';
$deluxe_room_id = $_GET['deluxe_room_id'] ?? 0;
$suite_room_id = $_GET['suite_room_id'] ?? 0;
$event_busan = $_GET['event_busan'] ?? 0;
$event_japan = $_GET['event_japan'] ?? 0;

if ($room_type == 'deluxe') {
    $room_id = $deluxe_room_id;
} else {
    $room_id = $suite_room_id;
}

if ($checkin && $checkout) {
    $checkin_date = new DateTime($checkin);
    $checkout_date = new DateTime($checkout);

    if ($checkout_date <= $checkin_date) {
        echo "<script>alert('체크아웃 날짜는 체크인 날짜보다 늦어야 합니다.'); history.back();</script>";
        exit;
    }
}

// 호텔 정보 불러오기
$hotel_sql = "SELECT * FROM hotels WHERE hotel_id = $hotel_id";
$hotel_result = mysqli_query($conn, $hotel_sql);
$hotel = $hotel_result ? mysqli_fetch_assoc($hotel_result) : null;

// 유저 정보 불러오기
if ($user_id) {
    $user_sql = "SELECT * FROM users WHERE user_id = '$user_id'";
    $user_result = mysqli_query($conn, $user_sql);
    $users = $user_result ? mysqli_fetch_assoc($user_result) : null;
} else {
    $users = null;
}

$days = 1;
if ($checkin && $checkout) {
    $start = new DateTime($checkin);
    $end = new DateTime($checkout);
    $days = $start->diff($end)->days;
    if ($days <= 0) $days = 1; // 최소 1박
}

// 객실 요금 계산
if ($event_busan == 1) {
    $room_price_sql = "SELECT price FROM rooms WHERE room_id = $room_id";
    $room_price_result = mysqli_query($conn, $room_price_sql);
    $room = mysqli_fetch_assoc($room_price_result);
    $price_per_night = $room['price'] * 0.6;
} else if ($event_japan == 1) {
    $room_price_sql = "SELECT price FROM rooms WHERE room_id = $room_id";
    $room_price_result = mysqli_query($conn, $room_price_sql);
    $room = mysqli_fetch_assoc($room_price_result);
    $price_per_night = $room['price'] * 0.8;
} else {
    $room_price_sql = "SELECT price FROM rooms WHERE room_id = $room_id";
    $room_price_result = mysqli_query($conn, $room_price_sql);
    $room = mysqli_fetch_assoc($room_price_result);
    $price_per_night = $room['price'] ?? 0;
}
$room_fee = $price_per_night * $days;

// 세금 및 수수료: 객실 요금의 10%
$tax = round($room_fee * 0.1);

// 총 결제 금액 = 객실 요금 + 세금
$total_price = $room_fee + $tax;

$pay_sql = "SELECT point FROM users WHERE user_id = '$user_id'";
$pay_result = mysqli_query($conn, $pay_sql);
$user = mysqli_fetch_assoc($pay_result);

?>
