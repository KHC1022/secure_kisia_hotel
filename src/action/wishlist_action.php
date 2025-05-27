<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../action/login_check.php';

$hotel_id = $_GET['hotel_id'];
$user_id = $_SESSION['user_id'];

// 먼저 중복 확인
$check_sql = "SELECT * FROM wishlist WHERE user_id = '$user_id' AND hotel_id = '$hotel_id'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    echo "<script>alert('이미 찜한 호텔입니다.'); history.back();</script>";
    exit;
}

// 중복이 아니면 삽입 시도
$wishlist_sql = "INSERT INTO wishlist (user_id, hotel_id, created_at) VALUES ('$user_id', '$hotel_id', NOW())";
$result = mysqli_query($conn, $wishlist_sql);

if ($result) {
    echo "<script>alert('찜 목록에 추가되었습니다.'); history.back();</script>";
} else {
    echo "<script>alert('오류가 발생했습니다.'); history.back();</script>";
}
?>