<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';


$user_id = $_SESSION['user_id'];
$coupon_id = $_GET['coupon_id'];

$sql = "SELECT * FROM user_coupons WHERE user_id = $user_id AND coupon_id = $coupon_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 0) {
    $insert_sql = "INSERT INTO user_coupons (user_id, coupon_id, received_at) VALUES ($user_id, $coupon_id, NOW())";
    mysqli_query($conn, $insert_sql);

    echo "<script>alert('쿠폰을 받았습니다.'); location.href='coupon-list.php';</script>";
} else {
    echo "<script>alert('이미 쿠폰을 받았습니다.'); history.back();</script>";
}
?>
