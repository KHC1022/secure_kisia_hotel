<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../action/login_check.php';

$user_id = $_SESSION['user_id'];
$charge_point = isset($_POST['point']) && is_numeric($_POST['point']) ? (float)$_POST['point'] : 0;

// 입력값 유효성 검사
if ($charge_point <= 0) {
    echo "<script>alert('0보다 큰 금액을 입력해주세요.'); history.back();</script>";
    exit;
}

if ($charge_point > 10000000) {
    echo "<script>alert('충전 금액은 1,000만 원 이하로 입력해주세요.'); history.back();</script>";
    exit;
}

// 소수점 2자리까지만 반올림
$charge_point = round($charge_point, 2);

// 현재 포인트 조회
$stmt = $conn->prepare("SELECT point FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_point);
$stmt->fetch();
$stmt->close();

$current_point = $current_point ?? 0.0;
$new_point = round($current_point + $charge_point, 2);

// 총 보유 포인트 한도 제한
if ($new_point > 100000000) {
    echo "<script>alert('보유 포인트는 1억 원을 초과할 수 없습니다.'); history.back();</script>";
    exit;
}

// 포인트 업데이트
$update_stmt = $conn->prepare("UPDATE users SET point = ? WHERE user_id = ?");
$update_stmt->bind_param("di", $new_point, $user_id);

if ($update_stmt->execute()) {
    echo "<script>alert('포인트가 충전되었습니다.'); location.href='../user/mypage.php';</script>";
} else {
    error_log("포인트 충전 실패: " . $update_stmt->error);
    echo "<script>alert('포인트 충전에 실패했습니다.'); history.back();</script>";
}

$update_stmt->close();
?>
