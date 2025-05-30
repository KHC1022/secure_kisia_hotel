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

// 입력값 길이 및 정규식 검증
if (strlen($search) > 100 || preg_match('/[<>"\'&]/', $search)) {
    echo "<script>alert('검색어에 허용되지 않는 문자가 포함되어 있거나 너무 깁니다.'); history.back();</script>";
    exit;
}
if (strlen($checkin) > 10 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkin)) {
    echo "<script>alert('체크인 날짜 형식이 잘못되었습니다.'); history.back();</script>";
    exit;
}
if (strlen($checkout) > 10 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkout)) {
    echo "<script>alert('체크아웃 날짜 형식이 잘못되었습니다.'); history.back();</script>";
    exit;
}

// 게스트 수 제한
$guests = max(1, min($guests, 4));

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
