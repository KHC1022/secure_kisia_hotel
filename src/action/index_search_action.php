<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

// 검색어 가져오기
$search = $_GET['search'] ?? '';
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$guests = $_GET['guests'] ?? 1;

// 게스트 수 제한
if ($guests > 4) {
    $guests = 4;
} elseif ($guests < 1) {
    $guests = 1;
}

// 검색어가 비어있으면 모든 호텔 표시
if (empty($search)) {
    $sql = "SELECT DISTINCT h.* 
            FROM hotels h 
            INNER JOIN rooms r ON h.hotel_id = r.hotel_id 
            WHERE r.max_person >= $guests";
} else {
    $sql = "SELECT DISTINCT h.* 
            FROM hotels h 
            INNER JOIN rooms r ON h.hotel_id = r.hotel_id 
            WHERE (h.name LIKE '%$search%' OR h.location LIKE '%$search%') 
            AND r.max_person >= $guests";
}

$result = mysqli_query($conn, $sql);

if (!$result) {
    $current_hotels = [];
} else {
    $current_hotels = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $current_hotels[] = $row;
    }
}

// 검색 파라미터 저장
$search_params = [
    'search' => $search,
    'checkin' => $checkin,
    'checkout' => $checkout,
    'guests' => $guests
];




?>