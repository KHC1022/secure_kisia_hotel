<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

// 관리자가 아닌 경우 CSRF 토큰 검증
if (!isset($_SESSION['is_admin'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('허용되지 않은 요청 방식입니다.');
}

// 입력값 필터링
$code = trim($_POST['code'] ?? '');
$name = trim($_POST['name'] ?? '');
$discount_type = trim($_POST['discount_type'] ?? '');
$discount_value = (int)($_POST['discount_value'] ?? 0);
$start_date = trim($_POST['start_date'] ?? '');
$end_date = trim($_POST['end_date'] ?? '');
$minimum_purchase = (int)($_POST['minimum_purchase'] ?? 0);
$maximum_discount = isset($_POST['maximum_discount']) ? (int)$_POST['maximum_discount'] : null;
$usage_limit = isset($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null;
$is_active = isset($_POST['is_active']) ? 1 : 0;

// 입력값 길이 제한 검사
if (strlen($code) > 30) {
    echo "<script>alert('쿠폰 코드는 30자 이내로 입력해주세요.'); history.back();</script>";
    exit;
}
if (strlen($name) > 50) {
    echo "<script>alert('쿠폰 이름은 50자 이내로 입력해주세요.'); history.back();</script>";
    exit;
}
if (strlen($discount_type) > 20) {
    echo "<script>alert('할인 유형은 20자 이내로 입력해주세요.'); history.back();</script>";
    exit;
}
if (!empty($start_date) && strlen($start_date) > 20) {
    echo "<script>alert('시작일 형식이 잘못되었습니다.'); history.back();</script>";
    exit;
}
if (!empty($end_date) && strlen($end_date) > 20) {
    echo "<script>alert('종료일 형식이 잘못되었습니다.'); history.back();</script>";
    exit;
}

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
