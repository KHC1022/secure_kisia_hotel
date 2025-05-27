<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/pagination.php';

// 검색어 및 정렬 파라미터 가져오기
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'recent';

// 페이지네이션 설정
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$items_per_page = 5;
$offset = ($page - 1) * $items_per_page;

// 공지사항 목록 조회
$query = "SELECT n.*, u.username 
          FROM notices n
          JOIN users u ON n.user_id = u.user_id
          WHERE n.is_released = 1";

// 검색어 조건
if (!empty($search)) {
    $query .= " AND n.title LIKE '%$search%'";
}

// 정렬 조건
$query .= ($sort === 'recent') ? " ORDER BY n.notice_id ASC" : " ORDER BY n.notice_id DESC";

// 전체 개수 조회
$count_query = "SELECT COUNT(*) as total FROM notices where is_released = 1";
$count_result = $conn->query($count_query);
if (!$count_result) {
    echo "<script>alert('전체 개수 조회 오류: " . $conn->error . "');</script>";
    $total_notices = 0;
} else {
    $total_notices = $count_result->fetch_assoc()['total'];
}
$total_pages = ceil($total_notices / $items_per_page);

// 페이지네이션 적용
$query .= " LIMIT $offset, $items_per_page";
$result = $conn->query($query);

if (!$result) {
    echo "<script>alert('목록 조회 오류: " . $conn->error . "');</script>";
    $notice_list = array();
} else {
    // 공지사항 목록 저장
    $notice_list = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notice_list[] = [
                'notice_id' => $row['notice_id'],
                'title' => $row['title'],
                'username' => $row['username'],
                'created_at' => $row['created_at']
            ];
        }
    }
}

// 전역 변수 설정
$GLOBALS['notice_list'] = $notice_list;
$GLOBALS['total_notice'] = $total_notices;
$GLOBALS['page'] = $page;
$GLOBALS['sort'] = $sort;
$GLOBALS['search'] = $search;
?>

