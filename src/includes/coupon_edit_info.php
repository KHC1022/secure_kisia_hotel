<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

// 쿠폰 ID 확인 및 필터링
if (!isset($_POST['coupon_edit']) || empty($_POST['coupon_edit'])) {
    header("Location: ../admin/admin.php?tab=coupons");
    exit;
}

$code = $_POST['coupon_edit'];

// 쿠폰 정보 조회 (Prepared Statement 사용)
$sql = "SELECT * FROM coupons WHERE code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('존재하지 않는 쿠폰입니다.'); history.back();</script>";
    exit;
}

$coupon = $result->fetch_assoc();

// 쿠폰 사용 횟수 조회
$usage_sql = "SELECT COUNT(*) as usage_count FROM user_coupons WHERE coupon_id = ?";
$usage_stmt = $conn->prepare($usage_sql);
$usage_stmt->bind_param('s', $code);
$usage_stmt->execute();
$usage_result = $usage_stmt->get_result();
$usage_data = $usage_result->fetch_assoc();
$coupon['usage_count'] = $usage_data['usage_count'] ?? 0;

// 날짜 및 상태 계산
$today = date('Y-m-d');
$coupon['is_expired'] = ($coupon['end_date'] < $today);
$coupon['is_active_now'] = ($coupon['is_active'] && !$coupon['is_expired']);
$coupon['is_usage_limited'] = (!is_null($coupon['usage_limit']));
$coupon['is_usage_exceeded'] = ($coupon['is_usage_limited'] && $coupon['usage_count'] >= $coupon['usage_limit']);

// 할인 정보 포맷팅
$coupon['discount_display'] = $coupon['discount_type'] === 'percentage' 
    ? $coupon['discount_value'] . '%' 
    : number_format($coupon['discount_value']) . '원';

$coupon['minimum_purchase_display'] = number_format($coupon['minimum_purchase']) . '원';
$coupon['maximum_discount_display'] = $coupon['maximum_discount'] 
    ? number_format($coupon['maximum_discount']) . '원' 
    : '제한 없음';

// 상태 메시지 설정
if (!$coupon['is_active']) {
    $coupon['status_message'] = '비활성화된 쿠폰입니다.';
} elseif ($coupon['is_expired']) {
    $coupon['status_message'] = '만료된 쿠폰입니다.';
} elseif ($coupon['is_usage_exceeded']) {
    $coupon['status_message'] = '사용 횟수가 초과된 쿠폰입니다.';
} else {
    $coupon['status_message'] = '사용 가능한 쿠폰입니다.';
}
?>
