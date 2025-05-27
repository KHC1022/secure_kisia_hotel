<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';

$user_id = $_SESSION['user_id'];
$charge_point = isset($_GET['point']) ? (float)$_GET['point'] : 0;

// 포인트 입력값 검증
if ($charge_point <= 0) {
    echo "<script>alert('0보다 큰 금액을 입력해주세요.'); history.back();</script>";
    exit;
}

// 소수점 2자리까지 반올림
$charge_point = round($charge_point, 2);

// 현재 포인트 조회
$sql = "SELECT point FROM users WHERE user_id='$user_id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$current_point = $row ? (float)$row['point'] : 0;

// 포인트 합산
$new_point = $current_point + $charge_point;

// 소수점 2자리까지 저장 후 합산
$update_sql = "UPDATE users SET point = ROUND('$new_point', 2) WHERE user_id = '$user_id'";
$update_result = mysqli_query($conn, $update_sql);

if ($update_result) {
    echo "<script>alert('포인트가 충전되었습니다.'); location.href='../user/mypage.php';</script>";
} else {
    echo "<script>alert('포인트 충전에 실패했습니다.'); history.back();</script>";
}
?>