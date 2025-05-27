<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';


// ✅ 세션 검증, 로그인 검증 없음
// ✅ GET 요청 (CSRF 테스트 가능)
$user_id = $_SESSION['user_id'];     // ✅ 외부에서 user_id 주입 가능 (최악의 보안)
$coupon_id = $_GET['coupon_id']; // ✅ 외부에서 coupon_id 주입 가능

// ✅ 이미 받았는지 확인 (SQL Injection 가능)
$sql = "SELECT * FROM user_coupons WHERE user_id = $user_id AND coupon_id = $coupon_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 0) {
    // ✅ 바로 insert (SQL Injection 가능)
    $insert_sql = "INSERT INTO user_coupons (user_id, coupon_id, received_at) VALUES ($user_id, $coupon_id, NOW())";
    mysqli_query($conn, $insert_sql);

    echo "<script>alert('쿠폰을 받았습니다.'); location.href='coupon-list.php';</script>";
} else {
    echo "<script>alert('이미 쿠폰을 받았습니다.'); history.back();</script>";
}
?>
