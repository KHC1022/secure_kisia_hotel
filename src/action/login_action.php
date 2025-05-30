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
$stmt = $conn->prepare("SELECT user_id, real_id, username, password, is_admin FROM users WHERE real_id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {

    //세션 ID 재생성
    session_regenerate_id(true);

    // 세션 설정
    $_SESSION['is_login'] = true;
    $_SESSION['username'] = $user['username'];
    $_SESSION['real_id'] = $user['real_id'];
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['is_admin'] = $user['is_admin'];

    // 로그인 성공 후 리디렉션
    if ($user['is_admin']) {
        echo "<script>alert('관리자님 안녕하세요.'); location.href = '../admin/admin.php';</script>";
    } else {
        echo "<script>alert('로그인 되었습니다.'); location.href = '../index.php';</script>";
    }
    exit;
} else {
    // 로그인 실패
    echo "<script>alert('아이디 또는 비밀번호가 일치하지 않습니다.'); history.back();</script>";
    exit;
}
?>
