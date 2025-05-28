<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$limit = 5;
$offset = ($page - 1) * $limit;

// 전체 댓글 수 조회
$count_query = "SELECT COUNT(*) as total FROM event_comments";
$count_result = $conn->query($count_query);

$total_event_comments = 0;
if ($count_result) {
    $total_event_comments = $count_result->fetch_assoc()['total'];
}
$total_pages = ceil($total_event_comments / $limit);

// 댓글 목록 조회
$query = "SELECT ec.*, u.username 
          FROM event_comments ec 
          JOIN users u ON ec.user_id = u.user_id
          ORDER BY ec.created_at DESC
          LIMIT $limit OFFSET $offset";

$result = $conn->query($query);  // ✅ 잘못된 query: 키워드 제거

$event_comments = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $event_comments[] = $row;
    }
}

// 전역 변수 설정
$GLOBALS['event_comments'] = $event_comments;
$GLOBALS['total_event_comments'] = $total_event_comments;
$GLOBALS['page'] = $page;
?>
