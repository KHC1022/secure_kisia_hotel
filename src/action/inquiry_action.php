<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$category_map = [
    'reservation' => '예약',
    'payment' => '결제',
    'room' => '객실',
    'other' => '기타'
];

// 기본값 및 입력값 검증
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$keyword = trim($_GET['keyword'] ?? '');
$sort = $_GET['sort'] ?? 'recent';
$order_by = ($sort === 'old') ? 'ASC' : 'DESC';
$allowed_sorts = ['recent', 'old'];

// 정렬값 유효성 검사
if (!in_array($sort, $allowed_sorts)) {
    $sort = 'recent';
    $order_by = 'DESC';
}

// 키워드 유효성 검사
if (strlen($keyword) > 100 || preg_match('/[<>"\'&]/', $keyword)) {
    echo "<script>alert('검색어에 허용되지 않는 문자가 포함되어 있거나 너무 깁니다.'); history.back();</script>";
    exit;
}

$inquiry_list = [];
$total_inquiries = 0;
$total_pages = 1;

// 키워드가 있을 경우: 검색 결과만
if ($keyword !== '') {
    $stmt = $conn->prepare("
        SELECT i.inquiry_id, i.category, i.title, i.created_at, i.is_secret, u.username
        FROM inquiries i
        JOIN users u ON i.user_id = u.user_id
        WHERE i.title LIKE CONCAT('%', ?, '%')
        ORDER BY i.inquiry_id $order_by
    ");
    $stmt->bind_param("s", $keyword);
    $stmt->execute();
    $result = $stmt->get_result();

    $total_inquiries = $result->num_rows;
    $total_pages = 1;
    $page = 1;
} else {
    // 전체 수 조회
    $count_query = "SELECT COUNT(*) AS total FROM inquiries";
    $count_result = mysqli_query($conn, $count_query);
    if ($count_result) {
        $count_row = mysqli_fetch_assoc($count_result);
        $total_inquiries = $count_row['total'];
        $total_pages = ceil($total_inquiries / $limit);
    }

    // 페이지당 목록 조회
    $stmt = $conn->prepare("
        SELECT i.inquiry_id, i.category, i.title, i.created_at, i.is_secret, u.username
        FROM inquiries i
        JOIN users u ON i.user_id = u.user_id
        ORDER BY i.inquiry_id $order_by
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
}

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $inquiry_id = $row['inquiry_id'];

        // 답변 조회
        $res_stmt = $conn->prepare("SELECT content, created_at FROM inquiry_responses WHERE inquiry_id = ? LIMIT 1");
        $res_stmt->bind_param("i", $inquiry_id);
        $res_stmt->execute();
        $res_result = $res_stmt->get_result();
        $response = $res_result->fetch_assoc();

        $inquiry_list[] = [
            'inquiry_id' => $inquiry_id,
            'category' => $category_map[$row['category']] ?? $row['category'],
            'title' => $row['title'],
            'username' => $row['username'],
            'created_at' => $row['created_at'],
            'is_secret' => $row['is_secret'],
            'response' => $response
        ];
    }
}

// 결과 전역 설정
$GLOBALS['inquiry_list'] = $inquiry_list;
$GLOBALS['totalPages'] = $total_pages;
$GLOBALS['page'] = $page;
$GLOBALS['sort'] = $sort;
$GLOBALS['keyword'] = htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8');  // 출력용 안전 처리
$GLOBALS['totalInquiries'] = $total_inquiries;
?>
