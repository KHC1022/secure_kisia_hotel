<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../action/login_check.php';

$user_id = $_SESSION['user_id'] ?? null;
$hotel_id = (int)($_GET['id'] ?? 0);
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$guests = (int)($_GET['guests'] ?? 0);
$room_type = $_GET['room_type'] ?? '';
$deluxe_room_id = (int)($_GET['deluxe_room_id'] ?? 0);
$suite_room_id = (int)($_GET['suite_room_id'] ?? 0);
$event_busan = (int)($_GET['event_busan'] ?? 0);
$event_japan = (int)($_GET['event_japan'] ?? 0);

$room_id = $room_type === 'deluxe' ? $deluxe_room_id : $suite_room_id;

// 날짜 유효성 검사
if ($checkin && $checkout) {
    $checkin_date = new DateTime($checkin);
    $checkout_date = new DateTime($checkout);

    if ($checkout_date <= $checkin_date) {
        echo "<script>alert('체크아웃 날짜는 체크인 날짜보다 늦어야 합니다.'); history.back();</script>";
        exit;
    }
}

// 호텔 정보 불러오기
$hotel = null;
if ($hotel_id > 0) {
    $hotel_sql = "SELECT * FROM hotels WHERE hotel_id = ?";
    $stmt = $conn->prepare($hotel_sql);
    $stmt->bind_param("i", $hotel_id);
    $stmt->execute();
    $hotel_result = $stmt->get_result();
    $hotel = $hotel_result->fetch_assoc();
}

// 유저 정보 불러오기
$users = null;
if ($user_id) {
    $user_sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($user_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $users = $user_result->fetch_assoc();
}

// 숙박 일수 계산
$days = 1;
if ($checkin && $checkout) {
    $start = new DateTime($checkin);
    $end = new DateTime($checkout);
    $days = $start->diff($end)->days;
    if ($days <= 0) $days = 1;
}

// 객실 요금 계산
$price_per_night = 0;
if ($room_id > 0) {
    $room_price_sql = "SELECT price FROM rooms WHERE room_id = ?";
    $stmt = $conn->prepare($room_price_sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();

    if ($room) {
        $price_per_night = $room['price'];
        if ($event_busan) {
            $price_per_night *= 0.6;
        } elseif ($event_japan) {
            $price_per_night *= 0.8;
        }
    }
}

$room_fee = $price_per_night * $days;
$tax = round($room_fee * 0.1);
$total_price = $room_fee + $tax;

// 사용자 포인트 확인
$user = null;
if ($user_id) {
    $pay_sql = "SELECT point FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($pay_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}
?>
