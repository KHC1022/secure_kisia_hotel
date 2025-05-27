<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$category_map = [
    'reservation' => '예약',
    'payment' => '결제',
    'room' => '객실',
    'other' => '기타'
];

$limit = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$keyword = $_GET['keyword'] ?? '';
$sort = $_GET['sort'] ?? 'recent';
$order_by = ($sort === 'old') ? 'ASC' : 'DESC';

$inquiry_list = [];

if ($keyword !== '') {
    $sql = "
        SELECT i.inquiry_id, i.category, i.title, i.created_at, i.is_secret, u.username
        FROM inquiries i
        JOIN users u ON i.user_id = u.user_id
        WHERE i.title LIKE '%$keyword%' 
        ORDER BY i.inquiry_id $order_by
    ";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        $inquiry_list = [];
        $total_inquiries = 0;
        $total_pages = 1;
        $page = 1;
    } else {
        $total_inquiries = mysqli_num_rows($result);
        $total_pages = 1;
        $page = 1;
    }
} else {
    $count_query = "SELECT COUNT(*) AS total FROM inquiries";
    $count_result = mysqli_query($conn, $count_query);
    if (!$count_result) {
        $total_inquiries = 0;
        $total_pages = 1;
    } else {
        $count_row = mysqli_fetch_assoc($count_result);
        $total_inquiries = $count_row['total'];
        $total_pages = ceil($total_inquiries / $limit);
    }

    $sql = "
        SELECT i.inquiry_id, i.category, i.title, i.created_at, i.is_secret, u.username
        FROM inquiries i
        JOIN users u ON i.user_id = u.user_id
        ORDER BY i.inquiry_id $order_by
        LIMIT $limit OFFSET $offset
    ";
    $result = mysqli_query($conn, $sql);
}

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $inquiry_id = $row['inquiry_id'];
        $res_query = "SELECT content, created_at FROM inquiry_responses WHERE inquiry_id = $inquiry_id LIMIT 1";
        $res_result = mysqli_query($conn, $res_query);
        $response = mysqli_fetch_assoc($res_result);

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

$GLOBALS['inquiry_list'] = $inquiry_list;
$GLOBALS['totalPages'] = $total_pages;
$GLOBALS['page'] = $page;
$GLOBALS['sort'] = $sort;
$GLOBALS['keyword'] = $keyword;
$GLOBALS['totalInquiries'] = $total_inquiries;