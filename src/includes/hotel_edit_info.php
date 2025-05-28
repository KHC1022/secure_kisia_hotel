<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$hotel_id = isset($_GET['hotel_id']) ? (int)$_GET['hotel_id'] : 0;

if ($hotel_id <= 0) {
    error_log("잘못된 호텔 ID: " . ($_GET['hotel_id'] ?? '설정되지 않음'));
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}

try {
    // 호텔 기본 정보
    $stmt = $conn->prepare("SELECT * FROM hotels WHERE hotel_id = ?");
    if (!$stmt) {
        throw new Exception("데이터베이스 준비 오류: " . $conn->error);
    }

    $stmt->bind_param("i", $hotel_id);
    if (!$stmt->execute()) {
        throw new Exception("데이터베이스 실행 오류: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $hotel = $result->fetch_assoc();

    if (!$hotel) {
        error_log("호텔을 찾을 수 없음: ID $hotel_id");
        echo "<script>alert('해당 호텔 정보를 찾을 수 없습니다.'); history.back();</script>";
        exit;
    }

    $hotel_name = $hotel['name'];
    $hotel_location = $hotel['location'];
    $hotel_description = $hotel['description'];
    $hotel_price_per_night = $hotel['price_per_night'];
    $hotel_main_image = $hotel['main_image'];
    $hotel_detail_image_1 = $hotel['detail_image_1'];
    $hotel_detail_image_2 = $hotel['detail_image_2'];
    $hotel_detail_image_3 = $hotel['detail_image_3'];
    $hotel_detail_image_4 = $hotel['detail_image_4'];

    // 호텔 부대시설
    $stmt = $conn->prepare("SELECT * FROM hotel_facilities WHERE hotel_id = ?");
    $stmt->bind_param("i", $hotel_id);
    $stmt->execute();
    $facility_result = $stmt->get_result();
    $hotel_facilities = $facility_result->fetch_assoc();

    // 디럭스 룸
    $room_type = 'deluxe';
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE hotel_id = ? AND room_type = ?");
    $stmt->bind_param("is", $hotel_id, $room_type);
    $stmt->execute();
    $deluxe_result = $stmt->get_result();
    $deluxe_room = $deluxe_result->fetch_assoc();

    // 스위트 룸
    $room_type = 'suite';
    $stmt->bind_param("is", $hotel_id, $room_type);
    $stmt->execute();
    $suite_result = $stmt->get_result();
    $suite_room = $suite_result->fetch_assoc();

} catch (Exception $e) {
    error_log("호텔 수정 중 오류 발생: " . $e->getMessage() . " 파일: " . $e->getFile() . " 라인: " . $e->getLine());
    echo "<script>alert('호텔 정보를 불러오는 중 오류가 발생했습니다.'); history.back();</script>";
    exit;
} catch (Error $e) {
    error_log("호텔 수정 중 시스템 오류 발생: " . $e->getMessage() . " 파일: " . $e->getFile() . " 라인: " . $e->getLine());
    echo "<script>alert('시스템 오류가 발생했습니다. 관리자에게 문의하세요.'); history.back();</script>";
    exit;
}
?>