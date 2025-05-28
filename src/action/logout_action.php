<?php
// 세션 시작
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php'; // 필요 시 유지

// 모든 세션 변수 제거
$_SESSION = array();

// 세션 쿠키 삭제
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 세션 종료
session_destroy();

// 사용자 알림 및 리디렉션
echo "<script>
    alert('로그아웃 되었습니다.');
    window.location.href = '../index.php';
</script>";
exit;
?>
