<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';

$hotel_id = $_GET['hotel_id'];
$user_id = $_SESSION['user_id'];


$delete_wishlist_sql = "DELETE FROM wishlist where user_id = '$user_id' and hotel_id='$hotel_id'";
$result = mysqli_query($conn, $delete_wishlist_sql);

if ($result) {
    echo "<script> alert(\"찜 목록에서 삭제되었습니다.\");
    location.href='../user/mypage.php';</script>";
} else {
    echo "<script>alert('오류가 발생했습니다.'); history.back();</script>";
}
?>