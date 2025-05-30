<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../action/login_check.php';

if (!isset($_GET['hotel_id']) || !is_numeric($_GET['hotel_id'])) {
    echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
    exit;
}

$hotel_id = (int)$_GET['hotel_id'];
$user_id = $_SESSION['user_id'];

// 중복 확인
$check_sql = "SELECT 1 FROM wishlist WHERE user_id = ? AND hotel_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ii", $user_id, $hotel_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<script>alert('이미 찜한 호텔입니다.'); history.back();</script>";
    $stmt->close();
    exit;
}
$stmt->close();

$insert_sql = "INSERT INTO wishlist (user_id, hotel_id, created_at) VALUES (?, ?, NOW())";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("ii", $user_id, $hotel_id);

if ($insert_stmt->execute()) {
    echo "<script>alert('찜 목록에 추가되었습니다.'); history.back();</script>";
} else {
    echo "<script>alert('오류가 발생했습니다.'); history.back();</script>";
}
$insert_stmt->close();
?>
