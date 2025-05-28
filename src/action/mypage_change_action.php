<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$user_id = $_SESSION['user_id'] ?? null;
$password = $_POST['password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$new_password_check = $_POST['new_password_check'] ?? '';

if (!$user_id || !$password || !$new_password || !$new_password_check) {
    echo "<script>alert('모든 항목을 입력해주세요.'); history.back();</script>";
    exit;
}

// 비밀번호 정책 검사
if (
    strlen($new_password) < 8 ||
    !preg_match('/[A-Z]/', $new_password) ||
    !preg_match('/[a-z]/', $new_password) ||
    !preg_match('/[0-9]/', $new_password) ||
    !preg_match('/[\W]/', $new_password)
) {
    echo "<script>alert('새 비밀번호는 8자 이상, 대/소문자, 숫자, 특수문자를 포함해야 합니다.'); history.back();</script>";
    exit;
}

// 기존 사용자 비밀번호 조회
$stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($password, $user['password'])) {
    echo "<script>alert('기존 비밀번호가 틀렸습니다.'); history.back();</script>";
    exit;
}

if ($new_password !== $new_password_check) {
    echo "<script>alert('새 비밀번호가 일치하지 않습니다.'); history.back();</script>";
    exit;
}

$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// 비밀번호 업데이트
$update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
$update_stmt->bind_param("si", $hashed_password, $user_id);
$success = $update_stmt->execute();

if ($success) {
    echo "<script>alert('비밀번호가 성공적으로 변경되었습니다.'); location.href='../user/mypage.php';</script>";
} else {
    error_log("비밀번호 업데이트 실패: " . $conn->error);
    echo "<script>alert('비밀번호 변경 중 오류가 발생했습니다.'); history.back();</script>";
}
exit;
