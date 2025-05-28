<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit('허용되지 않은 요청 방식입니다.');
}

// 입력값 필터링
$code = trim($_GET['code'] ?? '');
$name = trim($_GET['name'] ?? '');
$discount_type = trim($_GET['discount_type'] ?? '');
$discount_value = (int)($_GET['discount_value'] ?? 0);
$start_date = trim($_GET['start_date'] ?? '');
$end_date = trim($_GET['end_date'] ?? '');
$minimum_purchase = (int)($_GET['minimum_purchase'] ?? 0);
$maximum_discount = isset($_GET['maximum_discount']) ? (int)$_GET['maximum_discount'] : null;
$usage_limit = isset($_GET['usage_limit']) ? (int)$_GET['usage_limit'] : null;
$is_active = isset($_GET['is_active']) ? 1 : 0;

// 할인 유형 및 날짜 유효성 검사
if (!in_array($discount_type, ['percentage', 'fixed'])) {
    echo "<script>alert('유효하지 않은 할인 유형입니다.'); history.back();</script>";
    exit;
}
if ($discount_type === 'percentage' && $discount_value > 100) {
    echo "<script>alert('퍼센트 할인은 100%를 초과할 수 없습니다.'); history.back();</script>";
    exit;
}
if (strtotime($start_date) === false || strtotime($end_date) === false || $start_date > $end_date) {
    echo "<script>alert('날짜 형식이 잘못되었거나 시작일보다 종료일이 빠릅니다.'); history.back();</script>";
    exit;
}

// 쿠폰 존재 여부 확인
$check_stmt = $conn->prepare("SELECT 1 FROM coupons WHERE code = ?");
$check_stmt->bind_param("s", $code);
$check_stmt->execute();
$check_stmt->store_result();
if ($check_stmt->num_rows === 0) {
    echo "<script>alert('존재하지 않는 쿠폰입니다.'); history.back();</script>";
    exit;
}

// 쿠폰 수정 처리
$stmt = $conn->prepare("
    UPDATE coupons SET 
        name = ?, 
        discount_type = ?, 
        discount_value = ?, 
        start_date = ?, 
        end_date = ?, 
        minimum_purchase = ?, 
        maximum_discount = ?, 
        usage_limit = ?, 
        is_active = ?
    WHERE code = ?
");
$stmt->bind_param(
    "ssissiiiss",
    $name,
    $discount_type,
    $discount_value,
    $start_date,
    $end_date,
    $minimum_purchase,
    $maximum_discount,
    $usage_limit,
    $is_active,
    $code
);

if ($stmt->execute()) {
    echo "<script>alert('쿠폰이 성공적으로 수정되었습니다.'); location.href='../admin/admin.php?tab=coupons';</script>";
} else {
    echo "<script>alert('쿠폰 수정 중 오류가 발생했습니다.'); history.back();</script>";
}
