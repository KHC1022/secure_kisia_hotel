<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['hotel_id']) || !is_numeric($_GET['hotel_id'])) {
    echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$hotel_id = (int)$_GET['hotel_id'];

// Prepared Statement 사용
$stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND hotel_id = ?");
$stmt->bind_param("ii", $user_id, $hotel_id);

if ($stmt->execute()) {
    echo "<script>alert('찜 목록에서 삭제되었습니다.'); location.href='../user/mypage.php';</script>";
} else {
    echo "<script>alert('오류가 발생했습니다.'); history.back();</script>";
}

$stmt->close();
?>
