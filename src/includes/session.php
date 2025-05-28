<?php
// 세션 유효시간 설정 (예: 1800초 = 30분)
ini_set('session.gc_maxlifetime', 600);
session_set_cookie_params(600);

// 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 사용자 활동 기반 만료
if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > 600) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();

// CSRF 토큰 생성
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
