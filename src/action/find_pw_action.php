<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// CSRF 토큰 검증
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

// 입력값 필터링
$real_id = $conn->real_escape_string($_POST['real_id']);
$username = $conn->real_escape_string($_POST['username']);
$email = $conn->real_escape_string($_POST['email']);

// 사용자 존재 확인
$sql = "SELECT user_id FROM users WHERE real_id='$real_id' AND username='$username' AND email='$email'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // 임시 비밀번호 생성
    $temp_password = bin2hex(random_bytes(4)); // 8자리 임시 비밀번호
    $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

    // 비밀번호 업데이트
    $update_sql = "UPDATE users SET password='$hashed_password' WHERE user_id=" . $user['user_id'];
    $conn->query($update_sql);

    echo "<script>
        alert('임시 비밀번호는 \"$temp_password\" 입니다. 로그인 후 비밀번호를 변경해주세요.');
        window.location.href = '../user/login.php';
    </script>";
    exit;
} else {
    echo "<script>
        alert('입력하신 정보가 일치하지 않습니다.');
        history.back();
    </script>";
    exit;
}
