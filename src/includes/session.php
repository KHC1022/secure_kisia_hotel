<?php
// 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF 토큰 생성
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>