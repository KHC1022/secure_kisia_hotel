<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$hotel_id = $_GET['id'];
$today = date('Y-m-d');

// 이벤트 변수 초기화
$event_busan = 0;
$event_japan = 0;

if (isset($_GET['event_busan'])) {
    $event_busan = $_GET['event_busan'];
}

if (isset($_GET['event_japan'])) {
    $event_japan = $_GET['event_japan'];
}

$sql = "SELECT * FROM hotels WHERE hotel_id = $hotel_id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// 호텔 정보 가져오기
$hotel_name = $row['name'];
$hotel_location = $row['location'];
$hotel_description = $row['description'];
$hotel_price_per_night = $row['price_per_night'];
$hotel_rating = $row['rating'];
$hotel_main_image = $row['main_image'];
$hotel_detail_image_1 = $row['detail_image_1'];
$hotel_detail_image_2 = $row['detail_image_2'];
$hotel_detail_image_3 = $row['detail_image_3'];
$hotel_detail_image_4 = $row['detail_image_4'];

// 호텔 부대시설 가져오기
$hotel_facilities = array();

$facility_sql = "SELECT hf.pool, hf.spa, hf.fitness, hf.restaurant, hf.parking, hf.wifi
                 FROM hotel_facilities hf
                 WHERE hf.hotel_id = $hotel_id";
$facility_result = $conn->query($facility_sql);

if ($facility_result && $facility_result->num_rows > 0) {
    $hotel_facilities = $facility_result->fetch_assoc();
} else {
    $hotel_facilities = array(
        'pool' => false,
        'spa' => false,
        'fitness' => false,
        'restaurant' => false,
        'parking' => false,
        'wifi' => false
    );
}

// 예약 가능한 객실 확인
$available_rooms_sql = "SELECT COUNT(*) as count FROM rooms WHERE hotel_id = $hotel_id AND status = 'available'";
$available_rooms_result = $conn->query($available_rooms_sql);
$available_rooms = $available_rooms_result->fetch_assoc()['count'];

// 호텔 객실 deluxe 정보 가져오기
$room_deluxe_sql = "SELECT * FROM rooms WHERE hotel_id = $hotel_id AND room_type = 'deluxe' AND status = 'available' ORDER BY price ASC LIMIT 1";
$room_deluxe_result = $conn->query($room_deluxe_sql);
$room_deluxe_count = $room_deluxe_result->num_rows;

$hotel_rooms_deluxe = [];
$deluxe_room_id = '';
if ($room_deluxe_result && $room_deluxe_result->num_rows > 0) {
    $room_row = $room_deluxe_result->fetch_assoc();
    $hotel_rooms_deluxe[] = $room_row;
    $deluxe_room_id = intval($room_row['room_id']);
}

// 호텔 객실 suite 정보 가져오기
$room_suite_sql = "SELECT * FROM rooms WHERE hotel_id = $hotel_id AND room_type = 'suite' AND status = 'available' ORDER BY price ASC LIMIT 1";
$room_suite_result = $conn->query($room_suite_sql);
$room_suite_count = $room_suite_result->num_rows;

$hotel_rooms_suite = [];
$suite_room_id = '';
if ($room_suite_result && $room_suite_result->num_rows > 0) {
    $room_row = $room_suite_result->fetch_assoc();
    $hotel_rooms_suite[] = $room_row;
    $suite_room_id = intval($room_row['room_id']);
}

// 이벤트 할인 적용
if ($event_busan == 1) {
    $hotel_price_per_night = $row['price_per_night'] * 0.6;
    
    if (!empty($hotel_rooms_deluxe)) {
        foreach ($hotel_rooms_deluxe as &$room) {
            $deluxe_sale_price = $room['price'] * 0.6;
        }
    }
    
    if (!empty($hotel_rooms_suite)) {
        foreach ($hotel_rooms_suite as &$room) {
            $suite_sale_price = $room['price'] * 0.6;
        }
    }
}

if ($event_japan == 1) {
    $hotel_price_per_night = $row['price_per_night'] * 0.8;
    
    if (!empty($hotel_rooms_deluxe)) {
        foreach ($hotel_rooms_deluxe as &$room) {
            $deluxe_sale_price = $room['price'] * 0.8;
        }
    }
    
    if (!empty($hotel_rooms_suite)) {
        foreach ($hotel_rooms_suite as &$room) {
            $suite_sale_price = $room['price'] * 0.8;
        }   
    }
}

// room_ids 배열 초기화
$room_ids = array();
if ($deluxe_room_id) {
    $room_ids[] = $deluxe_room_id;
}
if ($suite_room_id) {
    $room_ids[] = $suite_room_id;
}

// 후기 정보 가져오기
$reviews_query = "SELECT r.*, u.username, u.profile_image,
                 (SELECT SUM(is_helpful) FROM review_helpful WHERE review_id = r.review_id) as count_is_helpful,
                 (SELECT SUM(not_helpful) FROM review_helpful WHERE review_id = r.review_id) as count_is_not_helpful
                 FROM reviews r
                 JOIN users u ON r.user_id = u.user_id
                 WHERE r.hotel_id = ?
                 ORDER BY r.created_at DESC
                 LIMIT 5";

$reviews_stmt = $conn->prepare($reviews_query);
$reviews_stmt->bind_param("i", $hotel_id);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();
$reviews = [];
$review_count = $reviews_result->num_rows;

while ($review = $reviews_result->fetch_assoc()) {
    $review['count_is_helpful'] = $review['count_is_helpful'] ?? 0;
    $review['count_is_not_helpful'] = $review['count_is_not_helpful'] ?? 0;
    $reviews[] = $review;
}

// 로그인한 사용자의 예약 ID 가져오기
$user_reservation_id = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $reservation_sql = "SELECT r.reservation_id 
                       FROM reservations r 
                       JOIN rooms rm ON r.room_id = rm.room_id 
                       WHERE r.user_id = $user_id 
                       AND rm.hotel_id = $hotel_id 
                       AND r.status = 'done'";
    $reservation_result = $conn->query($reservation_sql);
    
    if ($reservation_result && $reservation_result->num_rows > 0) {
        $reservation_row = $reservation_result->fetch_assoc();
        $user_reservation_id = $reservation_row['reservation_id'];
    }
}

?>