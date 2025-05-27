<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../action/login_check.php';

$user_id = $_SESSION['user_id'];

// 사용자 가입일 확인
$user_sql = "SELECT created_at FROM users WHERE user_id = '$user_id'";
$user_result = mysqli_query($conn, $user_sql);
if (!$user_result) {
    die('회원 조회 오류: ' . mysqli_error($conn));
}
$user = mysqli_fetch_assoc($user_result);
$user_created_at = strtotime($user['created_at']);
$one_week_ago = strtotime('-1 week');

// 신규 회원 여부 확인
$is_new_user = ($user_created_at >= $one_week_ago);

// VIP 상태 확인
$vip_sql = "SELECT vip FROM users WHERE user_id = '$user_id'";
$vip_result = mysqli_query($conn, $vip_sql);
if (!$vip_result) {
    die('VIP 조회 오류: ' . mysqli_error($conn));
}
$vip_user = mysqli_fetch_assoc($vip_result);
$is_vip = $vip_user['vip'];

// 전체 쿠폰 조회
$sql = "SELECT * FROM coupons WHERE is_active = 1 ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die('쿠폰 정보 조회 오류: ' . mysqli_error($conn));
}
$coupons = mysqli_fetch_all($result, MYSQLI_ASSOC);

// 내가 받은 (아직 사용 안 한) 쿠폰 목록
$received_result = mysqli_query($conn, "
    SELECT coupon_id 
    FROM user_coupons 
    WHERE user_id = '$user_id' AND is_used = 0
");
if (!$received_result) {
    die('받은 쿠폰 조회 오류: ' . mysqli_error($conn));
}

$received = [];
while ($row = mysqli_fetch_assoc($received_result)) {
    $received[] = $row['coupon_id'];
}
function isCouponUsed($conn, $user_id, $coupon_id) {
    $result = mysqli_query($conn, "
        SELECT is_used 
        FROM user_coupons 
        WHERE user_id = $user_id 
          AND coupon_id = $coupon_id
        LIMIT 1
    ");
    $row = mysqli_fetch_assoc($result);
    return $row && $row['is_used'] == 1;
}
function hasCoupon($conn, $user_id, $coupon_id) {
    $result = mysqli_query($conn, "
        SELECT 1 
        FROM user_coupons 
        WHERE user_id = $user_id 
          AND coupon_id = $coupon_id
        LIMIT 1
    ");
    return mysqli_num_rows($result) > 0;
}
?>