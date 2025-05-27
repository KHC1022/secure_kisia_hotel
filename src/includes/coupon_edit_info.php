<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

// 쿠폰 ID 확인
if (!isset($_GET['coupon_edit'])) {
    header("Location: ../admin/admin.php?tab=coupons");
    exit;
}

$code = $_GET['coupon_edit'];

$sql = "SELECT * FROM coupons WHERE code = '$code'";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "<script>alert('존재하지 않는 쿠폰입니다.'); history.back();</script>";
    exit;
}

$coupon = mysqli_fetch_assoc($result);

$usage_sql = "SELECT COUNT(*) as usage_count FROM user_coupons WHERE coupon_id = '$code'";
$usage_result = mysqli_query($conn, $usage_sql);
$usage_data = mysqli_fetch_assoc($usage_result);
$coupon['usage_count'] = $usage_data['usage_count'];

// 쿠폰 만료 여부 확인
$today = date('Y-m-d');
$coupon['is_expired'] = ($coupon['end_date'] < $today);
$coupon['is_active_now'] = ($coupon['is_active'] && !$coupon['is_expired']);

// 쿠폰 사용 제한 확인
$coupon['is_usage_limited'] = ($coupon['usage_limit'] !== null);
$coupon['is_usage_exceeded'] = ($coupon['is_usage_limited'] && $coupon['usage_count'] >= $coupon['usage_limit']);

// 쿠폰 할인 정보 포맷팅
$coupon['discount_display'] = $coupon['discount_type'] === 'percentage' 
    ? $coupon['discount_value'] . '%' 
    : number_format($coupon['discount_value']) . '원';

$coupon['minimum_purchase_display'] = number_format($coupon['minimum_purchase']) . '원';
$coupon['maximum_discount_display'] = $coupon['maximum_discount'] 
    ? number_format($coupon['maximum_discount']) . '원' 
    : '제한 없음';

// 쿠폰 상태 메시지
$coupon['status_message'] = '';
if (!$coupon['is_active']) {
    $coupon['status_message'] = '비활성화된 쿠폰입니다.';
} else if ($coupon['is_expired']) {
    $coupon['status_message'] = '만료된 쿠폰입니다.';
} else if ($coupon['is_usage_exceeded']) {
    $coupon['status_message'] = '사용 횟수가 초과된 쿠폰입니다.';
} else {
    $coupon['status_message'] = '사용 가능한 쿠폰입니다.';
}
?> 