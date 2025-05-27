<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT c.*, uc.is_used
    FROM user_coupons uc
    JOIN coupons c ON uc.coupon_id = c.coupon_id
    WHERE uc.user_id = $user_id
      AND c.is_active = 1 
      AND c.start_date <= CURDATE()
      AND c.end_date >= CURDATE()
    ORDER BY c.created_at DESC
";

$result = mysqli_query($conn, $sql);
$available_coupons = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
