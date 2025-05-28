<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../action/login_check.php';

// CSRF 토큰 검증
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}

// 토큰 일회성 사용
unset($_SESSION['csrf_token']);

$user_id = (int)$_SESSION['user_id'];
$coupon_id = isset($_POST['coupon_id']) ? (int)$_POST['coupon_id'] : 0;

if ($coupon_id < 1) {
    echo "<script>alert('잘못된 쿠폰 요청입니다.'); history.back();</script>";
    exit;
}

// 쿠폰 유효성 검사 (선택적 적용)
$check_coupon_stmt = $conn->prepare("SELECT 1 FROM coupons WHERE coupon_id = ? AND is_active = 1 AND start_date <= CURDATE() AND end_date >= CURDATE()");
$check_coupon_stmt->bind_param("i", $coupon_id);
$check_coupon_stmt->execute();
$check_coupon_stmt->store_result();
if ($check_coupon_stmt->num_rows === 0) {
    echo "<script>alert('유효하지 않은 쿠폰입니다.'); history.back();</script>";
    exit;
}
$check_coupon_stmt->close();

// 중복 수령 체크
$stmt = $conn->prepare("SELECT 1 FROM user_coupons WHERE user_id = ? AND coupon_id = ?");
$stmt->bind_param("ii", $user_id, $coupon_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    // 쿠폰 수령 처리
    $insert_stmt = $conn->prepare("INSERT INTO user_coupons (user_id, coupon_id, received_at) VALUES (?, ?, NOW())");
    $insert_stmt->bind_param("ii", $user_id, $coupon_id);
    if ($insert_stmt->execute()) {
        echo "<script>alert('쿠폰을 받았습니다.'); location.href='coupon-list.php';</script>";
    } else {
        error_log("쿠폰 등록 실패: " . $insert_stmt->error);
        echo "<script>alert('쿠폰 수령 중 오류가 발생했습니다.'); history.back();</script>";
    }
    $insert_stmt->close();
} else {
    echo "<script>alert('이미 쿠폰을 받았습니다.'); history.back();</script>";
}

$stmt->close();
?>
