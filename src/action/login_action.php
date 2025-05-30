<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$id = trim($_POST['real_id'] ?? '');
$password = $_POST['password'] ?? '';

if ($id === '' || $password === '') {
    echo "<script>alert('아이디 또는 비밀번호를 입력해주세요.'); history.back();</script>";
    exit;
}

// 사용자 조회
$stmt = $conn->prepare("SELECT user_id, real_id, username, password, is_admin, login_attempts, last_failed_at FROM users WHERE real_id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $user_id = $user['user_id'];
    $attempts = $user['login_attempts'];
    $last_failed = $user['last_failed_at'];

    // 5회 이상 실패한 경우
    if ($attempts >= 5) {
        $now = new DateTime();
        $last = new DateTime($last_failed);
        $diff = $now->getTimestamp() - $last->getTimestamp();

        // 5분 제한
        if ($diff < 300) {
            $remain = 300 - $diff;
            echo "<script>alert('로그인을 5회 이상 실패하였습니다. 약 " . ceil($remain / 60) . "분 뒤 다시 시도해주세요.'); history.back();</script>";
            exit;
        } else {
            // 5분이 지나면 시도 초기화
            $stmt = $conn->prepare("UPDATE users SET login_attempts = 0, last_failed_at = NULL WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $attempts = 0;
        }
    }

    // 로그인 시도
    if (password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        // 로그인 성공 → 실패 기록 초기화
        $stmt = $conn->prepare("UPDATE users SET login_attempts = 0, last_failed_at = NULL WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $_SESSION['is_login'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['real_id'] = $user['real_id'];
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['is_admin'] = $user['is_admin'];

        if ($user['is_admin']) {
            echo "<script>alert('관리자님 안녕하세요.'); location.href = '../admin/admin.php';</script>";
        } else {
            echo "<script>alert('로그인 되었습니다.'); location.href = '../index.php';</script>";
        }
        exit;

    } else {
        // 실패 기록 증가
        $stmt = $conn->prepare("UPDATE users SET login_attempts = login_attempts + 1, last_failed_at = NOW() WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        echo "<script>alert('아이디 또는 비밀번호가 일치하지 않습니다.'); history.back();</script>";
        exit;
    }

} else {
    echo "<script>alert('존재하지 않는 계정입니다.'); history.back();</script>";
    exit;
}
?>
