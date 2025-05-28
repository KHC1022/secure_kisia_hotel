<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$hotels = array();
$today = date('Y-m-d');

// 검색어 및 필터 파라미터 가져오기 (기본값 처리)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$price_filter = $_GET['price'] ?? '';
$facility_filters = isset($_GET['facilities']) ? (is_array($_GET['facilities']) ? $_GET['facilities'] : explode(',', $_GET['facilities'])) : array();
$sort = $_GET['sort'] ?? '';

// 유효한 필터만 허용
$valid_price_filters = ['price-0-100000', 'price-100000-200000', 'price-200000-300000', 'price-300000-'];
$valid_sort_options = ['price-low', 'price-high', 'rating', 'none'];

$query = "SELECT h.*, 
          hf.pool, hf.spa, hf.fitness, hf.restaurant, hf.parking, hf.wifi
          FROM hotels h
          LEFT JOIN hotel_facilities hf ON h.hotel_id = hf.hotel_id
          WHERE 1=1";

// SQL 바인딩용 변수
$params = [];
$types = '';

// 검색어 조건
if (!empty($search)) {
    $query .= " AND (h.name LIKE ? OR h.location LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}

// 가격 필터 조건
if (in_array($price_filter, $valid_price_filters)) {
    switch ($price_filter) {
        case 'price-0-100000':
            $query .= " AND h.price_per_night <= ?";
            $params[] = 100000;
            $types .= 'i';
            break;
        case 'price-100000-200000':
            $query .= " AND h.price_per_night > ? AND h.price_per_night <= ?";
            $params[] = 100000;
            $params[] = 200000;
            $types .= 'ii';
            break;
        case 'price-200000-300000':
            $query .= " AND h.price_per_night > ? AND h.price_per_night <= ?";
            $params[] = 200000;
            $params[] = 300000;
            $types .= 'ii';
            break;
        case 'price-300000-':
            $query .= " AND h.price_per_night > ?";
            $params[] = 300000;
            $types .= 'i';
            break;
    }
}

// 편의시설 필터 조건
$safe_facilities = ['pool', 'spa', 'fitness', 'restaurant', 'parking', 'wifi'];
foreach ($facility_filters as $facility) {
    if (in_array($facility, $safe_facilities)) {
        $query .= " AND hf.$facility = 1";
    }
}

// 정렬 조건
if (in_array($sort, $valid_sort_options)) {
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
        default:
            $query .= " ORDER BY h.hotel_id ASC";
            break;
    }
} else {
    $query .= " ORDER BY h.hotel_id ASC";
}

// prepare + bind
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($hotel = $result->fetch_assoc()) {
        $hotels[] = $hotel;
    }
}
$stmt->close();

// 페이징 처리
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$total_hotels = count($hotels);
$total_pages = ceil($total_hotels / 9);
$start_index = ($page - 1) * 9;
$current_hotels = array_slice($hotels, $start_index, 9);

// 추천 호텔 (정적 ID)
$featured_hotels = [];
$featured_ids = [5, 34, 41];
$id_placeholders = implode(',', array_fill(0, count($featured_ids), '?'));
$types_featured = str_repeat('i', count($featured_ids));

$stmt = $conn->prepare("SELECT h.*, 
    hf.pool, hf.spa, hf.fitness, hf.restaurant, hf.parking, hf.wifi
    FROM hotels h
    LEFT JOIN hotel_facilities hf ON h.hotel_id = hf.hotel_id
    WHERE h.hotel_id IN ($id_placeholders)");
$stmt->bind_param($types_featured, ...$featured_ids);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $featured_hotels[] = $row;
}
$stmt->close();

// 타임딜 호텔
$busan_hotels = [];
$busan_ids = [43, 3, 49];
$stmt = $conn->prepare("SELECT h.*, 
    hf.pool, hf.spa, hf.fitness, hf.restaurant, hf.parking, hf.wifi
    FROM hotels h
    LEFT JOIN hotel_facilities hf ON h.hotel_id = hf.hotel_id
    WHERE h.hotel_id IN ($id_placeholders)");
$stmt->bind_param($types_featured, ...$busan_ids);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $busan_hotels[] = $row;
}
$stmt->close();

// 일본 호텔 특가
$japan_hotels = [];
$japan_ids = [6, 21, 46];
$stmt = $conn->prepare("SELECT h.*, 
    hf.pool, hf.spa, hf.fitness, hf.restaurant, hf.parking, hf.wifi
    FROM hotels h
    LEFT JOIN hotel_facilities hf ON h.hotel_id = hf.hotel_id
    WHERE h.hotel_id IN ($id_placeholders)");
$stmt->bind_param($types_featured, ...$japan_ids);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $japan_hotels[] = $row;
}
$stmt->close();
?>
