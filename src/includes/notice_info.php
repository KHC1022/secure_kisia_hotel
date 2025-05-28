<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/pagination.php';

// 검색어 및 정렬 파라미터
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'recent';

// 페이지네이션 설정
$page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
$items_per_page = 5;
$offset = ($page - 1) * $items_per_page;

// 기본 쿼리
$where = " WHERE n.is_released = 1";
if (!empty($search)) {
    $escaped_search = $conn->real_escape_string($search);
    $where .= " AND n.title LIKE '%$escaped_search%'";
}

// 정렬 조건
$order_by = ($sort === 'recent') ? " ORDER BY n.notice_id DESC" : " ORDER BY n.notice_id ASC";

// 총 개수 쿼리 (검색 포함)
$count_query = "SELECT COUNT(*) AS total FROM notices n $where";
$count_result = $conn->query($count_query);
$total_notices = ($count_result && $row = $count_result->fetch_assoc()) ? (int)$row['total'] : 0;
$total_pages = ceil($total_notices / $items_per_page);

// 공지사항 조회 쿼리
$list_query = "
    SELECT n.notice_id, n.title, n.created_at, u.username 
    FROM notices n
    JOIN users u ON n.user_id = u.user_id
    $where
    $order_by
    LIMIT $offset, $items_per_page
";

$result = $conn->query($list_query);
$notice_list = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notice_list[] = [
            'notice_id' => $row['notice_id'],
            'title' => $row['title'],
            'username' => $row['username'],
            'created_at' => $row['created_at']
        ];
    }
} elseif (!$result) {
    echo "<script>alert('공지사항 조회 오류: " . $conn->error . "');</script>";
}

// 전역 변수 설정
$GLOBALS['notice_list'] = $notice_list;
$GLOBALS['total_notice'] = $total_notices;
$GLOBALS['page'] = $page;
$GLOBALS['sort'] = $sort;
$GLOBALS['search'] = $search;
?>
