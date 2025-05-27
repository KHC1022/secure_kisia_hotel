<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$hotels = array();
$today = date('Y-m-d');

// 검색어 및 필터 파라미터 가져오기
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$price_filter = isset($_GET['price']) ? $_GET['price'] : '';
$facility_filters = isset($_GET['facilities']) ? (is_array($_GET['facilities']) ? $_GET['facilities'] : explode(',', $_GET['facilities'])) : array();
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

// 필터링된 호텔 목록 가져오기
$query = "SELECT h.*, 
          hf.pool, hf.spa, hf.fitness, hf.restaurant, hf.parking, hf.wifi
          FROM hotels h
          LEFT JOIN hotel_facilities hf ON h.hotel_id = hf.hotel_id
          WHERE 1=1";

// 검색어 조건
if (!empty($search)) {
    $query .= " AND (h.name LIKE '%$search%' OR h.location LIKE '%$search%')";
}


// 가격 필터 조건
if (!empty($price_filter)) {
    switch ($price_filter) {
        case 'price-0-100000':
            $query .= " AND h.price_per_night <= 100000";
            break;
        case 'price-100000-200000':
            $query .= " AND h.price_per_night > 100000 AND h.price_per_night <= 200000";
            break;
        case 'price-200000-300000':
            $query .= " AND h.price_per_night > 200000 AND h.price_per_night <= 300000";
            break;
        case 'price-300000-':
            $query .= " AND h.price_per_night > 300000";
            break;
    }
}

// 편의시설 필터 조건
if (!empty($facility_filters)) {
    foreach ($facility_filters as $facility) {
        $facility = $conn->real_escape_string($facility);
        $query .= " AND hf.$facility = 1";
    }
}

// 정렬 조건
if (!empty($sort)) {
    switch ($sort) {
        case 'price-low':
            $query .= " ORDER BY h.price_per_night ASC, h.hotel_id ASC";
            break;
        case 'price-high':
            $query .= " ORDER BY h.price_per_night DESC, h.hotel_id ASC";
            break;
        case 'rating':
            $query .= " ORDER BY h.rating DESC, h.hotel_id ASC";
            break;
        case 'none':
            $query .= " ORDER BY h.hotel_id ASC";
            break;
        default:
            $query .= " ORDER BY h.hotel_id ASC";
    }
} else {
    $query .= " ORDER BY h.hotel_id ASC";
}

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($hotel = $result->fetch_assoc()) {
        $hotels[] = $hotel;
    }
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);

$total_hotels = count($hotels);
$total_pages = ceil($total_hotels / 9);
$start_index = ($page - 1) * 9;
$current_hotels = array_slice($hotels, $start_index, 9);

// 추천 호텔
$featured_query = "SELECT h.*, 
                  hf.pool, hf.spa, hf.fitness, hf.restaurant, hf.parking, hf.wifi
                  FROM hotels h
                  LEFT JOIN hotel_facilities hf ON h.hotel_id = hf.hotel_id
                  WHERE h.hotel_id IN (5, 34, 41)";
$featured_result = $conn->query($featured_query);

$featured_hotels = array();

if ($featured_result && $featured_result->num_rows > 0) {
    while ($hotel = $featured_result->fetch_assoc()) {
        $featured_hotels[] = $hotel;
    }
}


// 부산 호텔 타임딜
$busan_query = "SELECT h.*, 
                  hf.pool, hf.spa, hf.fitness, hf.restaurant, hf.parking, hf.wifi
                  FROM hotels h
                  LEFT JOIN hotel_facilities hf ON h.hotel_id = hf.hotel_id
                  WHERE h.hotel_id IN (43, 3, 49)";
$busan_result = $conn->query($busan_query);

$busan_hotels = array();

if ($busan_result && $busan_result->num_rows > 0) {
    while ($hotel = $busan_result->fetch_assoc()) {
        $busan_hotels[] = $hotel;
    }
}


// 일본 호텔 특가
$japan_query = "SELECT h.*, 
                  hf.pool, hf.spa, hf.fitness, hf.restaurant, hf.parking, hf.wifi
                  FROM hotels h
                  LEFT JOIN hotel_facilities hf ON h.hotel_id = hf.hotel_id
                  WHERE h.hotel_id IN (6, 21, 46)";
$japan_result = $conn->query($japan_query);

$japan_hotels = array();

if ($japan_result && $japan_result->num_rows > 0) {
    while ($hotel = $japan_result->fetch_assoc()) {
        $japan_hotels[] = $hotel;
    }
}

?>