<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/pagination.php';

// 검색어 및 정렬 파라미터
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'recent';

// 검색어 필터링 (XSS 방지 및 형식 검사)
if (strlen($search) > 100 || preg_match('/[<>"\'&]/', $search)) {
    echo "<script>alert('검색어가 너무 길거나 유효하지 않은 문자가 포함되어 있습니다.'); history.back();</script>";
    exit;
}

// 정렬 필터링 (화이트리스트 적용)
$allowed_sorts = ['recent', 'old'];
if (!in_array($sort, $allowed_sorts)) {
    $sort = 'recent';
}

// 페이지네이션 설정
$page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
$items_per_page = 5;
$offset = ($page - 1) * $items_per_page;

// 기본 쿼리
$where = " WHERE n.is_released = 1";
$params = [];
$types = '';
if (!empty($search)) {
    $where .= " AND n.title LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}

// 정렬 조건
$order_by = ($sort === 'recent') ? " ORDER BY n.notice_id DESC" : " ORDER BY n.notice_id ASC";

// 총 개수 쿼리
$count_query = "SELECT COUNT(*) AS total FROM notices n $where";
$count_stmt = $conn->prepare($count_query);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_notices = ($count_result && $row = $count_result->fetch_assoc()) ? (int)$row['total'] : 0;
$total_pages = ceil($total_notices / $items_per_page);
$count_stmt->close();

// 공지사항 조회 쿼리
$list_query = "
    SELECT n.notice_id, n.title, n.created_at, u.username 
    FROM notices n
    JOIN users u ON n.user_id = u.user_id
    $where
    $order_by
    LIMIT ?, ?
";
$list_stmt = $conn->prepare($list_query);

// 바인딩 값 설정
$params[] = $offset;
$params[] = $items_per_page;
$types .= 'ii';

$list_stmt->bind_param($types, ...$params);
$list_stmt->execute();
$result = $list_stmt->get_result();
$notice_list = [];

while ($row = $result->fetch_assoc()) {
    $notice_list[] = [
        'notice_id' => $row['notice_id'],
        'title' => $row['title'],
        'username' => $row['username'],
        'created_at' => $row['created_at']
    ];
}

$list_stmt->close();

// 전역 변수 설정
$GLOBALS['notice_list'] = $notice_list;
$GLOBALS['total_notice'] = $total_notices;
$GLOBALS['page'] = $page;
$GLOBALS['sort'] = htmlspecialchars($sort, ENT_QUOTES, 'UTF-8');
$GLOBALS['search'] = htmlspecialchars($search, ENT_QUOTES, 'UTF-8');
?>
