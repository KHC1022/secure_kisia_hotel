<?php
// 호텔 ID 가져오기
$hotel_id = $_GET['hotel_edit'];

// 호텔 기본 정보 가져오기
$sql = "SELECT * FROM hotels WHERE hotel_id = $hotel_id";
$result = $conn->query($sql);
$hotel = $result->fetch_assoc();

$hotel_name = $hotel['name'];
$hotel_location = $hotel['location'];
$hotel_description = $hotel['description'];
$hotel_price_per_night = $hotel['price_per_night'];
$hotel_main_image = $hotel['main_image'];
$hotel_detail_image_1 = $hotel['detail_image_1'];
$hotel_detail_image_2 = $hotel['detail_image_2'];
$hotel_detail_image_3 = $hotel['detail_image_3'];
$hotel_detail_image_4 = $hotel['detail_image_4'];

// 호텔 부대시설 가져오기
$facility_sql = "SELECT * FROM hotel_facilities WHERE hotel_id = $hotel_id";
$facility_result = $conn->query($facility_sql);
$hotel_facilities = $facility_result->fetch_assoc();

// 디럭스 룸 정보 가져오기
$deluxe_sql = "SELECT * FROM rooms WHERE hotel_id = $hotel_id AND room_type = 'deluxe'";
$deluxe_result = $conn->query($deluxe_sql);
$deluxe_room = $deluxe_result->fetch_assoc();

// 스위트 룸 정보 가져오기
$suite_sql = "SELECT * FROM rooms WHERE hotel_id = $hotel_id AND room_type = 'suite'";
$suite_result = $conn->query($suite_sql);
$suite_room = $suite_result->fetch_assoc();
?> 