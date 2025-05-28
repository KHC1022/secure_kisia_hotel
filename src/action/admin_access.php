<?php
include_once __DIR__ . '/../includes/session.php';

// 관리자만 접근 허용
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    http_response_code(404);
    error_log("[접근 차단] 비관리자 접근 시도 - IP: {$_SERVER['REMOTE_ADDR']}, URI: {$_SERVER['REQUEST_URI']}");
    include_once __DIR__ . '/../error/error.php';
    exit;
}

?>