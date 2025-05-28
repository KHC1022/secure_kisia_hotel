<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../action/login_check.php';

$user_id = $_SESSION['user_id'];

// 사용자 가입일 확인
$user_stmt = $conn->prepare("SELECT created_at FROM users WHERE user_id = ?");
$user_stmt->bind_param('s', $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
if (!$user_result) {
    die('회원 조회 오류: ' . $conn->error);
}
$user = $user_result->fetch_assoc();
$user_created_at = strtotime($user['created_at']);
$is_new_user = ($user_created_at >= strtotime('-1 week'));

// VIP 상태 확인
$vip_stmt = $conn->prepare("SELECT vip FROM users WHERE user_id = ?");
$vip_stmt->bind_param('s', $user_id);
$vip_stmt->execute();
$vip_result = $vip_stmt->get_result();
if (!$vip_result) {
    die('VIP 조회 오류: ' . $conn->error);
}
$is_vip = $vip_result->fetch_assoc()['vip'];

// 전체 쿠폰 조회
$coupon_query = "SELECT * FROM coupons WHERE is_active = 1 ORDER BY created_at DESC";
$coupon_result = mysqli_query($conn, $coupon_query);
if (!$coupon_result) {
    die('쿠폰 정보 조회 오류: ' . mysqli_error($conn));
}
$coupons = mysqli_fetch_all($coupon_result, MYSQLI_ASSOC);

// 받은 쿠폰 목록 조회 (사용 안한 것)
$received_stmt = $conn->prepare("
    SELECT coupon_id 
    FROM user_coupons 
    WHERE user_id = ? AND is_used = 0
");
$received_stmt->bind_param('s', $user_id);
$received_stmt->execute();
$received_result = $received_stmt->get_result();
if (!$received_result) {
    die('받은 쿠폰 조회 오류: ' . $conn->error);
}
$received = [];
while ($row = $received_result->fetch_assoc()) {
    $received[] = $row['coupon_id'];
}

// 보조 함수들
function isCouponUsed($conn, $user_id, $coupon_id) {
    $stmt = $conn->prepare("
        SELECT is_used 
        FROM user_coupons 
        WHERE user_id = ? AND coupon_id = ?
        LIMIT 1
    ");
    $stmt->bind_param('ss', $user_id, $coupon_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row && $row['is_used'] == 1;
}

function hasCoupon($conn, $user_id, $coupon_id) {
    $stmt = $conn->prepare("
        SELECT 1 
        FROM user_coupons 
        WHERE user_id = ? AND coupon_id = ?
        LIMIT 1
    ");
    $stmt->bind_param('ss', $user_id, $coupon_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
?>
