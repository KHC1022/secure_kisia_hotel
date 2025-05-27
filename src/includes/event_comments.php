<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * 5;

$count_query = "SELECT COUNT(*) as total FROM event_comments";
$count_result = $conn->query($count_query);
$total_event_comments = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_event_comments / 5);

$query = "SELECT ec.*, u.username 
          FROM event_comments ec 
          JOIN users u ON ec.user_id = u.user_id
          ORDER BY ec.created_at DESC
          LIMIT 5 OFFSET $offset";
$result = $conn->query(query: $query);

$event_comments = array();
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $event_comments[] = $row;
    }
}

?>