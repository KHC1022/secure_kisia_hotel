<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(404);
    exit;
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');

if (empty($username) || empty($email)) {
    echo "<script>alert('이름과 이메일을 모두 입력해주세요.'); history.back();</script>";
    exit;
}

// SQL Injection 방지용 Prepared Statement
$stmt = $conn->prepare("SELECT real_id, username FROM users WHERE username = ? AND email = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $safe_username = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
    $safe_real_id = htmlspecialchars($user['real_id'], ENT_QUOTES, 'UTF-8');
    echo "<script>
            alert('{$safe_username} 님의 아이디는 {$safe_real_id} 입니다.');
            window.location.href = '../user/login.php';
          </script>";
} else {
    // 사용자 존재 여부 숨기기
    echo "<script>
            alert('입력하신 정보와 일치하는 계정을 찾을 수 없습니다.');
            history.back();
          </script>";
}
?>
