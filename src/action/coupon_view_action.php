<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die("<script>alert('로그인이 필요합니다.'); location.href='../user/login.php';</script>");
}

$user_id = $_SESSION['user_id'];
$available_coupons = [];

try {
    $stmt = $conn->prepare("
        SELECT c.*, uc.is_used
        FROM user_coupons uc
        JOIN coupons c ON uc.coupon_id = c.coupon_id
        WHERE uc.user_id = ?
          AND c.is_active = 1
          AND c.start_date <= CURDATE()
          AND c.end_date >= CURDATE()
        ORDER BY c.created_at DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $available_coupons = $result->fetch_all(MYSQLI_ASSOC);
} catch (Throwable $e) {
    error_log("[쿠폰 조회 오류] " . $e->getMessage());
    echo "<script>alert('쿠폰 정보를 불러오는 중 오류가 발생했습니다.'); history.back();</script>";
    exit;
}
?>
