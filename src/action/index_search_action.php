<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

// GET 파라미터 과도하게 전송된 경우 방어
if (count($_GET) > 100) {
    echo "<script>alert('입력 항목이 너무 많습니다.'); history.back();</script>";
    exit;
}

// 검색어 및 파라미터 안전하게 가져오기
$search = trim($_GET['search'] ?? '');
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$guests = isset($_GET['guests']) ? (int)$_GET['guests'] : 1;

// 길이 제한 검증
if (strlen($search) > 100 || strlen($checkin) > 10 || strlen($checkout) > 10) {
    echo "<script>alert('검색어 또는 날짜 형식이 너무 깁니다.'); history.back();</script>";
    exit;
}

// 게스트 수 제한
$guests = max(1, min($guests, 4));

// SQL 준비
$current_hotels = [];

try {
    if (empty($search)) {
        $stmt = $conn->prepare("
            SELECT DISTINCT h.* 
            FROM hotels h 
            INNER JOIN rooms r ON h.hotel_id = r.hotel_id 
            WHERE r.max_person >= ?
        ");
        $stmt->bind_param("i", $guests);
    } else {
        $like_search = '%' . $search . '%';
        $stmt = $conn->prepare("
            SELECT DISTINCT h.* 
            FROM hotels h 
            INNER JOIN rooms r ON h.hotel_id = r.hotel_id 
            WHERE (h.name LIKE ? OR h.location LIKE ?) 
              AND r.max_person >= ?
        ");
        $stmt->bind_param("ssi", $like_search, $like_search, $guests);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $current_hotels[] = $row;
    }

    $stmt->close();
} catch (Throwable $e) {
    error_log("[호텔 검색 오류] " . $e->getMessage());
    $current_hotels = [];
}

// 검색 파라미터 저장 (출력용)
$search_params = [
    'search' => htmlspecialchars($search, ENT_QUOTES, 'UTF-8'),
    'checkin' => htmlspecialchars($checkin, ENT_QUOTES, 'UTF-8'),
    'checkout' => htmlspecialchars($checkout, ENT_QUOTES, 'UTF-8'),
    'guests' => $guests
];
?>
