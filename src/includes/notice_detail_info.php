<?php 
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$notice_id = $_GET['notice_id'];

$query = "SELECT n.*, u.username 
          FROM notices n
          JOIN users u ON n.user_id = u.user_id
          WHERE n.notice_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $notice_id);
$stmt->execute();
$result = $stmt->get_result();
$notice = $result->fetch_assoc();

$GLOBALS['notice'] = $notice;

?>