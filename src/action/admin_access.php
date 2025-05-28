<?php
include_once __DIR__ . '/../includes/session.php';

// 관리자만 접근 허용
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    http_response_code(404);
    include_once __DIR__ . '/../error/error.php';
    exit;
}

?>